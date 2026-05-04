@extends('front_new.layouts.app')
@section('title')
{!! $postDetail->title !!}
@endsection
@section('meta_image')
{{ $postDetail->post_image }}
@endsection
@section('meta_tags')
{!! $postDetail->keywords !!}
@endsection
@section('meta_description')
{!! $postDetail->description !!}
@endsection
@push('jsonld')
@include('front_new.layouts.newsarticle-jsonld')
@endpush
@section('pageCss')
{{-- <link href="{{asset('front_web/build/scss/news-details.css')}}" rel="stylesheet" type="text/css"> --}}
{{-- <link href="{{asset('front_web/css/swiper.min.css')}}" rel="stylesheet" type="text/css"> --}}

@endsection
@push('css')

<!-- End emoji-picker Stylesheets -->
@endpush
@section('content')

<style>
    iframe {
        width: 100% !important;
    }

    video {
        width: 100% !important;
    }

    table,
    th,
    td {
        border: 1px solid black;
        border-collapse: collapse;
    }

    .title-with-dot {
        position: relative;
    }

    .title-with-dot::before {
        content: "";
        position: absolute;
        left: -30px;
        top: 11%;
        width: 20px;
        height: 20px;
        background: url("{{asset('assets/image/breaking_img.png')}}") no-repeat center;
        border-radius: 50%;
    }

    .ticker {
        margin-top: 20px;
    }

    .ticker .ticker-item {
        position: relative;
    }

    .ticker .ticker-item p {
        /*opacity: 0.6;*/
    }

    .ticker .ticker-item .time {
        background: #7E026D;
        color: white;
        padding: 3px 10px;
        border-radius: 20px;
        position: absolute;
        left: -6px;
        transform: translateX(-100%);
    }

    .live-container .title {
        margin-top: 25px;
    }

    .live-container .title {
        /* font-size: 20px; */
        /* opacity: 0.7; */
    }

    .live-container iframe {
        margin: 20px 0px;
    }

    @media screen and (max-width: 1000px) {
        .ticker .ticker-item .time {
            left: 0px;
            transform: translate(0%, -100%);
            top: -7px;
        }

        .ticker .ticker-item p {
            margin-top: 50px;
        }

        .title-with-dot::before {
            left: 0px;
        }

        .title-with-dot {
            padding-left: 27px;
        }
    }
    #ticker .ticker-item p {
        color: #b042ff;
        font-weight: 600;
    }
    
    .dark-mode #ticker .ticker-item p {
        color: #dfb2ff;
        font-weight: 600;
    }
    
    .comment-section-disabled,
    .comment-section-disabled .alert {
        animation: none !important;
        transition: none !important;
        opacity: 1 !important;
        visibility: visible !important;
        display: block !important;
    }

    /* Article Action Buttons - Like and Comment Hover Effects */
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

    .like-btn i,
    .like-btn .like-count {
        color: #fff !important;
        font-size: 12px;
    }

    .like-btn i.fa-thumbs-up {
        font-size: 12px;
    }

    /* Default white color for thumb icon */
    .like-btn i.fa-thumbs-up,
    .like-btn svg.fa-thumbs-up {
        color: #fff;
    }

    .like-btn svg.svg-inline--fa {
        color: #fff;
    }

    .like-btn svg.svg-inline--fa path {
        fill: #fff;
    }

    /* When liked, JavaScript will set color to #B051B0 */
    .like-btn i.fa-thumbs-up[style*="#B051B0"],
    .like-btn svg.svg-inline--fa[style*="#B051B0"],
    .article-action-btn.like-btn i.fa-thumbs-up[style*="#B051B0"] {
        color: #B051B0 !important;
    }

    .like-btn svg.svg-inline--fa[style*="#B051B0"] path,
    .article-action-btn.like-btn svg.svg-inline--fa[style*="#B051B0"] path {
        fill: #B051B0 !important;
    }

    /* Comment icon and count size matching like button */
    .comment-link i.fa-comments,
    .comment-link i.fa-solid.fa-comments {
        font-size: inherit;
    }

    .comment-link span {
        font-size: inherit;
    }

    /* Comments section like buttons - visible in light theme */
    .comment-section .like-btn i.fa-thumbs-up:not([style*="color"]),
    .comment-section .like-btn svg.fa-thumbs-up:not([style*="color"]) {
        color: #666 !important;
    }

    .comment-section .like-btn svg.svg-inline--fa:not([style*="fill"]) path {
        fill: #666 !important;
    }
    
    /* Ensure purple color when liked (override grey) */
    .comment-section .like-btn i.fa-thumbs-up[style*="#B051B0"],
    .comment-section .like-btn svg.svg-inline--fa[style*="#B051B0"] {
        color: #B051B0 !important;
    }
    
    .comment-section .like-btn svg.svg-inline--fa[style*="#B051B0"] path {
        fill: #B051B0 !important;
    }

    /* Like count visibility in comments section */
    .comment-section .like-btn .like-count {
        color: #B051B0 !important;
        visibility: visible !important;
        display: inline !important;
        opacity: 1 !important;
    }

    /* Hover effect only for article detail page buttons, not comments section */
    .article-action-btn.like-btn:hover i,
    .article-action-btn.like-btn:hover .like-count {
        color: #fff !important;
    }

    .comment-link,
    .comment-link i,
    .comment-link span {
        color: #fff !important;
        font-size: 12px;
    }

    .comment-link i.fa-comments,
    .comment-link i.fa-solid.fa-comments {
        font-size: 12px;
    }

    .comment-link:hover,
    .comment-link:hover i,
    .comment-link:hover span {
        color: #fff !important;
    }

    .article-action-btn[data-title]:hover::after,
    .comment-btn-wrapper[data-title]:hover::after {
        content: attr(data-title);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background-color: #333;
        color: #fff;
        padding: 6px 10px;
        border-radius: 4px;
        font-size: 12px;
        white-space: nowrap;
        margin-bottom: 5px;
        z-index: 1000;
        pointer-events: none;
        opacity: 0;
        animation: fadeInTooltip 0.3s ease forwards;
    }

    .article-action-btn[data-title]:hover::before,
    .comment-btn-wrapper[data-title]:hover::before {
        content: '';
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 5px solid transparent;
        border-top-color: #333;
        margin-bottom: -5px;
        z-index: 1000;
        opacity: 0;
        animation: fadeInTooltip 0.3s ease forwards;
    }

    /* Prevent default browser tooltip - handled by JavaScript */

    @keyframes fadeInTooltip {
        from {
            opacity: 0;
            transform: translateX(-50%) translateY(5px);
        }
        to {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
    }

    /* Prevent text overflow on mobile - Break long words */
    /* Only apply to text elements, exclude link preview and images */
    .video-content font:not(.link-preview-card font):not(.link-preview-content font),
    .news-desc font:not(.link-preview-card font):not(.link-preview-content font),
    .post-content font:not(.link-preview-card font):not(.link-preview-content font),
    .video-content span:not(.link-preview-card span):not(.link-preview-content span):not(.link-preview-image-wrapper):not(.link-preview-image),
    .news-desc span:not(.link-preview-card span):not(.link-preview-content span):not(.link-preview-image-wrapper):not(.link-preview-image),
    .post-content span:not(.link-preview-card span):not(.link-preview-content span):not(.link-preview-image-wrapper):not(.link-preview-image),
    .video-content p:not(.link-preview-card p):not(.link-preview-content p),
    .news-desc p:not(.link-preview-card p):not(.link-preview-content p),
    .post-content p:not(.link-preview-card p):not(.link-preview-content p) {
        word-wrap: break-word !important;
        word-break: break-word !important;
        overflow-wrap: break-word !important;
        max-width: 100% !important;
        overflow-x: hidden !important;
    }

    /* Exclude link preview containers from text breaking */
    .link-preview-card,
    .link-preview-card *,
    .link-preview-image-wrapper,
    .link-preview-image-wrapper *,
    .link-preview-image,
    .link-preview-content {
        word-break: normal !important;
        overflow-wrap: normal !important;
        word-wrap: normal !important;
    }

    /* Screenshots gallery - neat grid, less gap (Insider Gaming style) */
    .detail-page-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 10px;
        margin-top: 12px;
    }
    .detail-page-gallery .gallery-item {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        aspect-ratio: 16 / 10;
        background: #f0f0f0;
    }
    .detail-page-gallery .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        cursor: pointer;
        display: block;
        transition: transform 0.2s ease;
    }
    .detail-page-gallery .gallery-item img:hover {
        transform: scale(1.02);
    }
    @media (max-width: 576px) {
        .detail-page-gallery {
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }
    }

    /* Mobile specific fixes */
    @media screen and (max-width: 767px) {
        .video-content:not(.link-preview-card),
        .news-desc:not(.link-preview-card),
        .post-content:not(.link-preview-card) {
            overflow-x: hidden !important;
        }

        /* Force break on any long string in text elements only */
        .video-content font:not(.link-preview-card font):not(.link-preview-content font),
        .news-desc font:not(.link-preview-card font):not(.link-preview-content font),
        .post-content font:not(.link-preview-card font):not(.link-preview-content font) {
            word-break: break-all !important;
            overflow-wrap: anywhere !important;
            max-width: 100% !important;
            display: inline-block !important;
        }

        /* Ensure link preview is completely protected */
        .link-preview-card,
        .link-preview-card * {
            word-break: normal !important;
            overflow-wrap: normal !important;
            word-wrap: normal !important;
            max-width: none !important;
        }

        .link-preview-image-wrapper {
            max-width: 100px !important;
            width: 100px !important;
            min-width: 100px !important;
        }

        .link-preview-image {
            max-width: 100% !important;
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
        }
    }

    /* Link Preview Card Styles */
    .link-preview-card {
        align-items: stretch !important;
    }

    .link-preview-image-wrapper {
        width: 120px;
        min-width: 120px;
        height: 100px;
        flex-shrink: 0;
        overflow: hidden;
        border-radius: 0.5rem;
    }

    .link-preview-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .link-preview-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .link-preview-title {
        font-size: 1rem;
        line-height: 1.3;
        margin-bottom: 0.5rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .link-preview-description {
        font-size: 0.85rem;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Mobile Responsive Styles */
    @media screen and (max-width: 767px) {
        .link-preview-card {
            flex-direction: row !important;
            align-items: stretch !important;
        }

        .link-preview-image-wrapper {
            width: 100px;
            min-width: 100px;
            height: auto;
            min-height: 100px;
            align-self: stretch;
        }

        .link-preview-image {
            width: 100%;
            height: 100%;
            min-height: 100px;
            object-fit: cover;
        }

        .link-preview-content {
            padding: 0.5rem !important;
            padding-left: 0.75rem !important;
        }

        .link-preview-title {
            font-size: 0.9rem;
            line-height: 1.2;
            margin-bottom: 0.25rem;
            -webkit-line-clamp: 2;
        }

        .link-preview-description {
            font-size: 0.75rem;
            line-height: 1.3;
            -webkit-line-clamp: 2;
        }
    }

    @media screen and (max-width: 480px) {
        .link-preview-image-wrapper {
            width: 90px;
            min-width: 90px;
            min-height: 90px;
        }

        .link-preview-image {
            min-height: 90px;
        }

        .link-preview-title {
            font-size: 0.85rem;
            -webkit-line-clamp: 2;
        }

        .link-preview-description {
            font-size: 0.7rem;
            -webkit-line-clamp: 2;
        }
    }

    .dark-mode .news-desc {
        color: unset !important;
    }
</style>


@php
$settings = getSettingValue();
$hasSocialMedia = !empty($settings['facebook']) || 
                 !empty($settings['twitter']) || 
                 !empty($settings['linkedin']) || 
                 !empty($settings['reddit']) || 
                 !empty($settings['whatsapp']);
@endphp
<div class="news-details-page mb-20">
    <div class="breadcrumb-section pt-4">
        <!-- <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="/" class="fs-14 fw-6"><i
                                class="fas fa-home {{ getFrontSelectLanguageIsoCode() == 'ar' ? 'ms-1' : 'me-1' }}"></i>{{ __('messages.details.home') }}
                        </a>
                    </li>
                    <li class="breadcrumb-item"><a href="{{ route('categoryPage', $postDetail->category->name) }}"
                            class="fs-14 fw-6">{!! $postDetail->category->name !!}</a></li>
                    <li class="breadcrumb-item active fs-14 fw-6"
                        aria-current="page">{!! $postDetail->title !!}</li>
                </ol>
            </nav>
        </div> -->
    </div>
    <!-- start news-details-section -->
    <section class="news-details-section">
        <div class="container">
            <div class="row">
                <div class="col-xl-8">
                    <!-- start news-details-left-section -->
                    <section class="news-details-left pe-xxl-3">
                        <div class="news-details">
                             @php
                                $c = optional($postDetail->livetickerContent);
                                $isLive = false;
                                $untilMs = null;
                                
                                // Check if live indicator is enabled and time hasn't passed
                                if ($c && $c->live_indicator_enabled && $c->live_indicator_until) {
                                    $nowBerlin = \Carbon\Carbon::now('Europe/Berlin');
                                    $untilBerlin = $c->live_indicator_until->setTimezone('Europe/Berlin');
                                    $isLive = $nowBerlin->lte($untilBerlin);
                                    
                                    // Convert to milliseconds for JavaScript
                                    $untilMs = $untilBerlin->utc()->valueOf();
                                }
                            @endphp

                            <h3 id="lt-title-{{ $postDetail->id }}"
                                class="text-black fw-7 fs-24 my-2 title {{ $isLive ? 'title-with-dot' : '' }}"
                                data-live-dot="1"
                                data-enabled="{{ $c->live_indicator_enabled ? '1' : '0' }}"
                                data-until="{{ $untilMs }}">
                                {!! $postDetail->title !!}
                            </h3>
                            <div class="post-content">
                                <p class="text-gray">{!! $postDetail->description !!}</p>
                            </div>
                            <div class="d-md-flex mb-2 ">
                                
                                <div class="d-flex align-items-center" style="flex: 1;">
                                    <div style="flex: 1;">
                                    <div class="d-flex">
                                        <div class="">
                                            <a href="{{ route('user.public.profile', optional($postDetail->user)->username ?? $postDetail->user->id) }}"
                                                class="profile-link"
                                                data-user-identifier="{{ optional($postDetail->user)->username ?? $postDetail->user->id }}">
                                                <img src="{{ $postDetail->user->profile_image }}" alt=""
                                                    class="h-40px {{ getFrontSelectLanguageIsoCode() == 'ar' ? 'ms-2' : 'me-2' }} image image-circle"
                                                    width="40">
                                            </a>

                                        </div>
                                        <div class="d-flex justify-content-start flex-column">
                                            <a
                                                href="{{ route('user.public.profile', optional($postDetail->user)->username ?? $postDetail->user->id) }}"
                                                class="profile-link"
                                                data-user-identifier="{{ optional($postDetail->user)->username ?? $postDetail->user->id }}">
                                                <h5 class="fs-12 text-black mb-0">{{ $postDetail->user->full_name }}
                                                </h5>
                                                <span
                                                    class="fs-12 text-gray">{{ $postDetail->created_at->format('d') }}. {{ ucfirst(__('messages.common.' . strtolower($postDetail->created_at->format('F')))) }}
                                                    {{ $postDetail->created_at->format('Y') }}</span>
                                            </a>
                                        </div>
                                    </div>
                                    </div>
                                    <div style="flex: 1;">
                                    <div class="news-text mb-2 d-flex align-items-center" style="gap: 10px">
                                        <div class="desc d-flex align-items-center like-btn article-action-btn" 
                                            style="cursor: pointer; position: relative;gap: 5px;"
                                            data-id="{{ $postDetail->id }}"
                                            data-type="post"
                                            data-auth="{{ auth()->check() ? '1' : '0' }}"
                                            title="{{ __('messages.comment.like_article') }}">
                                            <i class="fa fa-thumbs-up" id="current_like_icon_{{ $postDetail->id }}"
                                                style="transition: color 0.3s ease; {{ !empty($postDetail->user_liked) && $postDetail->user_liked ? 'color: #B051B0; fill: #B051B0;' : '' }}"></i>
                                            <span class="">

                                                <span style="transition: color 0.3s ease;" class="like-count">{{ $postDetail->likes_count }}</span>
                                            </span>
                                        </div>
                                        
                                        <div class="desc d-inline-block article-action-btn comment-btn-wrapper" 
                                            style="position: relative;"
                                            title="{{ __('messages.comment.comment_article') }}">
                                            <a href="#commentFormSection" class="comment-link d-flex align-items-center gap-1" 
                                                style="text-decoration: none; transition: color 0.3s ease;">
                                                <i class="fa-solid fa-comments me-1" style="transition: color 0.3s ease;"></i>
                                                <span
                                                    class="me-1" style="transition: color 0.3s ease;">{{ $totalComments ? $totalComments : 0 }}</span>
                                            </a>
                                        </div>
                                        
                                        <div class="desc d-flex align-items-center gap-2">
                                            <i class="fa-solid fa-clock fs-12 text-gray me-1"></i>
                                            <span class="fs-14 text-gray me-1">
                                                {{ getReadingTime($postDetail->postArticle?->article_content ? $postDetail->postArticle->article_content : $postDetail->description) }}</span>
                                        </div>
                                        <!-- <div class="desc d-inline-block">
                                            <i class="fa-solid fa-eye fs-12 text-gray me-1"></i>
                                            <span class="fs-14 text-gray me-1"> {{ getPostViewCount($postDetail->id) }}
                                            </span>
                                        </div> -->
                                        </div>
                                    </div>
                                </div>
                                @if($hasSocialMedia)

                                <div class="flex-1">
                                    <section class="share-this-post-section">
                                        <div class="share-this-post">
                                            <div class="post-blog d-flex flex-wrap justify-content-end">
                                                @if (getSettingValue()['facebook'])
                                                <div class="post text-center p-2 text-white fb">
                                                    <a target="_blank"
                                                        href="https://www.facebook.com/sharer.php?u={{ getUrl() }}">
                                                        <i class="social-icon fab fa-facebook-f fs-5"></i>
                                                    </a>
                                                </div>
                                                @endif
                                                @if (getSettingValue()['twitter'])
                                                <div class="post text-center p-2 text-white" style="background-color: black;">
                                                    <a target="_blank"
                                                        href="https://www.twitter.com/share?url={{ getUrl() }}">
                                                        <img src="{{asset('uploads/logo/x-logo.png')}}" alt="X Logo" style="width: 20px; height: 20px;">
                                                        <!-- <i class="social-icon fab fa-twitter fs-5"></i> -->
                                                    </a>
                                                </div>
                                                @endif
                                                @if (getSettingValue()['linkedin'])
                                                <div class="post text-center p-2 text-white ln">
                                                    <a target="_blank"
                                                        href="https://www.linkedin.com/shareArticle?mini=true&url={{ getUrl() }}">
                                                        <i class="social-icon fab fa-linkedin fs-5"></i>
                                                    </a>
                                                </div>
                                                @endif
                                                @if (getSettingValue()['reddit'])
                                                <div class="post text-center p-2 text-white rd">
                                                    <a target="_blank"
                                                        href="https://reddit.com/submit?url={{ getUrl() }}">
                                                        <i class="social-icon fab fa-reddit fs-5"></i>
                                                    </a>
                                                </div>
                                                @endif
                                                @if (getSettingValue()['whatsapp'])
                                                <div class="post text-center p-2 text-white wh">
                                                    <a target="_blank"
                                                        href="https://wa.me/?text={{ getUrl() }}">
                                                        <i class="social-icon fab fa-whatsapp fs-5"></i>
                                                    </a>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </section>
                                </div>
                                @endif

                            </div>
                            <div class="news-content-img position-relative">
                                <div class="news-details-img rounded-10">
                                    <a href="#">
                                        <img src="{{ $postDetail->post_image }}" class="w-100 h-100">
                                    </a>
                                </div>
                                <a href="#" class="tags position-absolute" style="background-color: {{ $postDetail->category->color ?? 'darkgray' }}; z-index: 5;">{{ $postDetail->category->name }}</a>
                                @if(!empty($postDetail->image_copyright))
                                <div class="image-copyright" style="position: absolute; bottom: 15px; right: 15px; color: #fff; font-size: 14px; font-weight: 600; text-shadow: 2px 2px 4px rgba(0,0,0,0.9); z-index: 15; pointer-events: none;">
                                    {{ '©' . $postDetail->image_copyright }}
                                </div>
                                @endif
                            </div>

                                <style>
                                    .news-desc table{
                                        /* width: max-content !important; */
                                        overflow-x: auto !important;
                                    }
                                    .news-desc .table-wrapper{
                                        /* overflow-x: auto !important; */
                                    }
                                    .news-desc img{
                                        width: auto !important;
                                    }
                                </style>

                            <div class="news-desc mb-20 video-content">
                                {{-- ✅ Header --}}
                                @if (!empty($postDetail->livetickerContent->header))
                                    <div class="liveticker-header mb-3">
                                        @php
                                            $html = $postDetail->livetickerContent->header ?? '';
                                            $html = preg_replace_callback(
                                                '/<table[^>]*>.*?<\/table>/is',
                                                function($matches) {
                                                    return '<div class="table-wrapper">' . $matches[0] . '</div>';
                                                },
                                                $html
                                            );
                                        @endphp
                                        {!! $html !!}
                                    </div>
                                @endif

                                <!-- ✅ Live Updates Section -->
                                <div class="live-container">
                                    @php
                                        $c = optional($postDetail->livetickerContent);
                                    
                                        $untilMs = null;
                                        $rawUntil = $c?->live_indicator_until;
                                    
                                        if (!empty($rawUntil)) {
                                            try {
                                                // Flexible parse; Berlin TZ apply karke UTC ms nikaal do
                                                $dt = \Carbon\Carbon::parse($rawUntil, 'Europe/Berlin');
                                                $untilMs = $dt->utc()->valueOf();
                                            } catch (\Exception $e) {
                                                // Optional: fallback agar parse fail ho (e.g. custom format without seconds)
                                                try {
                                                    $dt = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $rawUntil, 'Europe/Berlin');
                                                    $untilMs = $dt->utc()->valueOf();
                                                } catch (\Exception $e2) {
                                                    $untilMs = null; // last resort: quietly disable the indicator
                                                }
                                            }
                                        }
                                    @endphp


                                    @if($c?->title)
                                        <h3 id="lt-title-{{ $postDetail->id }}"
                                            class="title {{ $c?->is_live ? 'title-with-dot' : '' }}"
                                            data-live-dot="1"
                                            data-enabled="{{ $c?->live_indicator_enabled ? '1' : '0' }}"
                                            @if(!is_null($untilMs)) data-until="{{ $untilMs }}" @endif>
                                            {{ $c?->title }}
                                        </h3>
                                   @endif


                                    <div id="ticker" class="ticker">
                                        @foreach ($postDetail->livetickerPosts as $update)
                                            <div class="ticker-item" id="ticker-item-{{ $update->id }}" data-ts="{{ $update->created_at ? $update->created_at->timestamp * 1000 : 0 }}">
                                                <span class="time">
                                                    @if($update->created_at)
                                                        {{ $update->created_at->timezone('Europe/Berlin')->format('H:i') }} Uhr
                                                    @else
                                                        --:-- Uhr
                                                    @endif
                                                </span>
                                                <p>{!! $update->content !!}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- ✅ Footer --}}
                                @if (!empty($postDetail->livetickerContent->footer))
                                    <div class="liveticker-footer mt-3">
                                        @php
                                            $html = $postDetail->livetickerContent->footer ?? '';
                                            $html = preg_replace_callback(
                                                '/<table[^>]*>.*?<\/table>/is',
                                                function($matches) {
                                                    return '<div class="table-wrapper">' . $matches[0] . '</div>';
                                                },
                                                $html
                                            );
                                        @endphp
                                        {!! $html !!}
                                    </div>
                                @endif
                            </div>


                            @if (!empty($getPoll) && $getPoll->count())
                            <div class="row justify-content-center">
                                <div class="col-md-6 col-12">
                                    <section class="voting-poll-section">
                                        <div class="section-heading border-0 mb-30 mt-5">
                                            <div class="row align-items-center">
                                                <div class="col-12 section-heading-left">
                                                    <h2 class="text-black mb-0">{{ __('messages.details.voting_poll') }}</h2>
                                                </div>
                                            </div>
                                        </div>
                                        @php $styleCss = 'style'; @endphp
                                        <style>
                                            .text-purple {
                                                color: rgb(115, 78, 150) !important;
                                            }

                                            .bg-purple {
                                                background-color: #a06cc8 !important;
                                            }

                                            .btn-purple {
                                                background-color: rgb(115, 78, 150) !important;
                                                border-color: rgb(115, 78, 150) !important;
                                                color: #fff;
                                                border: none;
                                                border-radius: 4px;
                                            }

                                            .btn-purple:hover {
                                                background-color: rgb(115, 78, 150);
                                                color: #fff;
                                            }

                                            .border-purple-divider {
                                                border-bottom: 2px solid #a06cc8;
                                                margin-bottom: 2rem;
                                                padding-bottom: 2rem;
                                            }

                                            .section-heading h2:after {
                                                background-color: rgb(115, 78, 150) !important;
                                            }
                                        </style>

                                        @foreach ($getPoll as $index => $poll)
                                        @if($index >= 1)
                                        <hr style="height: 3px; color:rgb(115, 78, 150);">
                                        @endif
                                        <div class="voting-poll">
                                            <p class="text-black fw-6 fs-16 mb-20">{!! $poll->question !!}</p>
                                            <form class="poll-vote-form">
                                                @csrf
                                                <input type="hidden" id="pollId" name="poll_id" value="{{ $poll->id }}">
                                                <div class="mb-2 @if($poll->has_voted) d-none @endif" id="pollOption{{ $poll->id }}">
                                                    @for($i = 1; $i <= 10; $i++)
                                                        @if(!empty($poll->{"option{$i}"}))
                                                        <div class="form-check">
                                                            <input class="form-check-input me-3 poll-answer"
                                                                type="{{ $poll->multi_select ? 'checkbox' : 'radio' }}"
                                                                name="{{ $poll->multi_select ? 'answer[]' : 'answer' }}"
                                                                id="pollAnswer-{{ $i }}-{{ $poll->id }}"
                                                                value="{{ $poll->{"option{$i}"} }}">
                                                            <label class="form-check-label fs-14"
                                                                for="pollAnswer-{{ $i }}-{{ $poll->id }}">
                                                                {!! $poll->{"option{$i}"} !!}
                                                            </label>
                                                        </div>
                                                        @endif
                                                        @endfor

                                                        <div class="vote d-flex justify-content-between align-items-center pt-2 mb-md-4 mb-4 mb-1">
                                                            <button type="submit" class="btn btn-primary poll-submit-btn btn-purple"
                                                                data-id="{{ $poll->id }}">
                                                                {{ __('messages.details.vote') }}
                                                            </button>
                                                            <a href="javascript:void(0);" class="fs-14 text-gray fw-6 view-statistic text-purple"
                                                                data-id="{{ $poll->id }}">
                                                                {{ __('messages.details.view_results') }}
                                                            </a>
                                                        </div>
                                                        <span id="voteError{{ $poll->id }}"></span>
                                                </div>
                                            </form>

                                            <div id="pollStatistic{{ $poll->id }}" class="mb-2 @if(!$poll->has_voted) d-none @endif">
                                                @php $vote = getPollStatistics($poll->id) @endphp
                                                @if (!empty($vote['optionAns']) && count($vote['optionAns']) > 0)
                                                @foreach ($vote['optionAns'] as $pollName => $statistic)
                                                <p class="mt-0 mb-2 fs-14">{{ $pollName }}</p>
                                                <div class="progress mb-3">
                                                    <div class="progress-bar progress-bar-striped"
                                                        {{ $styleCss }}="width: {{ $statistic }}%;" role="progressbar"
                                                        aria-valuenow="{{ $statistic }}" aria-valuemin="0" aria-valuemax="100">
                                                        <span>{{ round($statistic, 2) }}%</span>
                                                    </div>
                                                </div>
                                                @endforeach
                                                @else
                                                <p class="text-warning">No percentage data available.</p>
                                                @endif
                                                <div class="vote d-flex justify-content-between align-items-center pt-2 mb-md-2 mb-1">
                                                    @if(showPollVotesCount())
                                                    <span class="text-black fs-14 fw-6">{{ __('messages.poll.total_vote') }}: {{ $vote['totalPollResults'] }}</span>
                                                    @else
                                                    <span></span>
                                                    @endif
                                                    <a href="javascript:void(0);" class="view-option fs-14 text-gray fw-6 text-purple"
                                                        data-id="{{ $poll->id }}">{{ __('messages.details.view_options') }}</a>
                                                </div>
                                                <span id="voteSuccess{{ $poll->id }}">
                                                    <p></p>
                                                </span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </section>
                                </div>
                            </div>
                            @endif

                            @if (getSettingValue()['linkedin'])
                            <div class="post text-center p-2 text-white ln">
                                <a target="_blank"
                                    href="https://www.linkedin.com/shareArticle?mini=true&url={{ getUrl() }}">
                                    <i class="social-icon fab fa-linkedin fs-5"></i>
                                </a>
                            </div>
                            @endif
                            @if (getSettingValue()['reddit'])
                            <div class="post text-center p-2 text-white rd">
                                <a target="_blank"
                                    href="https://reddit.com/submit?url={{ getUrl() }}">
                                    <i class="social-icon fab fa-reddit fs-5"></i>
                                </a>
                            </div>
                            @endif
                            <!-- @if (getSettingValue()['whatsapp'])
                            <div class="post text-center p-2 text-white wh">
                                <a target="_blank"
                                    href="https://wa.me/?text={{ getUrl() }}">
                                    <i class="social-icon fab fa-whatsapp fs-5"></i>
                                </a>
                            </div>
                            @endif -->
                        </div>


                        <!-- news card -->
                        <!-- <div class="card d-flex flex-row py-4 border-bottom border-secondary w-100">
                            <img src="https://lumiere-a.akamaihd.net/v1/images/star-wars-zero-company-key-art-logo-11_99a0ab38.jpeg?region=0%2C0%2C1080%2C1080" alt="News" class="img-fluid rounded-3 object-fit-cover" style="width: 120px;height:100px">
                            <div class="p-2 ps-4">
                                <p class="text-uppercase text-secondary mb-0" style="font-size: 0.85rem;">News</p>
                                <h5 class="fw-medium">MindsEye: Gameplay geleakt – Ein Hauch von GTA</h5>
                                <p class="text-secondary mb-0" style="font-size: 0.85rem;">05. Mai, 2025</p>
                            </div>
                        </div> -->

                        @if ($postDetail->additional_image)
                        <div class="mt-4">
                            <div class="col-xl-12 col-lg-12">
                                <h4 class="">{{ __('messages.common.images') }}</h4>
                            </div>
                            <div class="detail-page-gallery" id="detail-page-gallery">
                                @foreach ($postDetail->additional_image as $image)
                                <div class="gallery-item">
                                    <img src="{{ $image }}" alt="" class="detail-gallery-img" data-src="{{ $image }}">
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        @if ($postDetail->rss_id != null)
                        @if ($postDetail->rssFeed->show_btn == \App\Models\RssFeed::YES)
                        <div class="d-flex justify-content-end">
                            <a href="{{ $postDetail->rss_link }}" target="_blank"
                                class="btn btn-success mb-2 text-white rounded-10">{{ __('messages.read_more') }}</a>
                        </div>
                        @endif
                        @endif
                        @if ($postDetail->optional_url != null)
                        <div class="d-flex justify-content-end">
                            <a href="{{ $postDetail->optional_url }}" target="_blank"
                                class="btn btn-success mb-2 text-white rounded-10">{{ __('messages.read_more') }}</a>
                        </div>
                        @endif
                        @if (!empty($postDetail->post_file) && count($postDetail->post_file))
                        <div class="mt-4 mb-4">
                            <div class="row">
                                <div class="col-xl-12 col-lg-12">
                                    <h4 class="">{{ __('messages.common.files') }}</h4>
                                    @foreach ($postDetail->post_file as $file)
                                    <div class="file">
                                        <a href="{{ $file }}"
                                            class="tag-link">{{ basename($file) }}</a>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif


                        <!-- start post Reaction -->
                        @include('front_new.detail_pages.post-reaction')


                        <!-- end post Reaction -->



                        <!-- start share-this-post-section -->
                        <section class="share-this-post-section mt-2 pt-md-3">
                            <div class="row admin-desc d-flex flex-wrap justify-content-between mb-20">
                                @php
                                $hasValidTags = false;
                                if (!empty($postDetail->tags)) {
                                    $tagsArray = is_array($postDetail->tags)
                                        ? $postDetail->tags
                                        : explode(',', $postDetail->tags);
                                    // Filter out empty tags and trim whitespace
                                    $validTags = array_filter(array_map('trim', $tagsArray), function($tag) {
                                        return !empty($tag);
                                    });
                                    $hasValidTags = !empty($validTags);
                                }
                                @endphp
                                @if ($hasValidTags)
                                <div class="col-sm-12">
                                    <h5 class="fs-16 fw-6 text-black mb-3 pb-1 mx-2 float-start">
                                        {{ __('messages.common.tags') }}
                                    </h5>
                                    <div class="tag-blogs d-flex overflow-auto">
                                        @foreach ($validTags as $tags)
                                        <div class="tag br-gray-100 d-inline-block py-2 px-3 mb-3 me-2">
                                            <a href="{{ route('popularTagPage', $tags) }}"
                                                class="fs-14 text-black ">{!! $tags !!}</a>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>

                            @if($postDetail->comment_enabled)
                            <!--start comment-section -->
                            <section class="comment-section mt-4 pt-3 blog-post-comment-view mb-3" id="blog-post-comment-view-section">
                                <div class="d-flex justify-content-between align-items-center mb-3 comment-data @if (empty($totalComments)) d-none @endif">
                                    <h3 class="text-black fw-6 mb-0">
                                        {{ __('messages.comments') }}:
                                        <span class="ms-2 count-data">
                                            {{ $totalComments }}
                                        </span>
                                    </h3>
                                    <!-- Comment Filter Dropdown -->
                                    <div class="dropdown">
                                        <button class="btn btn-primary dropdown-toggle" type="button" id="commentFilterBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-filter me-1"></i> <span id="filterText">{{ __('messages.comment.newest') }}</span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="commentFilterBtn">
                                            <li><a class="dropdown-item filter-option active" href="#" data-sort="newest">{{ __('messages.comment.newest') }}</a></li>
                                            <li><a class="dropdown-item filter-option" href="#" data-sort="oldest">{{ __('messages.comment.oldest') }}</a></li>
                                            <li><a class="dropdown-item filter-option" href="#" data-sort="top">{{ __('messages.comment.top_comments') }}</a></li>
                                        </ul>
                                    </div>
                                </div>
                                @if($postDetail->comment_enabled)
                                    <section class="post-comment-section bg-light px-30 py-4 mb-5" style="box-shadow: 0px 0px 10px #00000020; border-radius: 15px;" id="commentFormSection">
                                        <h5 class="fs-16 text-black fw-6 mb-3">{{ __('messages.comment.post_a_comment') }}</h5>
                                        <form id="commentForm">
                                            @csrf
                                            <input type="hidden" name="post_id" value="{{ $postDetail->id }}">
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
                                                    @if ($showCaptcha == '1')
                                                    <input type="hidden" value="{{ $settings['show_captcha'] }}"
                                                        id="googleCaptch">
                                                    <div class="form-group mb-1">
                                                        <div class="g-recaptcha" id="gRecaptchaContainerPostDetails"
                                                            data-sitekey="{{ $settings['site_key'] }}"></div>
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
                                @endif
                                @php
                                $inStyle = 'style=';
                                $style = '"overflow-y: auto; max-height: 325px"';
                                @endphp
                                <div id="blog-post-comment-body" class="comment-view" {!! $totalComments>= 3 ? $inStyle . '' : '' !!}>

                                </div>
                                <nav class="mt-4" id="pagination-container"></nav>
                            </section>
                            <!--end comment-section -->
                            @else
                            <!-- Comments disabled message -->
                            <section class="comment-section mt-4 pt-3 mb-3 comment-section-disabled">
                                <div class="alert alert-info text-center">
                                    <i class="fa-solid fa-comment-slash me-2"></i>
                                    {{ __('messages.comment.comments_disabled') }}
                                </div>
                            </section>
                            @endif

                            <!-- start post-comment-section -->
                            <!-- <section class="my-4">
                                <div class="comment-box position-relative">
                                    <div class="d-flex justify-content-between mb-2">
                                        <div class="d-flex align-items-center">
                                            <img src="http://127.0.0.1:8000/assets/image/avatar.png" alt="User Avatar" class="rounded-circle me-2" width="40" height="40">
                                            <div>
                                                <div class="user-info">29Players</div>
                                                <small class="text-muted">Dein Kommentar</small>
                                            </div>
                                        </div>
                                        <div class="close-btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x-icon lucide-x">
                                                <path d="M18 6 6 18" />
                                                <path d="m6 6 12 12" />
                                            </svg>
                                        </div>
                                    </div>

                                    <textarea name="comment"
                                        id="comment" class="mb-3" rows="4" placeholder="Tippe hier deine Meinung"></textarea>
                                    <div class="d-flex justify-content-end">
                                        <button class="btn btn-primary comment-btn">Kommentar posten</button>
                                    </div>
                                </div>
                            </section> -->
                            <!-- <div class="admin-post position-relative pt-60">
                                @if (!empty($previousPost))
                                <a href="{{ route('detailPage', $previousPost->slug) }}"
                                    class='prev-btn fs-16 text-black fw-6'>
                                    <i
                                        class="fa-solid fa-angle-left fs-14 me-1"></i>{{ __('messages.details.previous_post') }}
                                </a>
                                @endif
                                @if (!empty($nextPost))
                                <a href="{{ route('detailPage', $nextPost->slug) }}"
                                    class='next-btn fs-16 text-black fw-6'>{{ __('messages.details.next_post') }}
                                    <i class="fa-solid fa-angle-right fs-14 ms-1"></i>
                                </a>
                                @endif
                                <div class="row">
                                    <div class="col-md-6">
                                        @if (!empty($previousPost))
                                        <div class="card d-flex flex-row mb-40">
                                            <div class="col-4 card-img-top">
                                                <a href="{{ route('detailPage', $previousPost->slug) }}">
                                                    {{-- <img data-src="{{ $previousPost->post_image }}" alt="" --}}
                                                    {{-- src="{{ asset('front_web/images/bg-process.png') }}" --}}
                                                    {{-- class="lazy" height="100" width="100"> --}}
                                                    {{-- <img src="{{ $previousPost->post_image }}" alt=""--}}
                                                    {{-- height="100" width="100">--}}

                                                    @if($previousPost->post_types == \App\Models\Post::VIDEO_TYPE_ACTIVE)
                                                    @if(!empty($previousPost['postVideo']['thumbnail_image_url']))
                                                    <img src="{{ $previousPost['postVideo']['thumbnail_image_url'] }}"
                                                        alt="Thumbnail" height="100" width="100" />
                                                    @else
                                                    <iframe width="auto" height="315"
                                                        src="{{ $previousPost['postVideo']['video_embed_code'] }}"
                                                        frameborder="0"
                                                        allow="autoplay; encrypted-media"
                                                        allowfullscreen height="100"
                                                        width="100"></iframe>
                                                    @endif
                                                    @else
                                                    <img src="{{$previousPost->post_image}}" alt=""
                                                        height="100" width="100">
                                                    @endif
                                                </a>
                                            </div>
                                            <div
                                                class="col-8 card-body {{ getFrontSelectLanguageIsoCode() == 'ar' ? 'me-4' : 'ms-4' }}">
                                                <h5 class="card-title fs-14 fw-6 text-black">
                                                    <a href="{{ route('detailPage', $previousPost->slug) }}"
                                                        class="fs-14 fw-6 text-black position-relative">
                                                        {!! \Illuminate\Support\Str::limit($previousPost['title'], 40, '...') !!}
                                                    </a>
                                                </h5>
                                                <span class="fs-14 text-gray">
                                                    {{ $previousPost['created_at']->format('d') }}. {{ ucfirst(__('messages.common.' . strtolower($previousPost['created_at']->format('M')))) }}
                                                    {{ $previousPost['created_at']->format('Y') }}</span>
                                            </div>

                                        </div>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        @if (!empty($nextPost))
                                        <div class="card d-flex flex-row mb-40">
                                            <div class="col-4 card-img-top ">
                                                <a href="{{ route('detailPage', $nextPost->slug) }}">
                                                    @if($nextPost->post_types == \App\Models\Post::VIDEO_TYPE_ACTIVE)
                                                    @if(!empty($nextPost['postVideo']['thumbnail_image_url']))
                                                    <img src="{{ $nextPost['postVideo']['thumbnail_image_url'] }}"
                                                        alt="Thumbnail" height="100" width="100" />
                                                    @else
                                                    <iframe width="560" height="315"
                                                        src="{{ $nextPost['postVideo']['video_embed_code'] }}"
                                                        frameborder="0"
                                                        allow="autoplay; encrypted-media"
                                                        allowfullscreen height="100"
                                                        width="100"></iframe>
                                                    @endif
                                                    @else
                                                    <img src="{{$nextPost->post_image}}" alt=""
                                                        height="100" width="100">
                                                    @endif
                                                </a>
                                            </div>
                                            <div
                                                class="col-8 {{ getFrontSelectLanguageIsoCode() == 'ar' ? 'me-4' : 'ms-4' }}">
                                                <h5 class="card-title fs-14 fw-6 text-black">
                                                    <a href="{{ route('detailPage', $nextPost->slug) }}"
                                                        class="fs-14 fw-6 text-black position-relative">
                                                        {!! \Illuminate\Support\Str::limit($nextPost['title'], 40, '...') !!}
                                                    </a>
                                                </h5>
                                                <span
                                                    class=" fs-14 text-gray">{{ $nextPost['created_at']->format('d') }}. {{ ucfirst(__('messages.common.' . strtolower($nextPost['created_at']->format('M')))) }}
                                                    {{ $nextPost['created_at']->format('Y') }}</span>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div> -->
                        </section>
                        <!-- end share-this-post-section -->


                        @if (checkAdSpaced('post_details'))
                        @if (isset(getAdImageDesktop(\App\Models\AdSpaces::POST_DETAILS)->code))
                        <div class="index-top-desktop ad-space-url-desktop">
                            {!! getAdImageDesktop(\App\Models\AdSpaces::POST_DETAILS)->code !!}
                        </div>
                        @elseif ($adsDesktop = getAdImageDesktop(\App\Models\AdSpaces::POST_DETAILS))
                        <div class="container index-top-desktop">
                            <a href="{{ $adsDesktop->ad_url }}" target="_blank">
                                <img src="{{ asset($adsDesktop->ad_banner) }}" width="800"
                                    class="img-fluid">
                            </a>
                        </div>
                        @endif
                        @if (isset(getAdImageDesktop(\App\Models\AdSpaces::POST_DETAILS)->code))
                        <div class="index-top-mobile ad-space-url-mobile">
                            {!! getAdImageDesktop(\App\Models\AdSpaces::POST_DETAILS)->code !!}
                        </div>
                        @elseif ($adRecord = getAdImageMobile(\App\Models\AdSpaces::POST_DETAILS))
                        <div class=" container index-top-mobile">
                            <a href="{{ $adRecord->ad_url }}" target="_blank">
                                <img src="{{ asset($adRecord->ad_banner) }}" width="350"
                                    class="img-fluid">
                            </a>
                        </div>
                        @endif
                        @endif
                        <!--start related-post-section -->
                        @if ($relatedPosts->count() > 0)
                        <section class="related-post-section pt-40 mb-xl-5 mb-lg-4">
                            <div class="section-heading border-0 mb-0">
                                <div class="row align-items-center">
                                    <div class="col-12 section-heading-left">
                                        <h2 class="text-black mb-0"> {{ __('messages.details.related_post') }}
                                        </h2>
                                    </div>
                                </div>
                            </div>
                            <div class="related-post pt-60">
                                <div class="row">
                                    @foreach ($relatedPosts as $relatedPost)
                                    <div class="col-lg-4 col-md-4 col-sm-6 mb-2">
                                        <div class="card position-relative slide-item">
                                            <div class="card-img-top">
                                                <a href="{{ route('detailPage', $relatedPost->slug) }}">
                                                    {{-- <img data-src="{{ $relatedPost['post_image'] }}" alt="" src="{{ asset('front_web/images/bg-process.png') }}" class="w-100 h-100 lazy"> --}}
                                                    <img src="{{ $relatedPost['post_image'] }}"
                                                        alt="" class="w-100 h-100">
                                                </a>
                                            </div>
                                            <div class="card-body">
                                                <a href="#"
                                                    class="tags position-absolute  fw-7" style="background-color: {{ $relatedPost->category->color ?? '#B051B0' }}; color: white;">{{ $relatedPost['category']['name'] }}</a>
                                                <h5 class="card-title mb-1 fs-16 text-black fw-6">
                                                    <a class="text-black"
                                                        href="{{ route('detailPage', $relatedPost->slug) }}">
                                                        {!! $relatedPost['title'] !!}
                                                    </a>
                                                </h5>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span
                                                        class="card-text fs-12 text-gray">{{ $relatedPost['created_at']->format('d') }}. {{ ucfirst(__('messages.common.' . strtolower($relatedPost['created_at']->format('M')))) }}
                                                        {{ $relatedPost['created_at']->format('Y') }}</span>
                                                    <a href="{{ route('detailPage', $relatedPost->slug) }}#blog-post-comment-view-section">
                                                        <p class="fs-14 text-gray mb-0"><i class="fa-solid fa-comments fs-12 text-gray me-1"></i> {{ $relatedPost->comments()->where('status', 1)->count() }}</p>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if ($loop->iteration >= 6)
                                    @break
                                    @endif
                                    @endforeach
                                </div>
                            </div>
                        </section>
                        @endif
                        <!--end related-post-section -->
                    </section>
                </div>
                <div class="col-xl-4 ">
                    @include('front_new.detail_pages.side-menu')
                </div>
            </div>
        </div>

    </section>
    <!-- end news-details-left-section -->

