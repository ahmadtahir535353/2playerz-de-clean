<?php

namespace App\Providers;

use App\Models\Language;
use App\Models\Post;
use App\Models\Page;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\User;
use App\Models\GameRelease;
use App\Observers\GameReleaseObserver;
use App\Observers\PostObserver;
use App\Observers\PageObserver;
use Filament\Support\Assets\Js;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentAsset;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use BezhanSalleh\FilamentLanguageSwitch\Enums\Placement;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Mariuzzo\LaravelJsLocalization\Commands\LangJsCommand;
use Mariuzzo\LaravelJsLocalization\Generators\LangJsGenerator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            LoginResponse::class,
            \App\Http\Responses\LoginResponse::class
        );

        $this->app->singleton(
            LogoutResponse::class,
            \App\Http\Responses\LogoutResponse::class
        );

        // Bind the Laravel JS Localization command into the app IOC.
        $this->app->singleton('localization.js', function ($app) {
            $app = $this->app;
            $laravelMajorVersion = (int) $app::VERSION;

            $files = $app['files'];

            if ($laravelMajorVersion === 4) {
                $langs = $app['path.base'] . '/app/lang';
            } elseif ($laravelMajorVersion >= 5 && $laravelMajorVersion < 9) {
                $langs = $app['path.base'] . '/resources/lang';
            } elseif ($laravelMajorVersion >= 9) {
                $langs = app()->langPath();
            }
            $messages = $app['config']->get('localization-js.messages');
            $generator = new LangJsGenerator($files, $langs, $messages);

            return new LangJsCommand($generator);
        });
    }

    /**
     * Bootstrap any application services.
     */
   public function boot(): void
{

    if ((request()->is('login') || request()->is('register')) && !session()->has('locale')) {
        app()->setLocale('de');
        session()->put('locale', 'de');
    } elseif (session()->has('locale')) {
     
        app()->setLocale(session('locale'));
    }

  
    View::composer('*', function ($view) {
        if (Auth::check()) {
            $userId = Auth::id();

            $notifications = DB::table('notifications')
                ->where('to_user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            $unreadCount = DB::table('notifications')
                ->where('to_user_id', $userId)
                ->whereNull('read_at')
                ->count();

            $view->with('userNotifications', $notifications);
            $view->with('userUnreadNotificationCount', $unreadCount);
        }
    });

   
    if ($this->isDbConnected()) {
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $languages = Language::pluck('iso_code')->toArray();

            $switch
                ->locales($languages)
                ->flags([
                    'ar' => asset('images/flags/arabic.svg'),
                    'en' => asset('images/flags/english.png'),
                    'fr' => asset('images/flags/france.png'),
                    'de' => asset('images/flags/german.png'),
                    'es' => asset('images/flags/spain.png'),
                    'pt' => asset('images/flags/portuguese.png'),
                    'it' => asset('images/flags/italian.png'),
                    'ru' => asset('images/flags/russian.png'),
                    'tr' => asset('images/flags/turkish.png'),
                    'zh' => asset('images/flags/china.png'),
                ])
                ->outsidePanelPlacement(Placement::TopLeft)
                ->visible(outsidePanels: true)
                ->outsidePanelRoutes([
                    'auth.login',
                    'auth.register',
                    'auth.password-reset',
                ]);
        });

        FilamentAsset::register([
            // Js::make('custom-script', __DIR__.'/../../resources/js/custom.js'),
        ]);
    } else {
        Log::error('Database connection failed.');
    }

        // $activeLanguage = Language::where('front_language_status', 1)->first();
        // $locale = $activeLanguage ? $activeLanguage->iso_code : 'en';
        // App::setLocale($locale);
        Post::observe(PostObserver::class);
        Page::observe(PageObserver::class);
        GameRelease::observe(GameReleaseObserver::class);

        View::composer('front_new.layouts.header', function ($view) {
        // Get categories that are shown in menu (for navigation) - this includes all visible categories
        $categories = Category::select('id','name')
            ->where('show_in_menu', Category::SHOW_IN_MENU_ACTIVE)
            ->orderBy('name', 'asc')
            ->get();

        $subcategories = SubCategory::select('id','name','parent_category_id')->get();

        $editorIds = Post::distinct()->pluck('created_by')->filter()->unique();
        $editors = User::whereIn('id', $editorIds)
            ->whereDoesntHave('roles', function($q) {
                $q->where('name', 'admin');
            })
            ->get()
            ->map(fn($u)=> [
                'id'=>$u->id,
                'name'=> trim($u->first_name.' '.$u->last_name)
            ]);

        $view->with(compact('categories','subcategories','editors'));
    });
}


    protected function isDbConnected(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            Log::error('Database connection error: ' . $e->getMessage());
            return false;
        }
    }
}
