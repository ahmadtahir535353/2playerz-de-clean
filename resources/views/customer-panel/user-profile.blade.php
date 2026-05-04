@extends('customer-panel.layout.main')
@section('title', $user->username ?: 'User')
@section('content')
<!-- @if(request()->has('return_to'))
    <a href="{{ request()->get('return_to') }}"
       class="btn btn-sm btn-outline-secondary"
       style="position: fixed; top: 10px; right: 10px; z-index: 9999;">
        ❌ Close & Return
    </a>
@endif -->

@php
    $backUrl = request()->get('return_to', url('/'));
@endphp

@if(session('error'))
    <div class="mb-4 p-3 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 rounded-md">{{ session('error') }}</div>
@endif

<div class="bg-[#F5F5F5] dark:bg-[#18171C] mb-4 p-3 rounded-md flex items-center justify-between">
    <!-- Back Arrow Icon (Left Side) -->
    <a href="{{ $backUrl }}" class="opacity-60 hover:opacity-100 transition-opacity flex items-center" title="{{ __('messages.other_lang.back_website') }}">
        <svg width="32" height="32" viewBox="0 0 448 448" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-gray-700 dark:text-gray-300">
            <!-- Circle -->
            <path d="M 224 0 A 224 224 0 0 0 0 224 A 224 224 0 0 0 224 448 A 224 224 0 0 0 448 224 A 224 224 0 0 0 224 0 Z M 224 32 A 192 192 0 0 1 416 224 A 192 192 0 0 1 224 416 A 192 192 0 0 1 32 224 A 192 192 0 0 1 224 32 Z" fill="currentColor"/>
            <!-- Back Arrow -->
            <path d="M 80 224 L 208 96 L 288 96 L 192 192 L 352 192 L 352 256 L 192 256 L 288 352 L 208 352 Z" fill="currentColor"/>
        </svg>
    </a>
    
    <!-- Back Website Button (Right Side) -->
    <a href="{{ $backUrl }}" class="opacity-60 hover:opacity-100 underline font-bold text-[20px] transition-opacity">
        {{ __('messages.other_lang.back_website') }}
    </a>
</div>


