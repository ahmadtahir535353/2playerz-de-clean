    <div>
        <x-filament-panels::form wire:submit="authenticate">
            <div class="w-full pb-2 text-center relative flex flex-col items-center">
                <a href="{{ route('front.home') }}" data-turbo="false"
                    class="text-decoration-none sidebar-logo flex items-center" target="_blank" title="InfyNews">
                    <div class="image image-mini">
                        <img src="{{ getAppLogo() }}" class="me-4" alt="InfyNews-logo" width="100px" height="30px">
                    </div>
                </a>
                <span>
                    <div class=""></div>
                </span>
                <h1
                    class="fi-simple-header-heading text-center text-2xl font-bold tracking-tight text-gray-950 dark:text-white">
                    {{ __('messages.common.sign_in') }}
                </h1>

                <p class="fi-simple-header-subheading mt-2 text-center text-sm text-gray-500 dark:text-gray-400">
                    {{ __('messages.common.new_here') . '?' }}
                    <a href="{{ route('filament.auth.auth.register') }}"
                        class="fi-link group/link relative inline-flex items-center justify-center outline-none fi-size-md fi-link-size-md gap-1.5 fi-color-custom fi-color-primary fi-ac-action fi-ac-link-action">
                        <span
                            class="font-semibold text-sm text-custom-600 dark:text-custom-400 group-hover/link:underline group-focus-visible/link:underline custom-signup-link"
                            style="--c-400:var(--primary-400);--c-600:var(--primary-600);">
                            {{ __('messages.common.create_an_account') }}
                        </span>
                    </a>
                </p>
            </div>
            {{ $this->form }}
            <x-filament-panels::form.actions :actions="$this->getFormActions()" />

            {{-- Google Login - Commented out as per client request --}}
            {{-- @if (config('app.google_client_id') && config('app.google_client_secret') && config('app.google_redirect'))
                <div class="">
                    <a href="{{ route('social.login', 'google') }}"
                        class="border border-gray-300 rounded mt-2 text-black bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm w-full px-5 py-2 text-center dark:bg-gray-200 dark:hover:bg-gray-300 dark:focus:ring-gray-400 flex items-center justify-center space-x-3">
                        <i class="fa-brands fa-google fs-2"></i>
                        <div class="flex items-center space-x-2">
                            <svg width="24px" height="24px" viewBox="0 0 48 48" version="1.1"
                                xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <title>Google-color</title>
                                <desc>Created with Sketch.</desc>
                                <defs></defs>
                                <g id="Icons" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <g id="Color-" transform="translate(-401.000000, -860.000000)">
                                        <g id="Google" transform="translate(401.000000, 860.000000)">
                                            <path
                                                d="M9.82727273,24 C9.82727273,22.4757333 10.0804318,21.0144 10.5322727,19.6437333 L2.62345455,13.6042667 C1.08206818,16.7338667 0.213636364,20.2602667 0.213636364,24 C0.213636364,27.7365333 1.081,31.2608 2.62025,34.3882667 L10.5247955,28.3370667 C10.0772273,26.9728 9.82727273,25.5168 9.82727273,24"
                                                id="Fill-1" fill="#FBBC05"></path>
                                            <path
                                                d="M23.7136364,10.1333333 C27.025,10.1333333 30.0159091,11.3066667 32.3659091,13.2266667 L39.2022727,6.4 C35.0363636,2.77333333 29.6954545,0.533333333 23.7136364,0.533333333 C14.4268636,0.533333333 6.44540909,5.84426667 2.62345455,13.6042667 L10.5322727,19.6437333 C12.3545909,14.112 17.5491591,10.1333333 23.7136364,10.1333333"
                                                id="Fill-2" fill="#EB4335"></path>
                                            <path
                                                d="M23.7136364,37.8666667 C17.5491591,37.8666667 12.3545909,33.888 10.5322727,28.3562667 L2.62345455,34.3946667 C6.44540909,42.1557333 14.4268636,47.4666667 23.7136364,47.4666667 C29.4455,47.4666667 34.9177955,45.4314667 39.0249545,41.6181333 L31.5177727,35.8144 C29.3995682,37.1488 26.7323182,37.8666667 23.7136364,37.8666667"
                                                id="Fill-3" fill="#34A853"></path>
                                            <path
                                                d="M46.1454545,24 C46.1454545,22.6133333 45.9318182,21.12 45.6113636,19.7333333 L23.7136364,19.7333333 L23.7136364,28.8 L36.3181818,28.8 C35.6879545,31.8912 33.9724545,34.2677333 31.5177727,35.8144 L39.0249545,41.6181333 C43.3393409,37.6138667 46.1454545,31.6490667 46.1454545,24"
                                                id="Fill-4" fill="#4285F4"></path>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                            <span
                                class="text-sm font-medium text-gray-700">{{ __('messages.placeholder.login_via_google') }}</span>
                        </div>
                    </a>
                </div>
            @endif --}}
            {{-- Facebook Login - Commented out as per client request --}}
            {{-- @if (config('app.facebook_app_id') && config('app.facebook_app_secret') && config('app.facebook_redirect'))
                <a href="{{ route('social.login', 'facebook') }}"
                    class="border border-gray-300 rounded mt-2 text-black bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm w-full px-5 py-2 text-center dark:bg-gray-200 dark:hover:bg-gray-300 dark:focus:ring-gray-400 flex items-center justify-center space-x-3">
                    <i class="fa-brands fa-facebook fs-2"></i>
                    <div class="flex items-center space-x-2">
                        <svg width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path fill="#1877F2"
                                d="M24 12.073c0-6.627-5.373-12-12-12S0 5.446 0 12.073c0 6.014 4.387 10.993 10.125 11.855v-8.385H7.078v-3.47h3.047V9.385c0-3.01 1.79-4.673 4.533-4.673 1.313 0 2.686.236 2.686.236v2.956h-1.513c-1.492 0-1.953.927-1.953 1.875v2.22h3.328l-.532 3.47h-2.796v8.385C19.613 23.066 24 18.087 24 12.073z" />
                        </svg>
                        <span
                            class="text-sm font-medium text-gray-700">{{ __('messages.placeholder.login_via_facebook') }}</span>
                    </div>
                </a>
            @endif --}}
        </x-filament-panels::form>
    </div>
