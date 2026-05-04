@php
    $settings = App\Models\Setting::pluck('value', 'key')->toArray();
@endphp
<div class="">
    <footer class="border-t border-gray-300 dark:border-gray-600 w-full pt-4 px-6 mt-7 sticky bottom-0">
        <div class="flex text-gray-400 justify-between mb-4 text-sm">
            <div>
                {{ $settings['copy_right_text'] }}
                <a href="#" class="no-underline">
                    {{ $settings['application_name'] }}
                </a>
            </div>
            @if (config('app.footer_version_show'))
                <div class="flex items-center">
                    <span class="ml-2">v{{ getCurrentVersion() }}</span>
                </div>
            @endif
        </div>
    </footer>
</div>
<style>
    .fi-main-ctn {
        min-height: 100vh;
        .fi-main {
            flex-grow: 1;
        }
    }
</style>
