<x-filament-panels::page>
    <style>
        /* Modern Card Styles */
        .fm-card {
            background: #252525;
            border: 1px solid #2f2f2f;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
            transition: all 150ms ease;
        }
        .fm-card:hover {
            background: #2b2b2b;
        }
        
        /* Status Cards Grid */
        .fm-status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
        }
        
        /* Chat Container */
        .fm-chat-container {
            background: #1e1e1e;
            border: 1px solid #2a2a2a;
            border-radius: 6px;
            padding: 20px;
        }
        
        /* Chat Entry */
        .fm-chat-entry {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 10px 12px;
            border-bottom: 1px solid #2a2a2a;
            line-height: 1.5;
            transition: background 120ms ease;
        }
        .fm-chat-entry:last-child {
            border-bottom: none;
        }
        .fm-chat-entry:hover {
            background: #262626;
        }
        
        /* Timestamp */
        .fm-timestamp {
            color: #6b7280;
            font-size: 0.75rem;
            font-family: ui-monospace, monospace;
            white-space: nowrap;
            min-width: 65px;
        }
        
        /* Username */
        .fm-username {
            color: #d1d5db;
            font-weight: 600;
        }
        
        /* Message */
        .fm-message {
            color: #e5e7eb;
        }
        
        /* System Message */
        .fm-system {
            color: #fbbf24;
        }
        
        /* Buttons */
        .fm-btn {
            border-radius: 6px;
            padding: 8px 14px;
            font-weight: 600;
            transition: all 150ms ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .fm-btn-primary {
            background: #3b82f6;
            color: white;
        }
        .fm-btn-primary:hover {
            background: #2563eb;
        }
        .fm-btn-secondary {
            background: #374151;
            color: white;
        }
        .fm-btn-secondary:hover {
            background: #4b5563;
        }
        .fm-btn-danger {
            background: #991b1b;
            color: white;
        }
        .fm-btn-danger:hover {
            background: #b91c1c;
        }
        
        /* Fade-in animation for new entries */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fm-chat-entry {
            animation: fadeIn 200ms ease;
        }
        
        /* Label styling */
        .fm-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        /* Value styling */
        .fm-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #ffffff;
        }
        
        /* Section Header */
        .fm-section-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid #2a2a2a;
        }
        .fm-section-title {
            font-size: 1rem;
            font-weight: 600;
            color: #e5e7eb;
        }

        /* Form Input Styling */
        .fm-input {
            background: #1e1e1e;
            border: 1px solid #3b3b3b;
            border-radius: 6px;
            padding: 10px 14px;
            color: #e5e7eb;
            width: 100%;
            transition: all 150ms ease;
        }
        .fm-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
        }
        .fm-input::placeholder {
            color: #6b7280;
        }

        /* Form Select Styling */
        .fm-select {
            background: #1e1e1e;
            border: 1px solid #3b3b3b;
            border-radius: 6px;
            padding: 10px 36px 10px 14px;
            color: #e5e7eb;
            width: 100%;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            cursor: pointer;
            transition: all 150ms ease;
        }
        .fm-select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
        }

        /* Form Label */
        .fm-form-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #9ca3af;
            margin-bottom: 0.5rem;
            display: block;
        }

        /* Quick Message Card */
        .fm-quick-card {
            background: #2a2a2a;
            border: 1px solid transparent;
            border-radius: 6px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: all 150ms ease;
            text-align: left;
            width: 100%;
        }
        .fm-quick-card:hover {
            background: #3a3a3a;
            transform: translateY(-1px);
        }
        .fm-quick-card:active {
            transform: scale(0.98);
        }

        /* Click flash animation */
        @keyframes clickFlash {
            0% { background: #4ade80; }
            100% { background: #2a2a2a; }
        }
        .fm-quick-card.clicked {
            animation: clickFlash 400ms ease;
        }

        /* Info Section */
        .fm-info-section {
            background: #1a1a1a;
            border-top: 1px solid #333;
            border-radius: 0 0 6px 6px;
            padding: 16px;
            margin: 0 -20px -20px -20px;
        }

        /* Hover scale for send button */
        .fm-btn-send {
            transition: all 150ms ease;
        }
        .fm-btn-send:hover {
            transform: scale(1.03);
        }
    </style>

    <div wire:poll.5s>
    @php
        $chatLog = $this->getChatLog();
        $chatLogAvailable = $chatLog !== null;
        $modInstalled = $this->isChatLogAvailable();
        $extendedStatus = $this->getExtendedStatus();
        $modVersion = $this->getModVersion();
    @endphp

    {{-- Extended Server Status Cards --}}
    @if($extendedStatus && $modInstalled)
    <div class="fm-status-grid" style="margin-bottom: 48px;">
        {{-- Game Time --}}
        <div class="fm-card p-5">
            <div class="flex items-center gap-4">
                <div class="p-3 rounded-lg" style="background: rgba(59, 130, 246, 0.1);">
                    <x-filament::icon icon="tabler-clock" class="h-6 w-6" style="color: #60a5fa;" />
                </div>
                <div>
                    <div class="fm-label">{{ __('factorio-manager::messages.status.game_time') }}</div>
                    <div class="fm-value">{{ $extendedStatus['time'] ?? '--:--:--' }}</div>
                </div>
            </div>
        </div>

        {{-- Players Online --}}
        <div class="fm-card p-5">
            <div class="flex items-center gap-4">
                <div class="p-3 rounded-lg" style="background: rgba(34, 197, 94, 0.1);">
                    <x-filament::icon icon="tabler-users" class="h-6 w-6" style="color: #4ade80;" />
                </div>
                <div>
                    <div class="fm-label">{{ __('factorio-manager::messages.status.players_online') }}</div>
                    <div class="fm-value" style="color: #4ade80;">{{ $extendedStatus['players'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        {{-- Evolution --}}
        <div class="fm-card p-5">
            <div class="flex items-center gap-4">
                <div class="p-3 rounded-lg" style="background: rgba(239, 68, 68, 0.1);">
                    <x-filament::icon icon="tabler-dna" class="h-6 w-6" style="color: #f87171;" />
                </div>
                <div>
                    <div class="fm-label">{{ __('factorio-manager::messages.status.evolution') }}</div>
                    <div class="fm-value" style="color: #f87171;">{{ number_format(($extendedStatus['evolution'] ?? 0) * 100, 1) }}%</div>
                </div>
            </div>
        </div>

        {{-- Current Research --}}
        <div class="fm-card p-5">
            <div class="flex items-center gap-4">
                <div class="p-3 rounded-lg" style="background: rgba(168, 85, 247, 0.1);">
                    <x-filament::icon icon="tabler-flask" class="h-6 w-6" style="color: #a78bfa;" />
                </div>
                <div class="min-w-0 flex-1">
                    <div class="fm-label">{{ __('factorio-manager::messages.status.research') }}</div>
                    <div class="fm-value text-lg truncate" style="color: #a78bfa;">{{ $extendedStatus['current_research'] ?? __('factorio-manager::messages.values.none') }}</div>
                </div>
            </div>
            @if($modVersion && !empty($modVersion['version']))
                <div class="mt-3 pt-3 border-t border-gray-700/50 text-xs" style="color: #6b7280;">
                    <x-filament::icon icon="tabler-puzzle" class="h-3 w-3 inline" /> Mod v{{ $modVersion['version'] }}
                </div>
            @endif
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Chat Log Section --}}
        <div class="lg:col-span-2 lg:row-span-2">
            @if($chatLogAvailable)
            <div class="fm-chat-container h-full">
                {{-- Header --}}
                <div class="fm-section-header">
                    <x-filament::icon icon="tabler-messages" class="h-5 w-5" style="color: #60a5fa;" />
                    <span class="fm-section-title">{{ __('factorio-manager::messages.chat.chat_log') }}</span>
                    <span class="ml-auto text-xs" style="color: #6b7280;">
                        {{ count($chatLog) }} {{ __('factorio-manager::messages.chat.messages') }}
                    </span>
                </div>

                {{-- Chat Entries --}}
                <div class="overflow-y-auto rounded" style="max-height: 380px; background: #1a1a1a;" id="chat-log">
                    @forelse($chatLog as $entry)
                        @php
                            $isSystem = in_array($entry['type'] ?? 'chat', ['system', 'server', 'join', 'leave', 'death', 'research', 'research_started', 'ban', 'unban', 'kick', 'promote', 'demote']);
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
                            $iconColor = match($entry['type'] ?? 'chat') {
                                'join' => '#4ade80',
                                'leave' => '#f87171',
                                'death' => '#f87171',
                                'respawn' => '#4ade80',
                                'research', 'research_started' => '#a78bfa',
                                'ban', 'kick' => '#f87171',
                                'unban' => '#4ade80',
                                'promote' => '#fbbf24',
                                'demote' => '#f97316',
                                'system', 'server' => '#fbbf24',
                                default => '#60a5fa',
                            };
                        @endphp
                        <div class="fm-chat-entry">
                            <span class="fm-timestamp">[{{ $entry['time'] ?? '' }}]</span>
                            <x-filament::icon icon="{{ $typeIcon }}" class="h-4 w-4 shrink-0 mt-0.5" style="color: {{ $iconColor }};" />
                            <div class="flex-1 min-w-0">
                                @if($isSystem)
                                    <span class="fm-system">{{ $entry['message'] ?? '' }}</span>
                                @else
                                    <span class="fm-username">{{ $entry['player'] ?? 'Unknown' }}:</span>
                                    <span class="fm-message">{{ $entry['message'] ?? '' }}</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-12" style="color: #6b7280;">
                            <x-filament::icon icon="tabler-message-off" class="h-12 w-12 mb-3 opacity-50" />
                            <span>{{ __('factorio-manager::messages.chat.no_messages') }}</span>
                        </div>
                    @endforelse
                </div>

                {{-- Action Buttons --}}
                <div class="mt-4 flex justify-between items-center">
                    <button wire:click="clearChatLog" class="fm-btn fm-btn-danger text-sm">
                        <x-filament::icon icon="tabler-trash" class="h-4 w-4" />
                        {{ __('factorio-manager::messages.chat.clear_log') }}
                    </button>
                    <button wire:click="$refresh" class="fm-btn fm-btn-primary text-sm">
                        <x-filament::icon icon="tabler-refresh" class="h-4 w-4" />
                        {{ __('factorio-manager::messages.chat.refresh') }}
                    </button>
                </div>
            </div>
            @else
            {{-- Mod not installed hint --}}
            <div class="fm-chat-container h-full">
                <div class="fm-section-header">
                    <x-filament::icon icon="tabler-messages" class="h-5 w-5" style="color: #6b7280;" />
                    <span class="fm-section-title">{{ __('factorio-manager::messages.chat.chat_log') }}</span>
                </div>

                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="p-4 rounded-full mb-4" style="background: rgba(251, 191, 36, 0.1);">
                        <x-filament::icon icon="tabler-puzzle" class="h-12 w-12" style="color: #fbbf24;" />
                    </div>
                    <h3 class="text-lg font-semibold mb-2" style="color: #e5e7eb;">
                        {{ __('factorio-manager::messages.chat.mod_required_title') }}
                    </h3>
                    <p class="max-w-md mb-6" style="color: #9ca3af;">
                        {{ __('factorio-manager::messages.chat.mod_not_installed') }}
                    </p>
                    <div class="fm-card p-4">
                        <p class="text-xs mb-3" style="color: #6b7280;">{{ __('factorio-manager::messages.chat.mod_install_hint') }}</p>
                        <a href="https://mods.factorio.com/mod/pelican-chat-logger" target="_blank" 
                           class="inline-flex items-center gap-2 text-sm font-medium transition-colors"
                           style="color: #60a5fa;"
                           onmouseover="this.style.color='#93c5fd'" 
                           onmouseout="this.style.color='#60a5fa'">
                            <x-filament::icon icon="tabler-external-link" class="h-4 w-4" />
                            pelican-chat-logger
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Main Chat Form --}}
        <div class="lg:col-span-1">
            {{-- Send Message Card --}}
            <div style="background: #252525; border: 1px solid #444; border-radius: 8px; padding: 24px; margin-bottom: 24px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 2px solid #3a3a3a;">
                    <div style="background: rgba(59, 130, 246, 0.2); padding: 8px; border-radius: 8px;">
                        <x-filament::icon icon="tabler-message-circle" class="h-5 w-5" style="color: #60a5fa;" />
                    </div>
                    <span style="font-size: 18px; font-weight: 600; color: #e5e7eb;">{{ __('factorio-manager::messages.chat.title') }}</span>
                </div>

                <p style="font-size: 14px; color: #9ca3af; margin-bottom: 20px;">
                    {{ __('factorio-manager::messages.chat.description') }}
                </p>

                <form wire:submit="sendMessage" class="space-y-4">
                    {{ $this->form }}

                    <div style="display: flex; justify-content: flex-end; padding-top: 12px;">
                        <button type="submit" class="fm-btn fm-btn-primary">
                            <x-filament::icon icon="tabler-send" class="h-4 w-4" />
                            {{ __('factorio-manager::messages.chat.send') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Quick Messages Card --}}
            <div style="background: #252525; border: 1px solid #444; border-radius: 8px; padding: 24px; margin-bottom: 24px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 2px solid #3a3a3a;">
                    <div style="background: rgba(251, 191, 36, 0.2); padding: 8px; border-radius: 8px;">
                        <x-filament::icon icon="tabler-bolt" class="h-5 w-5" style="color: #fbbf24;" />
                    </div>
                    <span style="font-size: 18px; font-weight: 600; color: #e5e7eb;">{{ __('factorio-manager::messages.chat.quick_messages') }}</span>
                </div>

                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;">
                    @php
                        $quickMessages = [
                            ['emoji' => '📢', 'color' => '#4ade80', 'bg' => 'rgba(22, 101, 52, 0.5)', 'border' => '#166534', 'text' => __('factorio-manager::messages.chat.quick.welcome')],
                            ['emoji' => '⚠️', 'color' => '#fbbf24', 'bg' => 'rgba(133, 77, 14, 0.5)', 'border' => '#854d0e', 'text' => __('factorio-manager::messages.chat.quick.maintenance')],
                            ['emoji' => '🔄', 'color' => '#60a5fa', 'bg' => 'rgba(30, 64, 175, 0.5)', 'border' => '#1e40af', 'text' => __('factorio-manager::messages.chat.quick.restart')],
                            ['emoji' => '💾', 'color' => '#a78bfa', 'bg' => 'rgba(91, 33, 182, 0.5)', 'border' => '#5b21b6', 'text' => __('factorio-manager::messages.chat.quick.backup')],
                        ];
                    @endphp

                    @foreach($quickMessages as $quick)
                        <button
                            wire:click="setQuickMessage('{{ addslashes($quick['text']) }}')"
                            style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 8px; text-align: left; background: {{ $quick['bg'] }}; border: 1px solid {{ $quick['border'] }}; cursor: pointer; transition: all 0.15s;"
                            title="{{ $quick['text'] }}"
                        >
                            <span style="font-size: 20px;">{{ $quick['emoji'] }}</span>
                            <span style="font-size: 14px; font-weight: 500; color: {{ $quick['color'] }}; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ Str::limit($quick['text'], 18) }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Information Card --}}
            <div style="background: #252525; border: 1px solid #444; border-radius: 8px; padding: 24px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 2px solid #3a3a3a;">
                    <div style="background: rgba(59, 130, 246, 0.2); padding: 8px; border-radius: 8px;">
                        <x-filament::icon icon="tabler-info-circle" class="h-5 w-5" style="color: #60a5fa;" />
                    </div>
                    <span style="font-size: 18px; font-weight: 600; color: #e5e7eb;">{{ __('factorio-manager::messages.chat.info_title') }}</span>
                </div>

                <p style="font-size: 14px; color: #9ca3af; margin-bottom: 16px;">
                    {{ __('factorio-manager::messages.chat.info') }}
                </p>

                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; align-items: center; gap: 16px; padding: 12px 16px; border-radius: 8px; background: #1a1a1a; border: 1px solid #333;">
                        <div style="background: rgba(59, 130, 246, 0.2); padding: 8px; border-radius: 8px;">
                            <span style="font-size: 18px;">📢</span>
                        </div>
                        <span style="font-size: 14px; color: #d1d5db;">{{ __('factorio-manager::messages.chat.info_all') }}</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 16px; padding: 12px 16px; border-radius: 8px; background: #1a1a1a; border: 1px solid #333;">
                        <div style="background: rgba(168, 85, 247, 0.2); padding: 8px; border-radius: 8px;">
                            <span style="font-size: 18px;">👤</span>
                        </div>
                        <span style="font-size: 14px; color: #d1d5db;">{{ __('factorio-manager::messages.chat.info_whisper') }}</span>
                    </div>
                </div>

                {{-- Footer Branding --}}
                <div style="text-align: center; margin-top: 24px; padding-top: 16px; border-top: 1px solid #3a3a3a;">
                    <span style="font-size: 12px; color: #6b7280;">© 2026 Pelican</span>
                </div>
            </div>
        </div>
    </div>
    </div>
</x-filament-panels::page>
