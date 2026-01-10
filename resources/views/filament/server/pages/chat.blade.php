<x-filament-panels::page>
    @php
        $chatLog = $this->getChatLog();
        $chatLogAvailable = $chatLog !== null;
        $modInstalled = $this->isChatLogAvailable();
        $extendedStatus = $this->getExtendedStatus();
        $modVersion = $this->getModVersion();
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Extended Server Status (only shown if mod is installed) --}}
        @if($extendedStatus && $modInstalled)
        <div class="lg:col-span-3">
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="tabler-server" class="h-5 w-5 text-success-400" />
                        {{ __('factorio-rcon::messages.status.server_info') }}
                        @if($modVersion)
                            <span class="text-xs text-gray-500 ml-2">(Mod v{{ $modVersion['version'] ?? '?' }})</span>
                        @endif
                    </div>
                </x-slot>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-gray-900 rounded-lg p-3">
                        <div class="text-xs text-gray-500 uppercase">{{ __('factorio-rcon::messages.status.game_time') }}</div>
                        <div class="text-lg font-semibold text-white">{{ $extendedStatus['time'] ?? '--:--:--' }}</div>
                    </div>
                    <div class="bg-gray-900 rounded-lg p-3">
                        <div class="text-xs text-gray-500 uppercase">{{ __('factorio-rcon::messages.status.players_online') }}</div>
                        <div class="text-lg font-semibold text-success-400">{{ $extendedStatus['players'] ?? 0 }}</div>
                    </div>
                    <div class="bg-gray-900 rounded-lg p-3">
                        <div class="text-xs text-gray-500 uppercase">{{ __('factorio-rcon::messages.status.evolution') }}</div>
                        <div class="text-lg font-semibold text-danger-400">{{ number_format(($extendedStatus['evolution'] ?? 0) * 100, 1) }}%</div>
                    </div>
                    <div class="bg-gray-900 rounded-lg p-3">
                        <div class="text-xs text-gray-500 uppercase">{{ __('factorio-rcon::messages.status.research') }}</div>
                        <div class="text-sm font-medium text-info-400 truncate">{{ $extendedStatus['current_research'] ?? __('factorio-rcon::messages.values.none') }}</div>
                    </div>
                </div>
            </x-filament::section>
        </div>
        @endif

        {{-- Chat Log Section --}}
        <div class="lg:col-span-2 lg:row-span-2">
            @if($chatLogAvailable)
            {{-- Chat Log (mod installed and working) --}}
            <x-filament::section class="h-full">
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="tabler-messages" class="h-5 w-5 text-primary-400" />
                        {{ __('factorio-rcon::messages.chat.chat_log') }}
                    </div>
                </x-slot>

                <div class="bg-gray-950 rounded-lg p-4 h-80 overflow-y-auto font-mono text-sm" id="chat-log">
                    @forelse($chatLog as $entry)
                        <div class="py-1 flex items-start gap-2">
                            <span class="text-gray-500 text-xs shrink-0">[{{ $entry['time'] ?? '' }}]</span>
                            @php
                                $colorClass = match($entry['color'] ?? 'white') {
                                    'green' => 'text-success-400',
                                    'red' => 'text-danger-400',
                                    'orange' => 'text-warning-400',
                                    'yellow' => 'text-yellow-400',
                                    'cyan' => 'text-info-400',
                                    'purple' => 'text-purple-400',
                                    'blue' => 'text-blue-400',
                                    'gold' => 'text-amber-400',
                                    'gray' => 'text-gray-500',
                                    default => 'text-gray-300',
                                };
                                $typeIcon = match($entry['type'] ?? 'chat') {
                                    'join' => 'tabler-login',
                                    'leave' => 'tabler-logout',
                                    'death' => 'tabler-skull',
                                    'respawn' => 'tabler-heart',
                                    'research' => 'tabler-flask',
                                    'research_started' => 'tabler-flask-2',
                                    'ban' => 'tabler-ban',
                                    'unban' => 'tabler-circle-check',
                                    'kick' => 'tabler-shoe',
                                    'promote' => 'tabler-crown',
                                    'demote' => 'tabler-crown-off',
                                    'system' => 'tabler-settings',
                                    'server' => 'tabler-server',
                                    default => 'tabler-message',
                                };
                            @endphp
                            <x-filament::icon icon="{{ $typeIcon }}" class="h-4 w-4 {{ $colorClass }} shrink-0 mt-0.5" />
                            <span class="{{ $colorClass }}">
                                @if($entry['type'] === 'chat')
                                    <span class="font-semibold">{{ $entry['player'] ?? 'Unknown' }}:</span>
                                @endif
                                {{ $entry['message'] ?? '' }}
                            </span>
                        </div>
                    @empty
                        <div class="text-gray-500 text-center py-8">
                            {{ __('factorio-rcon::messages.chat.no_messages') }}
                        </div>
                    @endforelse
                </div>

                <div class="mt-2 flex justify-between items-center">
                    <x-filament::button
                        wire:click="clearChatLog"
                        color="danger"
                        size="sm"
                        icon="tabler-trash"
                        outlined
                    >
                        {{ __('factorio-rcon::messages.chat.clear_log') }}
                    </x-filament::button>
                    <x-filament::button
                        wire:click="$refresh"
                        color="gray"
                        size="sm"
                        icon="tabler-refresh"
                    >
                        {{ __('factorio-rcon::messages.chat.refresh') }}
                    </x-filament::button>
                </div>
            </x-filament::section>
            @else
            {{-- Mod not installed hint --}}
            <x-filament::section class="h-full">
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="tabler-messages" class="h-5 w-5 text-gray-400" />
                        {{ __('factorio-rcon::messages.chat.chat_log') }}
                    </div>
                </x-slot>

                <div class="flex flex-col items-center justify-center h-80 text-center">
                    <x-filament::icon icon="tabler-puzzle" class="h-16 w-16 text-warning-400 mb-4" />
                    <h3 class="text-lg font-semibold text-gray-300 mb-2">
                        {{ __('factorio-rcon::messages.chat.mod_required_title') }}
                    </h3>
                    <p class="text-gray-500 max-w-md mb-4">
                        {{ __('factorio-rcon::messages.chat.mod_not_installed') }}
                    </p>
                    <div class="bg-gray-900 rounded-lg p-4 text-left">
                        <p class="text-xs text-gray-400 mb-2">{{ __('factorio-rcon::messages.chat.mod_install_hint') }}</p>
                        <a href="https://mods.factorio.com/mod/pelican-chat-logger" target="_blank" class="inline-flex items-center gap-2 text-primary-400 hover:text-primary-300 transition-colors">
                            <x-filament::icon icon="tabler-external-link" class="h-4 w-4" />
                            <code class="text-xs">pelican-chat-logger</code>
                        </a>
                    </div>
                </div>
            </x-filament::section>
            @endif
        </div>

        {{-- Main Chat Form --}}
        <div class="lg:col-span-1">
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="tabler-message-circle" class="h-5 w-5 text-gray-400" />
                        {{ __('factorio-rcon::messages.chat.title') }}
                    </div>
                </x-slot>

                <x-slot name="description">
                    {{ __('factorio-rcon::messages.chat.description') }}
                </x-slot>

                <form wire:submit="sendMessage">
                    {{ $this->form }}

                    <div class="mt-6 flex justify-end">
                        <x-filament::button type="submit" icon="tabler-send">
                            {{ __('factorio-rcon::messages.chat.send') }}
                        </x-filament::button>
                    </div>
                </form>
            </x-filament::section>
        </div>

        {{-- Quick Messages Sidebar --}}
        <div class="lg:col-span-1">
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="tabler-bolt" class="h-5 w-5 text-warning-400" />
                        {{ __('factorio-rcon::messages.chat.quick_messages') }}
                    </div>
                </x-slot>

                <div class="space-y-2">
                    @php
                        $quickMessages = [
                            ['icon' => 'tabler-hand-wave', 'color' => 'success', 'text' => __('factorio-rcon::messages.chat.quick.welcome')],
                            ['icon' => 'tabler-tool', 'color' => 'warning', 'text' => __('factorio-rcon::messages.chat.quick.maintenance')],
                            ['icon' => 'tabler-refresh', 'color' => 'info', 'text' => __('factorio-rcon::messages.chat.quick.restart')],
                            ['icon' => 'tabler-database', 'color' => 'gray', 'text' => __('factorio-rcon::messages.chat.quick.backup')],
                        ];
                    @endphp

                    @foreach($quickMessages as $quick)
                        <x-filament::button
                            wire:click="setQuickMessage('{{ addslashes($quick['text']) }}')"
                            color="{{ $quick['color'] }}"
                            icon="{{ $quick['icon'] }}"
                            class="w-full justify-start"
                            outlined
                        >
                            {{ $quick['text'] }}
                        </x-filament::button>
                    @endforeach
                </div>
            </x-filament::section>

            {{-- Info Box --}}
            <x-filament::section class="mt-6">
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="tabler-info-circle" class="h-5 w-5 text-info-400" />
                        {{ __('factorio-rcon::messages.chat.info_title') }}
                    </div>
                </x-slot>

                <div class="text-sm text-gray-500 dark:text-gray-400 space-y-2">
                    <p>{{ __('factorio-rcon::messages.chat.info') }}</p>
                    <ul class="list-disc list-inside space-y-1">
                        <li>{{ __('factorio-rcon::messages.chat.info_all') }}</li>
                        <li>{{ __('factorio-rcon::messages.chat.info_whisper') }}</li>
                    </ul>
                </div>
            </x-filament::section>
        </div>
    </div>
</x-filament-panels::page>
