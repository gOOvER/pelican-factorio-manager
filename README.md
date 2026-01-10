# Factorio RCON Manager for Pelican Panel

A plugin for [Pelican Panel](https://pelican.dev/) that enables management of Factorio servers via RCON.

## Overview
The **Factorio RCON Manager** plugin enables direct management of Factorio servers through the Pelican Panel. Using RCON, you can manage players, send commands, and monitor server status without entering the game.

## Features
* **Real-time Player List**: View all online and offline players
* **Player Management**:
  * **Kick**: Remove players from the server
  * **Ban/Unban**: Ban or unban players
  * **Promote/Demote**: Grant or revoke admin rights
* **In-Game Chat**:
  * Send global messages to all players
  * Whisper to individual players
  * Predefined quick messages
  * Real-time delivery to game
  * **Chat Log**: View chat history (requires Factorio mod)
* **Server Status**: Monitor current server status and online players
* **Extended Status** (with mod): Evolution factor, current research, game time
* **Multi-language Support**: Fully localized in German and English

## Pelican Chat Logger Mod

For advanced features like **chat history** and **extended server status**, install the companion Factorio mod:

📦 **[Pelican Chat Logger](https://mods.factorio.com/mod/pelican-chat-logger)** | [GitHub](https://github.com/gOOvER/factorio-pelican-chat-logger) 

The mod provides RCON commands that return JSON data directly:

| Command | Description | Response |
|---------|-------------|----------|
| `/pelican.chat [count]` | Get last N chat messages | JSON array of messages |
| `/pelican.status` | Server status (players, evolution, research) | JSON object |
| `/pelican.players` | Detailed online player info | JSON array |
| `/pelican.say <msg>` | Send server message (logged) | `{"status":"ok"}` |
| `/pelican.clear` | Clear chat log | `{"status":"ok"}` |
| `/pelican.version` | Mod version check | `{"name":"...","version":"..."}` |

Without the mod, the plugin still works but without chat log and extended status features.

## Requirements
* **PHP**: 8.2 or higher
* **Node.js**: v20 or higher
* **Yarn**: v1.22 or higher
* **Pelican Panel**: v1.0.0 or higher
* **Factorio Server**:
  * **Egg Tag**: The server egg MUST have the `factorio` tag
  * **Egg Feature**: The server egg MUST have the `factorio-rcon` feature
  * **RCON Variables**: The egg must have `RCON_PORT` and `RCON_PASSWORD` variables configured
* **Optional**: [Pelican Chat Logger](https://mods.factorio.com/mod/pelican-chat-logger) mod for chat log features

## Installation

### Via Panel Frontend
1. Download the plugin ZIP file
2. Navigate to the plugin list in Pelican Panel (Admin area)
3. Use the "Import" button to upload and install the plugin
4. Ensure RCON is configured in your Factorio egg (see below)

### Manually
1. Download and extract the plugin release
2. Copy the `factorio-rcon` folder to your Pelican Panel's `plugins` directory
3. Install via the Panel Administration page
4. Ensure RCON is configured in your Factorio egg (see below)

## RCON Configuration

The plugin reads RCON settings from the server's **egg variables**. Your Factorio egg must have these variables defined:

| Variable | Description | Example |
|----------|-------------|---------|
| `RCON_PORT` | Port for RCON connections | `25575` |
| `RCON_PASSWORD` | Password for RCON authentication | `YourSecurePassword` |

These variables are typically passed to the Factorio server via startup parameters:
```bash
--rcon-port {{RCON_PORT}} --rcon-password {{RCON_PASSWORD}}
```

> **Note:** Most Factorio eggs already include these variables. Check your egg configuration in the Admin panel.

### Server Egg Configuration

For the plugin to appear on Factorio servers, your server egg must have:

1. **Tag**: Add `factorio` as a tag
2. **Feature**: Add `factorio-rcon` to the egg features array

Example egg configuration:
```json
{
  "tags": ["factorio"],
  "features": ["factorio-rcon"]
}
```

## Usage

After installation, the plugin will automatically appear in the server panel for all Factorio servers.

**Available Actions:**
* View player list (Online/Offline/Admin/Banned)
* Kick players with optional reason
* Ban/unban players
* Promote/demote admin rights
* **In-Game Chat**: Send messages to all or individual players
* Quick messages for common announcements

## Supported Commands

The plugin supports the following Factorio RCON commands:

### Standard Factorio Commands
* `/players` - Player list
* `/kick <player> [reason]` - Kick player
* `/ban <player> [reason]` - Ban player
* `/unban <player>` - Unban player
* `/promote <player>` - Promote to admin
* `/demote <player>` - Remove admin
* `/admins` - Admin list
* `/banlist` - Ban list
* `/say <message>` - Message to all
* `/whisper <player> <message>` - Whisper message

### Pelican Chat Logger Mod Commands
* `/pelican.chat [count]` - Get chat history as JSON
* `/pelican.status` - Extended server status as JSON
* `/pelican.players` - Detailed player info as JSON
* `/pelican.say <message>` - Send message (logged in chat)
* `/pelican.clear` - Clear chat log
* `/pelican.version` - Check mod version

## License

This project is licensed under the GNU General Public License v3.0 - see the [LICENSE](LICENSE) file for details.

## Development

### Building for Distribution

To prepare the plugin for distribution:

1. Remove the `meta` section from `plugin.json` if present
2. Zip the entire `factorio-rcon` folder
3. Distribute the ZIP file

Users can then install it via the Panel's plugin import feature.

### Structure

The plugin follows the standard Pelican plugin structure:
- `plugin.json` - Plugin metadata
- `config/` - Configuration files
- `src/` - PHP source code
- `resources/` - Views and translations
- `lang/` - Language files (auto-discovered)

## Credits

Based on the [Minecraft Player Manager](https://github.com/kumagames-fou/minecraft-player-manager) plugin by KumaGames.

Developed for [Pelican Panel](https://pelican.dev/).