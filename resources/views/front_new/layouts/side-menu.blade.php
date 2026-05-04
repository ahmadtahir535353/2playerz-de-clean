<!-- start Newest Comments -->
<style>
    .text-ellipsis {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
<section class="popular-news-section">
    <div class="section-heading border-0 mb-2">
        <div class="row align-items-center">
            <div class="col-12 section-heading-left ">
                <h2 class="text-black mb-1">{{ __('messages.other_lang.newest_comments') }}</h2>
            </div>
        </div>
    </div>
    <div class="popular-news-post">
        <div class="row">
            @foreach($latestComment->take(10) as $comment) {{-- Limit to 6 comments --}}
            <div class="col-lg-12 col-sm-6 card py-2 pe-lg-0 pe-md-4 pe-sm-3">
                <div class="row align-items-center">
                    <div class="col-2 card-top">
                        <div class="card-">
                            {{-- Link to User Profile --}}
                            <a href="#"
                                class="profile-link"
                               
                                data-user-identifier="{{ optional($comment->users)->username ?? $comment->user->id }}">
                                <img src="{{ $comment->users->profile_image ?? asset('web/media/avatars/150-2.jpg') }}"
                                    alt="Profile Image"
                                    class="border border-secondary card-img-top"
                                    style="height: 40px; width: 40px; object-fit: cover; border-radius: 50%;">
                            </a>

                        </div>
                    </div>
                    <div class="col-10">
                        <div class="card-body p-0">
                            <p class="card-text fs-12 text-gray text-ellipsis m-0">
                                {{-- Name → Profile --}}
                                <a href="#"
                                    class="fw-bold profile-link"
                                    
                                    data-user-identifier="{{ optional($comment->users)->username ?? $comment->user->id }}"
                                    style="color: #734E96;">
                                    {{ $comment->users->username }}:
                                </a>

                                {{-- Comment → Article Scroll --}}
                                <a href="{{ route('detailPage', $comment->post->slug ?? '') }}#comment-{{ $comment->id }}"
                                    class="text-gray">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($comment->comment), 130, '...') }}
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

        </div>
    </div>
</section>

<!-- end newest comments  -->

