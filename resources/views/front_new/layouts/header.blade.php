<style>
    .dropdown-item:hover,
    .dropdown-item:focus {
        background-color: #B051B0 !important;
    }
    
    .dropdown-item:hover a,
    .dropdown-item:focus a {
        color: #ffffff !important;
    }

    .dropdown-item a {
        text-overflow: ellipsis;
        width: 100%;
        display: inline-block;
        overflow: hidden;
    }

    .d-none {
        display: none;
    }

    .text-dark {
        color: #000 !important;
    }

    .bg-dark {
        background-color: #000 !important;
    }

    .text-light {
        color: #fff !important;
    }

    .bg-light {
        background-color: #f8f9fa !important;
    }

    .unread-notification {
        background-color: #f8f9fa !important;
    }

    .topbar-theme-toggle {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding: 0 15px;
        height: 100%;
        margin-left: 85%;
    }

    .theme-switch-box {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .theme-status {
        font-size: 16px;
        transition: color 0.3s;
    }

    .switch-label {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 22px;
    }

    .switch-label input {
        display: none;
    }

    .switch {
        position: absolute;
        cursor: pointer;
        background-color: #ccc;
        border-radius: 22px;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        transition: 0.4s;
    }

    .switch::before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 2px;
        bottom: 2px;
        background-color: white;
        border-radius: 50%;
        transition: 0.4s;
    }

    .switch-label input:checked+.switch::before {
        transform: translateX(22px);
    }

    /* Theme styling */
    .light-mode .switch {
        /*background-color: purple !important;*/
    }

    .dark-mode .switch {
        background-color: #ddd !important;
    }

    .light-mode .sun-icon {
        color: purple;
    }

    .light-mode .moon-icon {
        color: #888;
    }

    .dark-mode .sun-icon {
        color: #aaa;
    }

    .dark-mode .moon-icon {
        color: #444;
    }

    .top-bar-logo,
    .footer-logo {
        width: 160px !important;
        height: auto !important;
    }

    header .nav .nav-item .nav-link {
        color: white !important;
    }

    .header-gradient {
        background: #000000;
        background: linear-gradient(90deg, rgba(0, 0, 0, 1) 0%, rgba(122, 0, 122, 1) 60%, rgba(75, 0, 75, 1) 100%);
    }

    /* @media screen and (max-width: 767px) {
        .filter-dropdown {
            transform: translate3d(19%, 27px, 0px) !important;
        }
    } */
</style>


<!-- start-breaking-news-section -->
@php
$breakingNewsStatus = \App\Models\Setting::where('key', 'breaking_news_status')->first()->value ?? false;
@endphp

@if($breakingNewsStatus == 1)
<div class="breaking-news-section py-2" id="topbar-wrap">
    <div class="container">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="title d-flex align-items-center justify-content-center">
                    <!-- <div
                        class="icon d-flex justify-content-center align-items-center {{ getFrontSelectLanguageIsoCode() == 'ar' ? 'ms-1' : 'me-1' }}"
                        style="background-color: #FF0000 !important;">
                        <i class="fas fa-bolt text-white"></i>
                        
                    </div> -->
                    <img src="{{asset('assets/image/breaking_img.png')}}" alt="2playerz" style="border-radius: 50%; width: 16px; margin-right: 5px">
                    <div class="trending-title d-flex ">
                        <a href="#" class="text-white">{{ __('messages.details.breaking') }}</a>
                    </div>
                    <span class="text-gray mx-2 h-100" aria-live="assertive" aria-atomic="true"> | </span>
                    <div class="content float-left breaking-slider swiper-container">
                        <div class="swiper-wrapper">
                            @foreach (getBreakingPost() as $breakingPost)
                            <div class="content item d-flex justify-content-start align-items-center swiper-slide">
                                <i
                                    class="fa-solid fa-circle text-white {{ getFrontSelectLanguageIsoCode() == 'ar' ? 'ms-2' : 'me-2' }}"></i>
                                <a href="{{ route('detailPage', $breakingPost->slug) }}" class="fs-12 text-white">
                                    {!! $breakingPost->title !!}
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<!-- end-breaking-news-section -->

