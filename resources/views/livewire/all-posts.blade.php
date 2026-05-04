<div>
    @if($allPosts->count() > 0)
        @foreach($allPosts as $post)
            <div class="row pt-60">
                <div class="col-lg-5 sports-content-img">
                    <div class="sports-img position-relative">
                        <a href="{{route('detailPage',$post->slug)}}">
                            @if($post->post_types == \App\Models\Post::AUDIO_TYPE_ACTIVE)
                                <button class="common-music-icon all-posts-music-icon"
                                        type="button">
                                    <i class="icon fa-solid fa-music text-white"></i>
                                </button>
                                <img src="{{$post->post_image}}" class="w-100 h-100" alt="">
                            @elseif($post->post_types == \App\Models\Post::VIDEO_TYPE_ACTIVE)
                                @php
                                    $thumbUrl = !empty($post->postVideo) && !empty($post->postVideo->thumbnail_image_url) ? $post->postVideo->thumbnail_image_url : null;
                                    $thumbImage = !empty($post->postVideo) && !empty($post->postVideo->uploaded_thumb) ? $post->postVideo->uploaded_thumb : asset('front_web/images/default.jpg')
                                @endphp
                                <button class="common-music-icon all-posts-music-icon"
                                        type="button">
                                    <i class="icon fa-solid fa-play text-white"></i>
                                </button>
                                <img src="{{ (!empty($thumbUrl) ? $thumbUrl : $thumbImage) }}" class="w-100 h-100" alt="">
                            @else
                                <img src="{{$post->post_image}}" class="w-100 h-100" alt="">
                            @endif
                        </a>
                        <a href="{{route('categoryPage',$post->category->slug)}}" class="tags position-absolute" style="background-color: {{ $post->category->color ?? '#B051B0' }}; color: white;">{{$post->category->name}}</a>
                    </div>
                </div>
                <div class="col-lg-7 ps-xl-4">
                    <div class="text pt-lg-0 pt-4">
                        <h3 class="text-black fw-7 mb-xxl-3 mb-xl-0 mb-lg-3 ">
                            <a href="{{route('detailPage',$post->slug)}}" class="text-black">
                                {!! $post->title !!}
                            </a>
                        </h3>
                        <p class=" fs-14 text-gray mb-xxl-4 mb-xl-2">
                            {!! $post->description !!}
                        </p>
                        <div class="desc d-flex flex-wrap">
                        <a href="{{ route('user.public.profile', optional($post->user)->username ?? $post->user_id ?? 0) }}" class="fs-12 text-black">
                            {{ __('messages.by') }} {{ $post->user->full_name }}
                        </a>

                            <span class="fs-12 text-primary mx-2"> | </span>
                            <span class="fs-12 text-black">{{$post->created_at->format('d')}}. {{ ucfirst(__('messages.common.' . strtolower($post->created_at->format('F')))) }} {{$post->created_at->format('Y')}}</span>
                            <span class="fs-12 text-primary mx-2"> | </span>
                            <span class=" fs-12 text-black"> <a href="{{route('detailPage',$post->slug)}}#commentFormSection" class="text-black">{{ $post->comment->count() }} {{__('messages.comments')}}</a></span>
                        </div>
                    </div>
                </div>
            </div>
            @if(($loop->index + 1) % 3 == 0)
                @if(checkAdSpaced('categories'))
                    <div class="index-top-desktop mt-3">
                        <a href="{{getAdImageDesktop(\App\Models\AdSpaces::CATEGORIES)->ad_url}}"
                           target="_blank">
                            <img src="{{asset(getAdImageDesktop(\App\Models\AdSpaces::CATEGORIES)->ad_banner)}}"
                                 width="800" class="img-fluid">
                        </a>
                    </div>
                    <div class="index-top-mobile mt-3">
                        <a href="{{getAdImageMobile(\App\Models\AdSpaces::CATEGORIES)->ad_url}}"
                           target="_blank">
                            <img src="{{asset(getAdImageMobile(\App\Models\AdSpaces::CATEGORIES)->ad_banner)}}"
                                 width="350" class="img-fluid">
                        </a>
                    </div>
                @endif
            @endif
        @endforeach
    @else
        <div class="d-flex justify-content-center pt-100">
            <h1>{{ __('messages.no_matching_records_found') }}</h1>
        </div>
    @endif

        @if($allPosts->count() > 0)
            <div class="row justify-content-center pt-60 mb-xl-4">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="row justify-content-between align-items-center mb-3">
                        <div class="col-12">
                            <p class="text-sm text-gray-600 mb-0">
                                Zeigt {{ $allPosts->firstItem() }} bis {{ $allPosts->lastItem() }} von {{ $allPosts->total() }} {{ __('messages.post.posts') }}
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            <select wire:model="perPage" wire:change="$refresh" class="form-select form-select-sm w-auto ms-3">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                        </div>

                        <div class="col-9 text-end">
                            {{ $allPosts->onEachSide(0)->links() }}
                        </div>
                    </div>
                </div>
            </div>
        @endif

{{--      @if($allPosts->count() > 0)--}}
{{--        <div class="row justify-content-center pt-60 mb-xl-4">--}}
{{--            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">--}}
{{--                <div class="mb-3">--}}
{{--                    <div class="col-md-4 col-12">--}}
{{--                    <div class="d-flex justify-content-center align-items-center">--}}
{{--                        <p class="text-sm text-gray-600 mb-0">--}}
{{--                            Zeigt {{ $allPosts->firstItem() }} bis {{ $allPosts->lastItem() }} von {{ $allPosts->total() }} Posts--}}
{{--                        </p>--}}
{{--                    </div>--}}
{{--                    <div class="col-md-1 col-3">--}}
{{--                        <select wire:model="perPage" wire:change="$refresh" class="form-select form-select-sm w-auto ms-3">--}}
{{--                            <option value="10">10</option>--}}
{{--                            <option value="25">25</option>--}}
{{--                            <option value="50">50</option>--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                    </div>--}}
{{--                    <div class="col-md-7 col-9 text-end">--}}
{{--                        {{ $allPosts->onEachSide(0)->links() }}--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    @endif--}}

<style>
     p.small.text-muted{
        display: none;
    }
</style>
</div>