</div>
<!-- <div id="comments-loader" class="text-center my-3" style="display: none;">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <p class="mt-2 text-muted">Please wait, fetching comments...</p>
</div> -->

<!-- end news-details-section -->
@include('front_new.detail_pages.template.template')
@push('js')

<!-- Begin emoji-picker JavaScript -->
<!-- <script src="{{asset('public/emoji/js/meteorEmoji.min.js')}}"></script> -->
<script async src="//www.instagram.com/embed.js"></script>
<script async src="https://embed.bsky.app/static/embed.js" charset="utf-8"></script>
<!-- jQuery (required) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('h3[data-enabled]').forEach(el => {
    const enabled = el.dataset.enabled === '1';
    const until = Number(el.dataset.until);

    // If not enabled, don't show dot
    if (!enabled) {
      el.classList.remove('title-with-dot');
      return;
    }

    // If no until time, don't show dot
    if (!Number.isFinite(until) || until === 0) {
      el.classList.remove('title-with-dot');
      return;
    }

    function update() {
      const now = Date.now();
      if (now >= until) {
        // Time reached, remove dot
        el.classList.remove('title-with-dot');
      } else {
        // Time not reached yet, show dot
        el.classList.add('title-with-dot');
      }
    }

    update();              // initial check
    setInterval(update, 60000); // keep it real-time (check every minute)
  });
});
</script>


