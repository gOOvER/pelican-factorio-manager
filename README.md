# Factorio Manager for Pelican Panel

[![GitHub Release](https://img.shields.io/github/v/release/gOOvER/pelican-factorio-manager?style=flat-square)](https://github.com/gOOvER/pelican-factorio-manager/releases)
[![License](https://img.shields.io/github/license/gOOvER/pelican-factorio-manager?style=flat-square)](LICENSE)

A plugin for [Pelican Panel](https://pelican.dev/) that enables management of Factorio servers via RCON.

## Overview

The **Factorio Manager** plugin enables direct management of Factorio servers through the Pelican Panel. Using RCON, you can manage players, send chat messages, and monitor server status - all without entering the game.

## Features

### Player Management
* **Real-time Player List** - View all online and offline players with auto-refresh
* **Kick** - Remove players from the server with optional reason
* **Ban/Unban** - Ban or unban players
* **Promote/Demote** - Grant or revoke admin rights
* **Filter** - Filter by status (online, offline, admin, banned)

### Server Chat
* **Send Messages** - Broadcast messages to all players
* **Whisper** - Send private messages to individual players
* **Quick Messages** - Predefined messages for common announcements
* **Chat Log** - View chat history with auto-refresh (requires mod)

### Server Status
* **Connection Status** - Monitor RCON connection
* **Online Players** - Current player count
* **Extended Status** (with mod):
  * Game time
  * Evolution factor
  * Current research
  * Mod version

### Localization
* 🇬🇧 English
* 🇩🇪 German (Deutsch)

## Pelican Chat Logger Mod

For advanced features like **chat history**, **in-game message delivery**, and **extended server status**, install the companion Factorio mod:

📦 **[Pelican Chat Logger on Mod Portal](https://mods.factorio.com/mod/pelican-chat-logger)** | **[GitHub](https://github.com/gOOvER/factorio-pelican-chat-logger)**

### Mod Commands

| Command | Description | Response |
|---------|-------------|----------|
| `/pelican.chat [count]` | Get last N chat messages | JSON array |
| `/pelican.status` | Server status (time, players, evolution, research) | JSON object |
| `/pelican.players` | Detailed online player info | JSON array |
| `/pelican.say <msg>` | Send server message (shows in game chat) | `{"ok":true}` |
| `/pelican.clear` | Clear chat log | `{"ok":true}` |
| `/pelican.version` | Mod version info | JSON object |

> **Note:** Without the mod, the plugin still works for player management but chat messages won't appear in-game and extended status is unavailable.

## Requirements

* **Pelican Panel**: v1.0.0 or higher
* **PHP**: 8.2 or higher
* **Factorio Server Egg**:
  * Tag: `factorio`
  * Feature: `factorio-rcon`
  * Variables: `RCON_PORT` and `RCON_PASSWORD`
* **Recommended**: [Pelican Chat Logger](https://mods.factorio.com/mod/pelican-chat-logger) mod

## Installation

### Via Panel Admin (Recommended)

1. Download the latest release ZIP from [Releases](https://github.com/gOOvER/pelican-factorio-manager/releases)
2. Go to **Admin → Plugins** in Pelican Panel
3. Click **Import** and upload the ZIP file
4. The plugin is now installed!

### Manual Installation

1. Download and extract the release
2. Copy the `factorio-manager` folder to `/var/www/pelican/plugins/`
3. Clear cache: `php artisan cache:clear`

## Egg Configuration

For the plugin to appear on Factorio servers, your egg must be configured:

### Required Egg Settings

```json
{
  "tags": ["factorio"],
  "features": ["factorio-rcon"]
}
```

### Required Variables

| Variable | Description | Example |
|----------|-------------|---------|
| `RCON_PORT` | RCON port | `25575` |
| `RCON_PASSWORD` | RCON password | `YourSecurePassword` |

### Startup Command

Ensure your egg passes RCON settings to Factorio:

```bash
--rcon-port {{RCON_PORT}} --rcon-password {{RCON_PASSWORD}}
```

> **Tip:** Most Factorio eggs already have RCON configured. Check your egg in **Admin → Eggs**.

## Usage

After installation, the plugin automatically appears in the server sidebar for all Factorio servers under the **Factorio** navigation group.

### Pages

| Page | Description |
|------|-------------|
| **Players** | View and manage all players (kick, ban, promote, etc.) |
| **Server Chat** | Send messages and view chat history |

### Auto-Refresh

Both pages refresh automatically every 5 seconds to show live data.

## RCON Commands Reference

### Standard Factorio Commands

| Command | Description |
|---------|-------------|
| `/players` | List all players |
| `/kick <player> [reason]` | Kick a player |
| `/ban <player> [reason]` | Ban a player |
| `/unban <player>` | Unban a player |
| `/promote <player>` | Promote to admin |
| `/demote <player>` | Remove admin |
| `/admins` | List admins |
| `/banlist` | List banned players |
| `/whisper <player> <msg>` | Private message |

### Mod Commands (pelican-chat-logger)

| Command | Description |
|---------|-------------|
| `/pelican.say <msg>` | Send server message (visible in game) |
| `/pelican.chat [n]` | Get last n chat messages |
| `/pelican.status` | Get extended server status |
| `/pelican.players` | Get detailed player info |
| `/pelican.clear` | Clear chat log |
| `/pelican.version` | Get mod version |

## License

This project is licensed under the [GNU General Public License v3.0](LICENSE).

## Links

- [Pelican Panel](https://pelican.dev/)
- [Pelican Chat Logger Mod](https://mods.factorio.com/mod/pelican-chat-logger)
- [Pelican Chat Logger GitHub](https://github.com/gOOvER/factorio-pelican-chat-logger)

## Credits

Inspired by the [Minecraft Player Manager](https://github.com/kumagames-fou/minecraft-player-manager) plugin by KumaGames.