<!-- start user ranking section -->
@if (!empty($topPlayers) && $topPlayers->count() > 0)
<section class="user-ranking-section pt-5">
    <div class="section-heading border-0 mb-4">
        <div class="row align-items-center">
            <div class="col-12 section-heading-left">
                <h2 class="text-black mb-1">{{ __('messages.other_lang.user_ranking') }}</h2>
            </div>
        </div>  
    </div>
    <div class="user-ranking-list">
        @foreach($topPlayers as $index => $player)
        <div class="ranking-item mb-3 pb-3" style="border-bottom: {{ $loop->last ? 'none' : '1px solid #e8e8e8' }};">
            <div class="d-flex align-items-start">
                <div class="ranking-avatar me-3">
                    <a href="#" class="profile-link" data-user-identifier="{{ $player->username ?? $player->id }}">
                        <img src="{{ $player->profile_image ?? asset('assets/image/avatar.png') }}"
                            alt="{{ $player->username }}"
                            class="border border-secondary"
                            style="height: 50px; width: 50px; object-fit: cover; border-radius: 50%;">
                    </a>
                </div>
                <div class="ranking-info flex-grow-1">
                    <div class="d-flex align-items-center mb-1">
                        <a href="#" class="profile-link fw-bold fs-14 text-decoration-none me-2" 
                           data-user-identifier="{{ $player->username ?? $player->id }}"
                           style="color: #734E96;">
                            {{ $player->username }}
                        </a>
                        @php
                        $levelObj = $player->level_object;
                        $badgeBgColor = ($levelObj && !empty($levelObj->badge_color)) ? $levelObj->badge_color : '#734E96';
                        $badgeTextColor = ($levelObj && !empty($levelObj->badge_text_color)) ? $levelObj->badge_text_color : '#ffffff';
                        $levelName = $player->level ?? 'Newbie';
                    @endphp
                    <div class="level-badge mb-1">
                        <span class="badge" style="background-color: {{ $badgeBgColor }}; color: {{ $badgeTextColor }}; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 500;">
                            {{ $levelName }}
                        </span>
                    </div>
                    </div>
                    
                    <div class="points-text">
                        <span class="fs-12 text-gray">{{ number_format($player->comment_points ?? 0, 0, ',', '.') }} {{ __('messages.other_lang.playerz_points') }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        
        <div class="view-full-ranking mt-3 pt-2">
            <a href="{{ route('playerz.ranking') }}" class="text-decoration-none" style="color: #734E96; font-size: 14px; font-weight: 500;">
                {{ __('messages.other_lang.view_full_ranking') }}
            </a>
        </div>
    </div>
</section>
@endif
<!-- end user ranking section -->

<!-- start latest members section -->
@if (!empty($latestMembers) && $latestMembers->count() > 0)
<section class="latest-members-section pt-5">
    <div class="section-heading border-0 mb-4">
        <div class="row align-items-center">
            <div class="col-12 section-heading-left">
                <h2 class="text-black mb-1">{{ __('messages.other_lang.latest_members') }}</h2>
            </div>
        </div>
    </div>
    <div class="latest-members-list">
        <div class="row g-0">
            @foreach($latestMembers as $index => $member)
            <div class="col-5 px-1">
                <div class="member-item d-flex align-items-center pb-2 mb-2" style="border-bottom: 1px solid #e8e8e8;">
                    <div class="member-avatar me-2">
                        <a href="#" class="profile-link" data-user-identifier="{{ $member->username ?? $member->id }}">
                            <img src="{{ $member->profile_image ?? asset('assets/image/avatar.png') }}"
                                alt="{{ $member->username }}"
                                class="border border-secondary"
                                style="height: 40px; width: 40px; object-fit: cover; border-radius: 50%;">
                        </a>
                    </div>
                    <div class="member-username flex-grow-1">
                        <a href="#" class="profile-link member-username-link fw-6 fs-12 text-decoration-none" data-user-identifier="{{ $member->username ?? $member->id }}" style="color: #734E96 !important;">
                            {{ \Illuminate\Support\Str::limit($member->username, 10, '...') }}
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
<!-- end latest members section -->

<!-- start voting-poll-section -->
@if (!empty($getPoll->count()))
<section class="voting-poll-section pt-5">
    <div class="section-heading border-0 mb-30">
        <div class="row align-items-center">
            <div class="col-12 section-heading-left ">
                <h2 class="text-black mb-1">{{ __('messages.details.voting_poll') }}</h2>
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
        <p class="text-black fw-6 fs-16 mb-20">{!! $poll['question'] !!}</p>
        <form class="poll-vote-form ">
            @csrf
            <input type="hidden" id="pollId" name="poll_id" value="{{ $poll['id'] }}">
            <div class="mb-2 @if($poll->has_voted) d-none @endif" id="pollOption{{ $poll->id }}">
                @foreach ($getOption as $option)
                @if (!empty($poll->$option))
                <div class="form-check">
                    <input class="form-check-input me-3 poll-answer"
                        type="{{ $poll->multi_select == 1 ? 'checkbox' : 'radio' }}"
                        name="{{ $poll->multi_select == 1 ? 'answer[]' : 'answer' }}"
                        id="pollAnswer-{{ $option }}-{{ $poll['id'] }}"
                        value="{{ $poll[$option] }}">
                    <label class="form-check-label fs-14"
                        for="pollAnswer-{{ $option }}-{{ $poll['id'] }}">
                        {!! $poll[$option] !!}
                    </label>
                </div>
                @endif
                @endforeach

                <div class="vote d-flex justify-content-between align-items-center pt-2 mb-md-4 mb-4 mb-1">
                    <button type="submit" class="btn btn-primary poll-submit-btn btn-purple"
                        data-id="{{ $poll['id'] }}">
                        {{ __('messages.details.vote') }}
                    </button>
                    <a href="javascript:void(0);" class="fs-14 text-gray fw-6 view-statistic text-purple "
                        data-id="{{ $poll->id }}">
                        {{ __('messages.details.view_results') }}
                    </a>
                </div>
                <span id="voteError{{ $poll->id }}"></span>
            </div>
        </form>

        <div id="pollStatistic{{ $poll->id }}" class="mb-2 @if(!$poll->has_voted) d-none @endif">
            @php $vote = getPollStatistics($poll->id) @endphp
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
            <div class="vote d-flex justify-content-between align-items-center pt-2 mb-md-2 mb-1">
                @if(showPollVotesCount())
                <span
                    class="text-black fs-14 fw-6">{{ __('messages.poll.total_vote') }}: {{ $vote['totalPollResults'] }}</span>
                @else
                <span></span>
                @endif
                <a href="javascript:void(0);" class="view-option fs-14 text-gray fw-6 text-purple "
                    data-id="{{ $poll->id }}">{{ __('messages.details.view_options') }} </a>
            </div>
            <span id="voteSuccess{{ $poll->id }}">
                <p> </p>
            </span>
        </div>
    </div>
    @endforeach
</section>
@endif
<!-- end voting-poll-section -->

<!-- start popular-news-section -->
@if (!empty(array_filter($getPopularNews)))
<section class="popular-news-section pt-5">
    <div class="section-heading border-0 mb-2">
        <div class="row align-items-center">
            <div class="col-12 section-heading-left ">
                <h2 class="text-black mb-1">{{ __('messages.details.popular_news') }}</h2>
            </div>
        </div>
    </div>
    <div class="popular-news-post">
        <div class="row">
            <div class="col-lg-12 d-flex flex-wrap justify-content-between">
                @foreach ($getPopularNews as $news)
                @if (!empty($news))
                <div class="col-lg-12 col-sm-6 card d-flex flex-xl-row py-2 pe-lg-0 pe-md-4 pe-sm-3">
                    <div class="row">
                        <div class="col-xl-4 col-5 card-top">
                            <div class="card-img-top">
                                <a href="{{ route('detailPage', $news['slug']) }}">
                                    {{-- <img data-src="{{ $news['post_image'] }}" src="{{ asset('front_web/images/bg-process.png') }}" alt="" class="w-100 h-100 w-300px lazy"> --}}
                                    @if ($news['post_types'] == \App\Models\Post::AUDIO_TYPE_ACTIVE)
                                    <button class="common-music-icon sidebar-music-icon" type="button">
                                        <i class="icon fa-solid fa-music text-white"></i>
                                    </button>
                                    <img src="{{ $news['post_image'] }}" alt="{{ $news['title'] }}"
                                        class="w-100 h-100 w-300px">
                                    @elseif($news['post_types'] == \App\Models\Post::VIDEO_TYPE_ACTIVE)
                                    @php
                                    $thumbUrl = !empty($news['post_video']) && !empty($news['post_video']['thumbnail_image_url']) ? $news['post_video']['thumbnail_image_url'] : null;
                                    $thumbImage = !empty($news['post_video']) && !empty($news['post_video']['uploaded_thumb']) ? $news['post_video']['uploaded_thumb'] : asset('front_web/images/default.jpg');
                                    @endphp
                                    <button class="common-music-icon sidebar-music-icon" type="button">
                                        <i class="icon fa-solid fa-play text-white"></i>
                                    </button>
                                    <img src="{{ !empty($thumbUrl) ? $thumbUrl : $thumbImage }}"
                                        alt="thumbnil" class="w-100 h-100 w-300px">
                                    @else
                                    <img src="{{ $news['post_image'] }}" alt="{{ $news['title'] }}"
                                        class="w-100 h-100 w-300px">
                                    @endif
                                </a>
                            </div>
                        </div>
                        <div class="col-xl-8 col-7">
                            <div class="card-body">
                                <h5 class="card-title mb-1 fs-12 text-gray fw-7">{!! $news['category']['name'] !!}
                                </h5>
                                <p class="card-title mb-0 fs-14 text-black fw-6">
                                    <a href="{{ route('detailPage', $news['slug']) }}" class="text-black">
                                        {!! $news['title'] !!}
                                    </a>
                                </p>
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-1">
                                    @php
                                        $newsCreatedAt = \Carbon\Carbon::parse($news['created_at'])->timezone(config('app.timezone'));
                                    @endphp
                                    <span class="card-text fs-12 text-gray">{{ $newsCreatedAt->format('d') }}. {{ ucfirst(__('messages.common.' . strtolower($newsCreatedAt->format('F')))) }} {{ $newsCreatedAt->format('Y') }}</span>
                                    <a href="{{ route('detailPage', $news['slug']) }}#commentFormSection" class="card-text fs-12 text-gray text-decoration-none"><i class="fa-solid fa-comments fs-12 text-gray me-1"></i>{{ (int) ($news['comment_count'] ?? 0) }}</a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @if ($loop->iteration >= 6)
                @break
                @endif
                @endforeach
                @if (checkAdSpaced('popular_news_index_page'))
                @if (isset(getAdImageDesktop(\App\Models\AdSpaces::INDEX_POPULAR_NEWS)->code))
                <div class="container index-top-desktop ad-space-url-desktop">
                    {!! getAdImageDesktop(\App\Models\AdSpaces::INDEX_POPULAR_NEWS)->code !!}
                </div>
                @elseif ($adsDesktop = getAdImageDesktop(\App\Models\AdSpaces::INDEX_POPULAR_NEWS))
                <div class="index-top-desktop mt-3">
                    <a href="{{ $adsDesktop->ad_url }}" target="_blank">
                        <img src="{{ asset($adsDesktop->ad_banner) }}" width="800" class="img-fluid">
                    </a>
                </div>
                @endif
                @if (isset(getAdImageDesktop(\App\Models\AdSpaces::INDEX_POPULAR_NEWS)->code))
                <div class="container index-top-mobile ad-space-url-mobile">
                    {!! getAdImageDesktop(\App\Models\AdSpaces::INDEX_POPULAR_NEWS)->code !!}
                </div>
                @elseif ($adRecord = getAdImageMobile(\App\Models\AdSpaces::INDEX_POPULAR_NEWS))
                <div class="index-top-mobile mt-3">
                    <a href="{{ $adRecord->ad_url }}" target="_blank">
                        <img src="{{ asset($adRecord->ad_banner) }}" width="350" class="img-fluid">
                    </a>
                </div>
                @endif
                @endif

            </div>
        </div>
    </div>
</section>
@endif
<!-- end popular-news-section -->

<!-- end popular-news-section -->
@if ($getRecommendedPost->count() > 0)
<section class="popular-news-section pt-5">
    <div class="section-heading border-0 mb-2">
        <div class="row align-items-center">
            <div class="col-12 section-heading-left ">
                <h2 class="text-black mb-1 w-200px custom-label-laptop">
                    {{ __('messages.details.recommended_post') }}
                </h2>
            </div>
        </div>
    </div>
    <div class="popular-news-post">
        <div class="row">
            <div class="col-lg-12 d-flex flex-wrap justify-content-between">
                @foreach ($getRecommendedPost as $recommendedPost)
                <div class="col-lg-12 col-sm-6 card d-flex flex-xl-row py-2 pe-lg-0 pe-md-4 pe-sm-3">
                    <div class="row">
                        <div class="col-xl-4 col-5 card-top">
                            <div class="card-img-top">
                                <a href="{{ route('detailPage', $recommendedPost->slug) }}">
                                    {{-- <img data-src="{{ $recommendedPost['post_image'] }}" alt="" src="{{ asset('front_web/images/bg-process.png') }}" class="w-100 h-100 w-300px lazy"> --}}
                                    @if ($recommendedPost['post_types'] == \App\Models\Post::AUDIO_TYPE_ACTIVE)
                                    <button class="common-music-icon sidebar-music-icon" type="button">
                                        <i class="icon fa-solid fa-music text-white"></i>
                                    </button>
                                    <img src="{{ $recommendedPost['post_image'] }}" alt="{{ $recommendedPost['title'] }}"
                                        class="w-100 h-100 w-300px">
                                    @elseif($recommendedPost['post_types'] == \App\Models\Post::VIDEO_TYPE_ACTIVE)
                                    @php
                                    $thumbUrl = !empty($recommendedPost->postVideo) && !empty($recommendedPost->postVideo->thumbnail_image_url) ? $recommendedPost->postVideo->thumbnail_image_url : null;
                                    $thumbImage = !empty($recommendedPost->postVideo) && !empty($recommendedPost->postVideo->uploaded_thumb) ? $recommendedPost->postVideo->uploaded_thumb : asset('front_web/images/default.jpg');
                                    @endphp
                                    <button class="common-music-icon sidebar-music-icon" type="button">
                                        <i class="icon fa-solid fa-play text-white"></i>
                                    </button>
                                    <img src="{{ !empty($thumbUrl) ? $thumbUrl : $thumbImage }}"
                                        alt="thumbnil" class="w-100 h-100 w-300px">
                                    @else
                                    <img src="{{ $recommendedPost['post_image'] }}" alt="{{ $recommendedPost['title'] }}"
                                        class="w-100 h-100 w-300px">
                                    @endif
                                </a>
                            </div>
                        </div>
                        <div class="col-xl-8 col-7">
                            <div class="card-body">
                                <h5 class="card-title mb-1 fs-12 text-gray fw-7">{!! $recommendedPost['category']['name'] !!}
                                </h5>
                                <p class="card-title mb-0 fs-14 text-black fw-6">
                                    <a href="{{ route('detailPage', $recommendedPost->slug) }}"
                                        class="text-black">
                                        {!! $recommendedPost['title'] !!}
                                    </a>
                                </p>
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-1">
                                    <span class="card-text fs-12 text-gray">{{ $recommendedPost['created_at']->format('d') }}. {{ ucfirst(__('messages.common.' . strtolower($recommendedPost->created_at->format('F')))) }} {{ $recommendedPost['created_at']->format('Y') }}</span>
                                    <a href="{{ route('detailPage', $recommendedPost->slug) }}#commentFormSection" class="card-text fs-12 text-gray text-decoration-none"><i class="fa-solid fa-comments fs-12 text-gray me-1"></i>{{ (int) ($recommendedPost->comment_count ?? 0) }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($loop->iteration >= 6)
                @break
                @endif
                @endforeach
                @if (checkAdSpaced('recommended_post_index_page'))
                @if (isset(getAdImageDesktop(\App\Models\AdSpaces::INDEX_RECOMMENDED_POST)->code))
                <div class="index-top-desktop ad-space-url-desktop">
                    {!! getAdImageDesktop(\App\Models\AdSpaces::INDEX_RECOMMENDED_POST)->code !!}
                </div>
                @elseif ($adsDesktop = getAdImageDesktop(\App\Models\AdSpaces::INDEX_RECOMMENDED_POST))
                <div class="index-top-desktop mt-3">
                    <a href="{{ $adsDesktop->ad_url }}" target="_blank">
                        <img src="{{ asset($adsDesktop->ad_banner) }}" width="800" class="img-fluid">
                    </a>
                </div>
                @endif
                @if (isset(getAdImageDesktop(\App\Models\AdSpaces::INDEX_RECOMMENDED_POST)->code))
                <div class="index-top-mobile ad-space-url-mobile">
                    {!! getAdImageDesktop(\App\Models\AdSpaces::INDEX_RECOMMENDED_POST)->code !!}
                </div>
                @elseif ($adRecord = getAdImageMobile(\App\Models\AdSpaces::INDEX_RECOMMENDED_POST))
                <div class="index-top-mobile mt-3">
                    <a href="{{ $adRecord->ad_url }}" target="_blank">
                        <img src="{{ asset($adRecord->ad_banner) }}" width="350" class="img-fluid">
                    </a>
                </div>
                @endif
                @endif

            </div>
        </div>
    </div>
</section>
@endif
<!-- start popular-tag-section -->

<!-- start popular-tag-section -->
@if (count($getPopularTags))
<section class="popular-tag-section pt-5">
    <div class="section-heading border-0 mb-30">
        <div class="row align-items-center">
            <div class="col-12 section-heading-left ">
                <h2 class="text-black mb-1">{{ __('messages.details.popular_tags') }}</h2>
            </div>
        </div>
    </div>
    <div class="popular-tags">
        @foreach ($getPopularTags as $tag)
        <div class="br-gray-100 d-inline-flex py-1 px-2 mb-2 me-2 " style="background-color: #734E96; border-radius:50px">
            <a href="{{ route('popularTagPage', $tag) }}" class="text-white fs-12">{!! $tag !!}</a>
        </div>
        @if ($loop->iteration >= 15)
        @break
        @endif
        @endforeach
    </div>
</section>
@endif
<!-- end popular-tag-section -->

<!-- start hot-categories-section -->
@if (!empty($getPopulerCategories))
<section class="hot-categories-section py-60 pb-4">
    <div class="section-heading border-0 mb-30">
        <div class="row align-items-center">
            <div class="col-12 section-heading-left ">
                <h2 class="text-black mb-1">{{ __('messages.details.hot_categories') }}</h2>
            </div>
        </div>
    </div>
    <div class="hot-categories-post">
        @foreach ($getPopulerCategories as $category)
        <div class="post bg-light d-flex justify-content-between align-items-center px-3 py-1 mb-3 ">
            <div class="desc d-flex align-items-center">
                <i class="fs-14 fa-solid fa-list me-3 text-primary"></i>
                <a href="{{ route('categoryPage', ['category' => $category['slug']]) }}"
                    class="fs-14 fw-6 text-black mb-0">{!! $category['name'] !!}</a>
            </div>
            <!-- <div
                        class="numbers d-flex align-items-center justify-content-center rounded-circle bg-primary w-30px h-30px">
                        <a href="#" class="fs-14 fw-6 text-white">{{ $category['posts_count'] }}</a>
                    </div> -->
        </div>
        @endforeach
    </div>
</section>
@endif
<!-- end hot-categories-section -->

<script>
    window.showPollVotesCount = @json(showPollVotesCount());
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.poll-vote-form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const pollId = form.querySelector('input[name="poll_id"]').value;
                const token = form.querySelector('input[name="_token"]').value;

                // Check for both types: radio or checkbox
                let selectedInputs = form.querySelectorAll('input[name="answer[]"]:checked');

                // If not checkbox-style, try radio
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
                        if (result.data) {
                            form.reset();

                            const statBlock = document.getElementById(`pollStatistic${result.data.pollId}`);
                            const optionBlock = document.getElementById(`pollOption${result.data.pollId}`);

                            optionBlock?.classList.add('d-none');
                            statBlock?.classList.remove('d-none');
                            statBlock.innerHTML = '';

                            for (const [label, val] of Object.entries(result.data.optionAns)) {
                                statBlock.innerHTML += `
                            <p class="mt-0 mb-2 fs-14">${label}</p>
                            <div class="progress mb-3">
                                <div class="progress-bar progress-bar-striped" role="progressbar"
                                     aria-valuenow="${val}" aria-valuemin="0" aria-valuemax="100"
                                     style="width: ${val}%;">
                                    <span>${val}%</span>
                                </div>
                            </div>`;
                            }

                            statBlock.innerHTML += `
                        <div class="vote d-flex justify-content-between align-items-center pt-2 mb-md-2 mb-1">
                            ${window.showPollVotesCount ? `<span class="text-black fs-14 fw-6">{{ __('messages.other_lang.total_vote')}}: ${result.data.totalPollResults}</span>` : '<span></span>'}
                            <a href="javascript:void(0);" class="view-option fs-14 text-gray fw-6"
                               data-id="${result.data.pollId}">{{ __('messages.other_lang.view_option')}}</a>
                        </div>
                        <span id="voteSuccess${pollId}"><p class="text-success">${result.message}</p></span>
                    `;

                            setTimeout(() => {
                                document.getElementById(`voteSuccess${pollId}`)?.remove();
                            }, 3000);
                        }
                    })
                    .catch(error => {
                        const errorBox = document.getElementById(`voteError${pollId}`);
                        if (errorBox) {
                            errorBox.innerHTML = `<p class="text-danger">{{ __('messages.other_lang.already_voted')}}</p>`;
                        }
                    });
            });
        });
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
</script>