<script>
document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll('.video-content p a, .video-content article a').forEach(link => {
    // styling
    link.style.fontWeight = '800';
    link.style.color = '#B051B0';

    if (link.getAttribute('target') === '_blank') {
      // inline SVG add INSIDE the anchor
      const svg = document.createElement('span');
      svg.innerHTML = `
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
             xmlns="http://www.w3.org/2000/svg"
             style="vertical-align:middle; display:inline-block;">
          <path d="M14 4H20M20 4V10M20 4L12 12" stroke="#B051B0" stroke-width="2"/>
          <path d="M11 5H7C5.89543 5 5 5.89543 5 7V17C5 18.1046 5.89543 19 7 19H17C18.1046 19 19 18.1046 19 17V13"
                stroke="#B051B0" stroke-width="2" stroke-linecap="round"/>
        </svg>
      `;
      // thoda sa spacing control (tiny but not huge)
      svg.style.marginLeft = '-4px';

      link.appendChild(svg); // << inside anchor

      // (optional) agar kabhi outside rakhna ho to trailing whitespace trim:
      // if (link.nextSibling && link.nextSibling.nodeType === Node.TEXT_NODE) {
      //   link.nextSibling.textContent = link.nextSibling.textContent.replace(/^\s+/, '');
      // }
    }
  });
});

