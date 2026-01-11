<?php

return [
    'navigation_label' => 'Players',
    
    'columns' => [
        'name' => 'Player Name',
        'status' => 'Status',
        'online' => 'Online',
        'offline' => 'Offline',
        'admin' => 'Admin',
    ],

    'filters' => [
        'all' => 'All',
        'online' => 'Online',
        'offline' => 'Offline',
        'admin' => 'Admins',
        'banned' => 'Banned',
    ],

    'sections' => [
        'player_info' => 'Player Information',
        'management' => 'Management',
        'management_desc' => 'Perform actions on this player',
    ],

    'fields' => [
        'username' => 'Username',
        'current_status' => 'Current Status',
    ],

    'actions' => [
        'refresh' => 'Refresh',
        'view' => 'View',
        'kick' => [
            'label' => 'Kick',
            'reason' => 'Reason',
            'default_reason' => 'Kicked by administrator',
            'notify' => 'Kick command sent',
            'confirm' => 'Are you sure you want to kick :name?',
        ],
        'ban' => [
            'label' => 'Ban',
            'reason' => 'Reason',
            'default_reason' => 'Banned by administrator',
            'notify' => 'Ban command sent',
            'confirm' => 'Are you sure you want to ban :name?',
        ],
        'unban' => [
            'label' => 'Unban',
            'notify' => 'Unban command sent',
            'confirm' => 'Are you sure you want to unban :name?',
        ],
        'promote' => [
            'label' => 'Promote to Admin',
            'notify' => 'Player promoted to admin',
            'confirm' => 'Are you sure you want to promote :name to admin?',
        ],
        'demote' => [
            'label' => 'Remove Admin',
            'notify' => 'Admin rights removed',
            'confirm' => 'Are you sure you want to remove admin rights from :name?',
        ],
        'message' => [
            'label' => 'Send Message',
            'message' => 'Message',
            'notify' => 'Message sent',
        ],
    ],

    'widget' => [
        'online_players' => 'Online Players',
        'server_status' => 'Server Status',
        'players_online_now' => 'Currently Playing',
    ],

    'pages' => [
        'list' => 'Player List',
        'view' => 'View Player',
        'chat' => 'Server Chat',
    ],

    'values' => [
        'online' => 'Online',
        'offline' => 'Offline',
        'connected' => 'Connected',
        'disconnected' => 'Disconnected',
        'none' => 'None',
        'no_players' => 'No players found',
    ],

    'settings' => [
        'rcon_enabled' => 'Enable RCON',
        'rcon_enabled_helper' => 'Enables management of Factorio server via RCON.',
        'nav_sort' => 'Navigation Order',
        'nav_sort_helper' => 'Sort order in the side menu. Lower numbers appear higher. (Default: 2)',
        'saved' => 'Settings saved successfully.',
    ],

    'chat' => [
        'title' => 'Server Chat',
        'description' => 'Send messages to all players or whisper to individual players.',
        'recipient' => 'Recipient',
        'all_players' => 'All Players',
        'message' => 'Message',
        'message_placeholder' => 'Enter your message...',
        'send' => 'Send',
        'empty_message' => 'Please enter a message',
        'server_not_found' => 'Server not found',
        'message_sent' => 'Message sent successfully',
        'message_failed' => 'Failed to send message',
        'info' => 'Messages will appear in-game immediately.',
        'info_title' => 'Information',
        'info_all' => 'Select "All Players" for a public broadcast',
        'info_whisper' => 'Select a player name for a private whisper',
        'quick_messages' => 'Quick Messages',
        'quick' => [
            'welcome' => 'Welcome to the server!',
            'maintenance' => 'Maintenance in 5 minutes',
            'restart' => 'Server restart in 10 minutes',
            'backup' => 'Backup running, minimal delay possible',
        ],
        'chat_log' => 'Chat Log',
        'no_messages' => 'No messages yet',
        'refresh' => 'Refresh',
        'mod_not_installed' => 'Install the "pelican-chat-logger" Factorio mod to enable chat history and extended server status.',
        'mod_required_title' => 'Mod Required',
        'mod_install_hint' => 'Install this Factorio mod on your server:',
        'clear_log' => 'Clear Log',
        'log_cleared' => 'Chat log cleared successfully',
        'clear_failed' => 'Failed to clear chat log',
    ],

    'status' => [
        'server_info' => 'Server Information',
        'game_time' => 'Game Time',
        'players_online' => 'Players Online',
        'evolution' => 'Evolution',
        'research' => 'Current Research',
    ],
];
