<div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
    <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            {{ __('factorio-manager::messages.chat.title') }}
        </h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {{ __('factorio-manager::messages.chat.description') }}
        </p>
    </div>

    <form wire:submit.prevent="sendMessage" class="space-y-4">
        <!-- Empfänger Auswahl -->
        <div>
            <label for="recipient" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ __('factorio-manager::messages.chat.recipient') }}
            </label>
            <select 
                wire:model="recipient" 
                id="recipient"
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
            >
                <option value="all">{{ __('factorio-manager::messages.chat.all_players') }}</option>
                @foreach($this->getOnlinePlayers() as $player)
                    <option value="{{ $player['name'] }}">{{ $player['name'] }}</option>
                @endforeach
            </select>
        </div>

        <!-- Nachricht Eingabe -->
        <div>
            <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ __('factorio-manager::messages.chat.message') }}
            </label>
            <div class="flex space-x-2">
                <input 
                    type="text" 
                    wire:model="message" 
                    id="message"
                    placeholder="{{ __('factorio-manager::messages.chat.message_placeholder') }}"
                    class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                    maxlength="255"
                />
                <button 
                    type="submit"
                    class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    {{ __('factorio-manager::messages.chat.send') }}
                </button>
            </div>
        </div>

        <!-- Info Box -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
            <div class="flex">
                <svg class="w-5 h-5 text-blue-500 dark:text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div class="ml-3">
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        {{ __('factorio-manager::messages.chat.info') }}
                    </p>
                </div>
            </div>
        </div>
    </form>

    <!-- Schnellnachrichten -->
    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
            {{ __('factorio-manager::messages.chat.quick_messages') }}
        </h4>
        <div class="grid grid-cols-2 gap-2">
            @foreach([
                'welcome' => __('factorio-manager::messages.chat.quick.welcome'),
                'maintenance' => __('factorio-manager::messages.chat.quick.maintenance'),
                'restart' => __('factorio-manager::messages.chat.quick.restart'),
                'backup' => __('factorio-manager::messages.chat.quick.backup'),
            ] as $key => $text)
                <button 
                    type="button"
                    wire:click="$set('message', '{{ $text }}')"
                    class="text-left px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors"
                >
                    {{ $text }}
                </button>
            @endforeach
        </div>
    </div>
</div>
