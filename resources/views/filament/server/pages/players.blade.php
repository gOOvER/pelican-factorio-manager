<x-filament-panels::page>
    <div wire:poll.5s>
    @php
        $players = $this->getPlayers();
    @endphp

    <div class="space-y-6">
        {{-- Filter --}}
        <div class="flex items-center gap-4">
            <x-filament::input.wrapper>
                <x-filament::input.select wire:model.live="statusFilter">
                    <option value="all">{{ __('factorio-manager::messages.filters.all') }}</option>
                    <option value="online">{{ __('factorio-manager::messages.filters.online') }}</option>
                    <option value="offline">{{ __('factorio-manager::messages.filters.offline') }}</option>
                    <option value="admin">{{ __('factorio-manager::messages.filters.admin') }}</option>
                    <option value="banned">{{ __('factorio-manager::messages.filters.banned') }}</option>
                </x-filament::input.select>
            </x-filament::input.wrapper>
            
            <span class="text-sm text-gray-500">
                {{ count($players) }} {{ __('factorio-manager::messages.columns.name') }}
            </span>
        </div>

        {{-- Table --}}
        <x-filament::section>
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('factorio-manager::messages.columns.name') }}
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('factorio-manager::messages.columns.status') }}
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('factorio-manager::messages.columns.admin') }}
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('factorio-manager::messages.filters.banned') }}
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('factorio-manager::messages.sections.management') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($players as $player)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $player['name'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($player['online'])
                                        <x-filament::icon icon="tabler-circle-check" class="h-5 w-5 text-success-500" />
                                    @else
                                        <x-filament::icon icon="tabler-circle-x" class="h-5 w-5 text-gray-400" />
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($player['is_admin'])
                                        <x-filament::icon icon="tabler-shield-check" class="h-5 w-5 text-warning-500" />
                                    @else
                                        <x-filament::icon icon="tabler-shield" class="h-5 w-5 text-gray-400" />
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($player['is_banned'])
                                        <x-filament::icon icon="tabler-ban" class="h-5 w-5 text-danger-500" />
                                    @else
                                        <x-filament::icon icon="tabler-circle-check" class="h-5 w-5 text-success-500" />
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Promote/Demote --}}
                                        @if($player['is_admin'])
                                            <x-filament::button
                                                x-on:click="if(confirm('{{ __('factorio-manager::messages.actions.demote.confirm', ['name' => $player['name']]) }}')) { $wire.demotePlayer('{{ $player['name'] }}') }"
                                                color="warning"
                                                size="xs"
                                                icon="tabler-shield-x"
                                            >
                                                {{ __('factorio-manager::messages.actions.demote.label') }}
                                            </x-filament::button>
                                        @else
                                            <x-filament::button
                                                x-on:click="if(confirm('{{ __('factorio-manager::messages.actions.promote.confirm', ['name' => $player['name']]) }}')) { $wire.promotePlayer('{{ $player['name'] }}') }"
                                                color="success"
                                                size="xs"
                                                icon="tabler-shield-check"
                                            >
                                                {{ __('factorio-manager::messages.actions.promote.label') }}
                                            </x-filament::button>
                                        @endif

                                        {{-- Kick --}}
                                        @if($player['online'])
                                            <x-filament::button
                                                x-on:click="if(confirm('{{ __('factorio-manager::messages.actions.kick.confirm', ['name' => $player['name']]) }}')) { $wire.kickPlayer('{{ $player['name'] }}') }"
                                                color="danger"
                                                size="xs"
                                                icon="tabler-door-exit"
                                            >
                                                {{ __('factorio-manager::messages.actions.kick.label') }}
                                            </x-filament::button>
                                        @endif

                                        {{-- Ban/Unban --}}
                                        @if($player['is_banned'])
                                            <x-filament::button
                                                x-on:click="if(confirm('{{ __('factorio-manager::messages.actions.unban.confirm', ['name' => $player['name']]) }}')) { $wire.unbanPlayer('{{ $player['name'] }}') }"
                                                color="success"
                                                size="xs"
                                                icon="tabler-circle-check"
                                            >
                                                {{ __('factorio-manager::messages.actions.unban.label') }}
                                            </x-filament::button>
                                        @else
                                            <x-filament::button
                                                x-on:click="if(confirm('{{ __('factorio-manager::messages.actions.ban.confirm', ['name' => $player['name']]) }}')) { $wire.banPlayer('{{ $player['name'] }}') }"
                                                color="danger"
                                                size="xs"
                                                icon="tabler-ban"
                                            >
                                                {{ __('factorio-manager::messages.actions.ban.label') }}
                                            </x-filament::button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    {{ __('factorio-manager::messages.values.no_players') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
    </div>
</x-filament-panels::page>
