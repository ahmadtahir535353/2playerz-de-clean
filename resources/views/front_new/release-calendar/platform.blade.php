@extends('front_new.layouts.app')
@section('title')
    {{ $setting->headline ?? ($platform . ' Releases') }}
@endsection
@section('meta_tags')
    {{ $setting->keywords ?? '' }}
@endsection
@section('meta_description')
    {{ $setting->short_description ?? '' }}
@endsection
@section('content')
    <div class="sports-page">
        <div class="breadcrumb-section pt-4">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="/" class="fs-14 fw-6"><i
                                    class="fas fa-home me-1"></i>{{ __('messages.details.home') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('release-calendar.all') }}" class="fs-14 fw-6">Releasekalender</a></li>
                        <li class="breadcrumb-item active fs-14 fw-6" aria-current="page">
                            {{ $platform }}
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- start sports-section -->
        <section class="sports-section">
            <div class="container">
                <div class="row">
                    <div class="col-xl-8">
                        <!-- start sports-left-section -->
                        <section class="sports-left-section">
                            <div class="release-calendar-page">
                                <!-- Page Headline -->
                                <h3 class="text-black fw-7 fs-24 my-2">
                                    @if($setting && $setting->headline)
                                        {{ $setting->headline }}
                                    @else
                                        {{ $platform }} Releases
                                    @endif
                                </h3>

                                <!-- Short Description -->
                                @if($setting && $setting->short_description)
                                    <div class="post-content">
                                        <p class="text-gray">{{ $setting->short_description }}</p>
                                    </div>
                                @endif

                                <!-- Author Info Row with Like/Comment/ReadTime (creator = user set in admin panel) -->
                                <div class="d-md-flex mb-2">
                                    <div class="d-flex align-items-center" style="flex: 1;">
                                        <div style="flex: 1;">
                                            <div class="d-flex">
                                                @php
                                                    $creator = $setting && $setting->creator ? $setting->creator : null;
                                                @endphp
                                                @if($creator && $creator->id)
                                                <div class="">
                                                    <a href="{{ route('user.public.profile', $creator->username ?? $creator->id) }}"
                                                        class="profile-link"
                                                        data-user-identifier="{{ $creator->username ?? $creator->id }}">
                                                        <img src="{{ $creator->profile_image }}" alt="{{ $creator->full_name ?? 'User' }}"
                                                            class="h-40px me-2 image image-circle"
                                                            width="40" height="40">
                                                    </a>
                                                </div>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <a href="{{ route('user.public.profile', $creator->username ?? $creator->id) }}"
                                                        class="profile-link"
                                                        data-user-identifier="{{ $creator->username ?? $creator->id }}">
                                                        <h5 class="fs-12 text-black mb-0">{{ $creator->full_name }}</h5>
                                                        <span class="fs-12 text-gray">
                                                            {{ $setting->updated_at->format('d') }}. {{ ucfirst(__('messages.common.' . strtolower($setting->updated_at->format('F')))) }}
                                                            {{ $setting->updated_at->format('Y') }}
                                                        </span>
                                                    </a>
                                                </div>
                                                @else
                                                @if(isset($adminUser) && $adminUser)
                                                <div class="">
                                                    <a href="{{ route('user.public.profile', $adminUser->username ?? $adminUser->id) }}"
                                                        class="profile-link"
                                                        data-user-identifier="{{ $adminUser->username ?? $adminUser->id }}">
                                                        <img src="{{ $adminUser->profile_image }}" alt="{{ $adminUser->full_name ?? 'Admin' }}"
                                                            class="h-40px me-2 image image-circle"
                                                            width="40" height="40">
                                                    </a>
                                                </div>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <a href="{{ route('user.public.profile', $adminUser->username ?? $adminUser->id) }}"
                                                        class="profile-link"
                                                        data-user-identifier="{{ $adminUser->username ?? $adminUser->id }}">
                                                        <h5 class="fs-12 text-black mb-0">{{ $adminUser->full_name ?? 'Admin' }}</h5>
                                                        @if($setting)
                                                        <span class="fs-12 text-gray">
                                                            {{ $setting->updated_at->format('d') }}. {{ ucfirst(__('messages.common.' . strtolower($setting->updated_at->format('F')))) }}
                                                            {{ $setting->updated_at->format('Y') }}
                                                        </span>
                                                        @endif
                                                    </a>
                                                </div>
                                                @else
                                                <div class="">
                                                    <img src="{{ asset('assets/image/avatar.png') }}" alt="Admin"
                                                        class="h-40px me-2 image image-circle"
                                                        width="40" height="40">
                                                </div>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <h5 class="fs-12 text-black mb-0">Admin</h5>
                                                    @if($setting)
                                                    <span class="fs-12 text-gray">
                                                        {{ $setting->updated_at->format('d') }}. {{ ucfirst(__('messages.common.' . strtolower($setting->updated_at->format('F')))) }}
                                                        {{ $setting->updated_at->format('Y') }}
                                                    </span>
                                                    @endif
                                                </div>
                                                @endif
                                                @endif
                                            </div>
                                        </div>
                                        <div style="flex: 1;">
                                            <div class="news-text mb-2 d-flex align-items-center" style="gap: 10px">
                                                <!-- Like Button -->
                                                @if($setting)
                                                <div class="desc d-flex align-items-center like-btn article-action-btn" 
                                                    style="cursor: pointer; position: relative; gap: 5px;"
                                                    data-id="{{ $setting->id }}"
                                                    data-type="release_list"
                                                    data-auth="{{ auth()->check() ? '1' : '0' }}"
                                                    title="{{ __('messages.comment.like_article') }}">
                                                    <i class="fa fa-thumbs-up" id="current_like_icon_{{ $setting->id }}"
                                                        style="color: #fff; transition: color 0.3s ease; {{ !empty($setting->user_liked) && $setting->user_liked ? 'color: #B051B0; fill: #B051B0;' : '' }}"></i>
                                                    <span class="like-count" style="color: #fff;">{{ $setting->likes_count ?? 0 }}</span>
                                                </div>
                                                @else
                                                <div class="desc d-flex align-items-center article-action-btn" 
                                                    style="cursor: pointer; position: relative; gap: 5px;"
                                                    title="{{ __('messages.comment.like_article') }}">
                                                    <i class="fa fa-thumbs-up" style="color: #fff;"></i>
                                                    <span class="like-count" style="color: #fff;">0</span>
                                                </div>
                                                @endif
                                                
                                                <!-- Comment Button -->
                                                <div class="desc d-inline-block article-action-btn comment-btn-wrapper" 
                                                    style="position: relative;">
                                                    <a href="#commentFormSection" class="comment-link d-flex align-items-center gap-1" 
                                                        style="text-decoration: none;">
                                                        <i class="fa-solid fa-comments me-1" style="color: #fff;"></i>
                                                        <span class="me-1 comment-count-display" style="color: #fff;">{{ $totalComments ?? 0 }}</span>
                                                    </a>
                                                </div>
                                                
                                                <!-- Read Time (based on short_description + all game names so list length is reflected) -->
                                                @php
                                                    $contentForReadingTime = $setting->short_description ?? '';
                                                    if ($gamesWithDates->isNotEmpty()) {
                                                        foreach ($gamesWithDates as $year => $sections) {
                                                            foreach ($sections as $section) {
                                                                foreach ($section['games'] ?? [] as $game) {
                                                                    $contentForReadingTime .= ' ' . ($game->name ?? '');
                                                                }
                                                            }
                                                        }
                                                    }
                                                    if ($gamesWithoutDates->isNotEmpty()) {
                                                        foreach ($gamesWithoutDates as $game) {
                                                            $contentForReadingTime .= ' ' . ($game->name ?? '');
                                                        }
                                                    }
                                                @endphp
                                                <div class="desc d-flex align-items-center gap-2">
                                                    <i class="fa-solid fa-clock fs-12 text-gray me-1"></i>
                                                    <span class="fs-14 text-gray me-1">
                                                        {{ getReadingTime($contentForReadingTime) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Page Image with Overlay -->
                                @if($setting && $setting->getFirstMedia('release_list_image'))
                                    <div class="news-content-img position-relative mb-4">
                                        <div class="news-details-img rounded-10">
                                            <img src="{{ $setting->getFirstMedia('release_list_image')->getUrl() }}" 
                                                 alt="{{ $setting->headline ?? $platform . ' Releases' }}" 
                                                 class="w-100 h-100 hero-image"
                                                 style="object-fit: cover; min-height: 400px;">
                                            <div class="image-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                                                 style="background: linear-gradient(to bottom, rgba(0,0,0,0.2), rgba(0,0,0,0.4));">
                                                <h2 class="overlay-text text-white fw-bold" style="font-size: 3rem; text-shadow: 2px 2px 4px rgba(0,0,0,0.8);">
                                                    {{ $setting->banner_title ?? $platform . '-Releases ' . date('Y') }}
                                                </h2>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Games with Release Dates (grouped by year, then month) -->
                                @if($gamesWithDates->isNotEmpty())
                                    <div class="releases-with-dates mb-5">
                                        @foreach($gamesWithDates as $year => $sections)
                                            <h2 class="section-title mb-4 mt-5 @if(!$loop->first) pt-4 @endif">
                                                <span class="gamepad-icon">🎮</span>
                                                {{ $platform }} Releases {{ $year }}
                                            </h2>
                                            
                                            @foreach($sections as $section)
                                                @if($section['heading'])
                                                    <div class="release-month-divider">
                                                        <h3 class="month-heading">{{ $section['heading'] }}</h3>
                                                    </div>
                                                @endif
                                                
                                                <ul class="release-list list-unstyled">
                                                    @foreach($section['games'] as $game)
                                                        @php $onWishlist = in_array($game->id, $wishlistGameIds ?? []); @endphp
                                                        <li class="release-item mb-3 pb-3 border-bottom">
                                                            <div class="release-date">
                                                                @if($game->release_date)
                                                                    {{ $game->release_date->format('d.m.Y') }}
                                                                @elseif($game->release_month && $game->release_year)
                                                                    {{ \Carbon\Carbon::create($game->release_year, $game->release_month, 1)->locale('de')->monthName }} {{ $game->release_year }}
                                                                @elseif($game->release_year)
                                                                    {{ __('messages.release_calendar.date_tba') }}
                                                                @endif
                                                            </div>
                                                            <div class="release-content">
                                                                @if(!empty(trim($game->link ?? '')))
                                                                    <a href="{{ $game->link }}" class="game-name fw-bold text-decoration-none">
                                                                        {{ $game->name }}
                                                                    </a>
                                                                @else
                                                                    <span class="release-game-name-plain fw-bold">{{ $game->name }}</span>
                                                                @endif
                                                                <span class="wishlist-toggle ms-2 {{ $onWishlist ? 'wishlist-on' : '' }}" data-game-id="{{ $game->id }}" data-on-wishlist="{{ $onWishlist ? '1' : '0' }}" role="button" tabindex="0">{{ $onWishlist ? '– Wunschliste' : '+ Wunschliste' }}</span>
                                                                @php $bc = $badgeColors ?? []; @endphp
                                                                <div class="platforms mt-2">
                                                                    @if($game->playstation || $game->xbox || $game->nintendo)
                                                                        <span class="platforms-label">(</span>
                                                                        @if($game->playstation)
                                                                            <span class="platform-badge release-badge-inline" style="background-color: {{ $bc['playstation']['bg'] ?? '#4a4a4a' }}; color: {{ $bc['playstation']['text'] ?? '#e0e0e0' }};">PlayStation</span>
                                                                        @endif
                                                                        @if($game->xbox)
                                                                            @if($game->playstation), @endif
                                                                            <span class="platform-badge release-badge-inline" style="background-color: {{ $bc['xbox']['bg'] ?? '#4a4a4a' }}; color: {{ $bc['xbox']['text'] ?? '#e0e0e0' }};">Xbox</span>
                                                                        @endif
                                                                        @if($game->nintendo)
                                                                            @if($game->playstation || $game->xbox), @endif
                                                                            <span class="platform-badge release-badge-inline" style="background-color: {{ $bc['nintendo']['bg'] ?? '#4a4a4a' }}; color: {{ $bc['nintendo']['text'] ?? '#e0e0e0' }};">Nintendo</span>
                                                                        @endif
                                                                        <span class="platforms-label">)</span>
                                                                    @endif
                                                                    
                                                                    @if($game->ps_plus || $game->game_pass)
                                                                        <span class="subscription-separator"> - </span>
                                                                        @if($game->ps_plus)
                                                                            <span class="subscription-badge release-badge-inline" style="background-color: {{ $bc['ps_plus']['bg'] ?? '#1976d2' }}; color: {{ $bc['ps_plus']['text'] ?? '#ffffff' }};">PS Plus</span>
                                                                        @endif
                                                                        @if($game->game_pass)
                                                                            @if($game->ps_plus), @endif
                                                                            <span class="subscription-badge release-badge-inline" style="background-color: {{ $bc['game_pass']['bg'] ?? '#107c10' }}; color: {{ $bc['game_pass']['text'] ?? '#ffffff' }};">Game Pass</span>
                                                                        @endif
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endforeach
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Games without Release Dates -->
                                @if($gamesWithoutDates->isNotEmpty())
                                    <div class="releases-without-dates mt-5 pt-4 border-top">
                                        <h2 class="section-title mb-4">{{ $setting?->date_not_fixed_label ?? 'Ohne feste Release Datum' }}</h2>
                                        <ul class="release-list list-unstyled">
                                            @foreach($gamesWithoutDates as $game)
                                                @php $onWishlist = in_array($game->id, $wishlistGameIds ?? []); @endphp
                                                <li class="release-item mb-3 pb-3 border-bottom">
                                                    <div class="release-content">
                                                        @if(!empty(trim($game->link ?? '')))
                                                            <a href="{{ $game->link }}" class="game-name fw-bold text-decoration-none">
                                                                {{ $game->name }}
                                                            </a>
                                                        @else
                                                            <span class="release-game-name-plain fw-bold">{{ $game->name }}</span>
                                                        @endif
                                                        <span class="wishlist-toggle ms-2 {{ $onWishlist ? 'wishlist-on' : '' }}" data-game-id="{{ $game->id }}" data-on-wishlist="{{ $onWishlist ? '1' : '0' }}" role="button" tabindex="0">{{ $onWishlist ? '– Wunschliste' : '+ Wunschliste' }}</span>
                                                        @php $bc = $badgeColors ?? []; @endphp
                                                        <div class="platforms mt-2">
                                                            @if($game->playstation || $game->xbox || $game->nintendo)
                                                                <span class="platforms-label">(</span>
                                                                @if($game->playstation)
                                                                    <span class="platform-badge release-badge-inline" style="background-color: {{ $bc['playstation']['bg'] ?? '#4a4a4a' }}; color: {{ $bc['playstation']['text'] ?? '#e0e0e0' }};">PlayStation</span>
                                                                @endif
                                                                @if($game->xbox)
                                                                    @if($game->playstation), @endif
                                                                    <span class="platform-badge release-badge-inline" style="background-color: {{ $bc['xbox']['bg'] ?? '#4a4a4a' }}; color: {{ $bc['xbox']['text'] ?? '#e0e0e0' }};">Xbox</span>
                                                                @endif
                                                                @if($game->nintendo)
                                                                    @if($game->playstation || $game->xbox), @endif
                                                                    <span class="platform-badge release-badge-inline" style="background-color: {{ $bc['nintendo']['bg'] ?? '#4a4a4a' }}; color: {{ $bc['nintendo']['text'] ?? '#e0e0e0' }};">Nintendo</span>
                                                                @endif
                                                                <span class="platforms-label">)</span>
                                                            @endif
                                                            
                                                            @if($game->ps_plus || $game->game_pass)
                                                                <span class="subscription-separator"> - </span>
                                                                @if($game->ps_plus)
                                                                    <span class="subscription-badge release-badge-inline" style="background-color: {{ $bc['ps_plus']['bg'] ?? '#1976d2' }}; color: {{ $bc['ps_plus']['text'] ?? '#ffffff' }};">PS Plus</span>
                                                                @endif
                                                                @if($game->game_pass)
                                                                    @if($game->ps_plus), @endif
                                                                    <span class="subscription-badge release-badge-inline" style="background-color: {{ $bc['game_pass']['bg'] ?? '#107c10' }}; color: {{ $bc['game_pass']['text'] ?? '#ffffff' }};">Game Pass</span>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if($gamesWithDates->isEmpty() && $gamesWithoutDates->isEmpty())
                                    <div class="alert alert-info">
                                        <i class="fa-solid fa-info-circle me-2"></i>
                                        Keine {{ $platform }} Releases gefunden.
                                    </div>
                                @endif

                                <!-- Comment Section - Same design as article-details -->
                                @if($setting)
                                <section class="comment-section mt-4 pt-3 blog-post-comment-view mb-3" id="blog-post-comment-view-section">
                                    <div class="d-flex justify-content-between align-items-center mb-3 comment-data {{ $totalComments > 0 ? '' : 'd-none' }}">
                                        <h3 class="text-black fw-6 mb-0">
                                            {{ __('messages.comments') }}:
                                            <span class="ms-2 count-data">{{ $totalComments }}</span>
                                        </h3>
                                    </div>
                                    
                                    <section class="post-comment-section bg-light px-30 py-4 mb-5" style="box-shadow: 0px 0px 10px #00000020; border-radius: 15px;" id="commentFormSection">
                                        <h5 class="fs-16 text-black fw-6 mb-3">{{ __('messages.comment.post_a_comment') }}</h5>
                                        <form id="commentForm">
                                            @csrf
                                            <input type="hidden" name="item_type" value="release_list">
                                            <input type="hidden" name="item_id" value="{{ $setting->id }}">
                                            <input type="hidden" name="user_id"
                                                value="{{ isset(getLogInUser()->id) ? getLogInUser()->id : null }}">
                                            <div class="row">
                                                @if (!Auth::check())
                                                <div class="col-md-12">
                                                    <a class="btn btn-primary" href="{{url('login')}}">Zum kommentieren einloggen</a>
                                                </div>
                                                @else
                                                <div class="col-12">
                                                    <p class="lead emoji-picker-container">
                                                        <textarea class="form-control textarea-control fs-14 text-gray" name="comment"
                                                            id="comment" style="color:rgb(123, 123, 123) !important" rows="3"
                                                            placeholder="{{ __('messages.comment.type_your_comments') }}"
                                                            required></textarea>
                                                    </p>
                                                </div>
                                                <div class="col-12 mb-2">
                                                    @php
                                                        $settings = getSettingValue();
                                                        $showCaptcha = $settings['show_captcha'] ?? '0';
                                                    @endphp
                                                    @if ($showCaptcha == '1')
                                                    <input type="hidden" value="{{ $showCaptcha }}"
                                                        id="googleCaptch">
                                                    <div class="form-group mb-1">
                                                        <div class="g-recaptcha" id="gRecaptchaContainerPostDetails"
                                                            data-sitekey="{{ $settings['site_key'] ?? '' }}"></div>
                                                        <div id="g-recaptcha-error"></div>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-12 mb-2">
                                                <p class="text-muted mb-0" style="font-size: 11px;">
                                                    Bitte lies unsere <a href="{{ route('page.Terms') }}" target="_blank" style="color: #B051B0; text-decoration: underline;">Kommentar-Regeln</a>, bevor Du einen Kommentar verfasst.
                                                </p>
                                            </div>
                                            <button type="submit" class="btn btn-primary comment-btn">{{ __('messages.common.submit') }}</button>
                                            @endif
                                        </form>
                                    </section>
                                    <!-- end post-comment-section -->
                                    
                                    @php
                                    $inStyle = 'style=';
                                    $style = '"overflow-y: auto; max-height: 325px"';
                                    @endphp
                                    <div id="blog-post-comment-body" class="comment-view" {!! $totalComments >= 3 ? $inStyle . $style : '' !!}>
                                        <!-- Comments will appear here -->
                                    </div>
                                </section>
                                @endif
                            </div>
                        </section>
                        <!-- end sports-left-section -->
                    </div>
                    <div class="col-xl-4">
                        @include('front_new.detail_pages.side-menu')
                    </div>
                </div>
            </div>
        </section>
        <!-- end sports-section -->
    </div>

    <style>
        .release-calendar-page {
            padding: 20px 0;
        }
        
        /* Article Action Buttons - Like and Comment */
        .article-action-btn {
            padding: 3px 10px;
            border-radius: 6px;
            transition: all 0.3s ease;
            background: linear-gradient(90deg, rgba(0, 0, 0, 1) 0%, rgba(122, 0, 122, 1) 60%, rgba(75, 0, 75, 1) 100%) !important;
            font-size: 12px;
        }

        .article-action-btn:hover {
            background: linear-gradient(90deg, rgba(0, 0, 0, 1) 0%, rgba(150, 0, 150, 1) 60%, rgba(100, 0, 100, 1) 100%) !important;
            transform: translateY(-2px);
        }

        .comment-link,
        .comment-link i,
        .comment-link span {
            color: #fff !important;
            font-size: 12px;
        }

        .news-details-img {
            position: relative;
            overflow: hidden;
            border-radius: 10px;
        }
        
        .hero-image {
            width: 100%;
            height: auto;
            object-fit: cover;
            min-height: 400px;
        }
        
        @media (max-width: 768px) {
            .overlay-text {
                font-size: 2rem !important;
            }
            .hero-image {
                min-height: 300px;
            }
        }
        
        /* Month/Year heading bar – solid dark purple background, white text (like client screenshot) */
        .release-month-divider {
            background: #3d2e5c;
            margin-top: 1.75rem;
            margin-bottom: 0.75rem;
            padding: 0.75rem 1rem;
            width: 100%;
            border-radius: 4px;
        }
        .release-month-divider:first-of-type {
            margin-top: 1rem;
        }
        .month-heading {
            font-size: 1.65rem;
            font-weight: 700;
            color: #ffffff !important;
            margin: 0;
            letter-spacing: 0.02em;
        }
        .dark-mode .release-month-divider {
            background: #4a3a6a;
        }
        .dark-mode .month-heading {
            color: #ffffff !important;
        }
        
        .release-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            padding: 10px 0;
        }
        
        .dark-mode .release-item.border-bottom {
            border-color: #4a4a4a !important;
        }
        
        .release-date {
            min-width: 120px;
            font-weight: 600;
            color: #666;
        }
        
        .dark-mode .release-date {
            color: #d0d0d0 !important;
        }
        
        /* Link: purple and hover. No-link: normal body text, no hover */
        a.game-name {
            color: #B051B0;
            font-size: 1.1rem;
            font-weight: bold;
        }
        a.game-name:hover {
            color: #8a3d8a;
            text-decoration: underline !important;
        }
        .dark-mode a.game-name:hover {
            color: #d47dd4 !important;
        }
        .release-game-name-plain {
            font-size: 1.1rem;
            font-weight: bold;
            color: inherit;
            cursor: default;
        }
        .release-game-name-plain:hover {
            color: inherit !important;
            text-decoration: none !important;
        }
        .dark-mode .release-game-name-plain {
            color: #e0e0e0 !important;
        }
        .dark-mode .release-game-name-plain:hover {
            color: #e0e0e0 !important;
        }

        .wishlist-toggle {
            cursor: pointer;
            font-size: 0.95rem;
            color: #B051B0;
        }
        .wishlist-toggle:hover {
            text-decoration: underline;
        }
        .wishlist-toggle.wishlist-on {
            color: #e67e22;
        }
        .dark-mode .wishlist-toggle {
            color: #d47dd4;
        }
        .dark-mode .wishlist-toggle.wishlist-on {
            color: #f39c12;
        }

        .wishlist-login-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 9998;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s, visibility 0.2s;
        }
        .wishlist-login-overlay.show {
            opacity: 1;
            visibility: visible;
        }
        .wishlist-login-modal {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            max-width: 360px;
            width: 100%;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
            text-align: center;
            transform: scale(0.95);
            transition: transform 0.2s;
        }
        .wishlist-login-overlay.show .wishlist-login-modal {
            transform: scale(1);
        }
        .dark-mode .wishlist-login-modal {
            background: #252525;
            border: 1px solid #444;
        }
        .wishlist-login-modal .wishlist-login-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 12px;
        }
        .dark-mode .wishlist-login-modal .wishlist-login-title { color: #e5e5e5; }
        .wishlist-login-modal .wishlist-login-msg {
            font-size: 0.95rem;
            color: #555;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        .dark-mode .wishlist-login-modal .wishlist-login-msg { color: #b0b0b0; }
        .wishlist-login-modal .wishlist-login-btn {
            display: inline-block;
            background: #B051B0;
            color: #fff !important;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
            transition: background 0.2s;
        }
        .wishlist-login-modal .wishlist-login-btn:hover {
            background: #8a3d8a;
            color: #fff !important;
        }
        .wishlist-login-modal .wishlist-login-close {
            margin-top: 12px;
            font-size: 0.85rem;
            color: #888;
            cursor: pointer;
            background: none;
            border: none;
        }
        .dark-mode .wishlist-login-modal .wishlist-login-close { color: #999; }

        .wishlist-info-popup {
            position: fixed;
            bottom: 20px;
            right: 20px;
            max-width: 380px;
            z-index: 9997;
            transform: translateX(120%);
            opacity: 0;
            transition: transform 0.35s ease-out, opacity 0.35s ease-out;
        }
        .wishlist-info-popup.show {
            transform: translateX(0);
            opacity: 1;
        }
        .wishlist-info-popup-inner {
            background: #fff;
            border-radius: 12px;
            padding: 20px 44px 20px 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.25);
            border: 1px solid rgba(176, 81, 176, 0.3);
            position: relative;
        }
        .dark-mode .wishlist-info-popup-inner {
            background: #252525;
            border-color: #444;
        }
        .wishlist-info-popup-close {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 28px;
            height: 28px;
            border: none;
            background: #eee;
            color: #333;
            font-size: 1.4rem;
            line-height: 1;
            cursor: pointer;
            border-radius: 6px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .wishlist-info-popup-close:hover { background: #ddd; }
        .dark-mode .wishlist-info-popup-close {
            background: #444;
            color: #eee;
        }
        .wishlist-info-popup-content {
            font-size: 0.9rem;
            line-height: 1.55;
            color: #444;
        }
        .dark-mode .wishlist-info-popup-content { color: #d0d0d0; }
        
        .platform-badge, .subscription-badge {
            display: inline-block;
            padding: 2px 8px;
            margin: 2px;
            border-radius: 3px;
            font-size: 0.9rem;
        }
        
        .platform-badge:not(.release-badge-inline), .subscription-badge:not(.release-badge-inline) {
            background-color: #f0f0f0;
            color: #333;
        }
        
        .subscription-badge:not(.release-badge-inline) {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        
        .dark-mode .platform-badge:not(.release-badge-inline) {
            background-color: #4a4a4a !important;
            color: #e0e0e0 !important;
        }
        
        .dark-mode .subscription-badge:not(.release-badge-inline) {
            background-color: #2a4a6a !important;
            color: #90caf9 !important;
        }
        
        .section-title {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #333;
        }
        
        .dark-mode .section-title {
            color: #e0e0e0 !important;
        }
        
        .platforms-label {
            color: #666;
        }
        
        .dark-mode .platforms-label {
            color: #d0d0d0 !important;
        }
        
        .gamepad-icon {
            font-size: 1.5rem;
        }

        .h-40px {
            height: 40px;
        }

        .image-circle {
            border-radius: 50%;
        }

        .profile-link {
            text-decoration: none;
        }

        .profile-link:hover h5 {
            color: #B051B0;
        }
    </style>

@if($setting && !empty(trim($setting->wishlist_info ?? '')))
<div id="wishlistInfoPopup" class="wishlist-info-popup" role="dialog" aria-label="{{ __('messages.wishlist.my_wishlist') }}">
    <div class="wishlist-info-popup-inner">
        <button type="button" class="wishlist-info-popup-close" id="wishlistInfoPopupClose" aria-label="{{ __('messages.common.cancel') }}">&times;</button>
        <div class="wishlist-info-popup-content">
            {!! nl2br(e($setting->wishlist_info)) !!}
        </div>
    </div>
</div>
@endif

<div id="wishlistLoginModal" class="wishlist-login-overlay" aria-hidden="true">
    <div class="wishlist-login-modal">
        <div class="wishlist-login-title">{{ __('messages.wishlist.login_required_title') ?? 'Anmeldung erforderlich' }}</div>
        <p class="wishlist-login-msg" id="wishlistLoginMsg">{{ __('messages.wishlist.login_required') }}</p>
        <a href="{{ url('login') }}" class="wishlist-login-btn">{{ __('messages.wishlist.go_to_login') ?? 'Zum Login' }}</a>
        <button type="button" class="wishlist-login-close" id="wishlistLoginClose">{{ __('messages.common.cancel') ?? 'Abbrechen' }}</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var loginRequiredMsg = "{{ __('messages.wishlist.login_required') }}";
    var isAuth = {{ auth()->check() ? 'true' : 'false' }};
    var modal = document.getElementById('wishlistLoginModal');
    var msgEl = document.getElementById('wishlistLoginMsg');
    var closeBtn = document.getElementById('wishlistLoginClose');

    function showWishlistLoginPopup(message) {
        if (msgEl) msgEl.textContent = message || loginRequiredMsg;
        if (modal) {
            modal.classList.add('show');
            modal.setAttribute('aria-hidden', 'false');
        }
    }
    function hideWishlistLoginPopup() {
        if (modal) {
            modal.classList.remove('show');
            modal.setAttribute('aria-hidden', 'true');
        }
    }
    if (closeBtn) closeBtn.addEventListener('click', hideWishlistLoginPopup);
    if (modal) modal.addEventListener('click', function(e) {
        if (e.target === modal) hideWishlistLoginPopup();
    });

    var wishlistInfoPopup = document.getElementById('wishlistInfoPopup');
    if (wishlistInfoPopup) {
        var wishlistInfoClose = document.getElementById('wishlistInfoPopupClose');
        function showWishlistInfoPopup() {
            wishlistInfoPopup.classList.add('show');
        }
        function hideWishlistInfoPopup() {
            wishlistInfoPopup.classList.remove('show');
        }
        setTimeout(showWishlistInfoPopup, 400);
        if (wishlistInfoClose) wishlistInfoClose.addEventListener('click', hideWishlistInfoPopup);
        setTimeout(hideWishlistInfoPopup, 30400);
    }

    document.querySelectorAll('.wishlist-toggle').forEach(function(el) {
        el.addEventListener('click', function() {
            if (!isAuth) {
                showWishlistLoginPopup(loginRequiredMsg);
                return;
            }
            var gameId = this.getAttribute('data-game-id');
            var toggle = this;
            fetch('{{ route("wishlist.toggle") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ game_release_id: gameId, _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') })
            }).then(function(r) { return r.json(); }).then(function(data) {
                if (data.require_login) {
                    showWishlistLoginPopup(data.message || loginRequiredMsg);
                    return;
                }
                if (data.success) {
                    toggle.setAttribute('data-on-wishlist', data.on_wishlist ? '1' : '0');
                    toggle.textContent = data.on_wishlist ? '– Wunschliste' : '+ Wunschliste';
                    toggle.classList.toggle('wishlist-on', data.on_wishlist);
                }
            });
        });
    });
});
</script>
@if($setting)
<script>
    var itemType = "release_list";
    var itemId = {{ $setting->id }};
    var _token = $('meta[name="csrf-token"]').attr('content');
    var currentSort = 'newest'; // Default sort
    
    $(document).ready(function() {
        // Initialize emoji picker for comment textarea
        if (typeof initEmojiPicker === 'function') {
            initEmojiPicker('#comment');
        }
        
        // Load initial comments
        loadComments(1, currentSort);
    });

    // Function to load comments for a given page
    function loadComments(page, sort = 'newest') {
        $.ajax({
            url: "{{ route('comments.page') }}",
            method: "POST",
            data: {
                _token: _token,
                page: page,
                item_type: itemType,
                item_id: itemId,
                sort: sort
            },
            success: function(data) {
                // Update comments HTML
                $('#blog-post-comment-body').html(data.comments_html);

                // Update comment count
                if (data.total_comments > 0) {
                    $('.count-data').text(data.total_comments);
                    $('.comment-count-display').text(data.total_comments);
                    $('.comment-data').removeClass('d-none');
                } else {
                    $('.comment-count-display').text('0');
                }

                // Build pagination if needed
                if (data.total_comments > 25) {
                    buildPagination(data.pagination.current_page, data.pagination.last_page);
                }

                // Scroll to specific comment if hash is present in URL
                const hash = window.location.hash;
                if (hash && hash.startsWith('#comment-')) {
                    let attempt = 0;
                    const maxAttempts = 20;
                    const interval = setInterval(() => {
                        const target = document.querySelector(hash);
                        if (target) {
                            target.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                            target.classList.add('highlight-comment');
                            setTimeout(() => {
                                target.classList.remove('highlight-comment');
                            }, 3000);
                            clearInterval(interval);
                        }
                        attempt++;
                        if (attempt >= maxAttempts) {
                            clearInterval(interval);
                        }
                    }, 300);
                }
            },
            error: function(xhr) {
                console.error('Error loading comments:', xhr);
            }
        });
    }

    // Pagination function
    function buildPagination(currentPage, lastPage) {
        let paginationHtml = '<div class="pagination-wrapper mt-3 text-center">';
        
        if (currentPage > 1) {
            paginationHtml += `<button class="btn btn-sm btn-outline-primary me-2" onclick="loadComments(${currentPage - 1}, currentSort)">Previous</button>`;
        }
        
        paginationHtml += `<span class="mx-2">Page ${currentPage} of ${lastPage}</span>`;
        
        if (currentPage < lastPage) {
            paginationHtml += `<button class="btn btn-sm btn-outline-primary ms-2" onclick="loadComments(${currentPage + 1}, currentSort)">Next</button>`;
        }
        
        paginationHtml += '</div>';
        
        // Remove existing pagination and add new one
        $('.pagination-wrapper').remove();
        $('#blog-post-comment-body').after(paginationHtml);
    }
</script>
@endif

@endsection
