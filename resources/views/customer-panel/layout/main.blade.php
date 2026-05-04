<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        @hasSection('title')
            @yield('title') | 2playerz.de
        @endif
        
    </title>
    @vite('resources/css/app.css')
    {{-- <script src="https://cdn.tailwindcss.com"></script>--}}
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <!-- Custom Emoji Picker with German Language Support -->
    <script src="{{ asset('assets/js/custom-emoji-picker.js') }}"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        (function() {
            const theme = localStorage.getItem("theme");
            if (theme) {
                document.documentElement.classList.add(theme);
            }

            // Server-side dark mode preference
            @if(getLogInUser() && getLogInUser()->theme == 'dark')
                document.documentElement.classList.add('dark');
            @endif
        })();
    </script>
    
    <style>
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
            }

            .mobile-loading-bar.active {
                width: 100%;
                transition: width 0.4s ease-out;
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
</head>

<body class="min-h-screen dark:bg-[#09090B] dark:text-white bg-white text-black">
    <!-- Mobile Loading Progress Bar -->
    <div class="mobile-loading-bar" id="mobileLoadingBar"></div>

    <!-- Mobile Header -->
    <div class="md:hidden flex items-center justify-between p-4 bg-gray-800 shadow sticky top-0 z-40" style="
                    background: linear-gradient(90deg, rgba(0, 0, 0, 1) 0%, rgba(122, 0, 122, 1) 60%, rgba(75, 0, 75, 1) 100%);">
        <a href="{{ url('/') }}">
            <img src="{{ !empty(getAppLogo()) ? getAppLogo() : asset('assets/image/infyom-logo.png') }}" alt="Logo" class="h-8 w-auto">
        </a>
        @auth
        <button id="menuToggle" class="text-white focus:outline-none text-2xl">☰</button>
        @endauth
    </div>

    <!-- Main Layout -->
    <div class="md:flex flex-1 gap-3 min-h-dvh">
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>

        <!-- Desktop Sidebar (Hide if URL starts with /user/*) -->
        @auth
        @if(!Request::is('user/*'))
        <aside class="hidden md:flex flex-col w-60 p-1 space-y-4 sticky top-0 h-screen overflow-y-auto" style="
                    background: linear-gradient(90deg, rgba(0, 0, 0, 1) 0%, rgba(122, 0, 122, 1) 60%, rgba(75, 0, 75, 1) 100%);
                ">
            <div class="p-2 flex-shrink-0"     style="padding-top: 10px;" >
                <a href="{{ env('APP_URL') }}"><img src="{{ !empty(getAppLogo()) ? getAppLogo() : asset('assets/image/infyom-logo.png') }}" alt="logo" class="" /></a>
            </div>
            <nav class="flex flex-col space-y-2 flex-1">
                <a href="{{ route('customer.profile') }}" class="text-white p-3 rounded-md transition-all hover:!text-[#b051b0] {{ request()->routeIs('customer.profile') ? '!text-[#b051b0]' : 'text-white' }}">
                    {{ __('messages.customer_profile.my_profile')}}
                </a>
                <a href="{{ route('customer.profile.edit') }}" class="text-white p-3 rounded-md transition-all hover:!text-[#b051b0] {{ request()->routeIs('customer.profile.edit') ? '!text-[#b051b0]' : 'text-white' }}">{{ __('messages.edit_profile.edit_profile')}}</a>
                <a href="{{ route('notifications') }}" class="text-white p-3 rounded-md transition-all hover:!text-[#b051b0] {{ request()->routeIs('notifications') ? '!text-[#b051b0]' : 'text-white' }}">{{ __('messages.customer_profile.notifications')}}</a>
                <a href="{{ route('members.following') }}" class="text-white p-3 rounded-md transition-all hover:!text-[#b051b0] {{ request()->routeIs('members.following') ? '!text-[#b051b0]' : 'text-white' }}">{{ __('messages.members_i_follow')}}</a>
                @if(auth()->user()->who_can_send_messages !== 'nobody')
                    <a href="{{ route('messages.index') }}" class="text-white p-3 rounded-md transition-all hover:!text-[#b051b0] {{ request()->routeIs('messages.*') ? '!text-[#b051b0]' : 'text-white' }}">{{ __('messages.other_lang.my_messages')}}</a>
                @else
                    <span class="text-gray-500 p-3 rounded-md cursor-not-allowed opacity-50" title="Messaging is disabled">{{ __('messages.other_lang.my_messages')}}</span>
                @endif
                <a href="{{ route('blocked.members') }}" class="text-white p-3 rounded-md transition-all hover:!text-[#b051b0] {{ request()->routeIs('blocked.members') ? '!text-[#b051b0]' : 'text-white' }}">{{ __('messages.block.blocked_members')}}</a>
                <a href="{{ route('profile.visitors') }}" class="text-white p-3 rounded-md transition-all hover:!text-[#b051b0] {{ request()->routeIs('profile.visitors') ? '!text-[#b051b0]' : 'text-white' }}">{{ __('messages.profile.my_profile_visitors')}}</a>
                <a href="{{ route('customer.profile.comments') }}" class="text-white p-3 rounded-md transition-all hover:!text-[#b051b0] {{ request()->routeIs('customer.profile.comments') ? '!text-[#b051b0]' : 'text-white' }}">{{ __('messages.customer_profile.my_comments')}}</a>
                <a href="{{ route('wishlist.index') }}" class="text-white p-3 rounded-md transition-all hover:!text-[#b051b0] {{ request()->routeIs('wishlist.index') ? '!text-[#b051b0]' : 'text-white' }}">{{ __('messages.wishlist.my_wishlist')}}</a>
                <a href="/" class="text-white p-3 rounded-md transition-all hover:!text-[#b051b0] {{ request()->is('/') ? '!text-[#b051b0]' : 'text-white' }}">{{ __('messages.customer_profile.to_website')}}</a>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-white p-3 rounded-md transition-all hover:!text-[#b051b0] {{ request()->is('logout') ? '!text-[#b051b0]' : 'text-white' }}">{{ __('messages.details.logout')}}</a>
            </nav>
        </aside>
        @endif
        @endauth

        <!-- Main Content -->
        <main class="flex-1 p-1">
            @yield('content')
        </main>
    </div>

    <!-- Mobile Sidebar -->
    @auth
    <div id="mobileSidebar" class="fixed inset-y-0 right-0 w-64 bg-gray-800 text-white transform translate-x-full transition-transform duration-300 ease-in-out z-50 md:hidden p-4 overflow-y-auto" style="
                    background: linear-gradient(90deg, rgba(0, 0, 0, 1) 0%, rgba(122, 0, 122, 1) 60%, rgba(75, 0, 75, 1) 100%);">
        <button id="closeSidebar" class="text-white text-xl mb-4 sticky top-0 bg-transparent">✖</button>
        <nav class="flex flex-col space-y-4">
            <a href="/" class="hover:text-purple-400 {{ request()->is('/') ? 'text-purple-400' : '' }}">{{ __('messages.customer_profile.to_website')}}</a>
            <a href="{{ route('customer.profile') }}" class="hover:text-purple-400 {{ request()->routeIs('customer.profile') ? 'text-purple-400' : '' }}">{{ __('messages.customer_profile.my_profile')}}</a>
            <a href="{{ route('customer.profile.edit') }}" class="hover:text-purple-400 {{ request()->routeIs('customer.profile.edit') ? 'text-purple-400' : '' }}">{{ __('messages.edit_profile.edit_profile')}}</a>
            <a href="{{ route('notifications') }}" class="hover:text-purple-400 {{ request()->routeIs('notifications') ? 'text-purple-400' : '' }}">{{ __('messages.customer_profile.notifications')}}</a>
            <a href="{{ route('members.following') }}" class="hover:text-purple-400 {{ request()->routeIs('members.following') ? 'text-purple-400' : '' }}">{{ __('messages.members_i_follow')}}</a>
            @if(auth()->user()->who_can_send_messages !== 'nobody')
                <a href="{{ route('messages.index') }}" class="hover:text-purple-400 {{ request()->routeIs('messages.*') ? 'text-purple-400' : '' }}">{{ __('messages.other_lang.my_messages')}}</a>
            @else
                <span class="text-gray-500 cursor-not-allowed opacity-50" title="Messaging is disabled">{{ __('messages.other_lang.my_messages')}}</span>
            @endif
            <a href="{{ route('blocked.members') }}" class="hover:text-purple-400 {{ request()->routeIs('blocked.members') ? 'text-purple-400' : '' }}">{{ __('messages.block.blocked_members')}}</a>
            <a href="{{ route('profile.visitors') }}" class="hover:text-purple-400 {{ request()->routeIs('profile.visitors') ? 'text-purple-400' : '' }}">{{ __('messages.profile.my_profile_visitors')}}</a>
            <a href="{{ route('customer.profile.comments') }}" class="hover:text-purple-400 {{ request()->routeIs('customer.profile.comments') ? 'text-purple-400' : '' }}">{{ __('messages.customer_profile.my_comments')}}</a>
            <a href="{{ route('wishlist.index') }}" class="hover:text-purple-400 {{ request()->routeIs('wishlist.index') ? 'text-purple-400' : '' }}">{{ __('messages.wishlist.my_wishlist')}}</a>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="hover:text-purple-400 {{ request()->is('logout') ? 'text-purple-400' : '' }}">{{ __('messages.details.logout')}}</a>
        </nav>
    </div>
    @endauth




    <script>
        const menuToggle = document.getElementById('menuToggle');
        const mobileSidebar = document.getElementById('mobileSidebar');
        const closeSidebar = document.getElementById('closeSidebar');

        // Check if mobileSidebar exists before adding event listeners
        if (menuToggle && mobileSidebar && closeSidebar) {
            menuToggle.addEventListener('click', () => {
                mobileSidebar.classList.remove('translate-x-full');
            });

            closeSidebar.addEventListener('click', () => {
                mobileSidebar.classList.add('translate-x-full');
            });
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            localStorage.setItem('userTimeZone', userTimeZone);

            // Logout form ke liye time zone pass
            document.querySelectorAll('form[action="/logout"]').forEach(form => {
                form.addEventListener('submit', function(e) {
                    const timeZone = localStorage.getItem('userTimeZone');
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'time_zone';
                    hiddenInput.value = timeZone;
                    this.appendChild(hiddenInput);
                });
            });
        });
    </script>

    <!-- Mobile Loading Progress Bar Script -->
    <script>
        (function() {
            // Only run on mobile devices
            if (window.innerWidth >= 768) {
                return;
            }

            const loadingBar = document.getElementById('mobileLoadingBar');
            let loadingTimeout;
            let isNavigating = false;

            // Show loading bar
            function showLoadingBar() {
                if (loadingBar && !isNavigating) {
                    isNavigating = true;
                    loadingBar.style.width = '0%';
                    loadingBar.style.opacity = '1';
                    loadingBar.classList.remove('complete');
                    loadingBar.classList.add('active');
                    
                    // Simulate progress
                    setTimeout(() => {
                        if (loadingBar) {
                            loadingBar.style.width = '70%';
                        }
                    }, 100);
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
                    }, 150);
                }
            }

            // Handle link clicks
            document.addEventListener('click', function(e) {
                let target = e.target;
                
                // Find the closest anchor tag
                while (target && target.tagName !== 'A') {
                    target = target.parentElement;
                }

                if (target && target.tagName === 'A') {
                    const href = target.getAttribute('href');
                    
                    // Only handle internal links (not external, mailto, tel, etc.)
                    if (href && 
                        !href.startsWith('#') && 
                        !href.startsWith('mailto:') && 
                        !href.startsWith('tel:') && 
                        !href.startsWith('javascript:') &&
                        !target.hasAttribute('target') &&
                        (href.startsWith('/') || href.includes(window.location.hostname))) {
                        
                        showLoadingBar();
                    }
                }
            }, true); // Use capture phase to catch clicks early

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
                    }, 100);
                });
            }

            // Handle browser back/forward buttons
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    // Page was loaded from cache
                    completeLoadingBar();
                }
            });

            // Show loading bar on beforeunload (when navigating away)
            window.addEventListener('beforeunload', function() {
                if (!isNavigating) {
                    showLoadingBar();
                }
            });
        })();
    </script>

    <!-- Success toast (same as comment success) -->
    <script>
    function showSuccessToast(message) {
        const existingToast = document.getElementById('custom-toast');
        if (existingToast) existingToast.remove();
        const toast = document.createElement('div');
        toast.id = 'custom-toast';
        toast.className = 'custom-toast';
        toast.innerHTML = '<div class="icon">✔</div><div class="message">' + (message || '') + '</div><div class="close-btn" onclick="this.parentElement.remove()">×</div><div class="timer-bar"></div>';
        if (!document.getElementById('toast-styles')) {
            const style = document.createElement('style');
            style.id = 'toast-styles';
            style.textContent = '.custom-toast{position:fixed;top:20px;right:20px;width:320px;background:#4caf50;color:#fff;padding:12px 16px;border-radius:4px;display:flex;align-items:center;box-shadow:0 0 12px rgba(0,0,0,0.3);z-index:10000;overflow:hidden;animation:slideInRight 0.3s ease-out}.custom-toast .icon{font-size:20px;margin-right:10px}.custom-toast .message{flex:1;font-weight:bold}.custom-toast .close-btn{margin-left:10px;cursor:pointer;font-size:20px;font-weight:bold}.custom-toast .timer-bar{position:absolute;bottom:0;left:0;height:4px;background:rgba(255,255,255,0.65);animation:shrinkBar 5s linear forwards}@keyframes slideInRight{from{transform:translateX(100%);opacity:0}to{transform:translateX(0);opacity:1}}@keyframes shrinkBar{from{width:100%}to{width:0%}}';
            document.head.appendChild(style);
        }
        document.body.appendChild(toast);
        setTimeout(function(){ if(toast.parentElement){ toast.style.animation='slideInRight 0.3s ease-out reverse'; setTimeout(function(){ toast.remove(); }, 300); } }, 5000);
    }
    </script>
</body>

</html>