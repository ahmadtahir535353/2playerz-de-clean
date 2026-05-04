<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\EditProfile;
use App\Http\Middleware\CheckSubscription;
use App\Http\Middleware\CheckUserIsVerified;
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

class CustomerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->spa()
            ->favicon(!empty(getAppFavicon()) ? getAppFavicon() : asset('assets/image/favicon-infyom.png'))
            ->id('customer')
            ->path('customer')
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->breadcrumbs(false)
            ->plugin(SimpleLightBoxPlugin::make())
            ->profile(EditProfile::class, isSimple: false)
            // ->profile()
            ->sidebarCollapsibleOnDesktop()
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label(fn() => __('messages.user.profile'))
                    ->icon(fn() => getLogInUser()->profile_image),
                MenuItem::make()
                    ->label(fn() => __('messages.subscription.manage_subscription'))
                    ->icon('heroicon-o-star')
                    ->url(fn() => route('filament.customer.pages.manage-subscription'))
                    ->hidden(fn() => ! auth()->user()->hasRole('customer')),
            ])
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
            ->renderHook(PanelsRenderHook::HEAD_START, fn() => view('layout.head'))
            // ->renderHook(PanelsRenderHook::SCRIPTS_BEFORE, fn() => view('layout.scripts'))
            ->renderHook('panels::user-menu.profile.after', fn() => Blade::render('@livewire(\'change-password\')'))
            ->renderHook('panels::user-menu.profile.after', fn() => $this->changePassword())
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
                fn() => view('layout.footer')
            )
            ->renderHook(
                PanelsRenderHook::SIDEBAR_NAV_START,
                fn() => view('layout.search-in-sidebar')
            )
            ->maxContentWidth(MaxWidth::Full)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            // ->widgets([
            //     Widgets\AccountWidget::class,
            //     Widgets\FilamentInfoWidget::class,
            // ])
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
                CheckSubscription::class,
                RoleMiddleware::class . ':customer',
            ])
            ->authMiddleware([
                CheckUserIsVerified::class,
                Authenticate::class,
            ]);
    }
    public function changePassword(): string
    {
        return '<a class="flex items-center w-full gap-2 p-2 text-sm transition-colors duration-75 rounded-md outline-none cursor-pointer whitespace-nowrap disabled:pointer-events-none disabled:opacity-70 hover:bg-gray-50 focus-visible:bg-gray-50 dark:hover:bg-white/5 dark:focus-visible:bg-white/5 fi-dropdown-list-item-color-gray fi-color-gray" @click="$dispatch(\'open-modal\', {id: \'change-password\'})">
                <svg class="w-5 h-5 text-gray-400 fi-dropdown-list-item-icon dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"></path>
                </svg>
                <span class="flex-1 text-gray-700 truncate fi-dropdown-list-item-label text-start dark:text-gray-200" style="">' . __('messages.user.change_password') . '</span>
                </a>';
    }
}