<!--start top-bar-section -->
<section class="top-bar-section py-lg-2 py-3 px-3 top-bar d-none header-gradient">
    <img src="{{asset('uploads/logo/216/01JS37FN18W2RSNP53AH5NFTHB.png') }}" alt="2playerz" style="width: 14%;" />

    <div class="col-lg-2 col-sm-3 col-3 ">
        <a href="/" class="top-bar-logo d-block">
            <img src="{{ !empty(getAppLogo()) ? getAppLogo() : asset('assets/image/infyom-logo.png') }}"
                alt="2playerz" class="img-fluid" />
        </a>
    </div>
    <div class="col-xxl-4 col-lg-4 col-sm-6  br-gray  text-end  pe-xl-4 pe-lg-4 ">
        <span
            class="text-secondary fs-14 pe-sm-0 d-none">{{ \Carbon\Carbon::now()->isoFormat('ddd, MMM DD YYYY') }}</span>
    </div>

    <div class="row align-items-center justify-content-between">
        <div class="col-xl-7 col-md-8 col-9 ">
            <div class="row align-items-center justify-content-end  ">
                <div class="col-lg-4 py-1 d-lg-block d-none ">
                    <div class="social-icon d-flex justify-content-around d-none">
                        <a href="{{ $settings['facebook_url'] }}" target="_blank"> <i
                                class="fa-brands fa-facebook fa-lg" style="color: #1c5bca;"></i>
                        </a>
                        <a href="{{ $settings['twitter_url'] }}" target="_blank">
                            <i class="fa-brands fa-twitter fa-lg" style="color: #54d3f2;"></i></a>
                        <a href="{{ $settings['linkedin_url'] }}" target="_blank">
                            <i class="fa-brands fa-linkedin fa-lg" style="color: #1e77ae;"></i></a>
                        <a href="{{ $settings['pinterest_url'] }}" target="_blank">
                            <i class="fa-brands fa-pinterest fa-lg" style="color: #eb0a0a;"></i></a>
                        <a href="{{ $settings['instagram_url'] }}" target="_blank">
                            <i class="fa-brands fa-instagram fa-lg" style="color: #ff14eb;"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6 d-flex flex-wrap justify-content-sm-between justify-content-end align-items-center">
                    <div class="topbar-theme-toggle d-lg-none" style="margin-bottom: 15px">
                        <div class="theme-switch-box">
                            <span class="theme-status"><i class="fa-solid fa-sun text-white"></i></span>

                            <label class="switch-label">
                                @php
                                $theme = session('theme', 'dark'); // Default to 'dark' if no theme is set
                                @endphp

                                <input type="checkbox" id="themeSwitchMobile" class="themeSwitchCheckbox"
                                    @if($theme=='light' ) checked @endif>
                                <span class="switch"></span>
                            </label>

                            <span class="theme-status"><i class="fas fa-moon text-white"></i></span>
                        </div>
                    </div>
                    @if (getLogInUser())
                    <div class="language-dropdown ms-2 d-none d-sm-block">
                        <a class="nav-link p-0 fs-14 pe-3 d-none" href="javascript:void(0)"
                            id="dropdownMenuButton1">
                            {{ getLogInUser()->last_name }}
                            <i class="fa-solid fa-angle-down icon fs-12"></i>
                        </a>
                        <ul class="nav submenu language-menu" aria-labelledby="dropdownMenuButton1">
                            <li class="nav-item languageSelection">
                                @if (Auth::user()->hasRole('customer'))
                                <a class="nav-link fs-14 d-flex align-items-center" data-turbo="false"
                                    href="{{ route('filament.customer.pages.dashboard') }}">
                                    {{ __('messages.details.admin_panel') }}
                                </a>
                                @endif
                                @if (Auth::user()->hasRole('admin'))
                                <a class="nav-link fs-14 d-flex align-items-center" data-turbo="false"
                                    href="{{ route('filament.admin.pages.dashboard') }}">
                                    {{ __('messages.details.admin_panel') }}
                                </a>
                                @endif
                            </li>
                            <li class="nav-item languageSelection">
                                <form id="logout-form" action="{{ route('logout') }}"
                                    method="POST" style="display: none;">
                                    @csrf
                                </form>

                                <a href="javascript:void(0);" class="nav-link fs-14 d-flex align-items-center"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    {{ __('messages.details.logout') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                    @else
                    <div class="d-flex align-items-center" style="margin-left: 80px;">
                        <a href="{{ route('filament.auth.auth.login') }}"
                            class="fs-14 text-primary fw-6 login-btn d-flex align-items-center"
                            data-turbo="false">
                            <i class="fas fa-sign-in-alt fs-5 me-1 text-white"></i>
                            <span class="d-none d-sm-inline">{{ __('messages.common.login') }}</span>
                        </a>
                    </div>
                    @endif
                    <div class="language-dropdown pe-sm-0 pe-2">
                        <ul class="mb-0 ps-0">
                            <li class="nav-item">
                                <a class="nav-link fs-14 p-0 d-none" href="javascript:void(0)">
                                    {{ getFrontSelectLanguageName() }} <i
                                        class="fa-solid fa-angle-down icon text-whte fs-12"></i></a>
                                <ul class="nav submenu language-menu">
                                    @foreach (getFrontLanguage() as $key => $language)
                                    <li class="nav-item languageSelection" data-prefix-value="ar">
                                        <a href="javascript:void(0)"
                                            class="nav-link fs-14 d-flex align-items-center selectLanguage
                                               @if (getFrontSelectLanguageName() == $language) active @endif"
                                            data-id="{{ $key }}">
                                            {{ $language }}
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>
                            </li>
                        </ul>
                    </div>

                    {{-- Notification Bell (Visible on all screen sizes) - Only for logged-in users --}}
                    @auth
                    <button class="dropdown-toggle border-0 bg-transparent position-relative me-3 d-lg-none text-white"
                        type="button"
                        id="notificationButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-bell fs-5 text-white"></i>
                        @php
                        $unreadCount = \DB::table('notifications')
                        ->where('to_user_id', auth()->id())
                        ->whereNull('read_at')
                        ->count();
                        @endphp
                        @if($unreadCount > 0)
                        <span
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $unreadCount }}
                        </span>
                        @endif
                    </button>

                    {{-- Dropdown content --}}
                    <ul class="dropdown-menu dropdown-menu-end bg-dark shadow" aria-labelledby="notificationButton"
                        style="width: 300px; max-height: 400px; overflow-y: auto;">
                        <!-- Notification Header -->
                        <li class="dropdown-header border-bottom border-secondary d-flex justify-content-between align-items-center px-3 py-2">
                            <span class="text-white fw-bold">Benachrichtigungen</span>
                        </li>
                        
                        @php
                        $notifications = \DB::table('notifications')
                        ->where('to_user_id', auth()->id())
                        ->latest()
                        ->take(10)
                        ->get();
                        @endphp

                        @forelse($notifications as $notification)
                        @php
                        $data = json_decode($notification->data, true);
                        $commentId = $data['comment_id'] ?? null;
                        $postId = $data['post_id'] ?? null;
                        $conversationId = $data['conversation_id'] ?? null;
                        $message = $data['message'] ?? 'You have a notification';
                        
                        // Check if this is an aggregated following activity notification
                        if ($notification->type == 'App\\Notifications\\AggregatedFollowingActivityNotification') {
                            $link = route('members.following');
                            $onclick = "markAsReadAndRedirect('{$notification->id}', '{$link}')";
                        } elseif ($conversationId) {
                            // Private message notification
                            $link = route('messages.show', $conversationId);
                            $onclick = "markAsReadAndRedirect('{$notification->id}', '{$link}')";
                        } elseif (str_contains(strtolower($message), 'folgt') || str_contains(strtolower($message), 'follow')) {
                            // Follow notification - link to user profile
                            $fromUsername = $data['sender_username'] ?? $data['from_username'] ?? $notification->from_user_id;
                            $link = route('user.public.profile', $fromUsername);
                            $onclick = "markAsReadAndRedirect('{$notification->id}', '{$link}')";
                        } elseif ($postId) {
                            $post = \App\Models\Post::find($postId);
                            $slug = $post?->slug ?? '#';
                            // Comment/like notification
                            $link = route('detailPage', $slug) . '#comment-' . $commentId;
                            $onclick = "markAsReadAndRedirect('{$notification->id}', '{$link}')";
                        } else {
                            // Default notification
                            $link = route('notifications.read', $notification->id);
                            $onclick = "markAsReadAndRedirect('{$notification->id}', '{$link}')";
                        }
                        @endphp
                        @php
                        $theme = session('theme', 'dark'); // default to 'light' if not set
                        @endphp
                        <li class="dropdown-item border-bottom {{ is_null($notification->read_at) ? 'bg-light text-dark' : '' }}">
                            <a class="{{$theme=='dark' ? 'text-light' : 'text-dark'}}"
                                href="javascript:void(0);"
                                onclick="{{ $onclick }}"
                                class="text-wrap d-block {{ is_null($notification->read_at) ? 'text-dark' : '' }}">
                                {{ $message }}
                                <small
                                    class="d-block text-muted {{$theme=='dark' ? 'text-light' : 'text-dark'}}">
                                    {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                </small>
                            </a>
                        </li>
                        @empty
                        <li class="dropdown-item text-muted">Keine Benachrichtigungen</li>
                        @endforelse
                        
                        <!-- Sticky Action Buttons -->
                        <li class="sticky-bottom border-top border-secondary" style="position: sticky; bottom: -10px; background-color: #212529; z-index: 10;">
                            <button type="button" 
                                onclick="event.preventDefault(); event.stopPropagation(); markAllAsRead(); return false;" 
                                class="w-100 btn-primary px-3 py-2 mb-2 mt-2"
                                style="border-radius: 0; box-shadow: none !important;">
                                {{ __('messages.other_lang.mark_all_as_read') }}
                            </button>
                            <a href="{{ route('notifications') }}" 
                                class="w-100 d-block btn-primary px-3 py-2 mb-2"
                                style="border-radius: 0; text-decoration: none; text-align: center; box-shadow: none !important;">
                                {{ __('messages.other_lang.go_to_notification_center') }}
                            </a>
                        </li>
                    </ul>
                    @endauth

                    <button class="dropdown border-0 bg-transparent position-relative me-2 d-lg-none" type="button"
                        id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                        <a href="javascript:void(0)"><i class="fa-solid fa-magnifying-glass fs-15 text-white"></i></a>
                    </button>

                    <!-- Search Bar  -->
                    <div class="dropdown-menu mobile-search">
                        <form action="{{ route('allPosts') }}" class="form search-form-box search-input">
                            <div class="form-group border-0 search-input">
                                <input type="text" name="search" id="search" placeholder="Search..."
                                    class="form-control bg-light rt-search-control custom-input-control search-input mb-0"
                                    value="">
                                <button type="submit" class="search-submit custom-submit search-input btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Side Menu -->

                    <div class="offcanvas-toggle d-lg-none d-block">
                        <a href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvasToggle"
                            aria-controls="offcanvasToggle">
                            <i class="fa-solid fa-bars text-white "></i>
                        </a>
                        <div class="offcanvas-wrapper offcanvas-wrapper-start bg-light" tabindex="-1"
                            id="offcanvasToggle" aria-labelledby="offcanvasToggleLabel">
                            <div class="offcanvas-content m-0">
                                <div class="text-end">
                                    <a href="#" data-bs-toggle="offcanvas"
                                        data-bs-target="#offcanvasToggle" aria-controls="offcanvasToggle">
                                        <i class="fa fa-close text-black fs-5 m-2 me-3"></i>
                                    </a>
                                </div>

                                @php
                                $nav = getHeaderElement();
                                @endphp
                                @foreach ($nav['navigations'] as $key => $navigation)
                                @if (
                                $navigation['navigationable']['lang_id'] == getFrontSelectLanguage() ||
                                $navigation->navigationable_type == \App\Models\Menu::class)
                                @php
                                $isSubNav = count($nav['navigationsTakeData'][$navigation->id]) > 0;
                                $subNavLangs = $nav['navigationsTakeData'][$navigation->id];
                                $menuName = $navigation->navigationable->name
                                ? $navigation->navigationable->name
                                : $navigation->navigationable->title;
                                $langId = false;
                                foreach ($subNavLangs as $subNavLang) {
                                if ($langId) {
                                continue;
                                }
                                if (
                                $subNavLang['navigationable_type'] ==
                                \App\Models\SubCategory::class
                                ) {
                                $langId = $subNavLang
                                ->navigationable()
                                ->where('lang_id', getFrontSelectLanguage())
                                ->exists();
                                }
                                }
                                @endphp
                                <div class="set">
                                    @if($key == 0)
                                    <a href="{{ route('allPosts') }}"
                                        class="fs-14 fw-6 sidebar_menu_li">
                                        {!! $navigation->navigationable->name ? $navigation->navigationable->name : $navigation->navigationable->title !!}
                                    </a>
                                    @else
                                    <a href="{{ route('categoryPage', ['category' => $navigation->navigationable->slug]) }}"
                                        class="fs-14 fw-6 sidebar_menu_li">
                                        {!! $navigation->navigationable->name ? $navigation->navigationable->name : $navigation->navigationable->title !!}
                                    </a>
                                    @endif
                                    @if (($langId || $navigation->navigationable_type == \App\Models\Menu::class) && $isSubNav)
                                    <a href="#" class="p-0" data-turbo="false"><i
                                            class="fa fa-plus"></i></a>
                                    @endif
                                    @if ($langId || $navigation->navigationable_type == \App\Models\Menu::class)
                                    @if ($isSubNav)
                                    <div class="content 1">
                                        @foreach ($nav['navigationsTakeData'] as $key => $navSub)
                                        @if ($key == $navigation->id)
                                        @foreach ($navSub as $sub)
                                        <li><a class="fs-14 fw-6 sidebar_menu_li"
                                                @if ($sub->navigationable->link !== null) href="{{ getNavUrl($sub->navigationable->link) }}"
                                                @else
                                                href="{{ route('categoryPage', ['category' => $navigation->navigationable->slug, 'slug' => $sub->navigationable->slug]) }}" @endif>
                                                {!! $sub->navigationable->name ? $sub->navigationable->name : $sub->navigationable->title !!}</a>
                                        </li>
                                        @endforeach
                                        @endif
                                        @endforeach
                                    </div>
                                    @endif
                                    @endif
                                </div>
                                @endif
                                @endforeach
                                <div class="set">
                                    <a href="{{ route('contact.index') }}"
                                        class="fs-14 fw-6 sidebar_menu_li {{ 'Contact' == ucfirst(last(request()->segments())) ? 'active' : '' }}">
                                        {{ __('messages.details.contact_us') }}
                                    </a>
                                </div>
                                <div class="set">
                                    <a href="{{ route('release-calendar.all') }}"
                                        class="fs-14 fw-6 sidebar_menu_li {{ Request::is('releasekalender') || Request::is('releasekalender/*') ? 'active' : '' }}">
                                        Releasekalender
                                    </a>
                                </div>
                                {{-- @if ($nav['pages']->count() > 0)
                                <div class="set">
                                    <a href="javascript:void(0)"
                                        class="fs-14 fw-6 sidebar_menu_li {{ 'Pages' == ucfirst(last(request()->segments())) ? 'active' : '' }}">
                                        {{ __('messages.pages') }}
                                    </a>
                                    <a href="#" class="p-0" data-turbo="false"><i class="fa fa-plus"></i></a>
                                    <div class="content 2">
                                        @foreach ($nav['pages'] as $page)
                                        <li>
                                            <a href="{{ route('pages.show-page-slug', $page->slug) }}"
                                                class="fs-14 fw-6 sidebar_menu_li">
                                                {!! $page->name !!}
                                            </a>
                                        </li>
                                        @endforeach
                                    </div>
                                </div>
                                @endif --}}
                                @if (getLogInUser())
                                <div class="set">
                                    <a href="javascript:void(0)" class="fs-14 fw-6 sidebar_menu_li">
                                        {{ getLogInUser()->last_name }}
                                    </a>
                                    <a href="#" class="p-0" data-turbo="false"><i class=" fa
                                        fa-plus"></i></a>
                                    <div class="content 3">
                                        <li>
                                            {{-- <a href="{{ route('filament.admin.pages.dashboard') }}" class="fs-14 fw-6 sidebar_menu_li"
                                            data-turbo="false">
                                            {{ __('messages.details.admin_panel') }}
                                            </a> --}}

                                            @if (Auth::user()->hasRole('customer'))
                                            <a href="{{ route('filament.customer.pages.dashboard') }}"
                                                class="fs-14 fw-6 sidebar_menu_li" data-turbo="false">
                                                {{ __('messages.details.admin_panel') }}
                                            </a>
                                            @endif
                                            @if (Auth::user()->hasRole('admin'))
                                            <a href="{{ route('filament.admin.pages.dashboard') }}"
                                                class="fs-14 fw-6 sidebar_menu_li" data-turbo="false">
                                                {{ __('messages.details.admin_panel') }}
                                            </a>
                                            @endif
                                        </li>
                                        <li>
                                            <form id="logout-form"
                                                action="{{ route('logout') }}"
                                                method="POST" style="display: none;">
                                                @csrf
                                            </form>

                                            <a href="javascript:void(0);"
                                                class="nav-link fs-14 d-flex align-items-center btn-primary"
                                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                {{ __('messages.details.logout') }}
                                            </a>
                                        </li>
                                    </div>
                                </div>
                                @else
                                <div class="set">
                                    <a href="{{ route('filament.auth.auth.register') }}"
                                        class="fs-14 fw-6 sidebar_menu_li {{ 'Contact' == ucfirst(last(request()->segments())) ? 'active' : '' }}"
                                        data-turbo="false">
                                        {{ __('messages.common.login') }}
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    @if (getLogInUser())
                    <div class="position-relative d-lg-none" id="customerInfo">
                        <button class="dropdown-toggle border-0 bg-transparent position-relative" type="button"
                            id="customerDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <img
                                src="{{ auth()->check() && auth()->user()->profile_image ? auth()->user()->profile_image : asset('web/media/avatars/150-2.jpg') }}"
                                alt="profile image" class="rounded-circle" style="width: 30px;height: 30px;">
                        </button>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <ul class="dropdown-menu dropdown-menu-end shadow text-center"
                            aria-labelledby="customerDropdown">
                            <li class="dropdown-item text-muted p-2 px-4 border-bottom border-light "><a href="{{ route('customer.profile') }}">{{ __('messages.customer_profile.my_profile')}}</a></li>
                            <li class="dropdown-item text-muted p-2 px-4 border-bottom border-light cursor-pointer"><a href="{{ route('customer.profile.edit') }}">{{ __('messages.edit_profile.edit_profile')}}</a></li>
                            <li class="dropdown-item text-muted p-2 px-4 border-bottom border-light cursor-pointer"><a href="{{ route('notifications') }}">{{ __('messages.customer_profile.notifications')}}</a></li>
                            <li class="dropdown-item text-muted p-2 px-4 border-bottom border-light cursor-pointer"><a href="{{ route('customer.profile.comments') }}">{{ __('messages.customer_profile.my_comments')}}</a></li>
                            <li class="dropdown-item text-muted p-2 px-4 border-bottom border-light cursor-pointer"><a href="{{ route('messages.index') }}">{{ __('messages.other_lang.my_messages')}}</a></li>
                            <li class="dropdown-item text-muted p-2 px-4 border-bottom border-light cursor-pointer"><a href="{{ route('members.following') }}">{{ __('messages.members_i_follow')}}</a></li>
                            <li class="dropdown-item text-muted p-2 px-4 border-bottom border-light cursor-pointer"><a href="{{ route('profile.visitors') }}">{{ __('messages.profile.my_profile_visitors')}}</a></li>
                            <li class="dropdown-item text-muted p-2 px-4 cursor-pointer"><a href="javascript:void(0)" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('messages.details.logout')}}</a></li>
                        </ul>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
    </div>
</section>


