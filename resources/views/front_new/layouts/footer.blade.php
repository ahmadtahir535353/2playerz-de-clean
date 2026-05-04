<!-- <style>
    footer {
        background: #000000;
        background: linear-gradient(90deg, rgba(0, 0, 0, 1) 0%, rgba(122, 0, 122, 1) 60%, rgba(75, 0, 75, 1) 100%);
    }
</style> -->
<footer class="footer pt-60">
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-lg-12 col-sm-12 text-center">
                <div style="display:inline-block" class="footer-logo">
                    <a href="{{ route('front.home') }}" class="p-3 rounded-3 w-full d-inline-block">
                        <img src="{{ !empty(getAppLogo()) ? getAppLogo() : asset('assets/image/infyom-logo.png') }}"
                            alt="2playerz" class="img-fluid" />
                    </a>
                </div>
                <p class="d-block text-center my-4 fs-18">
                    {!! $settings['about_text'] !!}
                </p>
            </div>
            <div class="col-lg-2 col-sm-4 mb-3  d-none">
                <div class="categories ps-xxl-5 ps-lg-4 ps-md-5 ms-lg-0 ms-md-5 ps-sm-4 ">
                    <h3 class="mb-3 text-black fw-7">{{ __('messages.categories') }}</h3>
                    <ul class="ps-0">
                        @foreach (getCategory()->take(6) as $category)
                        <li>
                            <a href="{{ route('categoryPage', $category->slug) }}"
                                class="text-decoration-none mb-3 d-block fs-14 text:hover">{!! $category->name !!}</a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-xxl-4 col-lg-5 col-md-7 col-sm-10 d-none">
                <h3 class="mb-3 text-black fw-7">{{ __('messages.recent_posts') }}</h3>
                <div class="footer-info d-flex flex-wrap justify-content-sm-between justify-content-start">
                    @foreach (getRecentPost() as $recentPost)
                    <div class="card me-sm-0 me-4  mb-4 bg-light {{ $loop->index ? 'mb-sm-0' : '' }}">
                        <div class="card-img-top">
                            <a href="{{ route('detailPage', ['data' => $recentPost->slug]) }}">
                                @if ($recentPost->post_types == \App\Models\Post::AUDIO_TYPE_ACTIVE)
                                <button class="common-music-icon sidebar-music-icon" type="button">
                                    <i class="icon fa-solid fa-music text-white"></i>
                                </button>
                                <img src="{{ $recentPost->post_image }}" alt="{{ $recentPost->title }}" class="w-100 h-100">
                                @elseif($recentPost->post_types == \App\Models\Post::VIDEO_TYPE_ACTIVE)
                                @php
                                $thumbUrl =
                                !empty($recentPost->postVideo) &&
                                !empty($recentPost->postVideo->thumbnail_image_url)
                                ? $recentPost->postVideo->thumbnail_image_url
                                : null;
                                $thumbImage =
                                !empty($recentPost->postVideo) &&
                                !empty($recentPost->postVideo->uploaded_thumb)
                                ? $recentPost->postVideo->uploaded_thumb
                                : asset('front_web/images/default.jpg');
                                @endphp
                                <button class="common-music-icon sidebar-music-icon" type="button">
                                    <i class="icon fa-solid fa-play text-white"></i>
                                </button>
                                <img src="{{ !empty($thumbUrl) ? $thumbUrl : $thumbImage }}" alt="thumbnil"
                                    class="w-100 h-100">
                                @else
                                <img src="{{ $recentPost->post_image }}" alt="{{ $recentPost->title }}" class="w-100 h-100">
                                @endif
                            </a>
                        </div>
                        <div class="card-body">
                            <p class="card-title mb-1 fs-12 fw-6 text-black">
                                <a href="{{ route('detailPage', ['data' => $recentPost->slug]) }}"
                                    class="text-decoration-none text-black">{!! $recentPost->title !!}</a>
                            </p>
                            <span
                                class="card-text fs-12">{{ $recentPost->created_at->translatedFormat('M d Y') }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="last-line pt-60 pb-4">
            <div class="row justify-content-between align-items-center">
                <div class="col-xxl-3 col-lg-4 col-sm-6 text-lg-start text-sm-end text-center order-2 order-lg-0">
                    <p href="#" class="fs-12 mb-0">{{ __('messages.common.all_rights') }} ©
                        {{ Illuminate\Support\Carbon::now()->format('Y') }} {{ $settings['application_name'] }}
                    </p>
                </div>
                <div
                    class="col-xxl-3 col-lg-4 col-sm-6 text-lg-center text-sm-end text-center my-sm-0 my-3  order-1 order-lg-1">
                    <div
                        class="social-icon d-flex justify-content-lg-center justify-content-sm-start justify-content-center gap-4">
                        <a href="{{ $settings['facebook_url'] }}" target="_blank"> <i
                                class="fa-brands fa-facebook-f fs-18  {{ getFrontSelectLanguageIsoCode() == 'ar' ? 'ms-xl-5 ms-4' : 'me-xl-5' }}"></i>
                        </a>
                        <a href="{{ $settings['twitter_url'] }}" target="_blank">
                            <!-- <i class="fa-brands fa-twitter text-gray fs-18 {{ getFrontSelectLanguageIsoCode() == 'ar' ? 'ms-xl-5 ms-4' : 'me-xl-5 me-4' }}"></i> -->
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 1227" role="img" class="navbar-nav-svg fs-18  {{ getFrontSelectLanguageIsoCode() == 'ar' ? 'ms-3' : '' }}" height="16" width="16">
                                <title>X</title>
                                <path fill="currentColor" d="M714.163 519.284 1160.89 0h-105.86L667.137 450.887 357.328 0H0l468.492 681.821L0 1226.37h105.866l409.625-476.152 327.181 476.152H1200L714.137 519.284h.026ZM569.165 687.828l-47.468-67.894-377.686-540.24h162.604l304.797 435.991 47.468 67.894 396.2 566.721H892.476L569.165 687.854v-.026Z"></path>
                            </svg>
                        </a>
                        <div class="d-none">
                            <a href="{{ $settings['linkedin_url'] }}" target="_blank"> <i
                                    class="fa-brands fa-linkedin-in  fs-18 {{ getFrontSelectLanguageIsoCode() == 'ar' ? 'ms-xl-5 ms-4' : 'me-xl-5 me-4' }}"></i></a>
                            <a href="{{ $settings['pinterest_url'] }}" target="_blank"> <i
                                    class="fa-brands fa-pinterest fs-18 {{ getFrontSelectLanguageIsoCode() == 'ar' ? 'ms-xl-5 ms-4' : 'me-xl-5 me-4' }}"></i></a>
                            <a href="{{ $settings['instagram_url'] }}" target="_blank"> <i
                                    class="fa-brands fa-instagram  fs-18  {{ getFrontSelectLanguageIsoCode() == 'ar' ? 'ms-xl-5 ms-4' : 'me-xl-5 me-4' }}"></i></a>
                            <a href="{{ $settings['vk_url'] }}" target="_blank"> <i
                                    class="fa-brands fa-vk fs-18  {{ getFrontSelectLanguageIsoCode() == 'ar' ? 'ms-xl-5 ms-4' : 'me-xl-5 me-4' }}"></i></a>
                            <a href="{{ $settings['telegram_url'] }}" target="_blank"> <i
                                    class="fa-brands fa-telegram  fs-18  {{ getFrontSelectLanguageIsoCode() == 'ar' ? 'ms-xl-5 ms-4' : 'me-xl-5 me-4' }}"></i></a>
                            <a href="{{ $settings['youtube_url'] }}" target="_blank"> <i
                                    class="fa-brands fa-youtube  fs-18 "></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-lg-4 col-sm-12 text-lg-end mb-lg-0 mb-sm-4 text-center order-0 order-lg-2">
                    <div class="desc  justify-content-center ">
                        <a href="{{ route('page.Terms') }}"
                            class="fs-12 me-4 {{ Request::is('terms-conditions*') ? 'text-success' : '' }}">{{ __('messages.setting.terms-conditions') }}</a>
                        <a href="{{ route('page.support') }}"
                            class="fs-12 me-4 {{ Request::is('support*') ? 'text-success' : '' }}">{{ __('messages.setting.support') }}</a>
                        <a href="{{ route('page.privacy') }}"
                            class="fs-12 {{ Request::is('privacy*') ? 'text-success' : '' }}">{{ __('messages.setting.privacy') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>