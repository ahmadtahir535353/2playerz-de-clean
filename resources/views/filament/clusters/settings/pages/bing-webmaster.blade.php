<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament-panels::form wire:submit.prevent="save">
            {{ $this->form }}

            <div class="flex gap-3 mt-6">
                <x-filament::button type="submit" wire:loading.attr="disabled">
                    {{ __('messages.common.save') }}
                </x-filament::button>

                @if($isConnected)
                    <x-filament::button color="danger" wire:click="disconnect" wire:loading.attr="disabled">
                        {{ __('messages.bing.disconnect') }}
                    </x-filament::button>
                @else
                    <x-filament::button color="info" wire:click="testConnection" wire:loading.attr="disabled">
                        {{ __('messages.bing.test_connection') }}
                    </x-filament::button>
                @endif
            </div>
        </x-filament-panels::form>

        <!-- Instructions -->
        <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
            <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-2">
                {{ __('messages.bing.how_to_get_api_key') }}
            </h3>
            <ol class="list-decimal list-inside space-y-2 text-sm text-blue-700 dark:text-blue-300">
                <li>{{ __('messages.bing.step_1') }}</li>
                <li>{{ __('messages.bing.step_2') }}</li>
                <li>{{ __('messages.bing.step_3') }}</li>
                <li>{{ __('messages.bing.step_4') }}</li>
                <li>{{ __('messages.bing.step_5') }}</li>
            </ol>
            <div class="mt-4">
                <a href="https://www.bing.com/webmasters" target="_blank" 
                   class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium">
                    {{ __('messages.bing.visit_bing_webmaster') }} →
                </a>
            </div>
        </div>
    </div>
</x-filament-panels::page>
