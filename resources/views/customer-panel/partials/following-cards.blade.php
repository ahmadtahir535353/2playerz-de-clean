@foreach($followingUsers as $follow)
    @php
        $user = $follow->follower;
    @endphp
    <div class="bg-white dark:bg-black rounded-lg shadow-md p-4 hover:shadow-lg transition-shadow following-card">
        <!-- Profile Image -->
        <div class="text-center mb-4">
            <img src="{{ $user->profile_image ?: asset('web/media/avatars/150-2.jpg') }}" 
                 alt="{{ $user->username }}" 
                 class="w-20 h-20 rounded-full mx-auto object-cover border-2 border-gray-200 dark:border-gray-700">
        </div>

        <!-- User Info -->
        <div class="text-center">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                {{ $user->username ?: 'Anonymous' }}
            </h3>
            
            @if($user->location)
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                    <i class="fas fa-map-marker-alt mr-1"></i>
                    {{ $user->location }}
                </p>
            @endif

            <!-- User Stats -->
            <div class="flex justify-center space-x-4 text-xs text-gray-500 dark:text-gray-400 mb-3">
                <div class="text-center">
                    <div class="font-semibold">{{ $user->comment_points ?? 0 }}</div>
                    <div>{{ __('messages.other_lang.player_points') }}</div>
                </div>
                <div class="text-center">
                    <div class="font-semibold">{{ $user->level ?? 'Newbie' }}</div>
                    <div>{{ __('messages.other_lang.level') }}</div>
                </div>
            </div>

            <!-- Online Status -->
            <div class="flex items-center justify-center mb-3">
                @if($user->isOnline())
                    <span class="bg-green-400 size-[8px] rounded-full block mr-2"></span>
                    <span class="text-xs text-green-400 font-medium">online</span>
                @else
                    <span class="bg-red-400 size-[8px] rounded-full block mr-2"></span>
                    <span class="text-xs text-red-400 font-medium">offline</span>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="space-y-2">
                <div class="flex space-x-2">
                    <a href="{{ route('user.public.profile', $user->username ?? $user->id) }}"
                       class="flex-1 bg-purple-600 hover:bg-purple-700 text-white text-xs py-2 px-3 rounded text-center transition-colors">
                        {{ __('messages.view_profile') }}
                    </a>
                </div>
                
                <!-- Unfollow Button -->
                <button onclick="unfollowUser({{ $user->id }}, '{{ $user->username }}')" 
                        class="w-full bg-white dark:bg-gray-800 hover:bg-red-50 dark:hover:bg-red-900 text-red-600 dark:text-red-400 border-2 border-red-600 dark:border-red-500 hover:border-red-700 dark:hover:border-red-400 text-xs font-semibold py-2 px-3 rounded shadow-sm hover:shadow-md transition-all duration-200">
                    <i class="fas fa-user-minus mr-1"></i>
                    {{ __('messages.unfollow') }}
                </button>
            </div>

            <!-- Followed Since -->
            <div class="mt-3 text-xs text-gray-400">
                {{ __('messages.followed_since') }}: {{ $follow->created_at->format('d M, Y') }}
            </div>
        </div>
    </div>
@endforeach

