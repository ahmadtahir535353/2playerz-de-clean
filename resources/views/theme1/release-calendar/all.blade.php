@extends('theme1.layouts.app')
@section('title')
    {{ $setting->headline ?? 'Alle bisher bekannten Spiele-Releases' }}
@endsection
@section('meta_tags')
    {{ $setting->keywords ?? '' }}
@endsection
@section('meta_description')
    {{ $setting->short_description ?? '' }}
@endsection
@section('content')
    <div class="">
        <div class="container mx-auto max-w-7xl xl:px-0 lg:px-10 md:px-8 px-4">
            <nav class="text-gray-600 pt-5">
                <ol class="list-none p-0 inline-flex flex-wrap">
                    <li class="flex items-center">
                        <a href="/" class="text-gray-300 text-sm font-medium">{{ __('messages.details.home') }}</a>
                        <span class="lg:mx-3 mx-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3"
                                stroke="#606060" class="w-3.5 h-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </span>
                    </li>
                    <li class="flex items-center text-primary font-medium">
                        {{ $setting->headline ?? 'Releasekalender' }}
                    </li>
                </ol>
            </nav>

            <div class="release-calendar-page py-8">
                <!-- Page Headline -->
                @if($setting && $setting->headline)
                    <h1 class="text-3xl font-bold mb-6">{{ $setting->headline }}</h1>
                @else
                    <h1 class="text-3xl font-bold mb-6">Alle bisher bekannten Spiele-Releases</h1>
                @endif

                <!-- Page Image with optional overlay text -->
                @if($setting && $setting->getFirstMedia('release_list_image'))
                    <div class="page-image mb-6 relative rounded-lg overflow-hidden">
                        <img src="{{ $setting->getFirstMedia('release_list_image')->getUrl() }}" 
                             alt="{{ $setting->headline ?? 'Release Calendar' }}" 
                             class="w-full rounded-lg min-h-[300px] object-cover">
                        <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-b from-black/20 to-black/40">
                            <h2 class="overlay-text text-white font-bold text-3xl md:text-4xl text-center drop-shadow-lg">
                                {{ $setting->banner_title ?? 'Game Releases ' . date('Y') }}
                            </h2>
                        </div>
                    </div>
                @endif

                <!-- Short Description -->
                @if($setting && $setting->short_description)
                    <div class="short-description mb-6 text-gray-600">
                        <p>{{ $setting->short_description }}</p>
                    </div>
                @endif

                <!-- Games with Release Dates (grouped by year, then month) -->
                @if($gamesWithDates->isNotEmpty())
                    <div class="releases-with-dates mb-8">
                        @foreach($gamesWithDates as $year => $sections)
                            <h2 class="text-2xl font-semibold mb-4 mt-8 @if(!$loop->first) pt-4 @endif">Alle Releases {{ $year }}</h2>
                            
                            @foreach($sections as $section)
                                @if($section['heading'])
                                    <div class="release-month-divider bg-[#3d2e5c] dark:bg-[#4a3a6a] mt-7 py-3 px-4 rounded mb-3 first:mt-4 w-full">
                                        <h3 class="month-heading text-2xl font-bold text-white tracking-wide m-0">{{ $section['heading'] }}</h3>
                                    </div>
                                @endif
                                
                                <ul class="release-list space-y-4">
                                    @foreach($section['games'] as $game)
                                        <li class="release-item pb-4 border-b border-gray-200">
                                            <div class="flex flex-col md:flex-row gap-4">
                                                <div class="release-date min-w-[120px] font-semibold text-gray-600">
                                                    @if($game->release_date)
                                                        {{ $game->release_date->format('d.m.Y') }}
                                                    @elseif($game->release_month && $game->release_year)
                                                        {{ \Carbon\Carbon::create($game->release_year, $game->release_month, 1)->locale('de')->monthName }} {{ $game->release_year }}
                                                    @elseif($game->release_year)
                                                        {{ __('messages.release_calendar.date_tba') }}
                                                    @endif
                                                </div>
                                                <div class="release-content flex-1">
                                                    @if(!empty(trim($game->link ?? '')))
                                                        <a href="{{ $game->link }}" class="game-name text-lg font-bold text-blue-600 hover:text-blue-800 hover:underline">
                                                            {{ $game->name }}
                                                        </a>
                                                    @else
                                                        <span class="release-game-name-plain text-lg font-bold text-gray-900 dark:text-gray-100 cursor-default">{{ $game->name }}</span>
                                                    @endif
                                                    @php $bc = $badgeColors ?? []; @endphp
                                                    <div class="platforms mt-2">
                                                        @if($game->playstation || $game->xbox || $game->nintendo)
                                                            <span class="text-gray-500">(</span>
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
                                                            <span class="text-gray-500">)</span>
                                                        @endif
                                                        
                                                        @if($game->ps_plus || $game->game_pass)
                                                            <span class="text-gray-500 mx-1">-</span>
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
                    <div class="releases-without-dates mt-8 pt-6 border-t border-gray-300">
                        <h2 class="text-2xl font-semibold mb-4">{{ $setting?->date_not_fixed_label ?? 'Ohne feste Release Datum' }}</h2>
                        <ul class="release-list space-y-4">
                            @foreach($gamesWithoutDates as $game)
                                <li class="release-item pb-4 border-b border-gray-200">
                                    <div class="release-content">
                                        @if(!empty(trim($game->link ?? '')))
                                            <a href="{{ $game->link }}" class="game-name text-lg font-bold text-blue-600 hover:text-blue-800 hover:underline">
                                                {{ $game->name }}
                                            </a>
                                        @else
                                            <span class="release-game-name-plain text-lg font-bold text-gray-900 dark:text-gray-100 cursor-default">{{ $game->name }}</span>
                                        @endif
                                        @php $bc = $badgeColors ?? []; @endphp
                                        <div class="platforms mt-2">
                                            @if($game->playstation || $game->xbox || $game->nintendo)
                                                <span class="text-gray-500">(</span>
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
                                                <span class="text-gray-500">)</span>
                                            @endif
                                            
                                            @if($game->ps_plus || $game->game_pass)
                                                <span class="text-gray-500 mx-1">-</span>
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
            </div>
        </div>
    </div>

    <style>
        .platform-badge, .subscription-badge {
            display: inline-block;
            padding: 2px 8px;
            margin: 2px;
            background-color: #f0f0f0;
            border-radius: 3px;
            font-size: 0.9rem;
        }
        .subscription-badge {
            background-color: #e3f2fd;
            color: #1976d2;
        }
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
        .dark .wishlist-info-popup-inner {
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
        .dark .wishlist-info-popup-close {
            background: #444;
            color: #eee;
        }
        .wishlist-info-popup-content {
            font-size: 0.9rem;
            line-height: 1.55;
            color: #444;
        }
        .dark .wishlist-info-popup-content { color: #d0d0d0; }
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
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var el = document.getElementById('wishlistInfoPopup');
        if (!el) return;
        var closeBtn = document.getElementById('wishlistInfoPopupClose');
        function showPopup() { el.classList.add('show'); }
        function hidePopup() { el.classList.remove('show'); }
        setTimeout(showPopup, 400);
        if (closeBtn) closeBtn.addEventListener('click', hidePopup);
        setTimeout(hidePopup, 30400);
    });
    </script>
    @endif

@endsection
