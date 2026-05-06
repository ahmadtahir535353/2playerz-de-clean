<!DOCTYPE html>
<html lang="de" {{ getFrontSelectLanguageIsoCode() == 'ar' ? 'dir=rtl' : '' }}>
@php
$settings = getSettingValue();
@endphp

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- @if (!empty(getSEOTools()->keyword)) --}}
    <meta name="keywords" content="@yield('meta_tags'),{{ !empty(getSEOTools()) ? getSEOTools()->keyword : '' }}">
    {{-- @endif --}}
    {{-- @if (!empty(getSEOTools()->site_description)) --}}
    <meta name="description"
        content="@if (View::hasSection('meta_description')) @yield('meta_description')
        @else{{ !empty(getSEOTools()) ? getSEOTools()->site_description : '' }} @endif">
    {{-- @endif --}}

    <meta http-equiv="content-language" content="{{ getFrontSelectLanguageName() ?? 'en' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
        @auth
            <meta name="user-id" content="{{ auth()->id() }}">
        @endauth

    <!-- icons for notifications -->
    <link rel="icon" href="/icon-192x192.png">
    @php
        // Priority 1: Check if page has custom meta_image (for post details, etc.)
        $ogImage = null;
        if (View::hasSection('meta_image')) {
            $customImage = trim(View::yieldContent('meta_image'));
            if (!empty($customImage)) {
                $ogImage = $customImage;
            }
        }
        
        // Priority 2: Use default OG image if no custom image
        if (empty($ogImage)) {
            $ogImage = 'https://2playerz.de/og-image1.jpg';
        }
        
        // Priority 3: Fallback to logo if default doesn't work
        // (This is a safety fallback - in practice, default should always work)
        if (empty($ogImage)) {
            $ogImage = getAppLogo();
        }
        
        // Priority 4: Final fallback to default image
        if (empty($ogImage)) {
            $ogImage = asset('front_web/images/default.jpg');
        }
        
        // Ensure absolute URL (convert relative to absolute) and force HTTPS
        if (!empty($ogImage)) {
            // If it's already absolute (starts with http:// or https://)
            if (preg_match('/^https?:\/\//', $ogImage)) {
                // Force HTTPS for better compatibility with Twitter/Facebook
                $ogImage = preg_replace('/^http:\/\//', 'https://', $ogImage);
            } else {
                // If it starts with /, it's already a path, use url() to make it absolute
                if (strpos($ogImage, '/') === 0) {
                    $ogImage = url($ogImage);
                } else {
                    // Otherwise, use asset() or url() to make it absolute
                    $ogImage = url($ogImage);
                }
                // Ensure HTTPS
                $ogImage = preg_replace('/^http:\/\//', 'https://', $ogImage);
            }
        }
        
        // Determine image type based on extension
        $imageType = 'image/jpeg';
        if (preg_match('/\.(png|jpg|jpeg|gif|webp)$/i', $ogImage, $matches)) {
            $ext = strtolower($matches[1]);
            $imageType = $ext === 'png' ? 'image/png' : ($ext === 'gif' ? 'image/gif' : ($ext === 'webp' ? 'image/webp' : 'image/jpeg'));
        }
        
        $pageTitle = $pageTitle ?? '2Playerz - Dein Konsolenmagazin';
        $pageDescription = $pageDescription ?? 'Neuigkeiten & Testberichte zu PlayStation, Xbox und Nintendo';
        $siteName = getAppName() ?? '2Playerz';
    @endphp
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:title" content="{{ $pageTitle }}" />
    <meta property="og:description" content="{{ $pageDescription }}" />
    <meta property="og:image" content="{{ $ogImage }}" />
    <meta property="og:image:secure_url" content="{{ $ogImage }}" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
    <meta property="og:image:type" content="{{ $imageType }}" />
    <meta property="og:site_name" content="{{ $siteName }}" />
    @if(isset($post))
        <meta property="og:updated_time" content="{{ $post->updated_at->toIso8601String() }}" />
    @else
        <meta property="og:updated_time" content="{{ now()->toIso8601String() }}" />
    @endif
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="@2playerz" />
    <meta name="twitter:creator" content="@2playerz" />
    <meta name="twitter:title" content="{{ $pageTitle }}" />
    <meta name="twitter:description" content="{{ $pageDescription }}" />
    <meta name="twitter:image" content="{{ $ogImage }}" />
    <meta name="twitter:image:src" content="{{ $ogImage }}" />
    <meta name="twitter:image:alt" content="{{ $pageTitle }}" />
    
    <!-- Additional meta for better compatibility -->
    <meta name="twitter:domain" content="2playerz.de" />
    <meta name="twitter:url" content="{{ url()->current() }}" />
    <!--<meta name="google-site-verification" content="S9yR7Z2PEdp7XXxXZ1pKAipexuYYSeD-SJEtiNwUupA" />-->
    <meta name="google-site-verification" content="VN9LVf3gghWH0OoiTv9I_YNz0Pm-CZXG6JCwAtApBU" />
    <meta name="yandex-verification" content="4128862151f890d5" />
    <title>@yield('title') |
        {{ !empty(getSEOTools()->site_title) ? getSEOTools()->site_title : $settings['application_name'] }}
    </title>

    <link rel="shortcut icon" type="image/x-icon"
        href="{{ !empty(getAppFavicon()) ? getAppFavicon() : asset('assets/image/favicon-infyom.png') }}">
    {{-- done --}}
    <link href="{{ asset('assets/css/front-third-party.css') }}" rel="stylesheet" type="text/css">

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="canonical" href="{{ url()->current() }}" />
    {{-- done --}}
    {{-- <link href="{{ mix('assets/css/front-pages.css') }}" rel="stylesheet" type="text/css"> --}}
    @stack('preload')
    @livewireStyles
    @livewireScripts
    {{-- <script src="https://cdn.jsdelivr.net/gh/livewire/turbolinks@v0.1.x/dist/livewire-turbolinks.js"
        data-turbolinks-eval="false" data-turbo-eval="false"></script> --}}
    {!! reCaptcha()->renderJs() !!}
    @php
    $langSession = Session::get('frontLanguageChange');
    $frontLanguage = !isset($langSession) ? getSettingValue()['front_language'] : $langSession;
    @endphp

    <script src='https://www.google.com/recaptcha/api.js'></script>
    {{-- done --}}
    <script src="{{ asset('assets/js/front-third-party.js') }}"></script>
    @routes
    {{-- note done --}}
    <script src="{{ asset('messages.js') }}"></script>
    <!-- Custom Emoji Picker with German Language Support -->
    <script src="{{ asset('assets/js/custom-emoji-picker.js') }}"></script>

    <script>
        // Process ALL imgur embeds manually (no limit on number of embeds)
        (function() {
            function createImgurEmbed(imgurId) {
                // Create iframe embed directly
                var iframe = document.createElement('iframe');
                iframe.className = 'imgur-embed-iframe';
                iframe.style.width = '100%';
                iframe.style.height = '500px';
                iframe.style.border = '0';
                iframe.style.borderRadius = '3px';
                iframe.style.display = 'block';
                iframe.style.marginBottom = '20px';
                iframe.setAttribute('allowfullscreen', 'allowfullscreen');
                iframe.setAttribute('scrolling', 'no');
                iframe.setAttribute('id', 'imgur-embed-iframe-pub-' + imgurId);
                iframe.setAttribute('src', 'https://imgur.com/' + imgurId + '/embed?pub=true&ref=https://2playerz.de');
                
                return iframe;
            }
            
            function processImgurEmbeds() {
                // Process blockquote.imgur-embed-pub elements
                document.querySelectorAll('blockquote.imgur-embed-pub:not([data-processed])').forEach(function(blockquote) {
                    var imgurId = blockquote.getAttribute('data-id');
                    if (imgurId) {
                        blockquote.setAttribute('data-processed', 'true');
                        
                        // Create iframe and replace blockquote
                        var iframe = createImgurEmbed(imgurId);
                        blockquote.parentNode.replaceChild(iframe, blockquote);
                    }
                });
                
                // Process imgur links that haven't been converted
                document.querySelectorAll('a[href*="imgur.com"]:not([data-processed])').forEach(function(link) {
                    link.setAttribute('data-processed', 'true');
                    
                    // Extract imgur ID from URL
                    var match = link.href.match(/imgur\.com\/(?:gallery\/|a\/)?([a-zA-Z0-9]+)/);
                    if (match && match[1]) {
                        var imgurId = match[1];
                        
                        // Create iframe and replace link
                        var iframe = createImgurEmbed(imgurId);
                        link.parentNode.replaceChild(iframe, link);
                    }
                });
            }
            
            // Process when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(processImgurEmbeds, 100);
                    setTimeout(processImgurEmbeds, 500);
                });
            } else {
                setTimeout(processImgurEmbeds, 100);
                setTimeout(processImgurEmbeds, 500);
            }
            
            // Watch for dynamically added content
            if (typeof MutationObserver !== 'undefined' && document.body) {
                var observer = new MutationObserver(function(mutations) {
                    var shouldProcess = false;
                    mutations.forEach(function(mutation) {
                        if (mutation.addedNodes.length) {
                            mutation.addedNodes.forEach(function(node) {
                                if (node.nodeType === 1) {
                                    if ((node.tagName === 'BLOCKQUOTE' && node.classList.contains('imgur-embed-pub')) ||
                                        (node.tagName === 'A' && node.href && node.href.indexOf('imgur.com') !== -1)) {
                                        shouldProcess = true;
                                    }
                                }
                            });
                        }
                    });
                    if (shouldProcess) {
                        setTimeout(processImgurEmbeds, 100);
                    }
                });
                
                observer.observe(document.body, {
                    childList: true,
                    subtree: true
                });
            }
        })();
    </script>

    <script data-turbo-eval="false">
        let userProfile = "{{ asset('images/avatar.png') }}";
        let siteKey = "{{ $settings['site_key'] }}"
        let frontLanguage = "{{ App\Models\Language::find($frontLanguage)->iso_code }}"
        let lang = "{{ getFrontSelectLanguageIsoCode() ?? 'en' }}"
        // Lang.setLocale(frontLanguage)
    </script>
    {{-- done --}}
    {{-- <script src="{{ mix('assets/js/front-pages.js') }}"></script> --}}
    {{-- @vite('resources/assets/js/turbo.js') --}}
    @vite('resources/assets/front/scss/main.scss')
    @vite('resources/assets/js/custom/helpers.js')
    @vite('resources/assets/js/web/custom.js')
    @vite('resources/assets/js/front/gallery-page.js')
    @vite('resources/assets/js/front/video-page.js')
    @vite('resources/assets/js/front/audio.js')
    @vite('resources/assets/js/front/home.js')
    @vite(['resources/js/app.js'])
    @vite('resources/assets/js/post-reaction/post_reaction.js')
    {!! !empty(getSEOTools()->google_analytics) ? getSEOTools()->google_analytics : '' !!}
    @if (getFrontSelectLanguageIsoCode() == 'ar')
    <style>
        .section-heading h2:after {
            left: -100px !important;
            right: auto !important;


        }

        @media (max-width: 575px) {
            .section-heading h2:after {
                /* display:none; */
                /* left: -70px !important; */
                /* right: auto !important; */
            }
        }

        .blog-section .blog .email-box .button {
            padding: 14px 30px;
            box-shadow: none;
            bottom: 0;
            height: 50px;
            left: 0 !important;
            right: auto !important;
            color: white;
            border: transparent;
            background: linear-gradient(180deg, #2c1057, #1a0b2c);
        }


        .offcanvas {
            height: 100%;
            width: 400px;
            position: fixed;
            z-index: 99999;
            top: 0;
            right: 0;
            overflow-x: hidden;
            transition: 0.5s;
        }

        .offcanvas-start {
            top: 0;
            left: 0;
            width: 400px;
            border-right: 1px solid rgba(50, 64, 77, .2);
            transform: translateX(480%);
        }

        .sticky-box {
            left: -10px;
            right: auto;
        }

        .theme-change-button {
            transform: rotate(90deg);
        }

        .theme-change-button:hover {
            transform: scale(1.1) rotate(90deg);
        }

        .breadcrumb-item+.breadcrumb-item::before {
            float: right;
            padding-left: .5rem;
            color: #6c757d;
            content: var(--bs-breadcrumb-divider, "/")
                /* rtl: var(--bs-breadcrumb-divider, "/") */
            ;
        }

        .toast-close-button {
            top: 0.1em !important;
        }

        .hero-section .hero-image .hero-content h1 {
            padding-left: 180px;
            padding-right: 0px;
        }

        .pagination {
            margin: 0px !important;
        }

        .themeSwitchCheckbox {
            display: none;
        }
    </style>
    @endif


    @stack('css')

    <style>
        html {
            background: #1a1a1a;
        }

        body {
            visibility: hidden;
        }

        body.dark-mode,
        body.light-mode {
            visibility: visible;
        }

        @media screen and (max-width: 500px) {
            .news-desc img {
                height: 100%;
                width: 100%;
            }
        }
         @media (max-width: 575px) {
            .section-heading h2:after {
                /* display:none; */
                /* left: -70px !important; */
                /* right: auto !important; */
            }
        }

        /* Mobile Loading Progress Bar - Only visible on mobile (< 768px) */
        @media (max-width: 767px) {
            .mobile-loading-bar {
                position: fixed;
                top: 0;
                left: 0;
                width: 0%;
                height: 3px;
                background: linear-gradient(90deg, #734E96, #B051B0);
                z-index: 99999;
                transition: width 0.3s ease;
                box-shadow: 0 2px 4px rgba(115, 78, 150, 0.3);
                display: block;
                pointer-events: none;
            }

            .mobile-loading-bar.active {
                width: 100%;
                transition: width 0.4s ease-out;
                display: block !important;
                opacity: 1 !important;
            }

            .mobile-loading-bar.complete {
                width: 100%;
                opacity: 0;
                transition: opacity 0.2s ease;
            }
        }

        @media (min-width: 768px) {
            .mobile-loading-bar {
                display: none !important;
            }
        }
    </style>

    <script>
        window.addEventListener('DOMContentLoaded', function() {
            var theme = localStorage.getItem('theme');
            if (theme !== 'dark' && theme !== 'light') {
                theme = 'dark';
                localStorage.setItem('theme', 'dark');
            }
            document.body.classList.add(theme + '-mode');
        });
    </script>

    <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "Organization",
          "name": "2Playerz",
          "url": "https://www.2playerz.de",
          "logo": "{{ asset('uploads/logo/216/01JS37FN18W2RSNP53AH5NFTHB.png') }}",
          "sameAs": [
            "https://www.youtube.com/@2Playerz‑Gaming"
          ]
        }
    </script>
    @stack('jsonld')
<script>
    window.Laravel = {
        user: @json(auth()->user())
    };
</script>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-17692635194"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'AW-17692635194');
</script>


</head>


<body class="body sss">
    <!-- Mobile Loading Progress Bar -->
    <div class="mobile-loading-bar" id="mobileLoadingBar"></div>
    @include('front_new.layouts.header')
    <div>
        @yield('content')
    </div>

    <!-- start footer section -->
    @include('front_new.layouts.footer')
    <!-- end footer section -->
    @if ($settings['show_cookie'])
    @include('cookie-consent::index')
    @endif

    <div class='sticky-box d-none'>
        <button type="button" class="tags fw-7 theme-change-button">Theme</button>
    </div>
    @include('setting.theme_change_modal')
    @stack('js')
    <script
        src="https://sak.userreport.com/2playerz/launcher.js"
        async
        id="userreport-launcher-script">
    </script>

    <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
    <script async="" src="https://embed.reddit.com/widgets.js" charset="UTF-8"></script>

    <!-- Mobile Loading Progress Bar Script -->
    <script>
        (function() {
            // Only run on mobile devices
            if (window.innerWidth >= 768) {
                return;
            }

            const loadingBar = document.getElementById('mobileLoadingBar');
            let isNavigating = false;

            // Show loading bar immediately
            function showLoadingBar() {
                if (loadingBar) {
                    isNavigating = true;
                    loadingBar.style.width = '0%';
                    loadingBar.style.opacity = '1';
                    loadingBar.style.display = 'block';
                    loadingBar.classList.remove('complete');
                    loadingBar.classList.add('active');
                    
                    // Immediately start progress animation
                    requestAnimationFrame(() => {
                        if (loadingBar) {
                            loadingBar.style.width = '30%';
                        }
                    });
                    
                    // Continue progress
                    setTimeout(() => {
                        if (loadingBar) {
                            loadingBar.style.width = '70%';
                        }
                    }, 50);
                }
            }

            // Complete loading bar
            function completeLoadingBar() {
                if (loadingBar) {
                    loadingBar.style.width = '100%';
                    setTimeout(() => {
                        if (loadingBar) {
                            loadingBar.classList.remove('active');
                            loadingBar.classList.add('complete');
                            setTimeout(() => {
                                if (loadingBar) {
                                    loadingBar.style.width = '0%';
                                    loadingBar.style.opacity = '1';
                                    loadingBar.classList.remove('complete');
                                    isNavigating = false;
                                }
                            }, 200);
                        }
                    }, 100);
                }
            }

            // Handle all link clicks - immediate response
            document.addEventListener('click', function(e) {
                let target = e.target;
                
                // Find the closest anchor tag or clickable element
                while (target && target.tagName !== 'A' && !target.onclick) {
                    target = target.parentElement;
                    if (!target || target === document.body) break;
                }

                if (target) {
                    // Handle anchor tags
                    if (target.tagName === 'A') {
                        const href = target.getAttribute('href');
                        
                        // Only handle internal links (not external, mailto, tel, etc.)
                        if (href && 
                            !href.startsWith('#') && 
                            !href.startsWith('mailto:') && 
                            !href.startsWith('tel:') && 
                            !href.startsWith('javascript:') &&
                            !target.hasAttribute('target') &&
                            (href.startsWith('/') || href.includes(window.location.hostname))) {
                            
                            // Show immediately
                            showLoadingBar();
                        }
                    }
                    // Handle Livewire or other dynamic navigation
                    else if (target.closest('[wire\\:click], [x-data], [data-turbo-link]')) {
                        showLoadingBar();
                    }
                }
            }, true); // Use capture phase to catch clicks early

            // Show loading on any navigation start
            window.addEventListener('beforeunload', function() {
                showLoadingBar();
            });

            // Handle page visibility changes (when user switches tabs and comes back)
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    // Page is being hidden, might be navigating
                    if (!isNavigating) {
                        showLoadingBar();
                    }
                }
            });

            // Hide loading bar when page is fully loaded
            if (document.readyState === 'complete') {
                completeLoadingBar();
            } else {
                window.addEventListener('load', function() {
                    completeLoadingBar();
                });

                // Also handle DOMContentLoaded for faster pages
                document.addEventListener('DOMContentLoaded', function() {
                    // Small delay to ensure page is rendering
                    setTimeout(function() {
                        if (!isNavigating) {
                            completeLoadingBar();
                        }
                    }, 50);
                });
            }

            // Handle browser back/forward buttons
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    // Page was loaded from cache
                    completeLoadingBar();
                } else {
                    // New page load
                    completeLoadingBar();
                }
            });

            // Handle popstate (browser navigation)
            window.addEventListener('popstate', function() {
                showLoadingBar();
            });
        })();
    </script>

</body>

</html>