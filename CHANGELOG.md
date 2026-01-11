# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-01-11

### Added
- Initial release
- Real-time player list with online/offline status
- Player management (kick, ban, unban, promote, demote)
- **Whitelist management** - Add/remove players from server whitelist
- **Server Admins table** - Separate list of all server admins with quick remove action
- **Whitelisted Players table** - Separate list of all whitelisted players with quick remove action
- In-game chat widget with message sending
- Server status widget showing online players and RCON connection
- Chat history support via Pelican Chat Logger mod
- Multi-language support (English and German)
- Filter options for player list (all, online, offline, admin, banned, whitelisted)
- Quick messages for common announcements
- Whisper functionality to individual players
- Auto-refresh for chat page (5 second polling)
- Extended server status display with game time, players, evolution, and research

### Changed
- **Plugin renamed from `factorio-rcon` to `factorio-manager`**
- Navigation group renamed from "Factorio RCON" to "Factorio"
- Players page navigation label now shows "Players" (EN) / "Spieler" (DE)
- Server status cards redesigned with icons and better styling
- Read max_players from server-settings.json instead of RCON
- Server status boxes now display horizontally (4 columns on desktop, 2 on tablet, 1 on mobile)

### Fixed
- Button click handlers now use Alpine.js confirmation dialogs (Livewire 3 compatible)
- Added missing translation key `values.no_players` for empty player list
- Fixed mod version display only showing when version is actually available
- **Fixed chat messages not appearing in-game** - Now uses `/pelican.say` (mod) which properly displays via `game.print()`
- Player list now refreshes automatically after promote/demote/kick/ban/unban actions
- Promote button changes to "Demote" (orange) when player is admin
- **Fixed admin status detection** - Now uses case-insensitive name comparison (fixes issue where Factorio stores names differently)

### UI
- **Modernized Chat page with custom dark theme design**
  - Switched from Tailwind classes to inline CSS for plugin compatibility
  - Three separate cards: Send Message, Quick Messages, Information
  - Card design with #252525 background, #444 border, and box-shadow
  - Header sections with colored icon badges and 2px bottom border
  - Quick Messages as 2-column grid with colored backgrounds per type
  - Info boxes with icon containers and #1a1a1a background
  - 24px spacing between cards for clear visual separation
  - Chat log with colored icons per message type (join, leave, death, research, etc.)
  - Fade-in animation for new chat entries
