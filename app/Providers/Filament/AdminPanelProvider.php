<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\EditProfile;
use App\Http\Middleware\CheckUserIsVerified;
use App\Models\Role;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use SolutionForest\FilamentSimpleLightBox\SimpleLightBoxPlugin;
use Spatie\Permission\Middleware\RoleMiddleware;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $role = [];
        try {
            \DB::connection()->getPdo();
            $role = Role::where('name', '!=', 'customer')->pluck('name')->toArray();
        } catch (\Exception $e) {
        }

        $rolesString = implode('|', $role);

        return $panel
            ->spa()
            // ->brandName('Infy News')
            ->favicon(! empty(getAppFavicon()) ? getAppFavicon() : asset('assets/image/favicon-infyom.png'))
            ->id('admin')
            ->path('admin')
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->breadcrumbs(false)
            ->plugin(SimpleLightBoxPlugin::make())
            ->profile(EditProfile::class, isSimple: false)
            // ->profile()
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label(fn () => __('messages.user.profile'))
                    ->icon(fn () => getLogInUser()->profile_image),
                MenuItem::make()
                    ->label(fn () => __('messages.subscription.manage_subscription'))
                    ->icon('heroicon-o-star')
                    ->url(fn () => route('filament.customer.pages.manage-subscription'))
                    ->hidden(fn () => ! auth()->user()->hasRole('customer')),
            ])
            ->sidebarCollapsibleOnDesktop()
            ->renderHook('panels::user-menu.before', function () {
                return Blade::render('
                    <a id="gotoFullScreen" title="Toggle Fullscreen">
                        <svg id="fullScreenIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="gray" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
                        </svg>
                    </a>

                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            var fullScreenButton = document.getElementById("gotoFullScreen");
                            var fullScreenIcon = document.getElementById("fullScreenIcon");

                            fullScreenButton.addEventListener("click", function() {
                                if (!document.fullscreenElement) {
                                    // Enter fullscreen mode
                                    document.documentElement.requestFullscreen();
                                    // Switch to "small screen" (exit fullscreen) icon
                                    fullScreenIcon.innerHTML = `
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 9V4.5M9 9H4.5M9 9 3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5 5.25 5.25" />
                                    `;
                                } else {
                                    // Exit fullscreen mode
                                    if (document.exitFullscreen) {
                                        document.exitFullscreen();
                                    }
                                    // Switch back to "fullscreen" icon
                                    fullScreenIcon.innerHTML = `
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
                                    `;
                                }
                            });
                        });
                    </script>
                ');
            })

            ->renderHook(PanelsRenderHook::HEAD_START, fn () => view('layout.head'))
            // ->renderHook(PanelsRenderHook::SCRIPTS_BEFORE, fn() => view('layout.scripts'))
            ->renderHook(PanelsRenderHook::BODY_END, fn () => Blade::render('
                <script>
                    // Inject translations for image copyright modal and copy/paste modal
                    window.translations = {
                        image_copyright_title: "{{ __("messages.other_lang.image_copyright_title") }}",
                        copyright_text_label: "{{ __("messages.other_lang.copyright_text_label") }}",
                        copyright_placeholder: "{{ __("messages.other_lang.copyright_placeholder") }}",
                        copyright_hint: "{{ __("messages.other_lang.copyright_hint") }}",
                        cancel: "{{ __("messages.cancel") }}",
                        save_copyright: "{{ __("messages.other_lang.save_copyright") }}",
                        copyright_added: "{{ __("messages.other_lang.copyright_added") }}",
                        copyright_removed: "{{ __("messages.other_lang.copyright_removed") }}",
                        copy_paste_options: "{{ __("messages.other_lang.copy_paste_options") }}",
                        copy: "{{ __("messages.other_lang.copy") }}",
                        cut: "{{ __("messages.other_lang.cut") }}",
                        paste: "{{ __("messages.other_lang.paste") }}",
                        text_copied: "{{ __("messages.other_lang.text_copied") }}",
                        text_cut: "{{ __("messages.other_lang.text_cut") }}",
                        text_pasted: "{{ __("messages.other_lang.text_pasted") }}",
                        please_select_text: "{{ __("messages.other_lang.please_select_text") }}",
                        paste_failed: "{{ __("messages.other_lang.paste_failed") }}",
                        use_ctrl_v: "{{ __("messages.other_lang.use_ctrl_v") }}"
                    };
                </script>
                <script src="{{ asset(\'js/image-copyright-helper.js\') }}"></script>
                <script src="{{ asset(\'js/copy-paste-helper.js\') }}"></script>
                @vite([\'resources/js/filament-seo-box.js\'])
            '))
            ->renderHook('panels::user-menu.profile.after', fn () => Blade::render('@livewire(\'change-password\')'))
            ->renderHook('panels::user-menu.profile.after', fn () => $this->changePassword())
            ->renderHook('panels::user-menu.after', function () {
                return Blade::render("
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const userAvatar = document.querySelector('.fi-user-avatar');

                            if (userAvatar) {
                                const parentButton = userAvatar.closest('button');

                                if (parentButton) {
                                    const newHtml = `
                                        <div class='flex flex-col px-4'>
                                            <p class='text-sm text-gray-600 dark:text-gray-200'>
                                                {{ auth()->user()->full_name }}
                                            </p>
                                            <p class='text-xs text-gray-500 dark:text-gray-400'>
                                                {{ auth()->user()->email }}
                                            </p>
                                        </div>
                                    `;

                                    parentButton.insertAdjacentHTML('afterend', newHtml);
                                }
                            }
                        });
                    </script>
                ");
            })
            ->renderHook(
                PanelsRenderHook::FOOTER,
                fn () => view('layout.footer')
            )
            ->renderHook(
                PanelsRenderHook::SIDEBAR_NAV_START,
                fn () => view('layout.search-in-sidebar')
            )
            ->maxContentWidth(MaxWidth::Full)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->widgets([
                \App\Filament\Widgets\AdminDashboardCardOverview::class,
                \App\Filament\Widgets\AdminDashboardPostViewsChart::class,
                \App\Filament\Widgets\AdminDashboardUniqueVisitorsChart::class,
                \App\Filament\Widgets\AdminDashboardRecentUserTable::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                CheckUserIsVerified::class,
                Authenticate::class,
                RoleMiddleware::class.':'.$rolesString,
            ]);
    }

    public function changePassword(): string
    {
        return '<a class="flex items-center w-full gap-2 p-2 text-sm transition-colors duration-75 rounded-md outline-none cursor-pointer whitespace-nowrap disabled:pointer-events-none disabled:opacity-70 hover:bg-gray-50 focus-visible:bg-gray-50 dark:hover:bg-white/5 dark:focus-visible:bg-white/5 fi-dropdown-list-item-color-gray fi-color-gray" @click="$dispatch(\'open-modal\', {id: \'change-password\'})">
                <svg class="w-5 h-5 text-gray-400 fi-dropdown-list-item-icon dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"></path>
                </svg>
                <span class="flex-1 text-gray-700 truncate fi-dropdown-list-item-label text-start dark:text-gray-200" style="">'.__('messages.user.change_password').'</span>
                </a>';
    }
}
