@extends('front_new.layouts.app')
@section('title')
    {{ $settings->page_title ?? __('messages.other_lang.user_ranking') }} - {{ getSEOTools()->meta_title ?? __('messages.details.home') }}
@endsection

@push('css')
    <style>
        /* Match Article Details (like/comment/readtime row) */
        .news-details-img {
            aspect-ratio: 16 / 9;
            min-height: 400px;
            max-width: 100%;
            width: 100%;
            box-sizing: border-box;
            background-color: #1f1f1f;
            display: block;
            border-radius: 14px;
            overflow: hidden;
        }
        .news-details-img img {
            object-fit: cover;
            width: 100%;
            height: 100%;
            max-width: 100%;
            display: block;
        }
        @media (max-width: 768px) {
            .ranking-page-container {
                overflow-x: hidden;
            }
            .ranking-page-container .news-details,
            .ranking-page-container .news-details-img {
                max-width: 100%;
            }
            .news-details-img {
                min-height: 280px;
                max-width: 100%;
                width: 100%;
            }
        }

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
        /* Like button (match article-details look, but avoid double JS handlers) */
        .ranking-like-btn i,
        .ranking-like-btn .like-count {
            color: #fff !important;
            font-size: 12px;
        }
        .ranking-like-btn i.fa-thumbs-up {
            font-size: 12px;
            color: #fff !important;
        }
        /* Ensure thumb icon is always visible in both themes */
        .ranking-like-btn i.fa-thumbs-up,
        .ranking-like-btn i.fa,
        #rankingLikeBtn i.fa-thumbs-up,
        #rankingLikeBtn i.fa {
            color: #fff !important;
            fill: #fff !important;
        }
        .light-mode .ranking-like-btn i.fa-thumbs-up,
        .light-mode .ranking-like-btn i.fa,
        .light-mode #rankingLikeBtn i.fa-thumbs-up,
        .light-mode #rankingLikeBtn i.fa {
            color: #fff !important;
            fill: #fff !important;
        }
        /* When liked, show purple */
        .ranking-like-btn i.fa-thumbs-up[style*="#B051B0"],
        #rankingLikeBtn i.fa-thumbs-up[style*="#B051B0"] {
            color: #B051B0 !important;
            fill: #B051B0 !important;
        }
        .comment-btn-wrapper .comment-link,
        .comment-btn-wrapper .comment-link i,
        .comment-btn-wrapper .comment-link span {
            color: #fff !important;
        }
        .comment-link i.fa-comments,
        .comment-link i.fa-solid.fa-comments {
            font-size: inherit;
        }
        .comment-link span {
            font-size: inherit;
        }

        /* Keep your ranking table styling */
        .ranking-section-title {
            font-size: 20px;
            font-weight: 900;
            color: #111;
            margin-top: 22px;
            margin-bottom: 12px;
        }
        .dark-mode .ranking-section-title { color: #fff; }
        .ranking-table-wrap {
            /* background: #1f1f1f; */
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.06);
            overflow: hidden;
        }
        .ranking-table-wrap table {
            margin: 0;
            color: rgba(255, 255, 255, 0.85);
        }
        .ranking-table-wrap thead th {
            background: rgba(255, 255, 255, 0.04);
            color: rgba(255, 255, 255, 0.7);
            font-weight: 800;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }
        .ranking-table-wrap tbody td {
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }
        .ranking-player {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .ranking-player img {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.12);
        }

        /* Light theme fixes: ensure table/text visible if theme forces light backgrounds */
        .light-mode .ranking-table-wrap {
            /* background: #fff !important; */
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
        }
        .light-mode .ranking-table-wrap table {
            color: rgba(0, 0, 0, 0.85) !important;
        }
        .light-mode .ranking-table-wrap thead th {
            background: rgba(0, 0, 0, 0.03) !important;
            color: rgba(0, 0, 0, 0.65) !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08) !important;
        }
        .light-mode .ranking-table-wrap tbody td {
            border-top: 1px solid rgba(0, 0, 0, 0.08) !important;
        }
        .light-mode .ranking-player a {
            color: rgba(0, 0, 0, 0.85) !important;
        }
        /* See-all pagination: single row, no stacking */
        .ranking-pagination-wrap {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }
        .ranking-pagination-wrap nav {
            display: block;
        }
        .ranking-pagination-wrap .pagination {
            display: flex !important;
            flex-wrap: nowrap;
            justify-content: center;
            align-items: center;
            gap: 2px;
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .ranking-pagination-wrap .pagination .page-item {
            display: inline-block;
        }
        .ranking-pagination-wrap .pagination .page-link {
            display: inline-block;
            padding: 8px 14px;
            min-width: 42px;
            text-align: center;
            border-radius: 8px;
            text-decoration: none;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.06);
            color: rgba(255, 255, 255, 0.9);
            transition: background 0.2s, border-color 0.2s;
        }
        .ranking-pagination-wrap .pagination .page-link:hover {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.25);
            color: #fff;
        }
        .ranking-pagination-wrap .pagination .page-item.active .page-link {
            background: #B051B0;
            border-color: #B051B0;
            color: #fff;
        }
        .ranking-pagination-wrap .pagination .page-item.disabled .page-link {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .light-mode .ranking-pagination-wrap .pagination .page-link {
            border-color: rgba(0, 0, 0, 0.15);
            background: rgba(0, 0, 0, 0.04);
            color: rgba(0, 0, 0, 0.85);
        }
        .light-mode .ranking-pagination-wrap .pagination .page-link:hover {
            background: rgba(0, 0, 0, 0.08);
            border-color: rgba(0, 0, 0, 0.2);
        }
        .light-mode .ranking-pagination-wrap .pagination .page-item.active .page-link {
            background: #B051B0;
            border-color: #B051B0;
            color: #fff;
        }
        /* Footer under ranking tables (prevents button clipping on edges) */
        .ranking-table-footer {
            padding: 10px 14px 14px 14px;
        }
        .ranking-table-footer .btn {
            display: inline-block;
        }
    </style>
@endpush

@section('content')
@php
    $creator = $settings->creator ?? null;
    $creatorAvatar = $creator?->profile_image ?? asset('assets/image/avatar.png');
    $creatorName = $creator?->full_name ?? $creator?->username ?? '2Playerz';
    $settingsDate = $settings->updated_at ?? $settings->created_at ?? now();
    $readingText = $settings->points_rules_content ?: ($settings->header_description ?: ($settings->page_subtitle ?? ''));
    $headerImageUrl = $settings->header_image ? Storage::url($settings->header_image) : asset('assets/image/playerz-ranking-header.png');
@endphp

<div class="container py-4 ranking-page-container">
    <div class="row">
        <div class="col-lg-8">
            <div class="news-details">
                <h3 class="text-black fw-7 fs-24 my-2">
                    {!! $settings->page_title ?? __('messages.other_lang.user_ranking') !!}
                </h3>
                <div class="post-content">
                    <p class="text-gray">
                        {!! $settings->header_description ?? $settings->page_subtitle ?? '' !!}
                    </p>
                </div>

                <div class="d-md-flex mb-2">
                    <div class="d-flex align-items-center" style="flex: 1;">
                        <div style="flex: 1;">
                            <div class="d-flex">
                                <div class="">
                                    @if($creator)
                                        <a href="{{ route('user.public.profile', $creator->username ?? $creator->id) }}"
                                           class="profile-link"
                                           data-user-identifier="{{ $creator->username ?? $creator->id }}">
                                            <img src="{{ $creatorAvatar }}" alt=""
                                                 class="h-40px {{ getFrontSelectLanguageIsoCode() == 'ar' ? 'ms-2' : 'me-2' }} image image-circle"
                                                 width="40">
                                        </a>
                                    @else
                                        <img src="{{ $creatorAvatar }}" alt=""
                                             class="h-40px {{ getFrontSelectLanguageIsoCode() == 'ar' ? 'ms-2' : 'me-2' }} image image-circle"
                                             width="40">
                                    @endif
                                </div>
                                <div class="d-flex justify-content-start flex-column">
                                    @if($creator)
                                        <a href="{{ route('user.public.profile', $creator->username ?? $creator->id) }}"
                                           class="profile-link"
                                           data-user-identifier="{{ $creator->username ?? $creator->id }}">
                                            <h5 class="fs-12 text-black mb-0">{{ $creatorName }}</h5>
                                            <span class="fs-12 text-gray">
                                                {{ $settingsDate->format('d') }}. {{ ucfirst(__('messages.common.' . strtolower($settingsDate->format('F')))) }} {{ $settingsDate->format('Y') }}
                                            </span>
                                        </a>
                                    @else
                                        <h5 class="fs-12 text-black mb-0">{{ $creatorName }}</h5>
                                        <span class="fs-12 text-gray">
                                            {{ $settingsDate->format('d') }}. {{ ucfirst(__('messages.common.' . strtolower($settingsDate->format('F')))) }} {{ $settingsDate->format('Y') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div style="flex: 1;">
                            <div class="news-text mb-2 d-flex align-items-center" style="gap: 10px">
                                <div class="desc d-flex align-items-center ranking-like-btn article-action-btn"
                                     id="rankingLikeBtn"
                                     style="cursor: pointer; position: relative;gap: 5px;"
                                     data-id="{{ $pageItemId }}"
                                     data-type="{{ $pageItemType }}"
                                     data-auth="{{ auth()->check() ? '1' : '0' }}"
                                     title="{{ __('messages.comment.like_article') }}">
                                    <i class="fa fa-thumbs-up" id="current_like_icon_page_{{ $pageItemId }}"
                                       style="transition: color 0.3s ease; color: #fff !important; fill: #fff !important; {{ !empty($pageUserLiked) && $pageUserLiked ? 'color: #B051B0 !important; fill: #B051B0 !important;' : '' }}"></i>
                                    <span>
                                        <span style="transition: color 0.3s ease;" class="like-count" id="rankingLikesCount">{{ (int) ($pageLikesCount ?? 0) }}</span>
                                    </span>
                                </div>

                                <div class="desc d-inline-block article-action-btn comment-btn-wrapper"
                                     style="position: relative;"
                                     title="{{ __('messages.comment.comment_article') }}">
                                    <a href="#commentFormSection" class="comment-link d-flex align-items-center gap-1"
                                       style="text-decoration: none; transition: color 0.3s ease;">
                                        <i class="fa-solid fa-comments me-1" style="transition: color 0.3s ease;"></i>
                                        <span class="me-1" style="transition: color 0.3s ease;" id="rankingCommentsCount">{{ (int) ($pageCommentsCount ?? 0) }}</span>
                                    </a>
                                </div>

                                <div class="desc d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-clock fs-12 text-gray me-1"></i>
                                    <span class="fs-14 text-gray me-1">{{ getReadingTime($readingText) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="news-details-img">
                    <img src="{{ $headerImageUrl }}" alt="{{ $settings->page_title ?? 'Playerz Ranking' }}">
                </div>
            </div>

            <div id="commentFormSection"></div>

            @if(empty($showType))
                {{-- Top-10 by Playerz Points (shown directly under the header image) --}}
                <div class="ranking-section-title mt-3">{{ __('messages.other_lang.top_players_by_points') }}</div>
                <div class="ranking-table-wrap">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 90px;">Platz</th>
                                    <th>Name</th>
                                    <th style="width: 140px;">Level</th>
                                    <th style="width: 120px;" class="text-end">Punkte</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topPlayers as $index => $player)
                                    @php
                                        $levelObj = $player->level_object;
                                        $badgeBgColor = ($levelObj && !empty($levelObj->badge_color)) ? $levelObj->badge_color : '#734E96';
                                        $badgeTextColor = ($levelObj && !empty($levelObj->badge_text_color)) ? $levelObj->badge_text_color : '#ffffff';
                                        $levelName = $player->level ?? 'Newbie';
                                    @endphp
                                    <tr>
                                        <td class="fw-bold">{{ $index + 1 }}.</td>
                                        <td>
                                            <div class="ranking-player">
                                                <a href="{{ route('user.public.profile', $player->username ?? $player->id) }}">
                                                    <img src="{{ $player->profile_image ?? asset('assets/image/avatar.png') }}" alt="{{ $player->username }}">
                                                </a>
                                                <a href="{{ route('user.public.profile', $player->username ?? $player->id) }}" style="font-weight: 800;">{{ $player->username }}</a>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge" style="background-color: {{ $badgeBgColor }}; color: {{ $badgeTextColor }}; padding: 6px 10px; border-radius: 8px; font-size: 12px; font-weight: 800;">{{ $levelName }}</span>
                                        </td>
                                        <td class="text-end fw-bold">{{ number_format($player->comment_points ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center py-4">{{ __('messages.other_lang.no_ranking_data') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="ranking-table-footer">
                        <a href="{{ url()->current() }}?show=points" class="btn btn-sm btn-outline-primary">{{ __('messages.other_lang.see_all_users') }}</a>
                    </div>
                </div>

                {{-- Text (points rules) --}}
                @if(!empty($settings->points_rules_content))
                    <div class="mt-4">
                        {!! $settings->points_rules_content !!}
                    </div>
                @endif

                {{-- Top-10 Commenting Users --}}
                <div class="ranking-section-title mt-4">{{ __('messages.other_lang.top_commenting_users') }}</div>
                <div class="ranking-table-wrap">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 90px;">Platz</th>
                                    <th>Name</th>
                                    <th style="width: 120px;" class="text-end">Kommentare</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topCommentingUsers ?? [] as $index => $row)
                                    <tr>
                                        <td class="fw-bold">{{ $index + 1 }}.</td>
                                        <td>
                                            <div class="ranking-player">
                                                @php $u = $row->user ?? null; @endphp
                                                @if($u)
                                                    <a href="{{ route('user.public.profile', $u->username ?? $u->id) }}">
                                                        <img src="{{ $u->profile_image ?? asset('assets/image/avatar.png') }}" alt="{{ $u->username }}">
                                                    </a>
                                                    <a href="{{ route('user.public.profile', $u->username ?? $u->id) }}" style="font-weight: 800;">{{ $u->username }}</a>
                                                @else
                                                    -
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold">{{ number_format($row->comments_count ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center py-4">{{ __('messages.other_lang.no_ranking_data') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="ranking-table-footer">
                        <a href="{{ url()->current() }}?show=commenting" class="btn btn-sm btn-outline-primary">{{ __('messages.other_lang.see_all_users') }}</a>
                    </div>
                </div>

                {{-- Most Active Users (likes + comments + articles) --}}
                <div class="ranking-section-title mt-4">{{ __('messages.other_lang.most_active_users') }}</div>
                <div class="ranking-table-wrap">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 90px;">Platz</th>
                                    <th>Name</th>
                                    <th style="width: 120px;" class="text-end">Likes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mostActive ?? [] as $index => $user)
                                    <tr>
                                        <td class="fw-bold">{{ $index + 1 }}.</td>
                                        <td>
                                            <div class="ranking-player">
                                                <a href="{{ route('user.public.profile', $user->username ?? $user->id) }}">
                                                    <img src="{{ $user->profile_image ?? asset('assets/image/avatar.png') }}" alt="{{ $user->username }}">
                                                </a>
                                                <a href="{{ route('user.public.profile', $user->username ?? $user->id) }}" style="font-weight: 800;">{{ $user->username }}</a>
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold">{{ number_format($user->likes_given ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center py-4">{{ __('messages.other_lang.no_ranking_data') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="ranking-table-footer">
                        <a href="{{ url()->current() }}?show=active" class="btn btn-sm btn-outline-primary">{{ __('messages.other_lang.see_all_users') }}</a>
                    </div>
                </div>

                {{-- Users with most likes on their comments --}}
                <div class="ranking-section-title mt-4">{{ __('messages.other_lang.most_liked_comment_authors') }}</div>
                <div class="ranking-table-wrap">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 90px;">Platz</th>
                                    <th>Name</th>
                                    <th style="width: 140px;" class="text-end">Likes (Kommentare)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mostLikedCommentAuthors ?? [] as $index => $row)
                                    <tr>
                                        <td class="fw-bold">{{ $index + 1 }}.</td>
                                        <td>
                                            <div class="ranking-player">
                                                @php $u = $row->user ?? null; @endphp
                                                @if($u)
                                                    <a href="{{ route('user.public.profile', $u->username ?? $u->id) }}">
                                                        <img src="{{ $u->profile_image ?? asset('assets/image/avatar.png') }}" alt="{{ $u->username }}">
                                                    </a>
                                                    <a href="{{ route('user.public.profile', $u->username ?? $u->id) }}" style="font-weight: 800;">{{ $u->username }}</a>
                                                @else
                                                    -
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold">{{ number_format($row->comment_likes_count ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center py-4">{{ __('messages.other_lang.no_ranking_data') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="ranking-table-footer">
                        <a href="{{ url()->current() }}?show=comment-likes" class="btn btn-sm btn-outline-primary">{{ __('messages.other_lang.see_all_users') }}</a>
                    </div>
                </div>
            @else
                {{-- See-all view: full paginated list for selected ranking type --}}
                <div class="ranking-section-title">
                    @if($showType === 'commenting')
                        {{ __('messages.other_lang.top_commenting_users') }} – {{ __('messages.other_lang.see_all_users') }}
                    @elseif($showType === 'points')
                        {{ __('messages.other_lang.top_players_by_points') }} – {{ __('messages.other_lang.see_all_users') }}
                    @elseif($showType === 'active')
                        {{ __('messages.other_lang.most_active_users') }} – {{ __('messages.other_lang.see_all_users') }}
                    @else
                        {{ __('messages.other_lang.most_liked_comment_authors') }} – {{ __('messages.other_lang.see_all_users') }}
                    @endif
                </div>
                <p class="mb-2"><a href="{{ url()->current() }}" class="btn btn-sm btn-outline-secondary">&larr; {{ __('messages.other_lang.back_to_ranking') }}</a></p>
                @if($seeAllPaginator && $seeAllPaginator->count() > 0)
                    <div class="ranking-table-wrap">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th style="width: 90px;">Platz</th>
                                        <th>Name</th>
                                        @if($showType === 'points')
                                            <th style="width: 140px;">Level</th>
                                            <th style="width: 120px;" class="text-end">Punkte</th>
                                        @elseif($showType === 'commenting')
                                            <th style="width: 120px;" class="text-end">Kommentare</th>
                                        @elseif($showType === 'active')
                                            <th style="width: 120px;" class="text-end">Likes</th>
                                        @else
                                            <th style="width: 140px;" class="text-end">Likes (Kommentare)</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($seeAllPaginator as $index => $user)
                                        @php
                                            $rank = $seeAllPaginator->firstItem() + $index;
                                            $levelObj = $user->level_object ?? null;
                                            $badgeBgColor = ($levelObj && !empty($levelObj->badge_color)) ? $levelObj->badge_color : '#734E96';
                                            $badgeTextColor = ($levelObj && !empty($levelObj->badge_text_color)) ? $levelObj->badge_text_color : '#ffffff';
                                        @endphp
                                        <tr>
                                            <td class="fw-bold">{{ $rank }}.</td>
                                            <td>
                                                <div class="ranking-player">
                                                    <a href="{{ route('user.public.profile', $user->username ?? $user->id) }}">
                                                        <img src="{{ $user->profile_image ?? asset('assets/image/avatar.png') }}" alt="{{ $user->username }}">
                                                    </a>
                                                    <a href="{{ route('user.public.profile', $user->username ?? $user->id) }}" style="font-weight: 800;">{{ $user->username }}</a>
                                                </div>
                                            </td>
                                            @if($showType === 'points')
                                                <td><span class="badge" style="background-color: {{ $badgeBgColor }}; color: {{ $badgeTextColor }}; padding: 6px 10px; border-radius: 8px; font-size: 12px; font-weight: 800;">{{ $user->level ?? 'Newbie' }}</span></td>
                                                <td class="text-end fw-bold">{{ number_format($user->comment_points ?? 0, 0, ',', '.') }}</td>
                                            @elseif($showType === 'commenting')
                                                <td class="text-end fw-bold">{{ number_format($user->comments_count ?? 0, 0, ',', '.') }}</td>
                                            @elseif($showType === 'active')
                                                <td class="text-end fw-bold">{{ number_format($user->likes_given ?? 0, 0, ',', '.') }}</td>
                                            @else
                                                <td class="text-end fw-bold">{{ number_format($user->comment_likes_count ?? 0, 0, ',', '.') }}</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 ranking-pagination-wrap">
                            {{ $seeAllPaginator->withQueryString()->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                @else
                    <p class="text-muted">{{ __('messages.other_lang.no_ranking_data') }}</p>
                @endif
            @endif

            <!-- Comments Section -->
            <section class="comment-section mt-5 pt-3 blog-post-comment-view mb-3" id="blog-post-comment-view-section">
                <div class="d-flex justify-content-between align-items-center mb-3 comment-data @if (empty($pageCommentsCount)) d-none @endif">
                    <h5 class="fs-18 text-black fw-7 mb-0">
                        {{ __('messages.comment.comments') }} 
                        <span class="count-data">{{ $pageCommentsCount ?? 0 }}</span>
                    </h5>
                    <div class="dropdown">
                        <button class="btn btn-primary btn-outline-secondary dropdown-toggle" type="button" id="commentFilterBtn" data-bs-toggle="dropdown" aria-expanded="false">
                            <span id="filterText">{{ __('messages.comment.newest') }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="commentFilterBtn">
                            <li><a class="dropdown-item filter-option active" href="javascript:void(0)" data-sort="newest">{{ __('messages.comment.newest') }}</a></li>
                            <li><a class="dropdown-item filter-option" href="javascript:void(0)" data-sort="oldest">{{ __('messages.comment.oldest') }}</a></li>
                            <li><a class="dropdown-item filter-option" href="javascript:void(0)" data-sort="top">{{ __('messages.comment.top') }}</a></li>
                        </ul>
                    </div>
                </div>

                @php
                    $settings = getSettingValue();
                    $showCaptcha = $settings['show_captcha'] ?? '0';
                @endphp

                <section class="post-comment-section bg-light px-30 py-4 mb-5" style="box-shadow: 0px 0px 10px #00000020; border-radius: 15px;" id="commentFormSection">
                    <h5 class="fs-16 text-black fw-6 mb-3">{{ __('messages.comment.post_a_comment') }}</h5>
                    <form id="commentForm">
                        @csrf
                        <input type="hidden" name="item_type" value="{{ $pageItemType }}">
                        <input type="hidden" name="item_id" value="{{ $pageItemId }}">
                        <input type="hidden" name="user_id" value="{{ isset(getLogInUser()->id) ? getLogInUser()->id : null }}">
                        <div class="row">
                            @if (!Auth::check())
                            <div class="col-md-12">
                                <a class="btn btn-primary" href="{{url('/admin/login')}}">Zum kommentieren einloggen</a>
                            </div>
                            @else
                            <div class="col-12">
                                <p class="lead emoji-picker-container">
                                    <textarea class="form-control textarea-control fs-14 text-gray" name="comment"
                                        id="comment" style="color:rgb(123, 123, 123) !important" rows="3"
                                        placeholder="{{ __('messages.comment.type_your_comments') }}"
                                        data-meteor-emoji="true" required></textarea>
                                </p>
                            </div>
                            <div class="col-12 mb-2">
                                @if ($showCaptcha == '1')
                                <input type="hidden" value="{{ $showCaptcha }}" id="googleCaptch">
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
                $style = '"overflow-y: auto;"';
                @endphp
                <div id="blog-post-comment-body" class="comment-view" {!! ($pageCommentsCount ?? 0) >= 3 ? $inStyle . $style : '' !!}>
                    <!-- Comments will appear here -->
                </div>
                <nav class="mt-4" id="pagination-container"></nav>
            </section>
            <!--end comment-section -->
        </div>

        <div class="col-lg-4">
            @include('front_new.detail_pages.side-menu')
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    async function toggleRankingLike(btn) {
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        const csrf = tokenMeta ? tokenMeta.getAttribute('content') : null;
        const itemId = btn.dataset.id;
        const itemType = btn.dataset.type;

        try {
            const res = await fetch("{{ route('like-toggle') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ item_id: itemId, item_type: itemType }),
            });

            if (res.status === 401) {
                window.location.href = "{{ url('/admin/login') }}";
                return;
            }

            const data = await res.json();
            if (typeof data.likes !== 'undefined') {
                const countEl = document.getElementById('rankingLikesCount');
                if (countEl) countEl.textContent = data.likes;
            }
            if (typeof data.liked !== 'undefined') {
                const icon = document.getElementById('current_like_icon_page_{{ $pageItemId }}');
                if (icon) {
                    if (data.liked) {
                        icon.style.color = '#B051B0';
                        icon.style.fill = '#B051B0';
                    } else {
                        icon.style.color = '';
                        icon.style.fill = '';
                    }
                }
            }
        } catch (e) {
            // fail silently
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const likeBtn = document.getElementById('rankingLikeBtn');
        if (likeBtn) {
            // Prevent double like/unlike when another delegated handler exists.
            likeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();

                if (likeBtn.dataset.loading === '1') return;
                likeBtn.dataset.loading = '1';

                Promise.resolve(toggleRankingLike(likeBtn)).finally(() => {
                    likeBtn.dataset.loading = '0';
                });
            }, true);
        }
    });

    // Comments loading functionality (same as release-calendar)
    var itemType = "{{ $pageItemType }}";
    var itemId = {{ $pageItemId }};
    var _token = $('meta[name="csrf-token"]').attr('content');
    var currentSort = 'newest'; // Default sort
    
    $(document).ready(function() {
        // Initialize emoji picker for comment textarea
        if (typeof initEmojiPicker === 'function') {
            initEmojiPicker('#comment');
        }
        
        // Load initial comments
        loadComments(1, currentSort);

        // Filter dropdown click handlers
        $('.filter-option').on('click', function(e) {
            e.preventDefault();
            const sort = $(this).data('sort');
            let sortText = '{{ __('messages.comment.newest') }}';
            if (sort === 'oldest') sortText = '{{ __('messages.comment.oldest') }}';
            if (sort === 'top') sortText = '{{ __('messages.comment.top') }}';
            
            // Update active state
            $('.filter-option').removeClass('active');
            $(this).addClass('active');
            
            // Update button text
            $('#filterText').text(sortText);
            
            // Update current sort and reload comments
            currentSort = sort;
            loadComments(1, currentSort);
        });
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

                // Update comment count (always update, even if 0)
                const totalCount = data.total_comments || 0;
                $('.count-data').text(totalCount);
                $('#rankingCommentsCount').text(totalCount);
                if (totalCount > 0) {
                    $('.comment-data').removeClass('d-none');
                } else {
                    $('.comment-data').addClass('d-none');
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
@endpush