<div class="bg-[#F5F5F5] dark:bg-[#161618] p-6 rounded-md shadow-md">
    <div class="flex flex-col md:flex-row gap-6">
        <!-- Avatar & Stats -->
        <div class="md:w-[20%]">
            <img src="{{ isset($user->profile_image) ? $user->profile_image : asset('web/media/avatars/150-2.jpg') }}" alt="Avatar" class="rounded border dark:border-gray-700 border-gray-200 w-full" />
            <div class="mt-4 space-y-1 text-sm">
                <div><span class="opacity-50">{{ __('messages.customer_profile.last_activity')}}:</span> {{ optional($user->last_activity_at)->format('d M, Y H:i:s') ?? '--' }}</div>
                <div><span class="opacity-50">{{ __('messages.customer_profile.registered_since')}}:</span> {{ $user->created_at->format('d M, Y') }}</div>
                <div><span class="opacity-50">{{ __('messages.customer_profile.comments')}}:</span> {{ $user->comments_count ?? 0 }}</div>
                <div><span class="opacity-50">{{ __('messages.customer_profile.likes')}}:</span> {{ $user->likes_count ?? '0' }}</div>
                <div><span class="opacity-50">{{ __('messages.customer_profile.last_seen')}}:</span> {{ optional($user->last_seen_at)->format('d M, Y H:i:s') ?? '--' }}</div>
                <div><span class="opacity-50">{{ __('messages.other_lang.player_points') }}:</span> {{ $user->comment_points ?? 0 }}</div>
                @php
                    $levelObj = $user->level_object;
                    $badgeBgColor = ($levelObj && !empty($levelObj->badge_color)) ? $levelObj->badge_color : '#1e40af';
                    $badgeTextColor = ($levelObj && !empty($levelObj->badge_text_color)) ? $levelObj->badge_text_color : '#93c5fd';
                    $levelName = $user->level ?? 'Newbie';
                @endphp
                <div><span class="opacity-50">{{ __('messages.other_lang.level') }}:</span> 
                    <span class="level-badge" style="display: inline-block; background-color: {{ $badgeBgColor }}; color: {{ $badgeTextColor }}; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 500; line-height: 1.4;">{{ $levelName }}</span>
                </div>
                <!-- <div><span class="opacity-50">{{ __('messages.profile.profile_visitors') }}:</span> {{ $user->visitor_count ?? 0 }}</div> -->
            </div>
        </div>

        <!-- Profile Info -->
        <div class="md:w-[80%] space-y-4">
            <div>
                <h2 class="text-2xl font-bold flex items-center gap-3">{{ $user->username ?: 'Anonymous' }}
                    <span class="flex items-center gap-1">
                        @if ($user->isOnline())
                            <span class="bg-green-400 size-[6px] rounded-full block"></span>
                            <p class="opacity-80 text-xs font-medium">online</p>
                        @else
                            <span class="bg-red-400 size-[6px] rounded-full block"></span>
                            <p class="opacity-80 text-xs font-medium">offline</p>
                        @endif
                    </span>

                </h2>

                <!-- <p class="text-sm text-gray-400">{{ __('messages.other_lang.from') }} {{ $user->location ?? __('messages.other_lang.unknown') }}</p> -->
                <div class="mt-2 space-x-2 flex flex-wrap gap-2">
                    <button class="bg-purple-600 hover:bg-purple-700 px-3 py-1 rounded text-sm text-white">{{ __('messages.customer_profile.profile')}}</button>
                    <a href="{{ route('user.public.comments', $user->username ?? $user->id) }}" class="bg-gray-700 hover:bg-gray-600 px-3 py-1 text-white rounded text-sm">{{ __('messages.customer_profile.comments')}}</a>
                    @auth
                        @if(auth()->id() !== $user->id)
                            @php
                                $isFollowing = \App\Models\Followers::where('following', auth()->id())
                                    ->where('followers', $user->id)
                                    ->exists();
                            @endphp
                            
                            @if($isFollowing)
                                <button onclick="toggleFollow({{ $user->id }}, 'unfollow')" class="bg-white dark:bg-gray-800 hover:bg-red-50 dark:hover:bg-red-900 text-red-600 dark:text-red-400 border-2 border-red-600 dark:border-red-500 hover:border-red-700 dark:hover:border-red-400 px-3 py-1 rounded text-sm font-semibold flex items-center gap-1 shadow-sm hover:shadow-md transition-all duration-200" id="follow-btn-{{ $user->id }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                    </svg>
                                    {{ __('messages.unfollow') }}
                                </button>
                            @else
                                <button onclick="toggleFollow({{ $user->id }}, 'follow')" class="bg-blue-600 hover:bg-blue-700 px-3 py-1 text-white rounded text-sm flex items-center gap-1" id="follow-btn-{{ $user->id }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                    </svg>
                                    {{ __('messages.follow_this_member') }}
                                </button>
                            @endif
                        @endif
                    @endauth
                    
                    @auth
                        @if(auth()->id() !== $user->id)
                            @if($user->canReceiveMessagesFrom(auth()->user()))
                                <a href="{{ route('messages.user', $user->id) }}" class="bg-gray-700 hover:bg-gray-600 px-3 py-1 text-white rounded text-sm flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ __('messages.other_lang.send_message') }}
                                </a>
                            @else
                                <span class="bg-gray-500 px-3 py-1 text-white rounded text-sm flex items-center gap-1 cursor-not-allowed opacity-50" title="This user does not allow messages from you">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ __('messages.send_message') }}
                                </span>
                            @endif
                            @if(isset($isBlockedByMe) && $isBlockedByMe)
                                <form action="{{ route('user.unblock', $user->username ?? $user->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 px-3 py-1 text-white rounded text-sm flex items-center gap-1">
                                        {{ __('messages.block.unblock') }}
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('user.block', $user->username ?? $user->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 px-3 py-1 text-white rounded text-sm flex items-center gap-1" title="{{ __('messages.block.block_this_user') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                        {{ __('messages.block.block') }}
                                    </button>
                                </form>
                            @endif
                        @endif
                    @endauth
                </div>
            </div>

            <div>
                <h3 class="text-xl font-semibold border-b border-gray-700 pb-1 mb-2">{{ __('messages.customer_profile.about_me')}}</h3>
                <p class="text-sm opacity-70">
                    {{ $user->about_me ?? __("messages.customer_profile.no_information") }}
                </p>
            </div>

            <div class="md:flex items-start gap-4 text-sm">
                <div class="flex-1">
                <div>
                    <h4 class="text-purple-400 font-semibold">{{ __('messages.customer_profile.loction')}}:</h4> <span class="opacity-70"> {{ $user->location ?? __("messages.customer_profile.not_specified") }} </span>
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
                    <p><strong>{{ __('messages.customer_profile.consoles')}}:</strong> <span class="opacity-70"> {{ $user->consoles ?? __("messages.customer_profile.not_specified") }} </span></p>
                    <p><strong>{{ __('messages.customer_profile.accessories')}}:</strong> <span class="opacity-70"> {{ $user->accessories ?? __("messages.customer_profile.not_specified") }} </span></p>
                </div>
                <div class="col-span-2">
                    <h4 class="text-purple-400 font-semibold">{{ __('messages.customer_profile.interests')}}</h4>
                    <p><strong>{{ __('messages.customer_profile.favourite_games')}}:</strong> <span class="opacity-70"> {{ $user->favorite_games ?? __("messages.customer_profile.not_specified") }} </span></p>
                    <p><strong>{{ __('messages.customer_profile.favourite_genre')}}:</strong> <span class="opacity-70"> {{ $user->favorite_genre ?? __("messages.customer_profile.not_specified") }} </span></p>
                    <p><strong>{{ __('messages.customer_profile.series')}}:</strong> <span class="opacity-70"> {{ $user->favorite_series ?? __("messages.customer_profile.not_specified") }} </span></p>
                    <p><strong>{{ __('messages.customer_profile.movies')}}:</strong> <span class="opacity-70"> {{ $user->favorite_films ?? __("messages.customer_profile.not_specified") }} </span></p>
                    <p><strong>{{ __('messages.customer_profile.music')}}:</strong> <span class="opacity-70"> {{ $user->favorite_music ?? __("messages.customer_profile.not_specified") }} </span></p>
                    <p><strong>{{ __('messages.customer_profile.hobbies')}}:</strong> <span class="opacity-70"> {{ $user->hobbies ?? __("messages.customer_profile.not_specified") }} </span></p>
                    <p><strong>{{ __('messages.customer_profile.my_motto')}}:</strong> <span class="opacity-70"> {{ $user->my_motto ?? __("messages.customer_profile.not_specified") }} </span></p>
                </div>
            </div>
                <div class="flex-1">

                    <div>
                        <h4 class="text-purple-400 font-semibold">{{ __('messages.customer_profile.occupation')}}:</h4> <span class="opacity-70"> {{ $user->occupation ?? __("messages.customer_profile.not_specified") }} </span>
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
                                                $followingUser = $following->follower;
                                            @endphp
                                            <a href="{{ route('user.public.profile', $followingUser->username ?? $followingUser->id) }}" title="{{ $followingUser->username }}">
                                                <img
                                                    src="{{ $followingUser->profile_image ?: asset('web/media/avatars/150-2.jpg') }}"
                                                    class="size-12 border rounded-full object-cover ring-1 ring-white/20 hover:ring-2 hover:ring-purple-400 transition-all cursor-pointer"
                                                    alt="{{ $followingUser->username }}"
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
                                                $followingUser = $following->follower;
                                            @endphp
                                            <a href="{{ route('user.public.profile', $followingUser->username ?? $followingUser->id) }}" title="{{ $followingUser->username }}">
                                                <img
                                                    src="{{ $followingUser->profile_image ?: asset('web/media/avatars/150-2.jpg') }}"
                                                    class="size-12 border rounded-full object-cover ring-1 ring-white/20 hover:ring-2 hover:ring-purple-400 transition-all cursor-pointer"
                                                    alt="{{ $followingUser->username }}"
                                                />
                                            </a>
                                        @endforeach
                                        </div>
                                        @endif

                                        <!-- Footer text - non-clickable "see more" -->
                                        @if(isset($remainingCount) && $remainingCount > 0)
                                        <div class="text-center text-purple-300 text-sm font-medium">
                                            <span class="cursor-default">
                                                {{ __('messages.other_lang.and_more', ['count' => $remainingCount]) }}
                                            </span>
                                        </div>
                                        @endif
                                    </div>
                                    @else
                                    <!-- Empty state -->
                                    <div class="text-center py-4">
                                        <div class="text-3xl mb-2">👥</div>
                                        <p class="text-sm text-gray-400">{{ __('messages.other_lang.user_not_following_anyone') ?? 'This user is not following anyone yet' }}</p>
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

<script>
function toggleFollow(userId, action) {
    const button = document.getElementById('follow-btn-' + userId);
    const originalText = button.innerHTML;
    
    // Show loading state
    button.disabled = true;
    button.innerHTML = '<svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Loading...';
    
    const url = action === 'follow' ? `/user/${userId}/follow` : `/user/${userId}/unfollow`;
    
    fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update button based on action
            if (action === 'follow') {
                button.className = 'bg-white dark:bg-gray-800 hover:bg-red-50 dark:hover:bg-red-900 text-red-600 dark:text-red-400 border-2 border-red-600 dark:border-red-500 hover:border-red-700 dark:hover:border-red-400 px-3 py-1 rounded text-sm font-semibold flex items-center gap-1 shadow-sm hover:shadow-md transition-all duration-200';
                button.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    {{ __('messages.unfollow') }}
                `;
                button.setAttribute('onclick', `toggleFollow(${userId}, 'unfollow')`);
            } else {
                button.className = 'bg-blue-600 hover:bg-blue-700 px-3 py-1 text-white rounded text-sm flex items-center gap-1';
                button.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    {{ __('messages.follow_this_member') }}
                `;
                button.setAttribute('onclick', `toggleFollow(${userId}, 'follow')`);
            }
            
            // Show success message
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message || 'An error occurred', 'error');
            button.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
        button.innerHTML = originalText;
    })
    .finally(() => {
        button.disabled = false;
    });
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-md text-white ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Remove notification after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
@if(session('success'))
document.addEventListener('DOMContentLoaded', function() { showSuccessToast(@json(session('success'))); });
@endif
</script>
@endsection

