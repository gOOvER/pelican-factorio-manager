<x-filament-widgets::widget>
    @php
        $status = $this->getServerStatus();
        $onlinePlayers = isset($status['players']) ? array_filter($status['players'], fn($p) => $p['online'] ?? false) : [];
        $onlineCount = count($onlinePlayers);
        $maxPlayers = $status['max_players'] ?? null;
    @endphp

    <style>
        .factorio-manager-grid {
            display: grid !important;
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            gap: 1.5rem !important;
        }
    </style>

    <div class="factorio-manager-grid">
        {{-- Server Status --}}
        <div class="fi-small-stat-block grid grid-flow-row w-full p-3 rounded-lg bg-white shadow-sm overflow-hidden ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <span>
                <span class="text-md font-medium text-gray-500 dark:text-gray-400">
                    {{ __('factorio-manager::messages.widget.server_status') }}
                </span>
                <span class="text-md font-semibold {{ $status['online'] ? 'text-success-500' : 'text-danger-500' }}">
                    {{ $status['online'] ? __('factorio-manager::messages.values.online') : __('factorio-manager::messages.values.offline') }}
                </span>
            </span>
        </div>

        {{-- Online Players --}}
        <div class="fi-small-stat-block grid grid-flow-row w-full p-3 rounded-lg bg-white shadow-sm overflow-hidden ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <span>
                <span class="text-md font-medium text-gray-500 dark:text-gray-400">
                    {{ __('factorio-manager::messages.widget.online_players') }}
                </span>
                <span class="text-md font-semibold text-gray-950 dark:text-white">
                    {{ $onlineCount }} / {{ $maxPlayers ?? '∞' }}
                </span>
            </span>
        </div>

        {{-- RCON Status --}}
        <div class="fi-small-stat-block grid grid-flow-row w-full p-3 rounded-lg bg-white shadow-sm overflow-hidden ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <span>
                <span class="text-md font-medium text-gray-500 dark:text-gray-400">
                    RCON
                </span>
                <span class="text-md font-semibold {{ ($status['online'] && !isset($status['error'])) ? 'text-success-500' : 'text-danger-500' }}">
                    {{ ($status['online'] && !isset($status['error'])) ? __('factorio-manager::messages.values.connected') : __('factorio-manager::messages.values.disconnected') }}
                </span>
            </span>
        </div>
    </div>
</x-filament-widgets::widget>
