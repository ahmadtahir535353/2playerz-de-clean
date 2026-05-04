<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-1">
                        {{ __('messages.gsc.google_search_console_analytics') }}
                    </h3>
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        {{ __('messages.gsc.view_performance_description') }}
                    </p>
                </div>
                <a href="{{ route('filament.admin.settings.pages.google-search-console') }}" 
                   class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium">
                    {{ __('messages.gsc.manage_connection') }} →
                </a>
            </div>
        </div>

        <x-filament-widgets::widgets
            :widgets="$this->getWidgets()"
            :columns="$this->getColumns()"
        />
    </div>
</x-filament-panels::page>