@if (checkAdSpaced('header'))
@if (isset(getAdImageDesktop(\App\Models\AdSpaces::HEADER)->code))
<div class=" container index-top-desktop ad-space-url-desktop-header">
    {!! getAdImageDesktop(\App\Models\AdSpaces::HEADER)->code !!}
</div>
@elseif ($adsDesktop = getAdImageDesktop(\App\Models\AdSpaces::HEADER))
<div class="container index-top-desktop">
    <a href="{{ $adsDesktop->ad_url }}" target="_blank">
        <img src="{{ asset($adsDesktop->ad_banner) }}" width="1300" class="img-fluid">
    </a>
</div>
@endif
@endif


{{-- <div class="container py-2 heder-ad"> --}}
{{-- <img src="{{asset('images/1300.png')}}" width="1300" height="130" class="img-fluid"> --}}
{{-- </div> --}}
<!--end top-bar-section -->

<!-- start header section -->


<!-- Desktop Header  -->
<header class="bg-light heade p-4 d-none d-lg-block header-gradient">
    <div class="d-flex justify-content-between">
        <div>
            <a href="{{ env("APP_URL") }}">
                <img src="{{asset('uploads/logo/504/01JTHQA9R70QTFSFVR2SA469E3-old.png') }}" alt="logo" style="width: 250px;" />
            </a>
        </div>
        <div class="d-flex align-items-center gap-4">
            <div>
                <!--- sstart dark-mode-sectionn --->
                <div class="topbar-theme-toggle">
                    <div class="theme-switch-box">
                        <span class="theme-status"><i class="fa-solid fa-sun text-white"></i></span>

                        <label class="switch-label">
                            <input type="checkbox" id="themeSwitchMobile" class="themeSwitchCheckbox"
                                @if($theme=='light' ) checked @endif>
                            <span class="switch"></span>
                        </label>

                        <span class="theme-status"><i class="fas fa-moon text-white"></i></span>
                    </div>
                </div>
                <!--- eend dark-mode-sections --->
            </div>
            @if(!getLogInUser())
            <div class="d-flex align-items-center">
                <a href="{{ route('filament.auth.auth.login') }}"
                    class="fs-14 text-primary fw-6 login-btn d-flex align-items-center"
                    data-turbo="false">
                    <i class="fas fa-sign-in-alt fs-5 me-1 text-white"></i>
                    <span class="d-none d-sm-inline text-white">{{ __('messages.common.login') }}</span>
                </a>
            </div>
            @endif
        </div>
    </div>

    <div class="d-flex align-items-center justify-content-between dropdown header-icon mt-3">
        <div>
            <nav>
                <ul class="nav">
                    @php
                    $nav = getNavigationDetails();
                    @endphp
                    @if ($nav['navigationsCount'] >= 0)
                    @foreach ($nav['navigations'] as $key => $navigation)
                    @if (
                    $navigation['navigationable']['lang_id'] == getFrontSelectLanguage() ||
                    $navigation->navigationable_type == \App\Models\Menu::class)
                    @php
                    $isSubNav = count($nav['navigationsTakeData'][$navigation->id]) > 0;
                    $subNavLangs = $nav['navigationsTakeData'][$navigation->id];
                    $menuName = $navigation->navigationable->name
                    ? $navigation->navigationable->name
                    : $navigation->navigationable->title;
                    $langId = false;
                    foreach ($subNavLangs as $subNavLang) {
                    if ($langId) {
                    continue;
                    }
                    if ($subNavLang['navigationable_type'] == \App\Models\SubCategory::class) {
                    $langId = $subNavLang
                    ->navigationable()
                    ->where('lang_id', getFrontSelectLanguage())
                    ->exists();
                    }
                    }
                    @endphp
                    <li class="nav-item dropdown">
                        <a class="nav-link  fs-14 fw-6 {{ $menuName == ucwords(str_replace('-', ' ', last(request()->segments()))) ? 'active' : '' }}"
                            aria-current="page"
                            @if ($navigation->navigationable->link !== null) href="{{ getNavUrl($navigation->navigationable->link) }}"
                            @else
                            href="{{ route('categoryPage', $navigation->navigationable->slug) }}" @endif>{!! $navigation->navigationable->name ? $navigation->navigationable->name : $navigation->navigationable->title !!}
                            @if (($langId || $navigation->navigationable_type == \App\Models\Menu::class) && $isSubNav)
                            <i class="fa-solid fa-angle-down icon ms-1 fs-12"></i>
                            @endif
                        </a>
                        @if ($langId || $navigation->navigationable_type == \App\Models\Menu::class)
                        @if ($isSubNav)
                        <ul class="dropdown-nav ps-0 text-center">
                            @php
                            $path = basename(Request::path());
                            @endphp
                            @foreach ($nav['navigationsTakeData'] as $key => $navSub)
                            @if ($key == $navigation->id)
                            @foreach ($navSub as $sub)
                            @if ($sub->navigationable_type == \App\Models\SubCategory::class)
                            @if ($sub->navigationable()->where('lang_id', getFrontSelectLanguage())->exists())
                            <li>
                                <a class="fs-14 fw-6 sidebar_menu_li {{ !empty($path) && $path == $sub->navigationable->slug ? 'active' : '' }}"
                                    @if ($sub->navigationable->link !== null) href="{{ getNavUrl($sub->navigationable->link) }}"
                                    @else
                                    href="{{ route('categoryPage', ['category' => $navigation->navigationable->slug, 'slug' => $sub->navigationable->slug]) }}" @endif>{!! $sub->navigationable->name ? $sub->navigationable->name : $sub->navigationable->title !!}
                                </a>
                            </li>
                            @endif
                            @else
                            <li>
                                <a class="fs-14 fw-6 sidebar_menu_li {{ !empty($path) && $path == $sub->navigationable->slug ? 'active' : '' }}"
                                    @if ($sub->navigationable->link !== null) href="{{ getNavUrl($sub->navigationable->link) }}"
                                    @else
                                    href="{{ route('categoryPage', ['category' => $navigation->navigationable->slug, 'slug' => $sub->navigationable->slug]) }}" @endif>{!! $sub->navigationable->name ? $sub->navigationable->name : $sub->navigationable->title !!}
                                </a>
                            </li>
                            @endif
                            @endforeach
                            
                            {{-- Add Release List items for PlayStation, Xbox, Nintendo --}}
                            @php
                                $navName = strtolower(trim($menuName));
                                $releaseListType = null;
                                $releaseRoute = null;
                                $isActive = false;
                                
                                if (str_contains($navName, 'playstation') || str_contains($navName, 'play station')) {
                                    $releaseListType = \App\Models\ReleaseListSetting::LIST_TYPE_PLAYSTATION;
                                    $releaseRoute = route('release-calendar.playstation');
                                    $isActive = Request::is('playstation/release-liste') || Request::is('playstation/release-liste/*');
                                } elseif (str_contains($navName, 'xbox')) {
                                    $releaseListType = \App\Models\ReleaseListSetting::LIST_TYPE_XBOX;
                                    $releaseRoute = route('release-calendar.xbox');
                                    $isActive = Request::is('xbox/release-liste') || Request::is('xbox/release-liste/*');
                                } elseif (str_contains($navName, 'nintendo')) {
                                    $releaseListType = \App\Models\ReleaseListSetting::LIST_TYPE_NINTENDO;
                                    $releaseRoute = route('release-calendar.nintendo');
                                    $isActive = Request::is('nintendo/release-liste') || Request::is('nintendo/release-liste/*');
                                }
                                
                                if ($releaseListType) {
                                    $releaseList = \App\Models\ReleaseListSetting::where('list_type', $releaseListType)->first();
                                    if ($releaseList) {
                            @endphp
                                        <li>
                                            <a class="fs-14 fw-6 sidebar_menu_li {{ $isActive ? 'active' : '' }}"
                                                href="{{ $releaseRoute }}">
                                                {{ $releaseList->headline ?? 'Release-Liste' }}
                                            </a>
                                        </li>
                            @php
                                    }
                                }
                            @endphp
                            @endif
                            @endforeach
                        </ul>
                        @endif
                        @endif
                    </li>
                    @endif
                    @endforeach
                    @endif

                    @if ($nav['navigationsCount'] >= 6)
                    <li class="nav-item dropdown">
                        <a class="nav-link" aria-current="page" href="#">
                            <i class="fa-solid fa-ellipsis "></i>
                        </a>
                        <ul class="dropdown-nav ps-0 text-center">
                            @foreach ($nav['navigationsSkipData'] as $key => $navigation)
                            @if (
                            $navigation['navigationable']['lang_id'] == getFrontSelectLanguage() ||
                            $navigation->navigationable_type == \App\Models\Menu::class)
                            @php
                            $isSubNav = count($nav['navigationsSkipItem'][$navigation->id]) > 0;
                            $subNavLangs = $nav['navigationsSkipItem'][$navigation->id];
                            $menuName = $navigation->navigationable->name
                            ? $navigation->navigationable->name
                            : $navigation->navigationable->title;
                            $langId = false;
                            foreach ($subNavLangs as $subNavLang) {
                            if ($langId) {
                            continue;
                            }
                            if (
                            $subNavLang['navigationable_type'] ==
                            \App\Models\SubCategory::class
                            ) {
                            $langId = $subNavLang
                            ->navigationable()
                            ->where('lang_id', getFrontSelectLanguage())
                            ->exists();
                            }
                            }
                            @endphp
                            <li class="dropdown-sub-nav">
                                <a href="{{ $navigation->navigationable_type == \App\Models\Menu::class ? $navigation->navigationable->link : route('categoryPage', $navigation->navigationable->slug) }}"
                                    class="fs-14 fw-6 sidebar_menu_li {{ $menuName == ucfirst(last(request()->segments())) ? 'active' : '' }}">
                                    {!! $navigation->navigationable->name ? $navigation->navigationable->name : $navigation->navigationable->title !!}
                                    @if (($langId || $navigation->navigationable_type == \App\Models\Menu::class) && $isSubNav)
                                    <i class="fa-solid fa-angle-right fs-12 "></i>
                                    @endif
                                </a>
                                @if ($langId || $navigation->navigationable_type == \App\Models\Menu::class)
                                @if ($isSubNav)
                                <ul class="dropdown-sub-list ps-0">
                                    @foreach ($nav['navigationsSkipItem'] as $key => $navSub)
                                    @if ($key == $navigation->id)
                                    @foreach ($navSub as $sub)
                                    @if ($sub->navigationable_type == \App\Models\SubCategory::class)
                                    @if ($sub->navigationable()->where('lang_id', getFrontSelectLanguage())->exists())
                                    <li>
                                        <a class="fs-14 fw-6 sidebar_menu_li"
                                            @if ($sub->navigationable->link !== null) href="{{ getNavUrl($sub->navigationable->link) }}"
                                            @else
                                            href="{{ route('categoryPage', ['category' => $navigation->navigationable->slug, 'slug' => $sub->navigationable->slug]) }}" @endif>{!! $sub->navigationable->name ? $sub->navigationable->name : $sub->navigationable->title !!}
                                        </a>
                                    </li>
                                    @endif
                                    @else
                                    <li>
                                        <a class="fs-14 fw-6 sidebar_menu_li"
                                            @if ($sub->navigationable->link !== null) href="{{ getNavUrl($sub->navigationable->link) }}"
                                            @else
                                            href="{{ route('categoryPage', ['category' => $navigation->navigationable->slug, 'slug' => $sub->navigationable->slug]) }}" @endif>{!! $sub->navigationable->name ? $sub->navigationable->name : $sub->navigationable->title !!}
                                        </a>
                                    </li>
                                    @endif
                                    @endforeach
                                    
                                    {{-- Add Release List items for PlayStation, Xbox, Nintendo --}}
                                    @php
                                        $navNameSkip = strtolower(trim($menuName));
                                        $releaseListTypeSkip = null;
                                        $releaseRouteSkip = null;
                                        $isActiveSkip = false;
                                        
                                        if (str_contains($navNameSkip, 'playstation') || str_contains($navNameSkip, 'play station')) {
                                            $releaseListTypeSkip = \App\Models\ReleaseListSetting::LIST_TYPE_PLAYSTATION;
                                            $releaseRouteSkip = route('release-calendar.playstation');
                                            $isActiveSkip = Request::is('playstation/release-liste') || Request::is('playstation/release-liste/*');
                                        } elseif (str_contains($navNameSkip, 'xbox')) {
                                            $releaseListTypeSkip = \App\Models\ReleaseListSetting::LIST_TYPE_XBOX;
                                            $releaseRouteSkip = route('release-calendar.xbox');
                                            $isActiveSkip = Request::is('xbox/release-liste') || Request::is('xbox/release-liste/*');
                                        } elseif (str_contains($navNameSkip, 'nintendo')) {
                                            $releaseListTypeSkip = \App\Models\ReleaseListSetting::LIST_TYPE_NINTENDO;
                                            $releaseRouteSkip = route('release-calendar.nintendo');
                                            $isActiveSkip = Request::is('nintendo/release-liste') || Request::is('nintendo/release-liste/*');
                                        }
                                        
                                        if ($releaseListTypeSkip) {
                                            $releaseListSkip = \App\Models\ReleaseListSetting::where('list_type', $releaseListTypeSkip)->first();
                                            if ($releaseListSkip) {
                                    @endphp
                                            <li>
                                                <a class="fs-14 fw-6 sidebar_menu_li {{ $isActiveSkip ? 'active' : '' }}"
                                                    href="{{ $releaseRouteSkip }}">
                                                    {{ $releaseListSkip->headline ?? 'Release-Liste' }}
                                                </a>
                                            </li>
                                    @php
                                            }
                                        }
                                    @endphp
                                    @endif
                                    @endforeach
                                </ul>
                                @endif
                                @endif
                            </li>
                            @endif
                            @endforeach
                            <li class="">
                                <a class="fs-14 fw-6 sidebar_menu_li {{ Request::is('releasekalender') || Request::is('releasekalender/*') ? 'active' : '' }}"
                                    href="{{ route('release-calendar.all') }}">Releasekalender</a>
                            </li>
                            <li class="">
                                <a class="fs-14 fw-6 sidebar_menu_li  {{ 'Contact' == ucfirst(last(request()->segments())) ? 'active' : '' }}"
                                    href="{{ route('contact.index') }}">{{ __('messages.details.contact_us') }}</a>
                            </li>
                            {{-- <li class="dropdown-sub-nav">
                                <a href="#"
                                    class="fs-14 fw-6 sidebar_menu_li {{ 'Page' == ucfirst(last(request()->segments())) ? 'active' : '' }}">{{ __('messages.pages') }}
                                    <i class="fa-solid fa-angle-right fs-12 "></i>
                                </a>
                                <ul class="dropdown-sub-list ps-0">
                                    @if ($nav['pages']->count() > 0)
                                        @foreach ($nav['pages'] as $page)
                                        <li>
                                            <a class="fs-14 fw-6 sidebar_menu_li"
                                                href="{{ route('pages.show-page-slug', $page->slug) }}">
                                                {!! $page->name !!}</a>
                                        </li>
                                        @endforeach
                                    @endif
                                </ul>
                            </li> --}}
                        </ul>
                    </li>
                    @endif
                    @if ($nav['navigationsCount'] <= 5)
                        <li class="nav-item">
                            <a class="nav-link fs-14 fw-6 {{ Request::is('releasekalender') || Request::is('releasekalender/*') ? 'active' : '' }}"
                                href="{{ route('release-calendar.all') }}">Releasekalender</a>
                        </li>
                        <li class="nav-item">
                        <a class="nav-link fs-14 fw-6 {{ 'Contact' == ucfirst(last(request()->segments())) ? 'active' : '' }}"
                            href="{{ route('contact.index') }}">{{ __('messages.details.contact_us') }}</a>
                        </li>
                        {{-- @if ($nav['pages']->count() > 0)
                        <li class="nav-item dropdown">
                            <a class="nav-link fs-14 fw-6 {{ 'Pages' == ucfirst(last(request()->segments())) ? 'active' : '' }}"
                                href="javascript:void(0)">{{ __('messages.pages') }}
                                <i class="fa-solid fa-angle-down icon ms-1 fs-12"></i>
                            </a>
                            <ul class="dropdown-nav ps-0">
                                @foreach ($nav['pages'] as $page)
                                <li>
                                    <a class="fs-14 fw-6 sidebar_menu_li"
                                        href="{{ route('pages.show-page-slug', $page->slug) }}">
                                        {!! $page->name !!}</a>
                                </li>
                                @endforeach
                            </ul>
                        </li>
                        @endif --}}
                        @endif
                </ul>

            </nav>
        </div>

        <!-- <div class="position-relative">
            <button class="dropdown-toggle border-0 bg-transparent position-relative me-4" type="button"
                id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                <a href="javascript:void(0)"><i class="fa-solid fa-magnifying-glass text-white fs-20 "></i></a>
            </button>

            <div class="dropdown-menu p-0 " style="background-color: transparent !important; box-shadow: none; ">
                <form action="{{ route('allPosts') }}" class="form search-form-box search-input m-0">
                    <div class="form-group border-0 search-input" style="background: linear-gradient(180deg, #720072, #000);border-radius: 20px;">
                        <input type="text" name="search" id="search"
                            placeholder="{{ __('messages.search') }}"
                            class="form-control bg-light rt-search-control custom-input-control search-input mb-0"
                            value=""
                            style="background-color: transparent !important ; color : white !important;">
                        <button type="submit" class="search-submit custom-submit search-input btn-primary" style="border-radius: 16px !important;">
                            <i class="fas fa-search text-white"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div> -->
        <form action="{{ route('allPosts') }}" class="form search-form-box m-0">
            <div class="d-flex align-items-center gap-2">
                <!-- Search Input -->


                <div class=" px-2 py-1 search-input">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#746E7B">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input name="search" id="search" type="text" class="form-control bg-transparent border-0 text-light m-0 p-1" placeholder="{{ __('messages.search') }}" value="{{ request('search') }}" style="font-size: 12px;">
                    <button type="submit" class="search-submit custom-submit search-input btn-primary"
                        style="border-radius: 100px !important;box-shadow: 0px 0px 50px 0px #ffffff20 ; padding: 8px;">
                        <!-- <i class="fas fa-search text-white fs-6"></i> -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="#fff">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </button>
                </div>

                <!-- Filter Icon with Custom Toggle -->
                <div class="dropdown">
                    <div id="filterToggle" class="position-relative ms-2 filter-icon" style="cursor: pointer;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#746E7B" style="width: 25px;">
                            <path fill-rule="evenodd" d="M3.792 2.938A49.069 49.069 0 0 1 12 2.25c2.797 0 5.54.236 8.209.688a1.857 1.857 0 0 1 1.541 1.836v1.044a3 3 0 0 1-.879 2.121l-6.182 6.182a1.5 1.5 0 0 0-.439 1.061v2.927a3 3 0 0 1-1.658 2.684l-1.757.878A.75.75 0 0 1 9.75 21v-5.818a1.5 1.5 0 0 0-.44-1.06L3.13 7.938a3 3 0 0 1-.879-2.121V4.774c0-.897.64-1.683 1.542-1.836Z" clip-rule="evenodd" />
                        </svg>
                        <span class="position-absolute badge">0</span>
                    </div>

                    <!-- Filter Panel -->
                    <div id="filterDropdown" class="dropdown-menu filter-dropdown mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6>{{ __('messages.other_lang.filter') }}</h6>
                            <span class="reset-link">{{ __('messages.other_lang.reset_filter') }}</span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('messages.other_lang.select_post_time') }}</label>
                            <select name="time" class="form-select">
                                <option value="">{{ __('messages.other_lang.all_time') }}</option>
                                <option value="today">{{ __('messages.other_lang.today_time') }}</option>
                                <option value="week">{{ __('messages.other_lang.weak_time') }}</option>
                                <option value="month">{{ __('messages.other_lang.month_time') }}</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('messages.other_lang.select_category') }}</label>
                            <select id="categoryFilter" name="category" class="form-select">
                                <option value="">{{ __('messages.other_lang.all_time') }}</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('messages.other_lang.select_sub_category') }}</label>
                            <select id="subcategoryFilter" name="subcategory" class="form-select">
                                <option value="">{{ __('messages.other_lang.all_time') }}</option>
                                @foreach($subcategories as $sub)
                                <option value="{{ $sub->name }}" data-parent="{{ $sub->category->name }}">
                                    {{ $sub->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">{{ __('messages.other_lang.select_editor') }}</label>
                            <select name="editor" class="form-select">
                                <option value="">{{ __('messages.other_lang.all_time') }}</option>
                                @foreach($editors as $ed)
                                <option value="{{ $ed['id'] }}">{{ $ed['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button class="mt-3 cursor-pointer flex items-center justify-center me-3 px-4 py-2 rounded-md text-sm font-medium btn-primary w-100">
                            {{ __('messages.other_lang.apply_button') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
        @auth
        <div>
            <button class="dropdown-toggle border-0 bg-transparent position-relative me-4" type="button"
                id="notificationButton" data-bs-toggle="dropdown" aria-expanded="false" onclick="hideBadge()">
                <i class="fa fa-bell fs-20 text-white"></i>
                @php
                $unreadCount = \DB::table('notifications')
                ->where('to_user_id', auth()->id())
                ->whereNull('read_at')
                ->count();
                @endphp
                @if($unreadCount > 0)
                <span id="notificationBadge"
                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notificationBadge">
                    {{ $unreadCount }}
                </span>
                @endif
            </button>

            {{-- Notification Dropdown --}}
            <ul class="dropdown-menu dropdown-menu-end bg-dark" aria-labelledby="notificationButton"
                style="width: 300px; max-height: 400px; overflow-y: auto;">
                <!-- Notification Header -->
                <li class="dropdown-header border-bottom border-secondary d-flex justify-content-between align-items-center px-3 py-2">
                    <span class="text-white fw-bold">Benachrichtigungen</span>
                </li>
                
                @php
                $notifications = \DB::table('notifications')
                ->where('to_user_id', auth()->id())
                ->latest()
                ->take(10)
                ->get();
                @endphp

                @forelse($notifications as $notification)
                @php
                $data = json_decode($notification->data, true);
                $commentId = $data['comment_id'] ?? null;
                $postId = $data['post_id'] ?? null;
                $conversationId = $data['conversation_id'] ?? null;
                $message = $data['message'] ?? 'You have a notification';

                // Check if this is an aggregated following activity notification
                if ($notification->type == 'App\\Notifications\\AggregatedFollowingActivityNotification') {
                    $link = route('members.following');
                    $onclick = "markAsReadAndRedirect('{$notification->id}', '{$link}')";
                } elseif ($conversationId) {
                    // Private message notification
                    $link = route('messages.show', $conversationId);
                    $onclick = "markAsReadAndRedirect('{$notification->id}', '{$link}')";
                } elseif (str_contains(strtolower($message), 'folgt') || str_contains(strtolower($message), 'follow')) {
                    // Follow notification - link to user profile
                    $fromUsername = $data['sender_username'] ?? $data['from_username'] ?? $notification->from_user_id;
                    $link = route('user.public.profile', $fromUsername);
                    $onclick = "markAsReadAndRedirect('{$notification->id}', '{$link}')";
                } elseif ($postId) {
                    $post = \App\Models\Post::find($postId);
                    $slug = $post?->slug ?? '#';
                    // Comment/like notification
                    $link = route('detailPage', $slug) . '#comment-' . $commentId;
                    $onclick = "markAsReadAndRedirect('{$notification->id}', '{$link}')";
                } else {
                    // Default notification
                    $link = route('notifications.read', $notification->id);
                    $onclick = "markAsReadAndRedirect('{$notification->id}', '{$link}')";
                }
                @endphp

                <li class="dropdown-item border-bottom {{ is_null($notification->read_at) ? 'unread-notification' : '' }}">
                    <a href="javascript:void(0);"
                        onclick="{{ $onclick }}"
                        class="text-wrap d-block {{ is_null($notification->read_at) ? 'text-dark' : '' }}">
                        {{ $message }}
                        <small class="d-block text-muted ">
                            {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                        </small>
                    </a>
                </li>
                @empty
                <li class="dropdown-item text-muted">Keine Benachrichtigungen</li>
                @endforelse
                
                <!-- Sticky Action Buttons -->
                <li class="sticky-bottom border-top border-secondary" style="position: sticky; bottom: -10px; background-color: #212529; z-index: 10;">
                    <button type="button" 
                        onclick="event.preventDefault(); event.stopPropagation(); markAllAsRead(); return false;" 
                        class="w-100 btn-primary px-3 py-2 mb-2 mt-2"
                        style="border-radius: 0; box-shadow: none !important;">
                        {{ __('messages.other_lang.mark_all_as_read') }}
                    </button>
                    <a href="{{ route('notifications') }}" 
                        class="w-100 d-block btn-primary px-3 py-2 mb-2"
                        style="border-radius: 0; text-decoration: none; text-align: center; box-shadow: none !important;">
                        {{ __('messages.other_lang.go_to_notification_center') }}
                    </a>
                </li>
            </ul>
        </div>



        @if (getLogInUser())
        <!-- Customer Info -->
        <div class="position-relative d-none d-lg-block" id="customerInfo">
            <button class="dropdown-toggle border-0 bg-transparent position-relative me-4" type="button"
                id="customerDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <img
                    src="{{ auth()->check() && auth()->user()->profile_image ? auth()->user()->profile_image : asset('web/media/avatars/150-2.jpg') }}"
                    alt="profile image" class="rounded-circle" style="width: 35px;height: 35px;">
            </button>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            <ul class="dropdown-menu dropdown-menu-end shadow text-center"
                aria-labelledby="customerDropdown">
                <li class="dropdown-item text-muted p-2 px-4 border-bottom border-light cursor-pointer "> <a href="{{ route('customer.profile') }}"> {{ __('messages.customer_profile.my_profile')}} </a></li>
                <li class="dropdown-item text-muted p-2 px-4 border-bottom border-light cursor-pointer"> <a href="{{ route('customer.profile.edit') }}"> {{ __('messages.edit_profile.edit_profile')}} </a></li>
                <li class="dropdown-item text-muted p-2 px-4 border-bottom border-light cursor-pointer"><a href="{{ route('notifications') }}"> {{ __('messages.customer_profile.notifications')}} </a></li>
                <li class="dropdown-item text-muted p-2 px-4 border-bottom border-light cursor-pointer"><a href="{{ route('customer.profile.comments') }}"> {{ __('messages.customer_profile.my_comments')}} </a></li>
                <li class="dropdown-item text-muted p-2 px-4 border-bottom border-light cursor-pointer"><a href="{{ route('messages.index') }}">{{ __('messages.other_lang.my_messages')}}</a></li>
                <li class="dropdown-item text-muted p-2 px-4 border-bottom border-light cursor-pointer"><a href="{{ route('members.following') }}">{{ __('messages.members_i_follow')}}</a></li>
                <li class="dropdown-item text-muted p-2 px-4 border-bottom border-light cursor-pointer"><a href="{{ route('profile.visitors') }}">{{ __('messages.profile.my_profile_visitors')}}</a></li>
                <li class="dropdown-item text-muted p-2 px-4 border-bottom border-light cursor-pointer"><a href="javascript:void(0)" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('messages.details.logout')}}</a></li>
            </ul>
        </div>
        @endauth
        @endif

    </div>


    <div class="row align-items-center justify-content-between">
        <div class="col-lg-11 col-12">


        </div>
        <div class="col-lg-1">
            <div
                class="dropdown header-icon d-lg-flex  justify-content-end align-items-center d-none position-relative">
                {{-- Notification Bell --}}
            </div>

        </div>
    </div>
</header>


<!-- Mobile header -->
<header class="bg-light p-2 pt-3 d-lg-none header-gradient">

    <div class="d-flex align-items-center justify-content-between header-icon position-relative">
        <!-- Dark / Light mode toggle  -->
        <div>
            <div class="topbar-theme-toggle ms-0 p-0">
                <div class="theme-switch-box">
                    <span class="theme-status"><i class="fa-solid fa-sun text-white"></i></span>

                    <label class="switch-label">
                        <input type="checkbox" id="themeSwitchMobile" class="themeSwitchCheckbox"
                            @if($theme=='light' ) checked @endif>
                        <span class="switch"></span>
                    </label>

                    <span class="theme-status"><i class="fas fa-moon text-white"></i></span>
                </div>
            </div>
        </div>

     

        <!-- Notification & user -->
        @auth
        <div>
            <!-- Notification -->
            <div class="d-flex">
                <button class="dropdown-toggle border-0 bg-transparent position-relative"
                    type="button"
                    id="notificationButton"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                    onclick="hideBadge()">
                    <i class="fa fa-bell fs-20 text-white"></i>
                    @php
                    $unreadCount = \DB::table('notifications')
                    ->where('to_user_id', auth()->id())
                    ->whereNull('read_at')
                    ->count();
                    @endphp
                    @if($unreadCount > 0)
                    <span id="notificationBadge"
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notificationBadge">
                        {{ $unreadCount }}
                    </span>
                    @endif
                </button>

                <!-- Notification Dropdown -->
                <ul class="dropdown-menu dropdown-menu-end bg-dark" aria-labelledby="notificationButton"
                    style="width: 300px; max-height: 400px; overflow-y: auto;">
                    <!-- Notification Header -->
                    <li class="dropdown-header bg-dark border-bottom border-secondary d-flex justify-content-between align-items-center px-3 py-2">
                        <span class="text-white fw-bold">Benachrichtigungen</span>
                    </li>
                    
                    @php
                    $notifications = \DB::table('notifications')
                    ->where('to_user_id', auth()->id())
                    ->latest()
                    ->take(10)
                    ->get();
                    @endphp

                    @forelse($notifications as $notification)
                    @php
                    $data = json_decode($notification->data, true);
                    $commentId = $data['comment_id'] ?? null;
                    $postId = $data['post_id'] ?? null;
                    $conversationId = $data['conversation_id'] ?? null;
                    $message = $data['message'] ?? 'You have a notification';
                    
                    // Check if this is an aggregated following activity notification
                    if ($notification->type == 'App\\Notifications\\AggregatedFollowingActivityNotification') {
                        $link = route('members.following');
                        $onclick = "markAsReadAndRedirect('{$notification->id}', '{$link}')";
                    } elseif ($conversationId) {
                        // Private message notification
                        $link = route('messages.show', $conversationId);
                        $onclick = "markAsReadAndRedirect('{$notification->id}', '{$link}')";
                    } elseif (str_contains(strtolower($message), 'folgt') || str_contains(strtolower($message), 'follow')) {
                        // Follow notification - link to user profile
                        $fromUsername = $data['sender_username'] ?? $data['from_username'] ?? $notification->from_user_id;
                        $link = route('user.public.profile', $fromUsername);
                        $onclick = "markAsReadAndRedirect('{$notification->id}', '{$link}')";
                    } elseif ($postId) {
                        $post = \App\Models\Post::find($postId);
                        $slug = $post?->slug ?? '#';
                        // Comment/like notification
                        $link = route('detailPage', $slug) . '#comment-' . $commentId;
                        $onclick = "markAsReadAndRedirect('{$notification->id}', '{$link}')";
                    } else {
                        // Default notification
                        $link = route('notifications.read', $notification->id);
                        $onclick = "markAsReadAndRedirect('{$notification->id}', '{$link}')";
                    }
                    @endphp

                    <li class="dropdown-item border-bottom {{ is_null($notification->read_at) ? 'unread-notification text-dark ' : '' }}">
                        <a href="javascript:void(0);"
                            onclick="{{ $onclick }}"
                            class="text-wrap d-block {{ is_null($notification->read_at) ? 'text-dark' : '' }}">
                            {{ $message }}
                            <small class="d-block text-muted">
                                {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                            </small>
                        </a>
                    </li>
                    @empty
                    <li class="dropdown-item text-muted">Keine Benachrichtigungen</li>
                    @endforelse
                    
                    <!-- Sticky Action Buttons -->
                    <li class="sticky-bottom border-top border-secondary" style="position: sticky; bottom: -10px; background-color: #212529; z-index: 10;">
                        <button type="button" 
                            onclick="event.preventDefault(); event.stopPropagation(); markAllAsRead(); return false;" 
                            class="w-100 btn-primary px-3 py-2 mb-2 mt-2"
                            style="border-radius: 0; box-shadow: none !important;">
                            {{ __('messages.other_lang.mark_all_as_read') }}
                        </button>
                        <a href="{{ route('notifications') }}" 
                            class="w-100 d-block btn-primary px-3 py-2 mb-2"
                            style="border-radius: 0; text-decoration: none; text-align: center; box-shadow: none !important;">
                            {{ __('messages.other_lang.go_to_notification_center') }}
                        </a>
                    </li>
                </ul>

                @if (getLogInUser())
                <!-- Customer Info -->
                <div class="position-relative d-block d-lg-none" id="customerInfo">
                    <button class="dropdown-toggle border-0 bg-transparent position-relative" type="button"
                        id="customerDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img
                            src="{{ auth()->check() && auth()->user()->profile_image ? auth()->user()->profile_image : asset('web/media/avatars/150-2.jpg') }}"
                            alt="profile image" class="rounded-circle" style="width: 30px;height: 30px;">
                    </button>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <ul class="dropdown-menu dropdown-menu-end shadow text-center"
                        aria-labelledby="customerDropdown">
                        <li class="dropdown-item text-muted p-2 px-4 border-bottom border-light "> <a href="{{ route('customer.profile') }}"> {{ __('messages.customer_profile.my_profile')}} </a></li>
                        <li class="dropdown-item text-muted p-2 px-4 border-bottom border-light "> <a href="{{ route('customer.profile.edit') }}"> {{ __('messages.edit_profile.edit_profile')}} </a></li>
                        <li class="dropdown-item text-muted p-2 px-4 border-bottom border-light "> <a href="{{ route('notifications') }}"> {{ __('messages.customer_profile.notifications')}} </a></li>
                        <li class="dropdown-item text-muted p-2 px-4 border-bottom border-light "> <a href="{{ route('customer.profile.comments') }}"> {{ __('messages.customer_profile.my_comments')}}</a></li>
                        <li class="dropdown-item text-muted p-2 px-4 border-bottom border-light cursor-pointer"><a href="{{ route('messages.index') }}">{{ __('messages.other_lang.my_messages')}}</a></li>
                        <li class="dropdown-item text-muted p-2 px-4 border-bottom border-light cursor-pointer"><a href="{{ route('members.following') }}">{{ __('messages.members_i_follow')}}</a></li>
                        <li class="dropdown-item text-muted p-2 px-4 border-bottom border-light cursor-pointer"><a href="{{ route('profile.visitors') }}">{{ __('messages.profile.my_profile_visitors')}}</a></li>
                        <li class="dropdown-item text-muted p-2 px-4 "><a href="javascript:void(0)" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-muted p-2 px-4 cursor-pointer">{{ __('messages.details.logout')}}</a></li>
                    </ul>
                </div>
                @endif
            </div>
        </div>
        @endauth

    </div>

    <!-- Logo -->
    <div class="text-center">
        <a href="/">
            <img src="{{asset('uploads/logo/504/01JTHQA9R70QTFSFVR2SA469E3-old.png') }}" alt="logo" class="mx-auto my-3" style="width: 53%;" />
        </a>
    </div>

    <div class="d-flex align-items-center justify-content-between dropdown header-icon position-relative">
        <!-- Search -->
        <!-- <div class="position-relative">
            <button class="dropdown-toggle border-0 bg-transparent position-relative me-4" type="button"
                id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                <a href="javascript:void(0)"><i class="fa-solid fa-magnifying-glass text-white fs-20 "></i></a>
            </button>

            <div class="dropdown-menu left-0" id="left0">
            </div>
        </div> -->
           <div>
            @if(!getLogInUser())
            <div class="d-flex">
                <a href="{{ route('filament.auth.auth.login') }}"
                    class="fs-14 text-primary fw-6 login-btn d-flex align-items-center"
                    data-turbo="false">
                    <i class="fas fa-sign-in-alt fs-5 me-1 text-white"></i>
                    <span class="d-none d-sm-inline">{{ __('messages.common.login') }}</span>
                </a>
            </div>
            @endif
        </div>


        <div>
            <!-- Hamburger Toggle Button -->
            <a href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExampleSmall"
                aria-controls="offcanvasExampleSmall">
                <i class="fa-solid fa-bars fs-20 text-white"></i>
            </a>

            <!-- Offcanvas Menu (from right) -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExampleSmall"
                aria-labelledby="offcanvasExampleLabel">

                <!-- Close Button -->
                <button type="button" class="btn-close text-reset position-absolute top-0 end-0 m-3 opacity-100"
                    data-bs-dismiss="offcanvas" aria-label="Close"></button>

                <div class="offcanvas-body pt-5">
                    <!-- Logo -->
                    <div class="news-logo mb-5">
                        <a href="{{ route('front.home') }}" class="p-3 rounded-3 d-inline-block" style="width:75%;">
                            <img src="{{ !empty(getAppLogo()) ? getAppLogo() : asset('assets/image/infyom-logo.png') }}"
                                alt="2playerz" class="img-fluid" />
                        </a>
                    </div>

                    <!-- Social Icons -->
                    <div class="social-icon d-flex my-4 flex-wrap">
                        <a href="{{ $settings['facebook_url'] }}" target="_blank">
                            <i class="fa-brands fa-facebook-f text-gray fs-18 {{ getFrontSelectLanguageIsoCode() == 'ar' ? 'ms-3' : 'me-3' }}"></i>
                        </a>
                        <a href="{{ $settings['twitter_url'] }}" target="_blank">
                            <!-- <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="text-gray fs-18 {{ getFrontSelectLanguageIsoCode() == 'ar' ? 'ms-3' : 'me-3' }}">
                                <path d="M459.4 151.7c.3 4.5 .3 9.1 .3 13.6 0 138.7-105.6 298.6-298.6 298.6-59.5 0-114.7-17.2-161.1-47.1 8.4 1 16.6 1.3 25.3 1.3 49.1 0 94.2-16.6 130.3-44.8-46.1-1-84.8-31.2-98.1-72.8 6.5 1 13 1.6 19.8 1.6 9.4 0 18.8-1.3 27.6-3.6-48.1-9.7-84.1-52-84.1-103v-1.3c14 7.8 30.2 12.7 47.4 13.3-28.3-18.8-46.8-51-46.8-87.4 0-19.5 5.2-37.4 14.3-53 51.7 63.7 129.3 105.3 216.4 109.8-1.6-7.8-2.6-15.9-2.6-24 0-57.8 46.8-104.9 104.9-104.9 30.2 0 57.5 12.7 76.7 33.1 23.7-4.5 46.5-13.3 66.6-25.3-7.8 24.4-24.4 44.8-46.1 57.8 21.1-2.3 41.6-8.1 60.4-16.2-14.3 20.8-32.2 39.3-52.6 54.3z" />
                            </svg> -->
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 1227" role="img" class="navbar-nav-svg text-gray fs-18 {{ getFrontSelectLanguageIsoCode() == 'ar' ? 'ms-3' : 'me-3' }}" height="16" width="16">
                                <title>X</title>
                                <path fill="currentColor" d="M714.163 519.284 1160.89 0h-105.86L667.137 450.887 357.328 0H0l468.492 681.821L0 1226.37h105.866l409.625-476.152 327.181 476.152H1200L714.137 519.284h.026ZM569.165 687.828l-47.468-67.894-377.686-540.24h162.604l304.797 435.991 47.468 67.894 396.2 566.721H892.476L569.165 687.854v-.026Z"></path>
                            </svg>
                            <!-- <i class="fa-brands fa-x-twitter text-gray fs-18 {{ getFrontSelectLanguageIsoCode() == 'ar' ? 'ms-3' : 'me-3' }}"></i> -->
                        </a>
                    </div>

                    <form action="{{ route('allPosts') }}" class="form search-form-box d-flex align-items-center mb-3">
                        <div class="form-group d-flex align-items-center border-0 search-input m-0" style="flex: 1;">
                            <input name="search" id="searchMobile" type="text"
                                class="form-control bg-transparent border-0 text-light m-0" style="font-size: 15px !important;"
                                placeholder="{{ __('messages.search') }}" value="{{ request('search') }}">

                            <button type="submit" class="search-submit custom-submit btn btn-primary ms-2" style="padding: 8px !important; border-radius: 10px !important;">
                                <i class="fas fa-search text-white"></i>
                            </button>
                        </div>

                        <!-- Filter Dropdown (MOBILE) -->
                        <div class="dropdown ms-2 position-relative">
                            <div id="filterToggleMobile" data-bs-toggle="dropdown" aria-expanded="false" class="position-relative filter-icon" style="cursor:pointer;">
                                <!-- Filter Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#746E7B" style="width: 25px;">
                                    <path fill-rule="evenodd" d="M3.792 2.938A49.069 49.069 0 0 1 12 2.25c2.797 0 5.54.236 8.209.688a1.857 1.857 0 0 1 1.541 1.836v1.044a3 3 0 0 1-.879 2.121l-6.182 6.182a1.5 1.5 0 0 0-.439 1.061v2.927a3 3 0 0 1-1.658 2.684l-1.757.878A.75.75 0 0 1 9.75 21v-5.818a1.5 1.5 0 0 0-.44-1.06L3.13 7.938a3 3 0 0 1-.879-2.121V4.774c0-.897.64-1.683 1.542-1.836Z" clip-rule="evenodd" />
                                </svg>
                                <span class="position-absolute badge">0</span>
                            </div>

                            <!-- Filter Panel -->
                            <div id="filterDropdownMobile" class="dropdown-menu p-3 shadow filter-dropdown mt-2" data-bs-display="static">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6>{{ __('messages.other_lang.filter') }}</h6>
                                    <span class="reset-link text-primary" style="cursor:pointer;">{{ __('messages.other_lang.reset_filter') }}</span>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">{{ __('messages.other_lang.select_post_time') }}</label>
                                    <select name="time" class="form-select">
                                        <option value="">{{ __('messages.other_lang.all_time') }}</option>
                                        <option value="today">{{ __('messages.other_lang.today_time') }}</option>
                                        <option value="week">{{ __('messages.other_lang.weak_time') }}</option>
                                        <option value="month">{{ __('messages.other_lang.month_time') }}</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">{{ __('messages.other_lang.select_category') }}</label>
                                    <select id="categoryFilterMobile" name="category" class="form-select">
                                        <option value="">{{ __('messages.other_lang.all_time') }}</option>
                                        @foreach($categories as $cat)
                                        <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">{{ __('messages.other_lang.select_sub_category') }}</label>
                                    <select id="subcategoryFilterMobile" name="subcategory" class="form-select">
                                        <option value="">{{ __('messages.other_lang.all_time') }}</option>
                                        @foreach($subcategories as $sub)
                                        <option value="{{ $sub->name }}" data-parent="{{ $sub->category->name }}">
                                            {{ $sub->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">{{ __('messages.other_lang.select_editor') }}</label>
                                    <select name="editor" class="form-select">
                                        <option value="">{{ __('messages.other_lang.all_time') }}</option>
                                        @foreach($editors as $ed)
                                        <option value="{{ $ed['id'] }}">{{ $ed['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <button class="mt-3 btn btn-primary w-100">
                                    {{ __('messages.other_lang.apply_button') }}
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="offcanvas-content m-0">

                        <div class="accordion" id="accordionExample">
                        </div>
                        @php
                        $nav = getHeaderElement();
                        @endphp
                        @foreach ($nav['navigations'] as $key => $navigation)
                        @if (
                        $navigation['navigationable']['lang_id'] == getFrontSelectLanguage() ||
                        $navigation->navigationable_type == \App\Models\Menu::class)
                        @php
                        $isSubNav = count($nav['navigationsTakeData'][$navigation->id]) > 0;
                        $subNavLangs = $nav['navigationsTakeData'][$navigation->id];
                        $menuName = $navigation->navigationable->name
                        ? $navigation->navigationable->name
                        : $navigation->navigationable->title;
                        $langId = false;
                        foreach ($subNavLangs as $subNavLang) {
                        if ($langId) {
                        continue;
                        }
                        if (
                        $subNavLang['navigationable_type'] ==
                        \App\Models\SubCategory::class
                        ) {
                        $langId = $subNavLang
                        ->navigationable()
                        ->where('lang_id', getFrontSelectLanguage())
                        ->exists();
                        }
                        }
                        @endphp
                        <!-- <div class="set"> -->
                        @if($key == 0)
                        <h3 class="accordion-header">
                            <a href="{{ route('allPosts') }}" class="fs-14 fw-6 sidebar_menu_li_mobile" aria-expanded="false">
                                {!! $navigation->navigationable->name ? $navigation->navigationable->name : $navigation->navigationable->title !!}
                            </a>
                        </h3>
                        @else
                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                @if($isSubNav)
                                <button class="accordion-button collapsed sidebar_menu_li_mobile" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapse-{{$navigation->id}}" aria-expanded="false"
                                    aria-controls="collapse-{{$navigation->id}}">
                                    {!! $navigation->navigationable->name ? $navigation->navigationable->name : $navigation->navigationable->title !!}
                                </button>
                                @else
                                <a href="{{ $navigation->navigationable->link !== null ? getNavUrl($navigation->navigationable->link) : route('categoryPage', $navigation->navigationable->slug) }}" 
                                    class="accordion-button collapsed sidebar_menu_li_mobile" 
                                    style="text-decoration: none; display: block; padding: 0.75rem 1.25rem;">
                                    {!! $navigation->navigationable->name ? $navigation->navigationable->name : $navigation->navigationable->title !!}
                                </a>
                                @endif
                            </h3>
                            <!-- <a href="{{ route('categoryPage', ['category' => $navigation->navigationable->slug]) }}"
                                class="fs-14 fw-6 sidebar_menu_li">
                                {!! $navigation->navigationable->name ? $navigation->navigationable->name : $navigation->navigationable->title !!}
                            </a> -->
                            @endif
                            @if (($langId || $navigation->navigationable_type == \App\Models\Menu::class) && $isSubNav)
                            <!-- <a href="#" class="p-0" data-turbo="false"><i
                                    class="fa fa-plus"></i></a> -->
                            @endif
                            @if ($langId || $navigation->navigationable_type == \App\Models\Menu::class)
                            @if ($isSubNav)
                            <div id="collapse-{{$navigation->id}}" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    @foreach ($nav['navigationsTakeData'] as $key => $navSub)
                                    @if ($key == $navigation->id)
                                    @foreach ($navSub as $sub)
                                    <li><a class="fs-14 fw-6 sidebar_menu_li_mobile"
                                            @if ($sub->navigationable->link !== null) href="{{ getNavUrl($sub->navigationable->link) }}"
                                            @else
                                            href="{{ route('categoryPage', ['category' => $navigation->navigationable->slug, 'slug' => $sub->navigationable->slug]) }}" @endif>
                                            {!! $sub->navigationable->name ? $sub->navigationable->name : $sub->navigationable->title !!}</a>
                                    </li>
                                    @endforeach
                                    
                                    {{-- Add Release List items for PlayStation, Xbox, Nintendo in mobile menu --}}
                                    @php
                                        $navNameMobile = strtolower(trim($menuName));
                                        $releaseListTypeMobile = null;
                                        $releaseRouteMobile = null;
                                        $isActiveMobile = false;
                                        
                                        if (str_contains($navNameMobile, 'playstation') || str_contains($navNameMobile, 'play station')) {
                                            $releaseListTypeMobile = \App\Models\ReleaseListSetting::LIST_TYPE_PLAYSTATION;
                                            $releaseRouteMobile = route('release-calendar.playstation');
                                            $isActiveMobile = Request::is('playstation/release-liste') || Request::is('playstation/release-liste/*');
                                        } elseif (str_contains($navNameMobile, 'xbox')) {
                                            $releaseListTypeMobile = \App\Models\ReleaseListSetting::LIST_TYPE_XBOX;
                                            $releaseRouteMobile = route('release-calendar.xbox');
                                            $isActiveMobile = Request::is('xbox/release-liste') || Request::is('xbox/release-liste/*');
                                        } elseif (str_contains($navNameMobile, 'nintendo')) {
                                            $releaseListTypeMobile = \App\Models\ReleaseListSetting::LIST_TYPE_NINTENDO;
                                            $releaseRouteMobile = route('release-calendar.nintendo');
                                            $isActiveMobile = Request::is('nintendo/release-liste') || Request::is('nintendo/release-liste/*');
                                        }
                                        
                                        if ($releaseListTypeMobile) {
                                            $releaseListMobile = \App\Models\ReleaseListSetting::where('list_type', $releaseListTypeMobile)->first();
                                            if ($releaseListMobile) {
                                    @endphp
                                            <li>
                                                <a class="fs-14 fw-6 sidebar_menu_li_mobile {{ $isActiveMobile ? 'active' : '' }}"
                                                    href="{{ $releaseRouteMobile }}">
                                                    {{ $releaseListMobile->headline ?? 'Release-Liste' }}
                                                </a>
                                            </li>
                                    @php
                                            }
                                        }
                                    @endphp
                                    @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <!-- <div class="content 4">
                                @foreach ($nav['navigationsTakeData'] as $key => $navSub)
                                @if ($key == $navigation->id)
                                @foreach ($navSub as $sub)
                                <li><a class="fs-14 fw-6 sidebar_menu_li"
                                        @if ($sub->navigationable->link !== null) href="{{ getNavUrl($sub->navigationable->link) }}"
                                        @else
                                        href="{{ route('categoryPage', ['category' => $navigation->navigationable->slug, 'slug' => $sub->navigationable->slug]) }}" @endif>
                                        {!! $sub->navigationable->name ? $sub->navigationable->name : $sub->navigationable->title !!}</a>
                                </li>
                                @endforeach
                                @endif
                                @endforeach
                            </div> -->
                        @endif
                        @endif
                        <!-- </div> -->
                        @endif
                        @endforeach
                        {{-- Release-Kalender (all platforms) - standalone link in mobile menu --}}
                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <a href="{{ route('release-calendar.all') }}"
                                    class="accordion-button collapsed sidebar_menu_li_mobile {{ Request::is('releasekalender') || Request::is('releasekalender/*') ? 'active' : '' }}"
                                    style="text-decoration: none; display: block; padding: 0.75rem 1.25rem;">
                                    Release-Kalender
                                </a>
                            </h3>
                        </div>
                        <div>
                            <a href="{{ route('contact.index') }}"
                                class="fs-14 fw-6 sidebar_menu_li_mobile btn-primary{{ 'Contact' == ucfirst(last(request()->segments())) ? 'active' : '' }}">
                                {{ __('messages.details.contact_us') }}
                            </a>
                        </div>
                        @if (getLogInUser())
                        <div class="set p-0 border-0">
                            <!-- <a href="javascript:void(0)" class="fs-14 fw-6 sidebar_menu_li">
                                {{ getLogInUser()->last_name }}
                            </a> -->
                            <!-- <a href="#" class="p-0" data-turbo="false"><i class="fa fa-plus"></i></a> -->
                            <div class="set content">
                                <li>
                                    {{-- <a href="{{ route('filament.admin.pages.dashboard') }}" class="fs-14 fw-6 sidebar_menu_li"
                                    data-turbo="false">
                                    {{ __('messages.other_lang.admin_panel') }}
                                    </a> --}}

                                    @if (Auth::user()->hasRole('customer'))
                                    <a href="{{ route('customer.profile') }}"
                                        class="fs-14 fw-6 sidebar_menu_li_mobile btn-primary" data-turbo="false">
                                        {{ __('messages.other_lang.admin_panel') }}
                                    </a>
                                    @endif
                                    @if (Auth::user()->hasRole('admin'))
                                    <a href="{{ route('filament.admin.pages.dashboard') }}"
                                        class="fs-14 fw-6 sidebar_menu_li_mobile btn-primary" data-turbo="false">
                                        {{ __('messages.other_lang.admin_panel') }}
                                    </a>
                                    @endif
                                </li>
                                <li>
                                    <form id="logout-form"
                                        action="{{ route('logout') }}"
                                        method="POST" style="display: none;">
                                        @csrf
                                    </form>

                                    <a href="javascript:void(0);"
                                        class="nav-link fs-14 d-flex align-items-center btn-primary"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        {{ __('messages.details.logout') }}
                                    </a>
                                </li>
                            </div>
                        </div>
                        @else
                        <div class="set">
                            <a href="{{ route('filament.auth.auth.login') }}"
                                class="fs-14 fw-6 sidebar_menu_li_mobile {{ 'Contact' == ucfirst(last(request()->segments())) ? 'active' : '' }}"
                                data-turbo="false">
                                {{ __('messages.common.login') }}
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- end header section -->

<style>
    .search-input {
        display: flex;
        align-items: center;
        border-radius: 10px;
        background: #242328;
        border: 1px solid #444;
    }

    .search-input svg {
        width: 20px;
        height: 20px;
    }

    .search-input input {
        font-size: 14px;
    }

    .form-control:focus {
        box-shadow: none;
    }

    .filter-icon .badge {
        top: -15px !important;
        right: -15px !important;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        font-size: 10px;
        background: #20253A !important;
        color: #6E70A1;
        border: 1px solid #6E70A1;
        border-radius: 50%;
    }

    /* Dropdown filter panel */
    .filter-dropdown {
        background: #020003;
        background: linear-gradient(0deg, rgba(2, 0, 3, 0.97) 0%, rgba(57, 1, 58, 1) 68%, rgba(72, 1, 72, 1) 84%, rgba(102, 1, 102, 1) 100%);
        border-radius: 12px;
        padding: 20px !important;
        width: 280px;
    }

    .filter-dropdown h6 {
        color: #fff;
        font-weight: bold;
    }

    .filter-dropdown .form-label {
        color: #ddd;
        font-size: 14px;
    }

    .filter-dropdown .form-select {
        background-color: #242328;
        color: #cfcfcf;
        border: 1px solid #444;
        font-size: 14px;
        border-radius: 7px;
    }

    .reset-link {
        font-size: 13px;
        color: #f66;
        cursor: pointer;
    }

    #customerInfo .dropdown-menu {
        width: fit-content;
        inset: 50% auto auto auto !important;
        transform: translate(-79%, 20px) !important;
        padding: 10px;
        width: auto;
        z-index: 999;
    }

    #customerInfo .dropdown-menu li {
        background: linear-gradient(180deg, #720072, #000);
        border-radius: 20px;
        margin-bottom: 5px;
    }

    #customerInfo .dropdown-menu li a {
        color: white !important;
    }

    #left0 {
        left: 0px !important;
    }

    .sidebar_menu_li_mobile {
        padding: 10px 15px;
        background: linear-gradient(180deg, rgba(44, 44, 90, 0.9) 0%, rgba(32, 32, 60, 0.9) 100%);
        border-radius: 6px;
        margin-bottom: 8px;
        display: flex;
        display: block !important;
        justify-content: space-between;
        align-items: center;
        color: #fff;
        font-weight: 600;
        transition: background 0.3s ease;
    }

    .sidebar_menu_li_mobile:hover {
        background: linear-gradient(180deg, rgba(74, 20, 140, 1) 0%, rgba(55, 0, 110, 1) 100%);
    }

    .accordion-header .sidebar_menu_li_mobile {
        cursor: pointer;
        font-size: 15px;
        background: linear-gradient(180deg, #3d1a57, #2a0d3a);
    }

    .accordion-body li a {
        padding: 6px 15px;
        display: block;
        color: #e0e0e0;
        font-size: 14px;
    }

    .accordion-body li a:hover {
        color: #fff;
    }

    .accordion-item {
        background-color: transparent;
    }

    body.dark-mode .sidebar_menu_li_mobile {
        background: linear-gradient(180deg, #2c1057, #1a0b2c);
        color: #fff;
    }

    /* body.light-mode .sidebar_menu_li_mobile {
        background: #f1f1f1;
        color: #000;
    } */
</style>


<style>
    .set {
        padding: 12px 16px;
        border: 1px solid #e0e0e0;
        /* Light gray border */
        border-radius: 6px;
        margin-bottom: 8px;
        font-weight: 500;
        color: #009688;
        /* Teal tone for links */
        /* background-color: #fff; */
        transition: all 0.3s ease;
        cursor: pointer;
    }

    /* Hover effect */
    /* .set:hover {
        background-color: #f5f5f5;
        border-color: #ccc;
    } */

    /* Optional: subtle shadow */
    .set {
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    body.light-theme {
        /*background-color: #800080; /* Purple */
    }

    body.dark-theme {
        background-color: #D3D3D3;
        /* Light Grey */
    }

    .navbar {
        position: relative;
        z-index: 1000;
    }

    .theme-toggle-btn {
        margin-left: auto;
        margin-right: 1rem;
    }

    /* Force the offcanvas to open from the right and stay there */
    .offcanvas.offcanvas-end {
        left: auto !important;
        right: 0 !important;
        transform: translateX(100%) !important;
        visibility: hidden !important;
    }

    .offcanvas.offcanvas-end.show {
        transform: translateX(0%) !important;
        visibility: visible !important;
    }

    /* Optional: prevent centering animation bug */
    .offcanvas-backdrop.show {
        opacity: 0.5 !important;
    }

    .btn-primary {
        background: linear-gradient(180deg, #2c1057, #1a0b2c) !important;
        color: #ffffff !important;
    }


    .btn-primary:hover,
    .btn-primary:focus {
        color: #8a8f95 !important;
    }

    header .nav .dropdown .dropdown-nav li {
        padding: 8px;
        background: linear-gradient(180deg, #720072, #000);
        /* margin-bottom: 4px; */
        color: white !important;
        border-radius: 16px;
    }

    header .nav .dropdown-nav {
        background-color: transparent !important;
        padding: 0px !important;
    }

    header .nav .dropdown .dropdown-nav a {
        color: white;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let categorySelect = document.getElementById('categoryFilter');
        let subcategorySelect = document.getElementById('subcategoryFilter');

        let filterDropdown = document.getElementById('filterDropdown');
        let filterToggle = document.getElementById('filterToggle');
        let badge = filterToggle.querySelector('.badge');
        let resetLink = document.querySelector('.reset-link');
        let formSelects = filterDropdown.querySelectorAll('select');
        let applyBtn = filterDropdown.querySelector('button');

        // Subcategory filter
        function filterSubcategories() {
            let selectedCat = categorySelect.value;

            [...subcategorySelect.options].forEach(opt => {
                if (opt.value === "" || opt.getAttribute('data-parent') === selectedCat) {
                    opt.style.display = "block";
                } else {
                    opt.style.display = "none";
                }
            });
        }

        // Function: Count active filters
        function updateFilterCount() {
            let count = 0;
            formSelects.forEach(sel => {
                if (sel.value !== "" && sel.value.toLowerCase() !== "all") {
                    count++;
                }
            });
            badge.textContent = count;
        }

        // Load saved filters from URL query params
        function loadFiltersFromURL() {
            let params = new URLSearchParams(window.location.search);
            formSelects.forEach(sel => {
                if (params.has(sel.name)) {
                    sel.value = params.get(sel.name);
                }
            });

            // Load search query from URL
            let searchInput = document.getElementById('search');
            if (searchInput && params.has('search')) {
                searchInput.value = params.get('search');
            }

            // 👇 Important: Run subcategory filter after setting values
            filterSubcategories();

            updateFilterCount();
        }

        // Reset filters
        resetLink.addEventListener('click', function() {
            formSelects.forEach(sel => sel.value = "");
            badge.textContent = 0;
            filterSubcategories(); // reset subcategory dropdown
        });

        // Apply filters (redirect with query params)
        applyBtn.addEventListener('click', function(e) {
            e.preventDefault();
            let params = new URLSearchParams();
            
            // Preserve search query if exists
            let searchInput = document.getElementById('search');
            if (searchInput && searchInput.value.trim() !== "") {
                params.set('search', searchInput.value.trim());
            }
            
            // Add filter parameters
            formSelects.forEach(sel => {
                if (sel.value !== "" && sel.value.toLowerCase() !== "all") {
                    params.set(sel.name, sel.value);
                }
            });
            window.location.href = "/p?" + params.toString();
        });

        // Update count on change
        formSelects.forEach(sel => {
            sel.addEventListener('change', () => {
                updateFilterCount();
                if (sel === categorySelect) {
                    filterSubcategories();
                }
            });
        });

        // Preserve filters when search form is submitted
        let searchForm = document.querySelector('form.search-form-box');
        if (searchForm) {
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                let params = new URLSearchParams();
                
                // Get search query from form
                let searchInput = document.getElementById('search');
                if (searchInput && searchInput.value.trim() !== "") {
                    params.set('search', searchInput.value.trim());
                }
                
                // Preserve existing filter parameters
                formSelects.forEach(sel => {
                    if (sel.value !== "" && sel.value.toLowerCase() !== "all") {
                        params.set(sel.name, sel.value);
                    }
                });
                
                // Redirect with both search and filters
                window.location.href = "/p?" + params.toString();
            });
        }

        // Init
        loadFiltersFromURL();
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let categorySelect = document.getElementById("categoryFilterMobile");
        let subcategorySelect = document.getElementById("subcategoryFilterMobile");
        let filterDropdown = document.getElementById("filterDropdownMobile");
        let filterToggle = document.getElementById("filterToggleMobile");
        let badge = filterToggle.querySelector(".badge");
        let resetLink = filterDropdown.querySelector(".reset-link");
        let formSelects = filterDropdown.querySelectorAll("select");
        let applyBtn = filterDropdown.querySelector("button");

        // --- Subcategory filter ---
        function filterSubcategories() {
            let selectedCat = categorySelect.value;
            [...subcategorySelect.options].forEach(opt => {
                if (opt.value === "" || opt.getAttribute("data-parent") === selectedCat) {
                    opt.style.display = "block";
                } else {
                    opt.style.display = "none";
                }
            });
        }

        // --- Count active filters ---
        function updateFilterCount() {
            let count = 0;
            formSelects.forEach(sel => {
                if (sel.value !== "" && sel.value.toLowerCase() !== "all") {
                    count++;
                }
            });
            badge.textContent = count;
        }

        // --- Load saved filters from URL ---
        function loadFiltersFromURL() {
            let params = new URLSearchParams(window.location.search);
            formSelects.forEach(sel => {
                if (params.has(sel.name)) {
                    sel.value = params.get(sel.name);
                }
            });
            
            // Load search query from URL
            let searchInput = document.getElementById('searchMobile');
            if (searchInput && params.has('search')) {
                searchInput.value = params.get('search');
            }
            
            filterSubcategories();
            updateFilterCount();
        }

        // --- Reset filters ---
        resetLink.addEventListener("click", function() {
            formSelects.forEach(sel => sel.value = "");
            badge.textContent = 0;
            filterSubcategories();
        });

        // --- Apply filters ---
        applyBtn.addEventListener("click", function(e) {
            e.preventDefault();
            let params = new URLSearchParams();
            
            // Preserve search query if exists
            let searchInput = document.getElementById('searchMobile');
            if (searchInput && searchInput.value.trim() !== "") {
                params.set('search', searchInput.value.trim());
            }
            
            // Add filter parameters
            formSelects.forEach(sel => {
                if (sel.value !== "" && sel.value.toLowerCase() !== "all") {
                    params.set(sel.name, sel.value);
                }
            });
            window.location.href = "/p?" + params.toString();
        });

        // --- Update count on change ---
        formSelects.forEach(sel => {
            sel.addEventListener("change", () => {
                updateFilterCount();
                if (sel === categorySelect) {
                    filterSubcategories();
                }
            });
        });

        // Preserve filters when mobile search form is submitted
        let searchFormMobile = document.querySelector('form.search-form-box');
        if (searchFormMobile) {
            searchFormMobile.addEventListener('submit', function(e) {
                e.preventDefault();
                let params = new URLSearchParams();
                
                // Get search query from form
                let searchInput = document.getElementById('searchMobile');
                if (searchInput && searchInput.value.trim() !== "") {
                    params.set('search', searchInput.value.trim());
                }
                
                // Preserve existing filter parameters
                formSelects.forEach(sel => {
                    if (sel.value !== "" && sel.value.toLowerCase() !== "all") {
                        params.set(sel.name, sel.value);
                    }
                });
                
                // Redirect with both search and filters
                window.location.href = "/p?" + params.toString();
            });
        }

        // --- Init ---
        loadFiltersFromURL();
    });
</script>


<script>
    $(document).ready(function() {

        var theme = localStorage.getItem('theme');

        // Sync the checkbox state with the theme
        var isChecked = theme === 'dark';
        $('.themeSwitchCheckbox').prop('checked', isChecked);
        // $('body').removeClass('light-mode dark-mode').addClass(theme + '-mode');

        $('.themeSwitchCheckbox').on('click', function() {
            var isChecked = $(this).prop('checked');
            var theme = isChecked ? 'dark' : 'light';

            // Update body class
            $('body').removeClass('light-mode dark-mode').addClass(theme + '-mode');
            // Save to localStorage
            localStorage.setItem('theme', theme);

            // Send AJAX to Laravel route
            // $.ajax({
            //     url: '{{ route("set-theme") }}',
            //     type: 'POST',
            //     data: {
            //         theme: theme,
            //         _token: $('meta[name="csrf-token"]').attr('content')
            //     },
            //     success: function(response) {
            //         console.log('Theme saved to session:', response.status);
            //     },
            //     error: function() {
            //         console.log('Failed to save theme to session');
            //     }
            // });
        });

        $(document).on('click', '.like-btn', function() {
            let btn = $(this);
            let isLoggedIn = btn.data('auth'); // 1 = logged in, 0 = guest

            if (!isLoggedIn) {
                alert('Bitte einloggen um ein Like zu hinterlassen.');
                return;
            }

            let itemId = btn.data('id');
            let itemType = btn.data('type');

            $.ajax({
                url: '{{route("like-toggle")}}',
                method: 'POST',
                data: {
                    item_id: itemId,
                    item_type: itemType,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    let icon = btn.find('i');
                    let count = btn.find('.like-count');
                    count.text(res.likes);
                    // Ensure like count is visible
                    count.css('display', 'inline').css('visibility', 'visible');

                    let iconElement = $('#current_like_icon_' + itemId);
                    // Check if this is in comments section or article detail page
                    let isCommentSection = btn.closest('.comment-section').length > 0;
                    
                    if (res.liked) {
                        iconElement.css('color', '#B051B0');
                        // For SVG elements, also set fill property
                        if (iconElement.is('svg') || iconElement.find('svg').length > 0) {
                            iconElement.css('fill', '#B051B0');
                            iconElement.find('path').css('fill', '#B051B0');
                        }
                    } else {
                        // Different color for comments section vs article detail page
                        if (isCommentSection) {
                            iconElement.css('color', '#666');
                            if (iconElement.is('svg') || iconElement.find('svg').length > 0) {
                                iconElement.css('fill', '#666');
                                iconElement.find('path').css('fill', '#666');
                            }
                        } else {
                            iconElement.css('color', '#fff');
                            if (iconElement.is('svg') || iconElement.find('svg').length > 0) {
                                iconElement.css('fill', '#fff');
                                iconElement.find('path').css('fill', '#fff');
                            }
                        }
                    }
                }
            });
        });
    });

    function markAsReadAndRedirect(notificationId, redirectUrl) {
        $.ajax({
            url: `/notification/mark-as-read/${notificationId}`,
            type: 'get',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                window.location.href = redirectUrl;
            },
            error: function(xhr) {
                console.error('Failed to mark notification as read:', xhr);
            }
        });
    }
</script>
<script>
    function hideBadge() {
        // Only hide badge visually when dropdown is opened
        // DO NOT mark all notifications as read - only mark individual ones when clicked
        // Badge will be updated when individual notification is clicked via markAsReadAndRedirect
    }


    function markAsReadAndRedirect(notificationId, url) {
        fetch(`/notification/mark-as-read/${notificationId}`, {
                method: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            })
            .then(response => response.json())
            .then(data => {
                // Update badge count immediately before redirect
                updateNotificationBadge();
                // Remove the notification from dropdown if visible
                const notificationItem = document.querySelector(`[onclick*="${notificationId}"]`)?.closest('li');
                if (notificationItem) {
                    notificationItem.classList.remove('unread-notification');
                    notificationItem.querySelector('a').classList.remove('text-dark');
                }
                // Redirect to the page
                window.location.href = url;
            })
            .catch(error => {
                console.error('Failed to mark notification as read:', error);
                // Still redirect even if marking as read fails
                window.location.href = url;
            });
    }

    // Function to update notification badge count via AJAX
    function updateNotificationBadge() {
        @auth
        fetch('/notification/unread-count', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                // Get all notification badges (there might be multiple instances in header)
                const badges = document.querySelectorAll('.notificationBadge, #notificationBadge');
                const bellButtons = document.querySelectorAll('#notificationButton');
                
                if (data.count > 0) {
                    // Update existing badges
                    badges.forEach(badge => {
                        badge.textContent = data.count;
                        badge.style.display = '';
                    });
                    
                    // Create badge if it doesn't exist for any bell button
                    bellButtons.forEach(button => {
                        const existingBadge = button.querySelector('.notificationBadge, #notificationBadge');
                        if (!existingBadge) {
                            const newBadge = document.createElement('span');
                            newBadge.id = 'notificationBadge';
                            newBadge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notificationBadge';
                            newBadge.textContent = data.count;
                            button.appendChild(newBadge);
                        }
                    });
                } else {
                    // Remove all badges if count is 0
                    badges.forEach(badge => badge.remove());
                }
            })
            .catch(error => {
                console.error('Failed to update notification badge:', error);
            });
        @endauth
    }

    // Function to mark all notifications as read
    function markAllAsRead() {
        @auth
        fetch('/notifications/mark-all-seen', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update badge count
                    updateNotificationBadge();
                    
                    // Find all notification dropdowns
                    const dropdowns = document.querySelectorAll('[aria-labelledby="notificationButton"]');
                    
                    dropdowns.forEach(dropdown => {
                        // Remove unread styling from all notification items
                        const notificationItems = dropdown.querySelectorAll('.dropdown-item');
                        notificationItems.forEach(item => {
                            // Remove all unread-related classes
                            item.classList.remove('unread-notification', 'text-dark', 'bg-light');
                            
                            // Update links inside
                            const links = item.querySelectorAll('a');
                            links.forEach(link => {
                                // Remove all text color classes
                                link.classList.remove('text-dark', 'text-light', 'text-white');
                                // Apply custom color #B051B0 for normal state (without !important so hover can override)
                                link.style.color = '#B051B0';
                            });
                        });
                    });
                }
            })
            .catch(error => {
                console.error('Failed to mark all notifications as read:', error);
            });
        @endauth
    }

    // Handle browser back/forward cache (bfcache)
    window.addEventListener('pageshow', function(event) {
        // If page was loaded from cache (back/forward button)
        if (event.persisted) {
            // Reload notification count
            updateNotificationBadge();
            // Optionally reload notifications dropdown
            if (document.getElementById('notificationButton')?.getAttribute('aria-expanded') === 'true') {
                // If dropdown is open, we might want to reload it
                // For now, just update the count
            }
        }
    });

    // Also update badge when page loads normally
    document.addEventListener('DOMContentLoaded', function() {
        // Small delay to ensure page is fully loaded
        setTimeout(updateNotificationBadge, 500);
    });
</script>


<script>
    const filterToggle = document.getElementById("filterToggle");
    const filterDropdown = document.getElementById("filterDropdown");

    filterToggle.addEventListener("click", () => {
        filterDropdown.classList.toggle("show");
    });
</script>