document.addEventListener("DOMContentLoaded", function () {
    const cartIcon = `
        <svg style="width: 16px; height: 16px; margin-left: -2px; vertical-align: middle;" 
             fill="currentColor" viewBox="0 0 24 24">
            <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zm10 0c-1.1
             0-1.99.9-1.99 2S15.9 22 17 22s2-.9 2-2-.9-2-2-2zM7.16
             6l.84 2h8.94l1.72-4H6.21L5.27 2H1v2h3l3.6
             7.59-1.35 2.44C5.11 15.37 6 17 7.34 17H19v-2H7.42c-.14
             0-.25-.11-.25-.25l.03-.12L8.1 13h7.45c.75
             0 1.41-.41 1.75-1.03L21 4H7.16z"/>
        </svg>
    `;

    // Select all links inside .video-content having target="_self_linkcart"
    const links = document.querySelectorAll('.video-content a[target="_self_linkcart"]');

    links.forEach(link => {
        // Add icon only if it isn't already added
        if (!link.dataset.cartAdded) {
            link.innerHTML += cartIcon;
            link.dataset.cartAdded = "true"; // prevents duplicate icons
        }
    });
});
</script>


<!-- <script>
    (() => {
        new MeteorEmoji()
    })()
</script> -->

<script>
    $(document).ready(function() {
        initEmojiPicker('#comment');
    });
