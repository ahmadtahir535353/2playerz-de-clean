<x-filament-panels::page>
    <div class="space-y-6">
        @if(!$this->isConnected)
            @if(!$this->hasCredentials)
                <x-filament-panels::form wire:submit.prevent="saveCredentials">
                    {{ $this->form }}
                    <div class="mt-4">
                        <x-filament::button wire:loading.attr="disabled" type="submit" class="px-4">
                            <span class="flex justify-center">
                                <svg wire:loading aria-hidden="true" role="status"
                                    class="hidden inline w-4 h-4 my-auto text-white me-1 animate-spin" viewBox="0 0 100 101"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                        fill="#E5E7EB" />
                                    <path
                                        d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                        fill="currentColor" />
                                </svg>
                                <span class="ms-1">
                                    {{ __('messages.common.save') }}
                                </span>
                            </span>
                        </x-filament::button>
                    </div>
                </x-filament-panels::form>
            @else
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-2">
                        {{ __('messages.gsc.connect_google_search_console') }}
                    </h3>
                    <p class="text-blue-700 dark:text-blue-300 mb-4">
                        {{ __('messages.gsc.connect_account_description') }}
                    </p>
                    <x-filament::button 
                        wire:click="connectGSC"
                        color="primary"
                        size="lg"
                    >
                        <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        {{ __('messages.gsc.connect_with_google') }}
                    </x-filament::button>
                </div>
            @endif
        @else
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-green-900 dark:text-green-100 mb-2">
                            ✓ {{ __('messages.gsc.connected_to_gsc') }}
                        </h3>
                        <p class="text-green-700 dark:text-green-300">
                            {{ __('messages.gsc.property_url') }}: <strong>{{ $this->token->property_url ?? 'N/A' }}</strong>
                        </p>
                    </div>
                    <x-filament::button 
                        wire:click="disconnectGSC"
                        color="danger"
                        size="sm"
                    >
                        {{ __('messages.gsc.disconnect') }}
                    </x-filament::button>
                </div>
            </div>
        @endif

        @if($this->isConnected)
            <x-filament-panels::form wire:submit.prevent="save">
                {{ $this->form }}
                <div>
                    <x-filament::button wire:loading.attr="disabled" type="submit" class="px-4">
                        <span class="flex justify-center">
                            <svg wire:loading aria-hidden="true" role="status"
                                class="hidden inline w-4 h-4 my-auto text-white me-1 animate-spin" viewBox="0 0 100 101"
                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                    fill="#E5E7EB" />
                                <path
                                    d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                    fill="currentColor" />
                            </svg>
                            <span class="ms-1">
                                {{ __('messages.common.save') }}
                            </span>
                        </span>
                    </x-filament::button>
                </div>
            </x-filament-panels::form>
        @endif

        @if($this->isConnected)
            <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">{{ __('messages.gsc.information') }}</h3>
                <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                    <li>• {{ __('messages.gsc.data_fetch_info') }}</li>
                    <li>• {{ __('messages.gsc.cron_job_route') }}: <code class="bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-2 py-1 rounded">{{ url('/fetch-gsc-data') }}</code></li>
                    <li>• {{ __('messages.gsc.last_data_fetch') }}</li>
                    <li>• {{ __('messages.gsc.gsc_data_dashboard') }}</li>
                </ul>
            </div>
        @endif
    </div>
</x-filament-panels::page>

