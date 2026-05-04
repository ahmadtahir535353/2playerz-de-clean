<x-filament-panels::page>
    <!-- Custom Filters Section -->
    <div class="mb-4 rounded-lg border border-gray-300 bg-white p-3 shadow-sm dark:border-gray-600 dark:bg-gray-800">
        <div class="flex items-center justify-end mb-3">
            <div class="flex items-center gap-2">
                <!-- Loading Spinner -->
                <div wire:loading class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-xs text-gray-600 dark:text-gray-400">{{ __('messages.common.loading') }}</span>
                </div>
                
                @if($this->hasActiveFilters())
                    <button 
                        type="button"
                        wire:click="clearFilters"
                        class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                        title="Clear all filters"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                @endif
            </div>
        </div>
        
        <form wire:submit.prevent="applyFilters" class="flex flex-wrap items-end gap-3">
            <!-- Date Range Group -->
            <div class="flex gap-2">
                <!-- From Date -->
                <div class="flex-shrink-0 relative">
                    <input 
                        type="date" 
                        id="fromDate"
                        wire:model.live="customFilters.fromDate"
                        placeholder="{{ __('messages.common.from_date') }}"
                        class="block w-32 rounded border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs"
                        wire:loading.attr="disabled"
                    />
                    <div wire:loading wire:target="customFilters.fromDate" class="absolute right-2 top-1/2 transform -translate-y-1/2">
                        <svg class="animate-spin h-3 w-3 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>

                <!-- To Date -->
                <div class="flex-shrink-0 relative">
                    <input 
                        type="date" 
                        id="toDate"
                        wire:model.live="customFilters.toDate"
                        placeholder="{{ __('messages.common.to_date') }}"
                        class="block w-32 rounded border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs"
                        wire:loading.attr="disabled"
                    />
                    <div wire:loading wire:target="customFilters.toDate" class="absolute right-2 top-1/2 transform -translate-y-1/2">
                        <svg class="animate-spin h-3 w-3 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Time Period -->
                <div class="flex-shrink-0 relative">
                    <select 
                        id="timePeriod"
                        wire:model.live="customFilters.timePeriod"
                        class="block w-28 rounded border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs"
                        wire:loading.attr="disabled"
                    >
                        <option value="all">{{ __('messages.common.all_time') }}</option>
                        <option value="today">{{ __('messages.common.today') }}</option>
                        <option value="last_week">{{ __('messages.common.last_week') }}</option>
                        <option value="last_month">{{ __('messages.common.last_month') }}</option>
                        <option value="last_year">{{ __('messages.common.last_year') }}</option>
                    </select>
                    <div wire:loading wire:target="customFilters.timePeriod" class="absolute right-2 top-1/2 transform -translate-y-1/2">
                        <svg class="animate-spin h-3 w-3 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Post Metrics -->
            <div class="flex-shrink-0">
                <div class="flex gap-2">
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            wire:model.live="customFilters.metrics" 
                            value="most_read"
                            class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600"
                        />
                        <span class="ml-1 text-xs text-gray-700 dark:text-gray-300">{{ __('messages.common.most_read') }}</span>
                    </label>
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            wire:model.live="customFilters.metrics" 
                            value="most_liked"
                            class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600"
                        />
                        <span class="ml-1 text-xs text-gray-700 dark:text-gray-300">{{ __('messages.common.most_liked') }}</span>
                    </label>
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            wire:model.live="customFilters.metrics" 
                            value="most_commented"
                            class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600"
                        />
                        <span class="ml-1 text-xs text-gray-700 dark:text-gray-300">{{ __('messages.common.most_commented') }}</span>
                    </label>
                </div>
            </div>
        </form>
    </div>

    <!-- Original Table Content -->
    {{ $this->table }}
</x-filament-panels::page>