</script>

<script>
    // function shw(id) {
    //     $("#answer_" + id).removeClass('d-none');
    // }

    function shw(commentId) {
        $('[id^="answer_"]').addClass('d-none');
        $('#answer_' + commentId).removeClass('d-none');
        
        // Initialize emoji picker for reply textarea
        setTimeout(function() {
            var replyTextarea = $('#comment-reply-' + commentId);
            if (replyTextarea.length && !replyTextarea.data('emojiPickerInit')) {
                initEmojiPicker('#comment-reply-' + commentId);
                replyTextarea.data('emojiPickerInit', true);
            }
        }, 100);
    }
</script>
<!-- <script>
    $('#voteBtn').on('click', function() {
        $.post("{{ route('user.vote') }}", {
            _token: "{{ csrf_token() }}"
        }, function(response) {
            alert(response.message);
        });
    });
</script> -->
<script src="https://js.pusher.com/8.3.0/pusher.min.js"></script>
<script>
  var pusher = new Pusher("{{ env('PUSHER_APP_KEY') }}", {
      cluster: "{{ env('PUSHER_APP_CLUSTER') }}",
      encrypted: true
  });

  const channel = pusher.subscribe('liveticker.' + {{ $postDetail->id }});

  // --- Helpers ---
  function formatTimeDE(dateStr) {
    try {
      return new Date(dateStr).toLocaleTimeString('de-DE', {
        hour: '2-digit',
        minute: '2-digit',
        timeZone: 'Europe/Berlin'
      }) + ' Uhr';
    } catch (e) {
      return '';
    }
  }

  // Insert an item into #ticker so that items are sorted by data-ts (DESC)
  function insertSortedByTimeDesc(ticker, item) {
    const ts = Number(item.dataset.ts || 0);
    const children = ticker.children;
    let placed = false;

    for (let i = 0; i < children.length; i++) {
      const childTs = Number(children[i].dataset.ts || 0);
      if (ts > childTs) {
        ticker.insertBefore(item, children[i]); // newer above older
        placed = true;
        break;
      }
    }
    if (!placed) {
      ticker.appendChild(item); // oldest goes at the end
    }
  }

  channel.bind('LiveTickerUpdated', function (data) {
    if (!data || !data.livetickerPost) return;

    const post = data.livetickerPost;
    const ticker = document.getElementById("ticker");
    if (!ticker) return;

    const itemId = "ticker-item-" + post.id;
    // Use created_at for sorting to maintain position when editing
    const ts = Date.parse(post.created_at); // milliseconds
    const itemHtml = `
      <span class="time">${formatTimeDE(post.created_at)}</span>
      <p>${post.content}</p>
    `;

    let existingItem = document.getElementById(itemId);

    if (existingItem) {
      // Update content ONLY - absolutely no repositioning
      // Just update the innerHTML, nothing else
      existingItem.innerHTML = itemHtml;
      
      // DO NOT touch data-ts, DO NOT touch position, DO NOT call any sorting functions
      // The item should stay exactly where it is in the DOM
    } else {
      // create fresh item and place it correctly
      const item = document.createElement("div");
      item.id = itemId;
      item.classList.add("ticker-item");
      item.dataset.ts = String(ts);
      item.innerHTML = itemHtml;
      insertSortedByTimeDesc(ticker, item);
    }
  });

  channel.bind('LiveTickerDeleted', function (data) {
    if (!data || !data.livetickerId) return;
    const itemId = "ticker-item-" + data.livetickerId;
    const existingItem = document.getElementById(itemId);
    if (existingItem) existingItem.remove();
  });
