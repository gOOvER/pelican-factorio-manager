<?php

namespace gOOvER\FactorioRcon\Services;

use App\Models\Server;
use Illuminate\Support\Facades\Log;

class FactorioRconProvider
{
    /**
     * Send a raw RCON command to the server
     */
    public function sendRconCommand(string $serverId, string $command): ?string
    {
        $server = Server::where('uuid', $serverId)->first();
        if (!$server) {
            Log::error("Server not found for UUID: $serverId");
            return null;
        }

        $rcon = $this->getRconConnection($server);
        if (!$rcon) {
            return null;
        }

        $response = $rcon->sendCommand($command);
        $rcon->disconnect();

        return $response;
    }

    /**
     * Get server status information
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

        $rcon = $this->getRconConnection($server);
        if (!$rcon) {
            return [
                'online' => false,
                'error' => 'Cannot connect to RCON'
            ];
        }

        // Get player count
        $playersResponse = $rcon->sendCommand('/players');
        
        // Get max players
        $maxPlayersResponse = $rcon->sendCommand('/config get max-players');
        $maxPlayers = null;
        if ($maxPlayersResponse && preg_match('/max-players\s+(?:is\s+)?(\d+)/i', $maxPlayersResponse, $matches)) {
            $value = (int) $matches[1];
            $maxPlayers = $value === 0 ? null : $value; // 0 = unlimited
        }
        
        $status = [
            'online' => true,
            'players' => $this->parsePlayersResponse($playersResponse),
            'max_players' => $maxPlayers,
        ];

        $rcon->disconnect();
        return $status;
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
     * Get admins list
     */
    public function getAdmins(string $serverId): array
    {
        $response = $this->sendRconCommand($serverId, '/admins');
        
        if (!$response) {
            return [];
        }

        return $this->parseAdminsResponse($response);
    }

    /**
     * Send a message to all players
     */
    public function sendMessage(string $serverId, string $message): bool
    {
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
     * Get maximum player slots
     */
    public function getMaxPlayers(string $serverId): ?int
    {
        $response = $this->sendRconCommand($serverId, '/config get max-players');
        
        if (!$response) {
            return null;
        }

        // Response format: "max-players is 100" or similar
        if (preg_match('/max-players\s+(?:is\s+)?(\d+)/i', $response, $matches)) {
            $value = (int) $matches[1];
            // 0 means unlimited in Factorio
            return $value === 0 ? null : $value;
        }

        return null;
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
            
            $admins[] = [
                'name' => $line
            ];
        }

        return $admins;
    }

    /**
     * Get chat log via RCON command /pelican.chat
     * Returns null if mod is not installed or RCON fails
     */
    public function getChatLog(string $serverId, int $limit = 50): ?array
    {
        // Check if chat log feature is enabled
        if (!config('factorio-rcon.chat_log.enabled', true)) {
            return null;
        }

        $maxMessages = min($limit, config('factorio-rcon.chat_log.max_messages', 50));
        
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
        if (!config('factorio-rcon.chat_log.enabled', true)) {
            return false;
        }

        // Check via /pelican.version command
        $response = $this->sendRconCommand($serverId, '/pelican.version');
        
        if (!$response) {
            return false;
        }

        try {
            $data = json_decode($response, true);
            return isset($data['name']) && $data['name'] === 'pelican-chat-logger';
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
            return isset($data['status']) && $data['status'] === 'ok';
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Clear chat log via RCON command /pelican.clear
     */
    public function clearChatLog(string $serverId): bool
    {
        $response = $this->sendRconCommand($serverId, '/pelican.clear');
        
        if (!$response) {
            return false;
        }

        try {
            $data = json_decode($response, true);
            return isset($data['status']) && $data['status'] === 'ok';
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get mod version info
     */
    public function getModVersion(string $serverId): ?array
    {
        $response = $this->sendRconCommand($serverId, '/pelican.version');
        
        if (!$response) {
            return null;
        }

        try {
            return json_decode($response, true);
        } catch (\Exception $e) {
            return null;
        }
    }
}
