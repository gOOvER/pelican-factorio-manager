<?php

return [
    'navigation_label' => 'Factorio RCON',

    'columns' => [
        'name' => 'Spielername',
        'status' => 'Status',
        'online' => 'Online',
        'offline' => 'Offline',
        'admin' => 'Admin',
    ],

    'filters' => [
        'all' => 'Alle',
        'online' => 'Online',
        'offline' => 'Offline',
        'admin' => 'Admins',
        'banned' => 'Gebannt',
    ],

    'sections' => [
        'player_info' => 'Spielerinformation',
        'management' => 'Verwaltung',
        'management_desc' => 'Aktionen für diesen Spieler ausführen',
    ],

    'fields' => [
        'username' => 'Benutzername',
        'current_status' => 'Aktueller Status',
    ],

    'actions' => [
        'view' => 'Ansehen',
        'kick' => [
            'label' => 'Kicken',
            'reason' => 'Grund',
            'default_reason' => 'Vom Administrator gekickt',
            'notify' => 'Kick-Befehl gesendet',
        ],
        'ban' => [
            'label' => 'Bannen',
            'reason' => 'Grund',
            'default_reason' => 'Vom Administrator gebannt',
            'notify' => 'Ban-Befehl gesendet',
        ],
        'unban' => [
            'label' => 'Entbannen',
            'notify' => 'Unban-Befehl gesendet',
        ],
        'promote' => [
            'label' => 'Zu Admin befördern',
            'notify' => 'Spieler wurde zum Admin befördert',
        ],
        'demote' => [
            'label' => 'Admin entfernen',
            'notify' => 'Admin-Rechte wurden entfernt',
        ],
        'message' => [
            'label' => 'Nachricht senden',
            'message' => 'Nachricht',
            'notify' => 'Nachricht gesendet',
        ],
    ],

    'widget' => [
        'online_players' => 'Online Spieler',
        'server_status' => 'Server Status',
        'players_online_now' => 'Aktuell spielend',
    ],

    'pages' => [
        'list' => 'Spielerliste',
        'view' => 'Spieler ansehen',
        'chat' => 'Server Chat',
    ],

    'values' => [
        'online' => 'Online',
        'offline' => 'Offline',
        'connected' => 'Verbunden',
        'disconnected' => 'Getrennt',
        'none' => 'Keine',
    ],

    'settings' => [
        'rcon_enabled' => 'RCON aktivieren',
        'rcon_enabled_helper' => 'Ermöglicht die Verwaltung des Factorio-Servers über RCON.',
        'nav_sort' => 'Navigationsreihenfolge',
        'nav_sort_helper' => 'Sortierreihenfolge im Seitenmenü. Niedrigere Zahlen erscheinen weiter oben. (Standard: 2)',
        'saved' => 'Einstellungen erfolgreich gespeichert.',
    ],

    'chat' => [
        'title' => 'Server Chat',
        'description' => 'Sende Nachrichten an alle Spieler oder flüstere einzelnen Spielern.',
        'recipient' => 'Empfänger',
        'all_players' => 'Alle Spieler',
        'message' => 'Nachricht',
        'message_placeholder' => 'Gib deine Nachricht ein...',
        'send' => 'Senden',
        'empty_message' => 'Bitte gib eine Nachricht ein',
        'server_not_found' => 'Server nicht gefunden',
        'message_sent' => 'Nachricht erfolgreich gesendet',
        'message_failed' => 'Nachricht konnte nicht gesendet werden',
        'info' => 'Nachrichten werden sofort im Spiel angezeigt.',
        'info_title' => 'Information',
        'info_all' => 'Wähle "Alle Spieler" für eine öffentliche Nachricht',
        'info_whisper' => 'Wähle einen Spielernamen für eine private Flüsternachricht',
        'quick_messages' => 'Schnellnachrichten',
        'quick' => [
            'welcome' => 'Willkommen auf dem Server!',
            'maintenance' => 'Wartungsarbeiten in 5 Minuten',
            'restart' => 'Server-Neustart in 10 Minuten',
            'backup' => 'Backup läuft, minimale Verzögerung möglich',
        ],
        'chat_log' => 'Chat-Verlauf',
        'no_messages' => 'Noch keine Nachrichten',
        'refresh' => 'Aktualisieren',
        'mod_not_installed' => 'Installiere die "pelican-chat-logger" Factorio Mod, um den Chat-Verlauf und erweiterten Server-Status zu aktivieren.',
        'mod_required_title' => 'Mod erforderlich',
        'mod_install_hint' => 'Installiere diese Factorio Mod auf deinem Server:',
        'clear_log' => 'Log löschen',
        'log_cleared' => 'Chat-Verlauf erfolgreich gelöscht',
        'clear_failed' => 'Chat-Verlauf konnte nicht gelöscht werden',
    ],

    'status' => [
        'server_info' => 'Server-Information',
        'game_time' => 'Spielzeit',
        'players_online' => 'Spieler Online',
        'evolution' => 'Evolution',
        'research' => 'Aktuelle Forschung',
    ],
];