</script>


<script>
    // document.addEventListener("DOMContentLoaded", function() {
    //     const videoContents = document.getElementsByClassName("video-content");

    //     for (let content of videoContents) {
    //         const links = content.getElementsByTagName("a");

    //         for (let link of links) {
    //             if (link) {
    //                 link.setAttribute("target", "_blank");
    //                 // Add rel="noopener noreferrer" for security
    //                 // link.setAttribute("rel", "noopener noreferrer");
    //             }
    //         }
    //     }
    // });

</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const newsDesc = document.querySelector('.video-content');
        if (newsDesc) {
            const markupElements = newsDesc.querySelectorAll('pre.language-markup');
            markupElements.forEach(element => {

                element.style.backgroundColor = '	#666666';
                element.style.border = '1px solid #ccc';
                element.style.padding = '10px';
                element.style.margin = '10px 0';
                element.style.display = 'block';


                element.style.whiteSpace = 'pre-wrap';
                element.style.overflowX = 'hidden';
                element.style.maxWidth = '100%';

                const codeElement = element.querySelector('code');
                if (codeElement) {
                    codeElement.style.display = 'inline';
                    codeElement.style.background = 'transparent';
                    codeElement.style.color = '#ffffff';
                    codeElement.style.padding = '0';
                }

                element.classList.add('info-box');
            });

            // Build image list: content images + gallery images (for lightbox prev/next)
            const imageElements = [];
            newsDesc.querySelectorAll('img').forEach(im => imageElements.push(im));
            document.querySelectorAll('.detail-page-gallery .detail-gallery-img').forEach(im => imageElements.push(im));

            imageElements.forEach((img, index) => {
                img.style.cursor = 'pointer';
                img.addEventListener('click', function() { openDetailLightbox(index); });
            });

            function openDetailLightbox(initialIndex) {
                let currentIndex = initialIndex;
                const modal = document.createElement('div');
                modal.className = 'modern-modal detail-lightbox';
                modal.setAttribute('role', 'dialog');
                modal.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:linear-gradient(135deg,rgba(0,0,0,0.92),rgba(50,50,50,0.92));display:flex;justify-content:center;align-items:center;z-index:10000;opacity:0;transition:opacity 0.3s ease;touch-action:none;overflow:hidden;';

                const modalImgContainer = document.createElement('div');
                modalImgContainer.style.cssText = 'position:relative;display:flex;align-items:center;justify-content:center;width:85vw;max-width:900px;height:80vh;max-height:700px;';

                const modalImg = document.createElement('img');
                modalImg.className = 'modal-image';
                modalImg.style.cssText = 'max-width:100%;max-height:100%;width:auto;height:auto;object-fit:contain;border-radius:10px;box-shadow:0 10px 30px rgba(0,0,0,0.5);transition:opacity 0.2s ease;';

                const modalCopyright = document.createElement('div');
                modalCopyright.style.cssText = 'position:absolute;color:#fff;font-size:14px;font-weight:600;z-index:1001;text-shadow:2px 2px 4px rgba(0,0,0,0.9);pointer-events:none;';

                function positionCopyrightOnImage() {
                    if (modalCopyright.style.display === 'none') return;
                    const c = modalImgContainer;
                    const w = c.offsetWidth;
                    const h = c.offsetHeight;
                    const imgW = modalImg.offsetWidth;
                    const imgH = modalImg.offsetHeight;
                    const nw = modalImg.naturalWidth;
                    const nh = modalImg.naturalHeight;
                    if (!w || !h) { modalCopyright.style.right = '15px'; modalCopyright.style.bottom = '15px'; return; }
                    const inset = 15;
                    var dw = imgW, dh = imgH;
                    if (nw && nh && (imgW >= w - 2 || imgH >= h - 2)) {
                        var scale = Math.min(w / nw, h / nh);
                        dw = Math.round(nw * scale);
                        dh = Math.round(nh * scale);
                    }
                    modalCopyright.style.right = ((w - dw) / 2 + inset) + 'px';
                    modalCopyright.style.bottom = ((h - dh) / 2 + inset) + 'px';
                }

                function schedulePositionCopyright() {
                    requestAnimationFrame(function() {
                        requestAnimationFrame(positionCopyrightOnImage);
                    });
                }

                function showImage(i) {
                    const el = imageElements[i];
                    const src = el.getAttribute('data-src') || el.currentSrc || el.src;
                    const copy = el.getAttribute('data-copyright');
                    modalCopyright.textContent = copy && copy.trim() ? '©' + copy : '';
                    modalCopyright.style.display = copy && copy.trim() ? 'block' : 'none';
                    prevBtn.style.visibility = i > 0 ? 'visible' : 'hidden';
                    nextBtn.style.visibility = i < imageElements.length - 1 ? 'visible' : 'hidden';
                    currentIndex = i;
                    modalImg.onload = schedulePositionCopyright;
                    modalImg.src = src;
                    if (modalImg.complete) schedulePositionCopyright();
                    else setTimeout(schedulePositionCopyright, 100);
                }

                const prevBtn = document.createElement('button');
                prevBtn.setAttribute('type', 'button');
                prevBtn.setAttribute('aria-label', 'Previous image');
                prevBtn.innerHTML = '&#10094;';
                prevBtn.style.cssText = 'position:fixed;left:20px;top:50%;transform:translateY(-50%);width:52px;height:52px;border-radius:50%;border:2px solid rgba(255,255,255,0.9);background:rgba(0,0,0,0.5);color:#fff;font-size:26px;cursor:pointer;z-index:10002;display:flex;align-items:center;justify-content:center;transition:background 0.2s;';
                prevBtn.onmouseover = () => { prevBtn.style.background = 'rgba(0,0,0,0.8)'; };
                prevBtn.onmouseout = () => { prevBtn.style.background = 'rgba(0,0,0,0.5)'; };
                prevBtn.onclick = (e) => { e.stopPropagation(); if (currentIndex > 0) showImage(currentIndex - 1); };

                const nextBtn = document.createElement('button');
                nextBtn.setAttribute('type', 'button');
                nextBtn.setAttribute('aria-label', 'Next image');
                nextBtn.innerHTML = '&#10095;';
                nextBtn.style.cssText = 'position:fixed;right:20px;top:50%;transform:translateY(-50%);width:52px;height:52px;border-radius:50%;border:2px solid rgba(255,255,255,0.9);background:rgba(0,0,0,0.5);color:#fff;font-size:26px;cursor:pointer;z-index:10002;display:flex;align-items:center;justify-content:center;transition:background 0.2s;';
                nextBtn.onmouseover = () => { nextBtn.style.background = 'rgba(0,0,0,0.8)'; };
                nextBtn.onmouseout = () => { nextBtn.style.background = 'rgba(0,0,0,0.5)'; };
                nextBtn.onclick = (e) => { e.stopPropagation(); if (currentIndex < imageElements.length - 1) showImage(currentIndex + 1); };

                const closeBtn = document.createElement('span');
                closeBtn.innerHTML = '×';
                closeBtn.className = 'modal-close';
                closeBtn.style.cssText = 'position:absolute;top:20px;right:20px;color:#fff;font-size:30px;font-weight:bold;cursor:pointer;background:rgba(255,0,0,0.7);border-radius:50%;width:40px;height:40px;display:flex;justify-content:center;align-items:center;z-index:1002;transition:background 0.3s;';
                closeBtn.onmouseover = () => { closeBtn.style.background = 'rgba(255,0,0,1)'; };
                closeBtn.onmouseout = () => { closeBtn.style.background = 'rgba(255,0,0,0.7)'; };

                function closeLightbox() {
                    modal.style.opacity = '0';
                    document.removeEventListener('keydown', onKey);
                    modal.removeEventListener('touchstart', onTouchStart);
                    modal.removeEventListener('touchmove', onTouchMove, { passive: false });
                    modal.removeEventListener('touchend', onTouchEnd);
                    setTimeout(() => {
                        if (modal.parentNode) document.body.removeChild(modal);
                        document.documentElement.style.overflow = '';
                        document.body.style.overflow = '';
                    }, 300);
                }
                closeBtn.onclick = closeLightbox;
                modal.onclick = function(e) { if (e.target === modal) closeLightbox(); };

                function onKey(e) {
                    if (e.key === 'Escape') closeLightbox();
                    else if (e.key === 'ArrowLeft' && currentIndex > 0) showImage(currentIndex - 1);
                    else if (e.key === 'ArrowRight' && currentIndex < imageElements.length - 1) showImage(currentIndex + 1);
                }

                var touchStartX = 0;
                function onTouchStart(e) { touchStartX = e.touches[0].clientX; }
                function onTouchMove(e) {
                    var dx = e.touches[0].clientX - touchStartX;
                    if (Math.abs(dx) > 8) e.preventDefault();
                }
                function onTouchEnd(e) {
                    var dx = e.changedTouches[0].clientX - touchStartX;
                    if (dx > 60 && currentIndex > 0) showImage(currentIndex - 1);
                    else if (dx < -60 && currentIndex < imageElements.length - 1) showImage(currentIndex + 1);
                }

                modalImgContainer.appendChild(modalImg);
                modalImgContainer.appendChild(modalCopyright);
                modal.appendChild(modalImgContainer);
                modal.appendChild(prevBtn);
                modal.appendChild(nextBtn);
                modal.appendChild(closeBtn);
                document.body.appendChild(modal);
                document.documentElement.style.overflow = 'hidden';
                document.body.style.overflow = 'hidden';
                document.addEventListener('keydown', onKey);
                modal.addEventListener('touchstart', onTouchStart, { passive: true });
                modal.addEventListener('touchmove', onTouchMove, { passive: false });
                modal.addEventListener('touchend', onTouchEnd, { passive: true });

                showImage(initialIndex);
                if (imageElements.length <= 1) { prevBtn.style.visibility = 'hidden'; nextBtn.style.visibility = 'hidden'; }
                setTimeout(() => { modal.style.opacity = '1'; }, 10);
            }
            const phpElements = newsDesc.querySelectorAll('pre.language-php');
            phpElements.forEach(element => {
                const codeText = element.querySelector('code').textContent.trim();
                const urlMatch = codeText.match(/https?:\/\/[^\s]+/);
                if (urlMatch) {
                    const url = urlMatch[0];
                    fetch('/proxy-fetch', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                url: url
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.error) throw new Error(data.error);
                            const {
                                title,
                                image,
                                description,
                                video_embed
                            } = data;

                            // Clean up image string (remove spaces/newlines)
                            const cleanImage = image?.trim();
                            const isDefaultImage = cleanImage === 'https://2playerz.de/front_web/images/default.jpg';

                            // Hide original <pre>
                            element.style.display = 'none';

                            const previewDiv = document.createElement('div');

                            // ✅ 1. Show CARD only if image exists and it's NOT the default one
                            if (cleanImage && !isDefaultImage) {
                                const linkCard = document.createElement('a');
                                linkCard.href = url;
                                // linkCard.target = '_blank';
                                linkCard.className = 'card d-flex flex-row p-2 border border-secondary w-100 mb-3 link-preview-card';
                                linkCard.style.textDecoration = 'none';
                                linkCard.style.color = 'inherit';
                                linkCard.style.setProperty('border-radius', '10px', 'important');
                                linkCard.style.setProperty('border-width', '5px', 'important');
                                linkCard.innerHTML = `
                                <div class="link-preview-image-wrapper">
                                    <img src="${cleanImage}" alt="${title}" class="link-preview-image img-fluid rounded-3 object-fit-cover">
                                </div>
                                <div class="link-preview-content p-2 ps-4">
                                    <h5 class="fw-medium link-preview-title">${title || 'No Title'}</h5>
                                    <p class="text-secondary mb-0 link-preview-description">${description || 'No Description'}</p>
                                </div>
                            `;
                                previewDiv.appendChild(linkCard);
                            }
                            // ✅ 2. If image is default or missing, but embed exists → show video iframe
                            else if (video_embed) {
                                const tempIframe = new DOMParser().parseFromString(video_embed, 'text/html').querySelector('iframe');
                                const videoSrc = tempIframe?.getAttribute('src') || '';

                                const linkCard = document.createElement('a');
                                linkCard.href = url;
                                // linkCard.target = '_blank';
                                linkCard.className = 'card d-flex flex-row p-2 border border-secondary w-100 mb-3 link-preview-card';
                                linkCard.style.textDecoration = 'none';
                                linkCard.style.color = 'inherit';
                                linkCard.style.setProperty('border-radius', '10px', 'important');
                                linkCard.style.setProperty('border-width', '5px', 'important');
                                linkCard.innerHTML = `
                            <div class="link-preview-image-wrapper">
                                <iframe
                                    src="${videoSrc}"
                                    frameborder="0"
                                    allowfullscreen
                                    style="width: 100%; height: 100%; object-fit: cover;"
                                ></iframe>
                            </div>
                            <div class="link-preview-content p-2 ps-4">
                                <h5 class="fw-medium link-preview-title">${title || 'No Title'}</h5>
                                <p class="text-secondary mb-0 link-preview-description">${description || 'No Description'}</p>
                            </div>
                        `;

                                previewDiv.appendChild(linkCard);

                            }


                            // Insert after <pre>
                            element.parentNode.insertBefore(previewDiv, element.nextSibling);
                        })


                        .catch(error => console.error('Error fetching preview:', error));
                }
            });
        }
    });
