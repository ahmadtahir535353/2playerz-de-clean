@extends('customer-panel.layout.main')
@section('title', __('messages.customer_profile.my_profile'))  {{-- "My Profile" --}}
@section('content')

<div class="bg-[#F5F5F5] dark:bg-[#18171C] mb-4 p-3 text-end rounded-md">
    <h3 class="text-xl opacity-60 tracking-wider">{{ __('messages.customer_profile.my_profile')}}</h3>
</div>
<div class="bg-[#F5F5F5] dark:bg-[#161618] p-6 rounded-md shadow-md">
    <div class="flex flex-col md:flex-row gap-6">
        <!-- Avatar & Stats -->
        <div class="md:w-[20%]">
            <img src="{{ isset($customer->profile_image) ? $customer->profile_image : asset('web/media/avatars/150-2.jpg') }}" alt="Avatar" class="rounded border dark:border-gray-700 border-gray-200 w-full" />
            <div class="mt-4 space-y-1 text-sm">
                <div><span class="opacity-50">{{ __('messages.customer_profile.last_activity')}}:</span> {{ optional($customer->last_seen_at)->format('d M, Y') ?? 'Not recorded' }}</div>
                <div><span class="opacity-50">{{ __('messages.customer_profile.registered_since')}}:</span> {{ $customer->created_at->format('d M, Y') }}</div>
                <div><span class="opacity-50">{{ __('messages.customer_profile.comments')}}:</span> {{ $customer->comments_count ?? 0 }}</div>
                <div><span class="opacity-50">{{ __('messages.customer_profile.likes')}}:</span> {{ $customer->likes_count ?? 0 }}</div>
                <div><span class="opacity-50">{{ __('messages.customer_profile.last_seen')}}:</span> {{ optional($customer->last_seen_at)->format('H:i:s') ?? 'Not recorded' }}</div>
                <div><span class="opacity-50">{{ __('messages.other_lang.player_points') }}:</span> {{ $customer->comment_points ?? 0 }}</div>
                @php
                    $levelObj = $customer->level_object;
                    $badgeBgColor = ($levelObj && !empty($levelObj->badge_color)) ? $levelObj->badge_color : '#1e40af';
                    $badgeTextColor = ($levelObj && !empty($levelObj->badge_text_color)) ? $levelObj->badge_text_color : '#93c5fd';
                    $levelName = $customer->level ?? 'Newbie';
                @endphp
                <div><span class="opacity-50">{{ __('messages.other_lang.level') }}:</span> 
                    <span class="level-badge" style="display: inline-block; background-color: {{ $badgeBgColor }}; color: {{ $badgeTextColor }}; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 500; line-height: 1.4;">{{ $levelName }}</span>
                </div>
                <div><span class="opacity-50">{{ __('messages.profile.profile_visitors') }}:</span> {{ $customer->visitor_count ?? 0 }}</div>
            </div>
        </div>

        <!-- Profile Info -->
        <div class="md:w-[80%] space-y-4">
            <div>
                <h2 class="text-2xl font-bold flex items-center gap-3">{{ $customer->username ?? 'User' }}
                    <!-- Status -->
                    <span class="flex items-center gap-1">
                        <span class="bg-green-400 size-[6px] rounded-full block"></span>
                        <p class="opacity-80 text-xs font-medium">online</p>
                    </span>
                </h2>
                <!-- <p class="text-sm text-gray-400">{{ __('messages.other_lang.from') }} {{ $customer->location ?? __('messages.other_lang.unknown') }}</p> -->
                <div class="mt-2 space-x-2">
                    <button class="bg-purple-600 hover:bg-purple-700 px-3 py-1 text-white rounded text-sm">{{ __('messages.customer_profile.profile')}}</button>
                    <a href="{{ route('customer.profile.comments') }}" class="bg-gray-700 hover:bg-gray-600 px-3 py-1 text-white rounded text-sm">{{ __('messages.customer_profile.comments')}}</a>
                </div>
            </div>

            <div>
                <h3 class="text-xl font-semibold border-b border-gray-700 pb-1 mb-2">{{ __('messages.customer_profile.about_me')}}</h3>
                <p class="text-sm opacity-70">
                    {{ $customer->about_me ?? __("messages.customer_profile.no_information") }}
                </p>
            </div>

            <div class="md:flex items-start gap-4 text-sm">
                <div class="flex-1">
                <div>
                    <h4 class="text-purple-400 font-semibold">{{ __('messages.customer_profile.loction')}}:</h4> <span class="opacity-70"> {{ $customer->location ?? __("messages.customer_profile.not_specified") }} </span>
                </div>
                <div class="col-span-2">
                    <h4 class="text-purple-400 font-semibold">{{ __('messages.customer_profile.gaming_profiles') }}</h4>
                    @if($psnCardUrl)
                        <p class="opacity-70">
                            <strong>PSN:</strong> 
                            <img src="{{ $psnCardUrl }}" alt="PSN Card" style="max-width: 100%;">
                        </p>
                    @else
                        <p>{{ __('messages.other_lang.psn_id') }}</p>
                    @endif
                    @if($xboxCardUrl)
                        <p class="opacity-70">
                            <strong>Xbox Live:</strong> 
                            <img src="{{ $xboxCardUrl }}" alt="Xbox Live Card" style="max-width: 100%;">
                        </p>
                        @else
                        <p>{{ __('messages.other_lang.xbox_id') }}</p>
                    @endif
                </div>
                <div class="col-span-2">
                    <h4 class="text-purple-400 font-semibold">{{ __('messages.customer_profile.hardware')}}</h4>
                    <p><strong>{{ __('messages.customer_profile.consoles')}}:</strong> <span class="opacity-70"> {{ $customer->consoles ?? __("messages.customer_profile.not_specified") }} </span></p>
                    <p><strong>{{ __('messages.customer_profile.accessories')}}:</strong> <span class="opacity-70"> {{ $customer->accessories ?? __("messages.customer_profile.not_specified") }} </span></p>
                </div>
                <div class="col-span-2">
                    <h4 class="text-purple-400 font-semibold">{{ __('messages.customer_profile.interests')}}</h4>
                    <p><strong>{{ __('messages.customer_profile.favourite_games')}}:</strong> <span class="opacity-70"> {{ $customer->favorite_games ?? __("messages.customer_profile.not_specified") }} </span></p>
                    <p><strong>{{ __('messages.customer_profile.favourite_genre')}}:</strong> <span class="opacity-70"> {{ $customer->favorite_genre ?? __("messages.customer_profile.not_specified") }} </span></p>
                    <p><strong>{{ __('messages.customer_profile.series')}}:</strong> <span class="opacity-70"> {{ $customer->favorite_series ?? __("messages.customer_profile.not_specified") }} </span></p>
                    <p><strong>{{ __('messages.customer_profile.movies')}}:</strong> <span class="opacity-70"> {{ $customer->favorite_films ?? __("messages.customer_profile.not_specified") }} </span></p>
                    <p><strong>{{ __('messages.customer_profile.music')}}:</strong> <span class="opacity-70"> {{ $customer->favorite_music ?? __("messages.customer_profile.not_specified") }} </span></p>
                    <p><strong>{{ __('messages.customer_profile.hobbies')}}:</strong> <span class="opacity-70"> {{ $customer->hobbies ?? __("messages.customer_profile.not_specified") }} </span></p>
                    <p><strong>{{ __('messages.customer_profile.my_motto')}}:</strong> <span class="opacity-70"> {{ $customer->my_motto ?? __("messages.customer_profile.not_specified") }} </span></p>
                </div>
            </div>
                <div class="flex-1">

                    <div>
                        <h4 class="text-purple-400 font-semibold">{{ __('messages.customer_profile.occupation')}}:</h4> <span class="opacity-70"> {{ $customer->occupation ?? __("messages.customer_profile.not_specified") }} </span>
                    </div>
                    <div class="flex items-center mt-5">
                
                        <div class="text-white">
                            <fieldset class="rounded-xl border-2 border-purple-400 bg-transparent px-4 py-4">
                                <legend>
                                            <div class="text-purple-400 font-semibold text-sm px-2">
                                    {{ __('messages.other_lang.is_following')}}
                                    </div>
                                </legend>

                                    <!-- Body box with purple outline -->
                                    <div class="">
                                    @if(isset($followings) && $followings->count() > 0)
                                    <!-- Avatars grid -->
                                    <div class="flex flex-col items-center">
                                        <!-- Row 1 - First 4 users -->
                                        <div class="flex justify-center gap-4 mb-3">
                                        @foreach($followings->take(4) as $following)
                                            @php
                                                $user = $following->follower;
                                            @endphp
                                            <a href="{{ route('user.public.profile', $user->username ?? $user->id) }}" title="{{ $user->username }}">
                                                <img
                                                    src="{{ $user->profile_image ?: asset('web/media/avatars/150-2.jpg') }}"
                                                    class="size-12 border rounded-full object-cover ring-1 ring-white/20 hover:ring-2 hover:ring-purple-400 transition-all cursor-pointer"
                                                    alt="{{ $user->username }}"
                                                />
                                            </a>
                                        @endforeach
                                        </div>

                                        @if(isset($followings) && $followings->count() > 4)
                                        <!-- Divider line -->
                                        <div class="w-full border-t border-white/20 mb-3"></div>

                                        <!-- Row 2 - Next 4 users -->
                                        <div class="flex justify-center gap-4 mb-4">
                                        @foreach($followings->slice(4, 4) as $following)
                                            @php
                                                $user = $following->follower;
                                            @endphp
                                            <a href="{{ route('user.public.profile', $user->username ?? $user->id) }}" title="{{ $user->username }}">
                                                <img
                                                    src="{{ $user->profile_image ?: asset('web/media/avatars/150-2.jpg') }}"
                                                    class="size-12 border rounded-full object-cover ring-1 ring-white/20 hover:ring-2 hover:ring-purple-400 transition-all cursor-pointer"
                                                    alt="{{ $user->username }}"
                                                />
                                            </a>
                                        @endforeach
                                        </div>
                                        @endif

                                        <!-- Footer text with link to all followings -->
                                        @if(isset($remainingCount) && $remainingCount > 0)
                                        <div class="text-center text-purple-300 text-sm font-medium">
                                            <a href="{{ route('members.following') }}" class="hover:text-purple-200 transition-colors">
                                                {{ __('messages.other_lang.and_more', ['count' => $remainingCount]) }}
                                            </a>
                                        </div>
                                        @endif
                                    </div>
                                    @else
                                    <!-- Empty state -->
                                    <div class="text-center py-4">
                                        <div class="text-3xl mb-2">👥</div>
                                        <p class="text-sm text-gray-400">{{ __('messages.no_following_yet') ?? 'No following yet' }}</p>
                                    </div>
                                    @endif
                                    </div>
                            </fieldset>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection