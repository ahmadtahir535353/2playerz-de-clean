@foreach($conversations as $conversation)
    @php
        $otherUser = $conversation->getOtherUser(auth()->id());
        $lastMessage = $conversation->lastMessage;
        $unreadCount = \App\Models\Message::where('conversation_id', $conversation->id)
            ->where('recipient_id', auth()->id())
            ->where('is_read', false)
            ->count();
        $isOtherBlocked = \App\Models\UserBlock::where('blocker_id', auth()->id())->where('blocked_id', $otherUser->id)->exists();
        $otherUserAvatar = $isOtherBlocked ? asset('web/media/avatars/150-2.jpg') : ($otherUser->profile_image ?? asset('web/media/avatars/150-2.jpg'));
    @endphp
    
    <div class="flex items-center justify-between bg-white dark:bg-black p-4 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors conversation-item" 
         data-conversation-id="{{ $conversation->id }}"
         data-conversation-date="{{ $conversation->last_message_at }}">
        <div class="flex items-center space-x-4 flex-1">
            <!-- Avatar (default when blocked) -->
            <div class="relative">
                <img src="{{ $otherUserAvatar }}" 
                     alt="{{ $otherUser->username }}" 
                     class="w-12 h-12 rounded-full object-cover">
                @if($otherUser->isOnline())
                    <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-400 rounded-full border-2 border-white dark:border-gray-800"></div>
                @else
                    <div class="absolute bottom-0 right-0 w-3 h-3 bg-red-400 rounded-full border-2 border-white dark:border-gray-800"></div>
                @endif
            </div>
            
            <!-- User Info -->
            <div class="flex-1">
                <div class="flex items-center justify-between">
                    <a href="{{ route('messages.show', $conversation->id) }}" class="text-lg font-semibold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        {{ $otherUser->username }}
                    </a>
                    <div class="flex items-center space-x-2">
                        @if($unreadCount > 0)
                            <span class="bg-red-500 text-white text-xs rounded-full px-2 py-1">
                                {{ $unreadCount }}
                            </span>
                        @endif
                        @if($lastMessage)
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $lastMessage->created_at->format('H:i') }}
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-300">
                    <span>{{ __('messages.other_lang.player_points') }}: {{ $otherUser->comment_points ?? 0 }}</span>
                    <span>•</span>
                    <span>{{ $otherUser->level ?? __('messages.other_lang.newbie') }}</span>
                </div>
                
                @if($lastMessage)
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 truncate">
                        {{ $lastMessage->sender_id == auth()->id() ? __('messages.other_lang.you_colon') : '' }}{{ $lastMessage->message }}
                    </p>
                @endif
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex items-center space-x-2">
            <!-- Message Icon -->
            <a href="{{ route('messages.show', $conversation->id) }}" 
               class="p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                @if($unreadCount > 0)
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                    </svg>
                @else
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                @endif
            </a>
            
            <!-- Delete Button -->
            <button onclick="deleteConversation({{ $conversation->id }})" 
                    class="p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    </div>
@endforeach