</script>
<script>
    window.showPollVotesCount = @json(showPollVotesCount());
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.poll-vote-form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const pollId = form.querySelector('input[name="poll_id"]').value;
                const token = form.querySelector('input[name="_token"]').value;

                let selectedInputs = form.querySelectorAll('input[name="answer[]"]:checked');
                if (selectedInputs.length === 0) {
                    selectedInputs = form.querySelectorAll('input[name="answer"]:checked');
                }

                if (selectedInputs.length === 0) {
                    const errorBox = document.getElementById(`voteError${pollId}`);
                    if (errorBox) errorBox.textContent = "{{ __('messages.placeholder.please_select_at_least_one_option') }}";
                    return;
                }

                const answers = Array.from(selectedInputs).map(input => input.value);

                fetch("{{ route('vote.poll') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            poll_id: pollId,
                            answer: answers
                        })
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            form.reset();

                            const statBlock = document.getElementById(`pollStatistic${pollId}`);
                            const optionBlock = document.getElementById(`pollOption${pollId}`);

                            optionBlock?.classList.add('d-none');
                            statBlock?.classList.remove('d-none');
                            statBlock.innerHTML = '';

                            if (result.data.optionAns && Object.keys(result.data.optionAns).length > 0) {
                                for (const [label, val] of Object.entries(result.data.optionAns)) {
                                    statBlock.innerHTML += `
                                        <p class="mt-0 mb-2 fs-14">${label}</p>
                                        <div class="progress mb-3">
                                            <div class="progress-bar progress-bar-striped" role="progressbar"
                                                 aria-valuenow="${val}" aria-valuemin="0" aria-valuemax="100"
                                                 style="width: ${val}%;"><span>${val}%</span></div>
                                        </div>`;
                                }
                            } else {
                                statBlock.innerHTML = '<p>No data available.</p>';
                            }

                            statBlock.innerHTML += `
                                <div class="vote d-flex justify-content-between align-items-center pt-2 mb-md-2 mb-1">
                                    ${(window.showPollVotesCount !== false) ? `<span class="text-black fs-14 fw-6">{{ __('messages.other_lang.total_vote') }}: ${result.data.totalPollResults || 0}</span>` : '<span></span>'}
                                    <a href="javascript:void(0);" class="view-option fs-14 text-gray fw-6"
                                       data-id="${pollId}">{{ __('messages.other_lang.view_option') }}</a>
                                </div>
                                <span id="voteSuccess${pollId}"><p class="text-success">${result.message || 'Vote recorded'}</p></span>
                            `;

                            fetch("{{ route('user.vote') }}", {
                                    method: "POST",
                                    headers: {
                                        "X-CSRF-TOKEN": token,
                                        "Content-Type": "application/json"
                                    },
                                    body: JSON.stringify({})
                                })
                                .then(res => res.json())
                                .then(pointsData => {
                                    console.log(pointsData.message); // "5 points added!"
                                });

                            setTimeout(() => {
                                document.getElementById(`voteSuccess${pollId}`)?.remove();
                            }, 3000);
                        } else {
                            const errorBox = document.getElementById(`voteError${pollId}`);
                            if (errorBox) errorBox.innerHTML = `<p class="text-danger">${result.message || 'An error occurred'}</p>`;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        const errorBox = document.getElementById(`voteError${pollId}`);
                        if (errorBox) errorBox.innerHTML = `<p class="text-danger">An error occurred. Please try again.</p>`;
                    });
            });
        });
    });
