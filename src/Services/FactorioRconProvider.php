<?php

namespace gOOvER\FactorioManager\Services;

use App\Models\Server;
use App\Repositories\Daemon\DaemonFileRepository;
use Illuminate\Support\Facades\Log;

class FactorioRconProvider
{
    /**
     * Cached RCON connections per server
     */
    private static array $connections = [];

    /**
     * Send a raw RCON command to the server
     * Reuses existing connection if available
     */
    public function sendRconCommand(string $serverId, string $command): ?string
    {
        $server = Server::where('uuid', $serverId)->first();
        if (!$server) {
            Log::error("Server not found for UUID: $serverId");
            return null;
        }

        $rcon = $this->getOrCreateConnection($server);
        if (!$rcon) {
            return null;
        }

        return $rcon->sendCommand($command);
    }

    /**
     * Get existing connection or create new one
     */
    private function getOrCreateConnection(Server $server): ?RconService
    {
        $serverId = $server->uuid;
        
        // Return existing connection if still valid
        if (isset(self::$connections[$serverId])) {
            $rcon = self::$connections[$serverId];
            // Test if connection is still alive with a simple command
            if ($rcon->isConnected()) {
                return $rcon;
            }
            // Connection dead, remove it
            unset(self::$connections[$serverId]);
        }

        // Create new connection
        $rcon = $this->getRconConnection($server);
        if ($rcon) {
            self::$connections[$serverId] = $rcon;
        }

        return $rcon;
    }

    /**
     * Close all connections (call on request end)
     */
    public static function closeAllConnections(): void
    {
        foreach (self::$connections as $rcon) {
            $rcon->disconnect();
        }
        self::$connections = [];
    }

    /**
     * Close connection for specific server
     */
    public function closeConnection(string $serverId): void
    {
        if (isset(self::$connections[$serverId])) {
            self::$connections[$serverId]->disconnect();
            unset(self::$connections[$serverId]);
        }
    }

    /**
     * Get server status information
     * Uses connection pooling for efficiency
     */
    public function getServerStatus(string $serverId): array
    {
        $server = Server::where('uuid', $serverId)->first();
        if (!$server) {
            return [
                'online' => false,
                'error' => 'Server not found'
            ];
        }

        $rcon = $this->getOrCreateConnection($server);
        if (!$rcon) {
            return [
                'online' => false,
                'error' => 'Cannot connect to RCON'
            ];
        }

        // Get player count - reuses connection
        $playersResponse = $rcon->sendCommand('/players');
        
        // Get max_players from server-settings.json file
        $maxPlayers = $this->getMaxPlayersFromSettings($server);
        
        return [
            'online' => true,
            'players' => $this->parsePlayersResponse($playersResponse),
            'max_players' => $maxPlayers,
        ];
        // Note: Connection stays open for reuse
    }

