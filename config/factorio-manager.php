<?php

return [
    /*
    |--------------------------------------------------------------------------
    | RCON Settings
    |--------------------------------------------------------------------------
    |
    | Enable or disable RCON functionality for Factorio servers
    |
    */
    'rcon_enabled' => env('FACTORIO_RCON_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Navigation Sort Order
    |--------------------------------------------------------------------------
    |
    | Define the sort order in the navigation menu
    |
    */
    'nav_sort' => env('FACTORIO_RCON_NAV_SORT', 2),

    /*
    |--------------------------------------------------------------------------
    | Chat Log Settings
    |--------------------------------------------------------------------------
    |
    | Enable chat log functionality via the pelican-chat-logger Factorio mod.
    | The mod provides RCON commands: /pelican.chat, /pelican.status, etc.
    | This feature only works if the mod is installed on the server.
    |
    | RCON Commands used:
    | - /pelican.chat [count]  - Get last N chat messages as JSON
    | - /pelican.status        - Get server status (tick, players, research, evolution)
    | - /pelican.players       - Get detailed online player info
    | - /pelican.say <msg>     - Send server message (logged in chat)
    | - /pelican.clear         - Clear chat log
    | - /pelican.version       - Check mod version
    |
    */
    'chat_log' => [
        'enabled' => env('FACTORIO_CHAT_LOG_ENABLED', true),
        'max_messages' => env('FACTORIO_CHAT_LOG_MAX', 50),
    ],
];