</script>
<script>
    var postId = "{{ $postDetail->id }}";
    var _token = $('meta[name="csrf-token"]').attr('content');
    var currentSort = 'newest'; // Default sort
    
    $(document).ready(function() {
        // Load initial comments
        loadComments(1, currentSort);
        
        // Handle filter dropdown clicks
        $('.filter-option').on('click', function(e) {
            e.preventDefault();
            var sort = $(this).data('sort');
            var sortText = $(this).text();
            
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
                post_id: postId,
                sort: sort
            },
            success: function(data) {
                // Update comments HTML
                $('#blog-post-comment-body').html(
                    data.comments_html);

                // Build pagination if needed
                if (data.total_comments > 25) {
                    buildPagination(data.pagination.current_page, data.pagination.last_page);
                }

                // ✅ Scroll to specific comment if hash is present in URL
                // ✅ Scroll to specific comment if hash is present in URL
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

                            // Add temporary highlight class
                            target.classList.add('highlight-comment');

                            // Remove highlight after 3 seconds
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
                console.error(xhr.responseText);
            }
        });
    }


    // Function to build pagination
    function buildPagination(currentPage, lastPage) {
        var paginationHtml = '<ul class="pagination justify-content-center">';

        // Previous page
        if (currentPage > 1) {
            paginationHtml += '<li class="page-item">' +
                '<a class="page-link" href="#" data-page="' + (currentPage - 1) + '" style="color: #fff !important; border: none !important; background: linear-gradient(180deg, #2c1057, #1a0b2c) !important;">' +
                '&laquo;' +
                '</a>' +
                '</li>';
        } else {
            paginationHtml += '<li class="page-item disabled">' +
                '<span class="page-link btn-primary" style="color: #fff !important; border: none !important; background: linear-gradient(180deg, #2c1057, #1a0b2c) !important;">' +
                '&laquo;' +
                '</span>' +
                '</li>';
        }

        // Page numbers (show limited window if needed)
        var startPage = Math.max(currentPage - 2, 1);
        var endPage = Math.min(currentPage + 2, lastPage);
        let customStyle = 'color: #fff !important; border: none !important; background: linear-gradient(180deg, #2c1057, #1a0b2c) !important;';

        for (var i = startPage; i <= endPage; i++) {
            if (i === currentPage) {
                paginationHtml += '<li class="page-item active"><span class="page-link btn-primary" style="' + customStyle + '">' + i + '</span></li>';
            } else {
                paginationHtml += '<li class="page-item"><a class="page-link btn-primary" href="#" data-page="' + i + '" style="' + customStyle + '">' + i + '</a></li>';
            }
        }

        // Next page
        if (currentPage < lastPage) {
            paginationHtml += '<li class="page-item"><a class="page-link btn-primary" href="#" data-page="' + (parseFloat(currentPage) + parseFloat(1)) + '" style="' + customStyle + '">&raquo;</a></li>';
        } else {
            paginationHtml += '<li class="page-item disabled"><span class="page-link btn-primary" style="' + customStyle + '">&raquo;</span></li>';
        }

        paginationHtml += '</ul>';

        $('#pagination-container').html(paginationHtml);
    }

    // Listen for pagination click
    $(document).on('click', '#pagination-container .page-link', function(e) {
        e.preventDefault();
        var page = $(this).data('page');
        if (page) {
            loadComments(page, currentSort);
            $('html, body').animate({
                scrollTop: $("#blog-post-comment-body").offset().top
            }, 500);
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const videoContent = document.querySelector('.video-content');
        if (videoContent) {
            const blockquotes = videoContent.querySelectorAll('blockquote');
            blockquotes.forEach(element => {
                element.style.borderLeft = '4px solid #800080';
                // element.style.backgroundColor = '#f9f9f9';
                element.style.padding = '10px 20px';
                element.style.margin = '10px 0';
                // element.style.color = '#333';
                element.style.fontStyle = 'italic';
                element.style.fontSize = '16px';
            });
        }
    });
</script>
<script>
    document.querySelectorAll('.profile-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const userIdentifier = this.dataset.userIdentifier;
            const currentUrl = window.location.href;
            const profileUrl = `/user/${userIdentifier}/profile?return_to=${encodeURIComponent(currentUrl)}`;
            window.location.href = profileUrl;
        });
    });

    // Prevent default browser tooltip on hover - only show custom CSS tooltip
    document.querySelectorAll('.article-action-btn[title], .comment-btn-wrapper[title]').forEach(btn => {
        const originalTitle = btn.getAttribute('title');
        btn.addEventListener('mouseenter', function() {
            this.setAttribute('data-title', originalTitle);
            this.removeAttribute('title');
        });
        btn.addEventListener('mouseleave', function() {
            this.setAttribute('title', originalTitle);
            this.removeAttribute('data-title');
        });
    });
</script>

<script>
    // Image Copyright Display Script
    document.addEventListener('DOMContentLoaded', function() {
        const newsDesc = document.querySelector('.news-desc');
        if (newsDesc) {
            const images = newsDesc.querySelectorAll('img');
            images.forEach(img => {
                const copyrightText = img.getAttribute('data-copyright');
                if (copyrightText && copyrightText.trim() !== '') {
                    if (!img.nextElementSibling || !img.nextElementSibling.classList.contains('image-copyright')) {
                        const imgContainer = document.createElement('div');
                        imgContainer.style.position = 'relative';
                        imgContainer.style.display = 'inline-block';
                        imgContainer.style.width = img.style.width || 'auto';
                        imgContainer.style.marginBottom = '15px';
                        
                        img.parentNode.insertBefore(imgContainer, img);
                        imgContainer.appendChild(img);
                        
                        const copyrightDiv = document.createElement('div');
                        copyrightDiv.className = 'image-copyright';
                        copyrightDiv.textContent = '©' + copyrightText;
                        copyrightDiv.style.position = 'absolute';
                        copyrightDiv.style.bottom = '5px';
                        copyrightDiv.style.right = '10px';
                        copyrightDiv.style.color = '#fff';
                        copyrightDiv.style.fontSize = '12px';
                        copyrightDiv.style.fontWeight = '500';
                        copyrightDiv.style.zIndex = '10';
                        copyrightDiv.style.pointerEvents = 'none';
                        copyrightDiv.style.textShadow = '1px 1px 2px rgba(0,0,0,0.8)';
                        
                        imgContainer.appendChild(copyrightDiv);
                    }
                }
            });
        }
    });
</script>

@endpush
@endsection
@section('script')
{{-- {!! reCaptcha()->renderJs() !!} --}}
<script>
    let userProfile = '{{ asset('
    images / avatar.png ') }}'
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const links = document.getElementsByTagName("a");

        for (let link of links) {
            if (link.getAttribute("href") && link.getAttribute("target") !== "_blank") {
                link.setAttribute("target", "_blank");
                link.setAttribute("rel", "noopener noreferrer");
            }
        }
    });
</script>
@endsection