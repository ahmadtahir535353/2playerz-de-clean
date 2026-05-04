<x-filament-panels::page>
    <div class="space-y-6" x-data="{ activeTab: 'gsc' }">
        <!-- Tabs Navigation -->
        <div class="flex">
            <nav class="flex max-w-full p-2 mx-auto overflow-x-auto bg-white shadow-sm fi-tabs gap-x-1 rounded-xl ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
                role="tablist">
                <button type="button"
                    @click="activeTab = 'gsc'; $wire.set('activeTab', 'gsc')"
                    :class="activeTab === 'gsc' ? 'fi-tabs-item-active bg-gray-50 dark:bg-white/5 text-primary-600 dark:text-primary-400' : 'hover:bg-gray-50 focus-visible:bg-gray-50 dark:hover:bg-white/5 dark:focus-visible:bg-white/5'"
                    class="fi-tabs-item group flex items-center gap-x-2 rounded-lg px-6 py-2 text-sm font-medium outline-none transition duration-75"
                    role="tab"
                    id="gsc-tab">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12.545,10.239v3.821h5.445c-0.712,2.315-2.647,3.972-5.445,3.972c-3.332,0-6.033-2.701-6.033-6.032s2.701-6.032,6.033-6.032c1.498,0,2.866,0.549,3.921,1.453l2.814-2.814C17.503,2.988,15.139,2,12.545,2C7.021,2,2.543,6.477,2.543,12s4.478,10,10.002,10c8.396,0,10.249-7.85,9.426-11.748L12.545,10.239z"/>
                    </svg>
                    {{ __('messages.search_analytics.google_search_console') }}
                </button>
                <button type="button"
                    @click="activeTab = 'bing'; $wire.set('activeTab', 'bing')"
                    :class="activeTab === 'bing' ? 'fi-tabs-item-active bg-gray-50 dark:bg-white/5 text-primary-600 dark:text-primary-400' : 'hover:bg-gray-50 focus-visible:bg-gray-50 dark:hover:bg-white/5 dark:focus-visible:bg-white/5'"
                    class="fi-tabs-item group flex items-center gap-x-2 rounded-lg px-6 py-2 text-sm font-medium outline-none transition duration-75"
                    role="tab"
                    id="bing-tab">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M5.71 3L3 4.29v15.03l6.77 3.43v-.01l.01-.01 3.65-2.07-2.27-1.11-2.8 1.6V7.29l8.8 3.23v4.79l-2.83 1.64 4.51 2.21L21 18.14V8.43L5.71 3zm8.36 11.53l-2.1-1.01V9.62l2.1.97v3.94z"/>
                    </svg>
                    {{ __('messages.search_analytics.bing_webmaster') }}
                </button>
            </nav>
        </div>

        <!-- Tab Contents -->
        <!-- Google Search Console Tab -->
        <div x-show="activeTab === 'gsc'" id="gsc-content" class="space-y-6">
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
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
                :widgets="$this->getGSCWidgets()"
                :columns="$this->getColumns()"
            />
        </div>

        <!-- Bing Webmaster Tab -->
        <div x-show="activeTab === 'bing'" id="bing-content" class="space-y-6" style="display: none;">
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-green-900 dark:text-green-100 mb-1">
                            {{ __('messages.bing.bing_webmaster_analytics') }}
                        </h3>
                        <p class="text-sm text-green-700 dark:text-green-300">
                            {{ __('messages.bing.view_performance_description') }}
                        </p>
                    </div>
                    <a href="{{ route('filament.admin.settings.pages.bing-webmaster') }}" 
                       class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 text-sm font-medium">
                        {{ __('messages.bing.manage_connection') }} →
                    </a>
                </div>
            </div>

            <x-filament-widgets::widgets
                :widgets="$this->getBingWidgets()"
                :columns="$this->getColumns()"
            />
        </div>
    </div>
</x-filament-panels::page>