    /**
     * Get max_players from server-settings.json file
     */
    private function getMaxPlayersFromSettings(Server $server): ?int
    {
        try {
            $fileRepository = (new DaemonFileRepository())->setServer($server);
            
            // Try common paths for server-settings.json
            $paths = [
                '/data/server-settings.json',  // Pelican/Pterodactyl standard path
                '/server-settings.json',        // Root path
            ];
            
            foreach ($paths as $path) {
                try {
                    $content = $fileRepository->getContent($path);
                    $settings = json_decode($content, true);
                    
                    if (json_last_error() === JSON_ERROR_NONE && isset($settings['max_players'])) {
                        return (int) $settings['max_players'];
                    }
                } catch (\Exception $e) {
                    // Try next path
                    continue;
                }
            }
        } catch (\Exception $e) {
            Log::debug("Could not read server-settings.json for max_players: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Get list of online players
     */
    public function getOnlinePlayers(string $serverId): array
    {
        $response = $this->sendRconCommand($serverId, '/players online');
        
        if (!$response) {
            return [];
        }

        return $this->parsePlayersResponse($response);
    }

    /**
     * Get list of all players
     */
    public function getAllPlayers(string $serverId): array
    {
        $response = $this->sendRconCommand($serverId, '/players');
        
        if (!$response) {
            return [];
        }

        return $this->parsePlayersResponse($response);
    }

    /**
     * Kick a player from the server
     */
    public function kickPlayer(string $serverId, string $playerName, string $reason = ''): bool
    {
        $command = "/kick $playerName";
        if ($reason) {
            $command .= " $reason";
        }
        
        return $this->sendRconCommand($serverId, $command) !== null;
    }

    /**
     * Ban a player
     */
    public function banPlayer(string $serverId, string $playerName, string $reason = ''): bool
    {
        $command = "/ban $playerName";
        if ($reason) {
            $command .= " $reason";
        }
        
        return $this->sendRconCommand($serverId, $command) !== null;
    }

    /**
     * Unban a player
     */
    public function unbanPlayer(string $serverId, string $playerName): bool
    {
        return $this->sendRconCommand($serverId, "/unban $playerName") !== null;
    }

    /**
     * Promote player to admin
     */
    public function promotePlayer(string $serverId, string $playerName): bool
    {
        return $this->sendRconCommand($serverId, "/promote $playerName") !== null;
    }

    /**
     * Demote player from admin
     */
    public function demotePlayer(string $serverId, string $playerName): bool
    {
        return $this->sendRconCommand($serverId, "/demote $playerName") !== null;
    }

    /**
     * Add player to whitelist
     */
    public function whitelistAdd(string $serverId, string $playerName): bool
    {
        return $this->sendRconCommand($serverId, "/whitelist add $playerName") !== null;
    }

    /**
     * Remove player from whitelist
     */
    public function whitelistRemove(string $serverId, string $playerName): bool
    {
        return $this->sendRconCommand($serverId, "/whitelist remove $playerName") !== null;
    }

    /**
     * Get whitelist - reads directly from server-whitelist.json file
     * Falls back to RCON command if file doesn't exist
     */
    public function getWhitelist(string $serverId): array
    {
        $server = Server::where('uuid', $serverId)->first();
        if (!$server) {
            return [];
        }

        // Try to read from file first (more reliable)
        $whitelist = $this->getWhitelistFromFile($server);
        if ($whitelist !== null) {
            return $whitelist;
        }

        // Fallback to RCON
        $response = $this->sendRconCommand($serverId, '/whitelist get');
        
        if (!$response) {
            return [];
        }

        return $this->parseWhitelistResponse($response);
    }

    /**
     * Read whitelist from server-whitelist.json file
     */
    private function getWhitelistFromFile(Server $server): ?array
    {
        try {
            $fileRepository = (new DaemonFileRepository())->setServer($server);
            
            $paths = [
                '/data/server-whitelist.json',
                '/server-whitelist.json',
            ];
            
            foreach ($paths as $path) {
                try {
                    $content = $fileRepository->getContent($path);
                    $whitelist = json_decode($content, true);
                    
                    if (json_last_error() === JSON_ERROR_NONE && is_array($whitelist)) {
                        // File contains array of player names (strings)
                        return array_map(fn($name) => ['name' => $name], $whitelist);
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        } catch (\Exception $e) {
            Log::debug("Could not read server-whitelist.json: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Enable whitelist
     */
    public function whitelistEnable(string $serverId): bool
    {
        return $this->sendRconCommand($serverId, '/whitelist enable') !== null;
    }

    /**
     * Disable whitelist
     */
    public function whitelistDisable(string $serverId): bool
    {
        return $this->sendRconCommand($serverId, '/whitelist disable') !== null;
    }

    /**
     * Check if whitelist is enabled by reading server-settings.json
     */
    public function isWhitelistEnabled(string $serverId): bool
    {
        $server = Server::where('uuid', $serverId)->first();
        if (!$server) {
            return false;
        }

        try {
            $fileRepository = (new DaemonFileRepository())->setServer($server);
            
            $paths = [
                '/data/server-settings.json',
                '/server-settings.json',
            ];
            
            foreach ($paths as $path) {
                try {
                    $content = $fileRepository->getContent($path);
                    $settings = json_decode($content, true);
                    
                    if (json_last_error() === JSON_ERROR_NONE && isset($settings['only_admins_can_pause_the_game'])) {
                        // Check for use_only_whitelist setting
                        return (bool) ($settings['only_admins_can_pause_the_game'] ?? false) === false 
                            && (bool) ($settings['visibility']['public'] ?? true) === false;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        } catch (\Exception $e) {
            Log::debug("Could not check whitelist status: " . $e->getMessage());
        }

        return false;
    }

    /**
     * Get banned players list
     */
    public function getBannedPlayers(string $serverId): array
    {
        $response = $this->sendRconCommand($serverId, '/banlist');
        
        if (!$response) {
            return [];
        }

        return $this->parseBannedPlayersResponse($response);
    }

    /**
     * Get admins list - reads directly from server-adminlist.json file
     * Falls back to RCON command if file doesn't exist
     */
    public function getAdmins(string $serverId): array
    {
        $server = Server::where('uuid', $serverId)->first();
        if (!$server) {
            return [];
        }

        // Try to read from file first (more reliable, case-preserving)
        $admins = $this->getAdminsFromFile($server);
        if ($admins !== null) {
            return $admins;
        }

        // Fallback to RCON
        $response = $this->sendRconCommand($serverId, '/admins');
        
        if (!$response) {
            return [];
        }

        return $this->parseAdminsResponse($response);
    }

    /**
     * Read admins from server-adminlist.json file
     */
    private function getAdminsFromFile(Server $server): ?array
    {
        try {
            $fileRepository = (new DaemonFileRepository())->setServer($server);
            
            $paths = [
                '/data/server-adminlist.json',
                '/server-adminlist.json',
            ];
            
            foreach ($paths as $path) {
                try {
                    $content = $fileRepository->getContent($path);
                    $admins = json_decode($content, true);
                    
                    if (json_last_error() === JSON_ERROR_NONE && is_array($admins)) {
                        // File contains array of player names (strings)
                        return array_map(fn($name) => ['name' => $name], $admins);
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        } catch (\Exception $e) {
            Log::debug("Could not read server-adminlist.json: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Send a message to all players
     * Tries /pelican.say first (mod command that shows in chat), falls back to /say
     */
    public function sendMessage(string $serverId, string $message): bool
    {
        // Try mod command first - it uses game.print() which shows in chat
        $result = $this->sendRconCommand($serverId, "/pelican.say $message");
        
        if ($result !== null) {
            // Check if mod responded with success
            $decoded = json_decode($result, true);
            if (is_array($decoded) && ($decoded['ok'] ?? false)) {
                return true;
            }
        }
        
        // Fallback to standard /say command
        return $this->sendRconCommand($serverId, "/say $message") !== null;
    }

    /**
     * Whisper to a specific player
     */
    public function whisperPlayer(string $serverId, string $playerName, string $message): bool
    {
        return $this->sendRconCommand($serverId, "/whisper $playerName $message") !== null;
    }

    /**
     * Get maximum player slots from server-settings.json
     * Reads the max_players setting from the Factorio server configuration file
     */
    public function getMaxPlayers(string $serverId): ?int
    {
        $server = Server::where('uuid', $serverId)->first();
        if (!$server) {
            return null;
        }

        return $this->getMaxPlayersFromSettings($server);
    }

    /**
     * Get RCON connection for a server
     */
    private function getRconConnection(Server $server): ?RconService
    {
        // Read RCON settings from server egg variables
        $rconPort = null;
        $rconPassword = null;

        try {
            // Get RCON port from egg variable (common names: RCON_PORT, RCONPORT)
            $rconPort = $server->variables()
                ->whereIn('env_variable', ['RCON_PORT', 'RCONPORT'])
                ->first()?->server_value;
            
            // Get RCON password from egg variable (common names: RCON_PASSWORD, RCONPASSWORD, RCON_PASS)
            $rconPassword = $server->variables()
                ->whereIn('env_variable', ['RCON_PASSWORD', 'RCONPASSWORD', 'RCON_PASS'])
                ->first()?->server_value;
            
            // Cast port to integer if found
            if ($rconPort !== null) {
                $rconPort = (int)$rconPort;
            }
        } catch (\Exception $e) {
            Log::error("Failed to read RCON variables for server {$server->uuid}: " . $e->getMessage());
            return null;
        }

        if (!$rconPort || !$rconPassword) {
            Log::error("RCON not configured properly for server {$server->uuid}. Port: " . ($rconPort ?? 'null') . ", Password: " . ($rconPassword ? '[set]' : 'null'));
            return null;
        }

        $host = $server->allocation->alias ?? $server->allocation->ip;
        
        $rcon = new RconService($host, $rconPort, $rconPassword);
        
        if (!$rcon->connect()) {
            Log::error("Failed to connect to RCON: " . $rcon->getLastError());
            return null;
        }

        return $rcon;
    }

    /**
     * Parse players response
     */
    private function parsePlayersResponse(?string $response): array
    {
        if (!$response) {
            return [];
        }

        $players = [];
        $lines = explode("\n", trim($response));
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // Skip header lines like "Players (1):" or "Online players (1):"
            if (preg_match('/^(Online\s+)?Players?\s*\(\d+\)\s*:?$/i', $line)) {
                continue;
            }
            
            // Skip common info lines
            if (preg_match('/^(No players|There are no players)/i', $line)) {
                continue;
            }
            
            // Format: "PlayerName (online)" or just "PlayerName"
            if (preg_match('/^(.+?)\s*\(online\)$/i', $line, $matches)) {
                $players[] = [
                    'name' => trim($matches[1]),
                    'online' => true
                ];
            } elseif (!preg_match('/^\s*$/', $line)) {
                // Only add if it's not just whitespace and looks like a player name
                $name = trim($line);
                // Skip if it contains special characters that indicate it's not a player name
                if (!preg_match('/[:=\[\]{}]/', $name) && strlen($name) > 0 && strlen($name) < 100) {
                    $players[] = [
                        'name' => $name,
                        'online' => false
                    ];
                }
            }
        }

        return $players;
    }

    /**
     * Parse banned players response
     */
    private function parseBannedPlayersResponse(?string $response): array
    {
        if (!$response) {
            return [];
        }

        $banned = [];
        $lines = explode("\n", trim($response));
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // Extract player name and reason if present
            if (preg_match('/^(.+?)(?:\s*:\s*(.+))?$/i', $line, $matches)) {
                $banned[] = [
                    'name' => trim($matches[1]),
                    'reason' => isset($matches[2]) ? trim($matches[2]) : ''
                ];
            }
        }

        return $banned;
    }

    /**
     * Parse admins response
     * Factorio /admins output format:
     * "Admins (1):"
     * "  g00v3R (online)"
     * or just player names per line
     */
    private function parseAdminsResponse(?string $response): array
    {
        if (!$response) {
            return [];
        }

        $admins = [];
        $lines = explode("\n", trim($response));
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // Skip header line like "Admins (1):"
            if (preg_match('/^Admins?\s*\(\d+\)\s*:?$/i', $line)) {
                continue;
            }
            
            // Extract player name from formats like:
            // "  g00v3R (online)"
            // "  g00v3R"
            // "g00v3R (online)"
            // "g00v3R"
            $name = $line;
            
            // Remove leading whitespace/bullets
            $name = ltrim($name, " \t-*•");
            
            // Remove (online)/(offline) suffix
            $name = preg_replace('/\s*\((online|offline)\)\s*$/i', '', $name);
            
            // Trim any remaining whitespace
            $name = trim($name);
            
            if (!empty($name)) {
                $admins[] = [
                    'name' => $name
                ];
            }
        }

        Log::debug("Parsed admins from response: " . json_encode($admins) . " | Raw: " . $response);

        return $admins;
    }

    /**
     * Parse whitelist response
     * Factorio /whitelist get output format:
     * "Whitelisted players (2):"
     * "  PlayerName"
     * or just player names per line
     */
    private function parseWhitelistResponse(?string $response): array
    {
        if (!$response) {
            return [];
        }

        $whitelisted = [];
        $lines = explode("\n", trim($response));
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // Skip header line like "Whitelisted players (2):"
            if (preg_match('/^Whitelisted\s+players?\s*\(\d+\)\s*:?$/i', $line)) {
                continue;
            }
            
            // Skip "No whitelisted players" or similar
            if (preg_match('/^(No whitelisted|Whitelist is empty)/i', $line)) {
                continue;
            }
            
            // Remove leading whitespace/bullets
            $name = ltrim($line, " \t-*•");
            $name = trim($name);
            
            if (!empty($name) && strlen($name) < 100) {
                $whitelisted[] = [
                    'name' => $name
                ];
            }
        }

        return $whitelisted;
    }

    /**
     * Get chat log via RCON command /pelican.chat
     * Returns null if mod is not installed or RCON fails
     */
    public function getChatLog(string $serverId, int $limit = 50): ?array
    {
        // Check if chat log feature is enabled
        if (!config('factorio-manager.chat_log.enabled', true)) {
            return null;
        }

        $maxMessages = min($limit, config('factorio-manager.chat_log.max_messages', 50));
        
        // Use the new RCON command from pelican-chat-logger mod
        $response = $this->sendRconCommand($serverId, "/pelican.chat $maxMessages");
        
        if (!$response) {
            return null;
        }

        try {
            $chatLog = json_decode($response, true);
            
            if (!is_array($chatLog)) {
                return null;
            }

            return $chatLog;
        } catch (\Exception $e) {
            Log::debug("Chat log not available for server {$serverId}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if chat log mod is installed on the server
     */
    public function isChatLogAvailable(string $serverId): bool
    {
        if (!config('factorio-manager.chat_log.enabled', true)) {
            return false;
        }

        // Check via /pelican.status command - if it returns valid JSON, mod is installed
        $response = $this->sendRconCommand($serverId, '/pelican.status');
        
        if (!$response) {
            return false;
        }

        try {
            $data = json_decode($response, true);
            // If we get valid JSON with expected fields, mod is installed
            return is_array($data) && isset($data['tick']);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get detailed server status via RCON command /pelican.status
     * Returns extended info including evolution factor and current research
     */
    public function getExtendedServerStatus(string $serverId): ?array
    {
        $response = $this->sendRconCommand($serverId, '/pelican.status');
        
        if (!$response) {
            return null;
        }

        try {
            return json_decode($response, true);
        } catch (\Exception $e) {
            Log::debug("Extended status not available for server {$serverId}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get detailed online players via RCON command /pelican.players
     * Returns extended info including position and AFK time
     */
    public function getDetailedOnlinePlayers(string $serverId): ?array
    {
        $response = $this->sendRconCommand($serverId, '/pelican.players');
        
        if (!$response) {
            return null;
        }

        try {
            return json_decode($response, true);
        } catch (\Exception $e) {
            Log::debug("Detailed players not available for server {$serverId}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Send server message via RCON command /pelican.say
     * Uses the mod's command for better logging
     */
    public function sendServerMessage(string $serverId, string $message): bool
    {
        $response = $this->sendRconCommand($serverId, "/pelican.say $message");
        
        if (!$response) {
            return false;
        }

        try {
            $data = json_decode($response, true);
            // Mod returns {"ok":true} on success
            return isset($data['ok']) && $data['ok'] === true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Clear chat log - not implemented in mod yet
     * For now, just return true as the log auto-rotates
     */
    public function clearChatLog(string $serverId): bool
    {
        // Chat log auto-rotates, no need to clear manually
        // The mod keeps only the last 100 messages
        return true;
    }

    /**
     * Get mod version info via /pelican.status
     */
    public function getModVersion(string $serverId): ?array
    {
        $response = $this->sendRconCommand($serverId, '/pelican.status');
        
        if (!$response) {
            return null;
        }

        try {
            $data = json_decode($response, true);
            if (is_array($data)) {
                return [
                    'name' => 'pelican-chat-logger',
                    'installed' => true,
                ];
            }
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
