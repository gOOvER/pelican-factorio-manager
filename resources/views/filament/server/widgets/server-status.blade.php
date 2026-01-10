<x-filament-widgets::widget>
    @php
        $status = $this->getServerStatus();
        $onlinePlayers = isset($status['players']) ? array_filter($status['players'], fn($p) => $p['online'] ?? false) : [];
        $onlineCount = count($onlinePlayers);
        $maxPlayers = $status['max_players'] ?? null;
    @endphp

    <div class="flex flex-col gap-4 sm:flex-row sm:gap-4">
        {{-- Server Status --}}
        <div class="flex-1 fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex gap-x-3">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    {{ __('factorio-rcon::messages.widget.server_status') }}
                </span>
                <span class="text-sm font-semibold {{ $status['online'] ? 'text-success-500' : 'text-danger-500' }}">
                    {{ $status['online'] ? __('factorio-rcon::messages.values.online') : __('factorio-rcon::messages.values.offline') }}
                </span>
            </div>
        </div>

        {{-- Online Players --}}
        <div class="flex-1 fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex gap-x-3">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    {{ __('factorio-rcon::messages.widget.online_players') }}
                </span>
                <span class="text-sm font-semibold text-gray-950 dark:text-white">
                    {{ $onlineCount }} / {{ $maxPlayers ?? '∞' }}
                </span>
            </div>
        </div>

        {{-- RCON Status --}}
        <div class="flex-1 fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex gap-x-3">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    RCON
                </span>
                <span class="text-sm font-semibold {{ ($status['online'] && !isset($status['error'])) ? 'text-success-500' : 'text-danger-500' }}">
                    {{ ($status['online'] && !isset($status['error'])) ? __('factorio-rcon::messages.values.connected') : __('factorio-rcon::messages.values.disconnected') }}
                </span>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
