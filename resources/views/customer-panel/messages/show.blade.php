@extends('customer-panel.layout.main')
@section('title', __('messages.other_lang.conversation_with') . ' ' . $otherUser->username)

@section('head')
<style>
    /* Custom scrollbar for messages container */
    #messages-container::-webkit-scrollbar {
        width: 6px;
    }
    
    #messages-container::-webkit-scrollbar-track {
        background: transparent;
    }
    
    #messages-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }
    
    #messages-container::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    .dark #messages-container::-webkit-scrollbar-thumb {
        background: #475569;
    }
    
    .dark #messages-container::-webkit-scrollbar-thumb:hover {
        background: #64748b;
    }
    
    /* Message bubble animations */
    .message-bubble {
        animation: messageSlideIn 0.2s ease-out;
    }
    
    @keyframes messageSlideIn {
        from {
            opacity: 0;
            transform: translateY(5px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    
    /* WhatsApp-like message styling */
    .message-bubble {
        border-radius: 18px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }
    
    /* Ensure right-side messages are visible in dark theme */
    .bg-blue-600,
    .message-bubble.bg-blue-600,
    div.bg-blue-600 {
        background-color: #2563eb !important;
        color: white !important;
    }
    
    /* Force blue background for all right-side messages */
    .flex.justify-end .bg-blue-600,
    .flex.justify-end .message-bubble,
    .flex.justify-end div[class*="bg-blue"] {
        background-color: #2563eb !important;
        color: white !important;
    }
    
    /* Override any dark theme conflicts */
    .dark .flex.justify-end .bg-blue-600,
    .dark .flex.justify-end .message-bubble {
        background-color: #2563eb !important;
        color: white !important;
    }
    
    /* Ensure left-side messages have white background and blue text */
    .bg-white.text-blue-600,
    .flex.justify-start .bg-white,
    .flex.justify-start .message-bubble,
    .flex.justify-start div[class*="bg-white"] {
        background-color: white !important;
        color: #2563eb !important;
    }
    
    /* Force blue text for left-side message content */
    .flex.justify-start .message-text,
    .flex.justify-start .text-sm {
        color: #2563eb !important;
    }
    
    /* Override any dark theme conflicts for left-side messages */
    .dark .flex.justify-start .bg-white,
    .dark .flex.justify-start .message-bubble {
        background-color: white !important;
        color: #2563eb !important;
    }
    
    /* Hover effects for message actions */
    .message-actions {
        transition: all 0.15s ease-in-out;
    }
    
    .message-actions:hover {
        transform: scale(1.05);
    }
    
    /* Facebook-like spacing */
    .message-container {
        margin-bottom: 2px;
    }
    
    /* Ensure input field maintains normal styling during edit mode */
    #message-input {
        background-color: white !important;
        color: #1f2937 !important;
        border: 1px solid #d1d5db !important;
    }
    
    .dark #message-input {
        background-color: #1f2937 !important;
        color: white !important;
        border: 1px solid #4b5563 !important;
    }
    
    /* Remove any inherited styles from message bubbles */
    #message-input:focus {
        background-color: white !important;
        color: #1f2937 !important;
        border-color: #8b5cf6 !important;
        box-shadow: 0 0 0 2px rgba(139, 92, 246, 0.2) !important;
    }
    
    .dark #message-input:focus {
        background-color: #1f2937 !important;
        color: white !important;
        border-color: #8b5cf6 !important;
        box-shadow: 0 0 0 2px rgba(139, 92, 246, 0.2) !important;
    }
</style>
@endsection
@section('content')

<style>
    @media (max-width: 768px) {
        .message-wrapper { height: calc(100vh - 80px) !important; }
    }
    @media (min-width: 769px) and (max-width: 1024px) {
        .message-wrapper { height: calc(100vh - 100px) !important; }
    }
    @media (min-width: 1025px) {
        .message-wrapper { height: calc(100vh - 100px) !important; }
    }
</style>

<div class="flex flex-col h-full message-wrapper" style="height: calc(100vh - 100px);">
    <div class="bg-[#F5F5F5] dark:bg-[#18171C] mb-2 p-3 text-end rounded-md flex-shrink-0">
        <a href="{{ route('messages.index') }}" class="opacity-60 underline font-bold mr-5 text-[20px]">
            {{ __('messages.other_lang.back_to_messages') }}
        </a>
    </div>

    <div class="w-full bg-[#F5F5F5] dark:bg-[#161618] rounded-md shadow-lg flex flex-col flex-1 overflow-hidden">
        <!-- Header -->
        <div class="bg-white dark:bg-black p-4 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
        <div class="flex items-center space-x-3">
            <a href="{{ route('user.public.profile', $otherUser->username ?? $otherUser->id) }}" class="relative hover:opacity-80 transition-opacity">
                <img src="{{ (isset($isBlocked) && $isBlocked) ? asset('web/media/avatars/150-2.jpg') : ($otherUser->profile_image ?? asset('web/media/avatars/150-2.jpg')) }}" 
                     alt="{{ $otherUser->username }}" 
                     class="w-10 h-10 rounded-full object-cover">
                @if($otherUser->isOnline())
                    <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-400 rounded-full border-2 border-white dark:border-gray-800"></div>
                @else
                    <div class="absolute bottom-0 right-0 w-3 h-3 bg-red-400 rounded-full border-2 border-white dark:border-gray-800"></div>
                @endif
            </a>
            <div>
                <a href="{{ route('user.public.profile', $otherUser->username ?? $otherUser->id) }}" class="text-lg font-semibold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                    {{ $otherUser->username }}
                </a>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    @if($otherUser->isOnline())
                        {{ __('messages.other_lang.online') }}
                    @else
                        {{ __('messages.other_lang.offline') }}
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Messages Container -->
    <div id="messages-container" class="overflow-y-auto p-6 bg-gradient-to-b from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 flex-1">
        <!-- Loading Indicator for Older Messages -->
        <div id="loading-older-messages" class="text-center py-4 hidden">
            <div class="inline-flex items-center space-x-2">
                <svg class="animate-spin h-5 w-5 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('messages.other_lang.loading_older_messages') }}</span>
            </div>
        </div>
        
        @foreach($messages as $message)
            <div class="flex {{ $message->sender_id == auth()->id() ? 'justify-end' : 'justify-start' }} mb-2 group" data-message-id="{{ $message->id }}">
                <div class="flex items-end space-x-2 max-w-xs lg:max-w-md">
                    @if($message->sender_id != auth()->id())
                        <!-- Avatar for other user (left side); use default when blocked -->
                        <div class="flex-shrink-0 w-8 h-8 rounded-full overflow-hidden">
                            <img src="{{ (isset($isBlocked) && $isBlocked) ? asset('web/media/avatars/150-2.jpg') : ($message->sender->profile_image ?? asset('web/media/avatars/150-2.jpg')) }}" 
                                 alt="{{ $message->sender->username }}" 
                                 class="w-full h-full object-cover"
                                 onerror="this.src='{{ asset('web/media/avatars/150-2.jpg') }}'">
                        </div>
                    @endif
                    
                    <!-- Message Bubble -->
                    <div class="relative">
                        <div class="px-3 py-2 rounded-2xl {{ $message->sender_id == auth()->id() ? 'bg-blue-600 text-white rounded-br-md' : 'bg-white text-blue-600 rounded-bl-md' }} shadow-sm message-bubble max-w-xs lg:max-w-sm" 
                             @if($message->sender_id == auth()->id()) 
                                 style="background-color: #2563eb !important; color: white !important;" 
                             @else 
                                 style="background-color: white !important; color: #2563eb !important;" 
                             @endif>
                            <!-- Username and Edited indicator at top of message -->
                            @php
                                $isDeleted = ($message->sender_id == auth()->id() && $message->is_deleted_by_sender) || 
                                            ($message->sender_id != auth()->id() && $message->is_deleted_by_recipient);
                            @endphp
                            
                            <div class="mb-1 flex justify-between items-center w-full">
                                <span class="text-xs font-bold {{ $message->sender_id == auth()->id() ? 'text-yellow-300' : 'text-purple-600' }}">
                                    {{ $message->sender_id == auth()->id() ? __('messages.other_lang.me') : $message->sender->username }}
                                </span>
                                @if($message->is_edited && !$isDeleted)
                                    <span class="text-xs {{ $message->sender_id == auth()->id() ? 'text-yellow-200' : 'text-purple-500' }} font-semibold italic flex items-center gap-1 ml-auto">
                                        <span>✏️</span>
                                        <span>{{ __('messages.other_lang.edited_label') }}</span>
                                    </span>
                                @endif
                            </div>
                            
                            <!-- Message Content -->
                            
                            @if($isDeleted)
                                <div class="message-text text-base leading-relaxed break-words italic {{ $message->sender_id == auth()->id() ? 'text-blue-100' : 'text-gray-500' }}" 
                                     @if($message->sender_id != auth()->id()) style="color: #6b7280 !important;" @endif>
                                    {{ __('messages.other_lang.this_message_was_deleted') }}
                                </div>
                            @else
                                <div class="message-text text-base leading-relaxed break-words" 
                                     @if($message->sender_id != auth()->id()) style="color: #2563eb !important;" @endif>
                                    {!! nl2br(e($message->message)) !!}
                                </div>
                            @endif
                            
                            <!-- Message Footer with Date and Time -->
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs {{ $message->sender_id == auth()->id() ? 'text-blue-100' : 'text-blue-500' }}">
                                    {{ $message->created_at->format('d.m.Y, H:i') }}
                                </span>
                                
                                @if($message->sender_id == auth()->id())
                                    <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity ml-2">
                                        <button onclick="editMessage({{ $message->id }})" class="p-1 rounded-full hover:bg-white hover:bg-opacity-20 transition-all duration-200" title="{{ __('messages.other_lang.edit') }}">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button onclick="deleteMessage({{ $message->id }})" class="p-1 rounded-full hover:bg-white hover:bg-opacity-20 transition-all duration-200" title="{{ __('messages.other_lang.delete') }}">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Message Tail -->
                        <div class="absolute {{ $message->sender_id == auth()->id() ? 'right-0 bottom-0' : 'left-0 bottom-0' }} w-0 h-0 {{ $message->sender_id == auth()->id() ? 'border-l-8 border-l-blue-600 border-b-8 border-b-transparent' : 'border-r-8 border-r-white border-b-8 border-b-transparent' }}"></div>
                    </div>
                    
                    @if($message->sender_id == auth()->id())
                        <!-- Avatar for own messages (right side) -->
                        <div class="flex-shrink-0 w-8 h-8 rounded-full overflow-hidden">
                            <img src="{{ $message->sender->profile_image }}" 
                                 alt="{{ $message->sender->username }}" 
                                 class="w-full h-full object-cover"
                                 onerror="this.src='{{ asset('assets/image/avatar.png') }}'">
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Message Input (hidden when blocked; show Facebook-style message) -->
    <div class="bg-white dark:bg-black p-4 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
        @if(isset($isBlocked) && $isBlocked)
            <p class="text-center text-gray-500 dark:text-gray-400 text-sm py-2">{{ __('messages.block.cannot_reply_blocked') }}</p>
        @else
        <form id="message-form" class="flex items-center space-x-2">
            @csrf
            <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">
            <input type="hidden" name="recipient_id" value="{{ $otherUser->id }}">
            
            <!-- Emoji Picker Button (Left side, outside of input) -->
            <div id="emoji-button-container"></div>
            
            <input type="text" 
                   name="message" 
                   id="message-input"
                   placeholder="{{ __('messages.other_lang.type_message_placeholder') }}"
                   class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-800 dark:text-white"
                   required>
            
            <!-- Send Button -->
            <button type="submit" id="send-button" class="bg-green-600 hover:bg-green-700 text-white p-2 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path>
                </svg>
            </button>
        </form>
        @endif
    </div>
</div>
</div>

<script>
var isConversationBlocked = @json(isset($isBlocked) && $isBlocked);
var defaultAvatarUrl = @json(asset('web/media/avatars/150-2.jpg'));
// Auto-scroll to bottom
function scrollToBottom() {
    const container = document.getElementById('messages-container');
    container.scrollTop = container.scrollHeight;
}

// Add message to conversation dynamically
function addMessageToConversation(messageData) {
    const messagesContainer = document.getElementById('messages-container');
    const currentUserId = {{ auth()->id() }};
    const isOwnMessage = messageData.sender_id == currentUserId;
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `flex ${isOwnMessage ? 'justify-end' : 'justify-start'} mb-2 group`;
    messageDiv.setAttribute('data-message-id', messageData.id);
    
    const messageContainer = document.createElement('div');
    messageContainer.className = 'flex items-end space-x-2 max-w-xs lg:max-w-md';
    
    // Avatar for other user (left side)
    let avatar = null;
    if (!isOwnMessage) {
        avatar = document.createElement('div');
        avatar.className = 'flex-shrink-0 w-8 h-8 rounded-full overflow-hidden';
        
        const avatarImg = document.createElement('img');
        avatarImg.src = isConversationBlocked ? defaultAvatarUrl : (messageData.sender.profile_image || defaultAvatarUrl);
        avatarImg.alt = messageData.sender.username;
        avatarImg.className = 'w-full h-full object-cover';
        avatarImg.onerror = function() {
            this.src = '{{ asset('assets/image/avatar.png') }}';
        };
        
        avatar.appendChild(avatarImg);
    }
    
    // Message Bubble Container
    const bubbleContainer = document.createElement('div');
    bubbleContainer.className = 'relative';
    
    const messageBubble = document.createElement('div');
    messageBubble.className = `px-3 py-2 rounded-2xl ${isOwnMessage ? 'bg-blue-600 text-white rounded-br-md' : 'bg-white text-blue-600 rounded-bl-md'} shadow-sm message-bubble max-w-xs lg:max-w-md`;
    
    // Force colors for messages
    if (isOwnMessage) {
        messageBubble.style.backgroundColor = '#2563eb';
        messageBubble.style.color = 'white';
    } else {
        messageBubble.style.backgroundColor = 'white';
        messageBubble.style.color = '#2563eb';
    }
    
    messageBubble.innerHTML = `
        <!-- Username and Edited indicator at top of message -->
        <div class="mb-1 flex justify-between items-center w-full">
            <span class="text-xs font-bold ${isOwnMessage ? 'text-yellow-300' : 'text-purple-600'}">
                ${isOwnMessage ? '{{ __('messages.other_lang.me') }}' : messageData.sender.username}
            </span>
            ${messageData.is_edited ? `
                <span class="text-xs ${isOwnMessage ? 'text-yellow-200' : 'text-purple-500'} font-semibold italic flex items-center gap-1 ml-auto">
                    <span>✏️</span>
                    <span>{{ __("messages.other_lang.edited_label") }}</span>
                </span>
            ` : ''}
        </div>
        
        <!-- Message Content -->
        <div class="message-text text-base leading-relaxed break-words" ${!isOwnMessage ? 'style="color: #2563eb !important;"' : ''}>
            ${messageData.message.replace(/\n/g, '<br>')}
        </div>
        
        <!-- Message Footer with Date and Time -->
        <div class="flex items-center justify-between mt-2">
            <span class="text-xs ${isOwnMessage ? 'text-blue-100' : 'text-blue-500'}">
                ${messageData.created_at}
            </span>
            ${isOwnMessage ? `
                <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity ml-2">
                    <button onclick="editMessage(${messageData.id})" class="p-1 rounded-full hover:bg-white hover:bg-opacity-20 transition-all duration-200" title="{{ __('messages.other_lang.edit') }}">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>
                    <button onclick="deleteMessage(${messageData.id})" class="p-1 rounded-full hover:bg-white hover:bg-opacity-20 transition-all duration-200" title="{{ __('messages.other_lang.delete') }}">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            ` : ''}
        </div>
    `;
    
    bubbleContainer.appendChild(messageBubble);
    
    // Message Tail
    const messageTail = document.createElement('div');
    messageTail.className = `absolute ${isOwnMessage ? 'right-0 bottom-0' : 'left-0 bottom-0'} w-0 h-0 ${isOwnMessage ? 'border-l-8 border-l-blue-600 border-b-8 border-b-transparent' : 'border-r-8 border-r-white border-b-8 border-b-transparent'}`;
    
    bubbleContainer.appendChild(messageTail);
    
    // Add avatar before bubble for other user, after bubble for own messages
    if (!isOwnMessage && avatar) {
        messageContainer.appendChild(avatar);
    }
    messageContainer.appendChild(bubbleContainer);
    
    // Add avatar after bubble for own messages (should show current user's avatar)
    if (isOwnMessage) {
        const ownAvatar = document.createElement('div');
        ownAvatar.className = 'flex-shrink-0 w-8 h-8 rounded-full overflow-hidden';
        
        const ownAvatarImg = document.createElement('img');
        console.log(messageData);
        // For own messages, show the current user's avatar (the sender)
        ownAvatarImg.src = messageData.sender.profile_image || '{{ asset('assets/image/avatar.png') }}';
        ownAvatarImg.alt = messageData.sender.username;
        ownAvatarImg.className = 'w-full h-full object-cover';
        ownAvatarImg.onerror = function() {
            this.src = '{{ asset('assets/image/avatar.png') }}';
        };
        
        ownAvatar.appendChild(ownAvatarImg);
        messageContainer.appendChild(ownAvatar);
    }
    
    messageDiv.appendChild(messageContainer);
    messagesContainer.appendChild(messageDiv);
}

// Add temporary "sending" message
function addTemporaryMessage(messageText) {
    const messagesContainer = document.getElementById('messages-container');
    const currentUserId = {{ auth()->id() }};
    
    const messageDiv = document.createElement('div');
    messageDiv.className = 'flex justify-end mb-2 group';
    messageDiv.id = 'temp-message';
    
    const messageContainer = document.createElement('div');
    messageContainer.className = 'flex items-end space-x-2 max-w-xs lg:max-w-md';
    
    // Message Bubble Container
    const bubbleContainer = document.createElement('div');
    bubbleContainer.className = 'relative';
    
    const messageBubble = document.createElement('div');
    messageBubble.className = 'px-3 py-2 rounded-2xl bg-gray-400 text-white rounded-br-md shadow-sm opacity-75 max-w-xs lg:max-w-sm';
    messageBubble.style.backgroundColor = '#6b7280';
    messageBubble.style.color = 'white';
    
    const now = new Date();
    const formattedDate = `${String(now.getDate()).padStart(2, '0')}.${String(now.getMonth() + 1).padStart(2, '0')}.${now.getFullYear()}, ${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;
    
    messageBubble.innerHTML = `
        <!-- Username at top of message -->
        <div class="mb-1">
            <span class="text-xs font-bold text-yellow-300">{{ __('messages.other_lang.me') }}</span>
        </div>
        
        <!-- Message Content -->
        <div class="text-base leading-relaxed break-words">
            ${messageText.replace(/\n/g, '<br>')}
        </div>
        
        <!-- Message Footer with Date -->
        <div class="flex items-center justify-between mt-2">
            <span class="text-xs text-gray-300">
                ${formattedDate}
            </span>
            <span class="text-xs text-gray-300">
                {{ __("messages.other_lang.sending") }}...
            </span>
        </div>
    `;
    
    bubbleContainer.appendChild(messageBubble);
    
    // Message Tail
    const messageTail = document.createElement('div');
    messageTail.className = 'absolute right-0 bottom-0 w-0 h-0 border-l-8 border-l-gray-400 border-b-8 border-b-transparent';
    
    bubbleContainer.appendChild(messageTail);
    
    messageContainer.appendChild(bubbleContainer);
    
    // Add avatar after bubble for own messages (temporary message)
    const avatar = document.createElement('div');
    avatar.className = 'flex-shrink-0 w-8 h-8 rounded-full overflow-hidden';
    
    const avatarImg = document.createElement('img');
    avatarImg.src = '{{ auth()->user()->profile_image }}';
    avatarImg.alt = '{{ auth()->user()->username }}';
    avatarImg.className = 'w-full h-full object-cover';
    avatarImg.onerror = function() {
        this.src = '{{ asset('assets/image/avatar.png') }}';
    };
    
    avatar.appendChild(avatarImg);
    messageContainer.appendChild(avatar);
    
    messageDiv.appendChild(messageContainer);
    messagesContainer.appendChild(messageDiv);
    scrollToBottom();
}

// Remove temporary message
function removeTemporaryMessage() {
    const tempMessage = document.getElementById('temp-message');
    if (tempMessage) {
        tempMessage.remove();
    }
}

// Send message or save edit (skip when conversation is blocked)
const messageForm = document.getElementById('message-form');
if (messageForm) {
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const messageInput = document.getElementById('message-input');
        const messageText = messageInput ? messageInput.value.trim() : '';
        if (messageText === '') return;
        if (window.editingMessageId) {
            saveEditedMessage(window.editingMessageId, messageText);
        } else {
            sendNewMessage(messageText);
        }
    });
}

// Send new message
function sendNewMessage(messageText) {
    const formData = new FormData();
    formData.append('conversation_id', {{ $conversation->id }});
    formData.append('recipient_id', {{ $conversation->getOtherUser(auth()->id())->id }});
    formData.append('message', messageText);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}');
    
    const sendButton = document.getElementById('send-button');
    if (!sendButton) {
        console.error('Send button not found!');
        return;
    }
    
    sendButton.disabled = true;
    sendButton.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>';
    
    // Add a temporary "sending" message
    addTemporaryMessage(messageText);
    
    fetch('/messages', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove temporary message
            removeTemporaryMessage();
            // Add message to the conversation
            addMessageToConversation(data.message);
            // Clear the input
            document.getElementById('message-input').value = '';
            // Scroll to bottom
            scrollToBottom();
        } else {
            // Remove temporary message on error
            removeTemporaryMessage();
            // Re-enable send button on error
            const sendButton = document.getElementById('send-button');
            if (sendButton) {
                sendButton.disabled = false;
                sendButton.innerHTML = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>';
            }
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ __("messages.other_lang.send_failed") }}',
                confirmButtonColor: '#7c3aed'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        removeTemporaryMessage();
        // Re-enable send button on error
        const sendButton = document.getElementById('send-button');
        if (sendButton) {
            sendButton.disabled = false;
            sendButton.innerHTML = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>';
        }
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ __("messages.other_lang.send_failed") }}',
            confirmButtonColor: '#7c3aed'
        });
    })
    .finally(() => {
        // Re-enable form
        sendButton.disabled = false;
        sendButton.innerHTML = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>';
    });
}

// Edit message - show in main input field
function editMessage(messageId) {
    const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
    const messageTextElement = messageElement.querySelector('.message-text');
    
    // Extract clean text by converting HTML breaks to newlines and trimming
    let originalText = messageTextElement.innerHTML;
    // Replace <br> tags with newlines
    originalText = originalText.replace(/<br\s*\/?>/gi, '\n');
    // Remove any remaining HTML tags
    originalText = originalText.replace(/<[^>]*>/g, '');
    // Decode HTML entities
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = originalText;
    originalText = tempDiv.textContent || tempDiv.innerText || '';
    // Trim whitespace
    originalText = originalText.trim();
    
    // Get the main input field and send button
    const messageInput = document.getElementById('message-input');
    const sendButton = document.getElementById('send-button');
    
    if (!sendButton) {
        console.error('Send button not found!');
        return;
    }
    
    // Store original state
    window.editingMessageId = messageId;
    window.originalMessageText = originalText;
    
    // Update UI for edit mode - clear any existing content first
    messageInput.value = '';
    messageInput.value = originalText;
    
    // Reset input field styling to ensure no inherited styles
    messageInput.style.backgroundColor = '';
    messageInput.style.color = '';
    messageInput.style.border = '';
    messageInput.style.boxShadow = '';
    messageInput.removeAttribute('style');
    
    messageInput.focus();
    // Remove .select() to prevent text from being highlighted/selected
    
    // Change send button to edit button
    sendButton.innerHTML = `
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
        </svg>
    `;
    sendButton.title = '{{ __("messages.other_lang.save_changes") }}';
    
    // Add cancel button
    if (!document.getElementById('cancel-edit-button')) {
        const cancelButton = document.createElement('button');
        cancelButton.id = 'cancel-edit-button';
        cancelButton.type = 'button';
        cancelButton.className = 'px-3 py-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200';
        cancelButton.innerHTML = `
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        `;
        cancelButton.title = '{{ __("messages.other_lang.cancel") }}';
        cancelButton.onclick = cancelEdit;
        
        // Insert cancel button before send button
        sendButton.parentNode.insertBefore(cancelButton, sendButton);
    }
    
    // Show edit indicator
    showEditIndicator();
}

// Cancel edit mode
function cancelEdit() {
    const messageInput = document.getElementById('message-input');
    const sendButton = document.getElementById('send-button');
    const cancelButton = document.getElementById('cancel-edit-button');
    
    if (!sendButton) {
        console.error('Send button not found!');
        return;
    }
    
    // Clear edit state
    window.editingMessageId = null;
    window.originalMessageText = null;
    
    // Restore UI
    messageInput.value = '';
    messageInput.placeholder = '{{ __("messages.other_lang.type_message_placeholder") }}';
    
    // Restore send button
    sendButton.innerHTML = `
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path>
        </svg>
    `;
    sendButton.title = '{{ __("messages.other_lang.send") }}';
    
    // Remove cancel button
    if (cancelButton) {
        cancelButton.remove();
    }
    
    // Hide edit indicator
    hideEditIndicator();
}

// Show edit indicator
function showEditIndicator() {
    let indicator = document.getElementById('edit-indicator');
    if (!indicator) {
        indicator = document.createElement('div');
        indicator.id = 'edit-indicator';
        indicator.className = 'bg-yellow-100 dark:bg-yellow-900 border-l-4 border-yellow-500 p-3 mb-4';
        indicator.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <span class="text-yellow-700 dark:text-yellow-200 text-sm font-medium">
                    {{ __('messages.other_lang.editing_message') }}
                </span>
            </div>
        `;
        
        // Insert before message input
        const messageForm = document.getElementById('message-form');
        messageForm.parentNode.insertBefore(indicator, messageForm);
    }
}

// Hide edit indicator
function hideEditIndicator() {
    const indicator = document.getElementById('edit-indicator');
    if (indicator) {
        indicator.remove();
    }
}

// Save edited message
function saveEditedMessage(messageId, newText) {
    if (newText.trim() === '') {
        Swal.fire({
            icon: 'warning',
            title: '{{ __("messages.other_lang.empty_message") }}',
            text: '{{ __("messages.other_lang.message_cannot_be_empty") }}',
            confirmButtonColor: '#7c3aed'
        });
        return;
    }
    
    if (newText.trim() === window.originalMessageText.trim()) {
        cancelEdit();
        return;
    }
    
    // Show loading state
    const sendButton = document.getElementById('send-button');
    if (!sendButton) {
        console.error('Send button not found!');
        return;
    }
    
    const originalButtonContent = sendButton.innerHTML;
    sendButton.disabled = true;
    sendButton.innerHTML = `
        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    `;
    
    fetch(`/messages/${messageId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
        },
        body: JSON.stringify({ message: newText.trim() })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the message in DOM
            const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
            if (messageElement) {
                const messageTextElement = messageElement.querySelector('.message-text');
                const messageHeader = messageElement.querySelector('.mb-1');
                
                // Update message text
                if (messageTextElement) {
                    messageTextElement.innerHTML = newText.trim().replace(/\n/g, '<br>');
                }
                
                // Add or update edited indicator with pen icon
                if (messageHeader && data.message.is_edited) {
                    // Check if edited span already exists
                    let editedSpan = messageHeader.querySelector('.italic');
                    const isOwnMessage = data.message.sender_id == {{ auth()->id() }};
                    
                    if (!editedSpan) {
                        // Create new edited span with pen icon
                        editedSpan = document.createElement('span');
                        editedSpan.className = `text-xs ${isOwnMessage ? 'text-yellow-200' : 'text-purple-500'} font-semibold italic flex items-center gap-1`;
                        editedSpan.innerHTML = '<span>✏️</span><span>{{ __("messages.other_lang.edited_label") }}</span>';
                        
                        // If header doesn't have flex, add it
                        if (!messageHeader.classList.contains('flex')) {
                            messageHeader.classList.add('flex', 'justify-between', 'items-center');
                        }
                        
                        messageHeader.appendChild(editedSpan);
                    } else {
                        // Update existing span to include pen icon if not already present
                        if (!editedSpan.innerHTML.includes('✏️')) {
                            editedSpan.innerHTML = '<span>✏️</span><span>{{ __("messages.other_lang.edited_label") }}</span>';
                            editedSpan.classList.add('flex', 'items-center', 'gap-1');
                        }
                    }
                }
            }
            
            // Show success message
            Swal.fire({
                title: '{{ __("messages.other_lang.message_updated") }}',
                icon: 'success',
                confirmButtonColor: '#7c3aed',
                timer: 1500,
                showConfirmButton: false
            });
            
            // Exit edit mode
            cancelEdit();
        } else {
            // Restore button and show error
            sendButton.disabled = false;
            sendButton.innerHTML = originalButtonContent;
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ __("messages.other_lang.edit_failed") }}',
                confirmButtonColor: '#7c3aed'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Restore button and show error
        sendButton.disabled = false;
        sendButton.innerHTML = originalButtonContent;
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ __("messages.other_lang.edit_failed") }}',
            confirmButtonColor: '#7c3aed'
        });
    });
}


// Delete message with SweetAlert2
function deleteMessage(messageId) {
    Swal.fire({
        title: '{{ __("messages.other_lang.delete_message_confirm") }}',
        text: "{{ __('messages.other_lang.delete_warning') }}",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '{{ __("messages.other_lang.delete") }}',
        cancelButtonText: '{{ __("messages.other_lang.cancel") }}',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: '{{ __("messages.other_lang.deleting") }}',
                text: '{{ __("messages.other_lang.please_wait") }}',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch(`/messages/${messageId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
                    if (messageElement) {
                        // Instead of removing, show "This message was deleted"
                        const messageBubble = messageElement.querySelector('.message-bubble');
                        const messageTextElement = messageElement.querySelector('.message-text');
                        const messageHeader = messageElement.querySelector('.mb-1');
                        const isOwnMessage = data.message.is_deleted_by_sender || (messageElement.querySelector('.justify-end') !== null);
                        
                        // Hide edited indicator if present
                        if (messageHeader) {
                            const editedSpan = messageHeader.querySelector('.italic');
                            if (editedSpan) {
                                editedSpan.style.display = 'none';
                            }
                        }
                        
                        if (messageTextElement) {
                            messageTextElement.innerHTML = '{{ __("messages.other_lang.this_message_was_deleted") }}';
                            messageTextElement.classList.add('italic');
                            if (isOwnMessage) {
                                messageTextElement.classList.add('text-blue-100');
                            } else {
                                messageTextElement.style.color = '#6b7280';
                            }
                        }
                        
                        // Hide edit/delete buttons
                        const actionButtons = messageElement.querySelector('.opacity-0');
                        if (actionButtons) {
                            actionButtons.style.display = 'none';
                        }
                    }
                    
                    Swal.fire({
                        title: '{{ __("messages.other_lang.deleted") }}',
                        text: '{{ __("messages.other_lang.message_deleted") }}',
                        icon: 'success',
                        confirmButtonColor: '#7c3aed',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        // Reset form state after delete to allow new messages
                        const messageInput = document.getElementById('message-input');
                        const sendButton = document.getElementById('send-button');
                        if (messageInput) {
                            messageInput.disabled = false;
                            messageInput.value = '';
                        }
                        if (sendButton) {
                            sendButton.disabled = false;
                            sendButton.style.opacity = '1';
                            sendButton.style.cursor = 'pointer';
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: '{{ __("messages.other_lang.delete_failed") }}',
                        icon: 'error',
                        confirmButtonColor: '#7c3aed'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: '{{ __("messages.other_lang.delete_failed") }}',
                    icon: 'error',
                    confirmButtonColor: '#7c3aed'
                });
            });
        }
    });
}



// Initialize Pusher
let pusher;
let channel;

// Scroll to bottom on page load
document.addEventListener('DOMContentLoaded', function() {
    scrollToBottom();
    
    // Initialize Pusher for real-time messaging
    initializePusher();
    
    // Initialize custom emoji picker for messages page
            const messageInput = document.getElementById('message-input');
    const emojiButtonContainer = document.getElementById('emoji-button-container');
    
    if (messageInput && emojiButtonContainer && typeof initCustomEmojiPicker === 'function') {
        // Create wrapper for emoji picker functionality but keep input outside visually
        const tempWrapper = document.createElement('div');
        tempWrapper.style.position = 'relative';
        tempWrapper.style.display = 'inline-block';
            
        // Don't actually wrap the input, just use it as reference
        const picker = new CustomEmojiPicker(messageInput);
        
        // Create emoji button manually
        const emojiSVG = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17.5,12 C20.5375661,12 23,14.4624339 23,17.5 C23,20.5375661 20.5375661,23 17.5,23 C14.4624339,23 12,20.5375661 12,17.5 C12,14.4624339 14.4624339,12 17.5,12 Z M12.0000002,1.99896738 C17.523704,1.99896738 22.0015507,6.47681407 22.0015507,12.0005179 C22.0015507,12.2637452 21.9913819,12.5245975 21.9714157,12.7827034 C21.5335438,12.3671164 21.0376367,12.012094 20.4972374,11.7307716 C20.3551544,7.16057357 16.6051843,3.49896738 12.0000002,3.49896738 C7.30472352,3.49896738 3.49844971,7.30524119 3.49844971,12.0005179 C3.49844971,16.6060394 7.16059249,20.3562216 11.7317296,20.4979161 C12.0124658,21.0381559 12.3673338,21.5337732 12.7825138,21.9716342 C12.5247521,21.9918733 12.2635668,22.0020684 12.0000002,22.0020684 C6.47629639,22.0020684 1.99844971,17.5242217 1.99844971,12.0005179 C1.99844971,6.47681407 6.47629639,1.99896738 12.0000002,1.99896738 Z M17.5,13.9992349 L17.4101244,14.0072906 C17.2060313,14.0443345 17.0450996,14.2052662 17.0080557,14.4093593 L17,14.4992349 L16.9996498,16.9992349 L14.4976498,17 L14.4077742,17.0080557 C14.2036811,17.0450996 14.0427494,17.2060313 14.0057055,17.4101244 L13.9976498,17.5 L14.0057055,17.5898756 C14.0427494,17.7939687 14.2036811,17.9549004 14.4077742,17.9919443 L14.4976498,18 L17.0006498,17.9992349 L17.0011076,20.5034847 L17.0091633,20.5933603 C17.0462073,20.7974534 17.207139,20.9583851 17.411232,20.995429 L17.5011076,21.0034847 L17.5909833,20.995429 C17.7950763,20.9583851 17.956008,20.7974534 17.993052,20.5933603 L18.0011076,20.5034847 L18.0006498,17.9992349 L20.5045655,18 L20.5944411,17.9919443 C20.7985342,17.9549004 20.9594659,17.7939687 20.9965098,17.5898756 L21.0045655,17.5 L20.9965098,17.4101244 C20.9594659,17.2060313 20.7985342,17.0450996 20.5944411,17.0080557 L20.5045655,17 L17.9996498,16.9992349 L18,14.4992349 L17.9919443,14.4093593 C17.9549004,14.2052662 17.7939687,14.0443345 17.5898756,14.0072906 L17.5,13.9992349 Z M8.46174078,14.7838355 C9.12309331,15.6232213 10.0524954,16.1974014 11.0917655,16.4103066 C11.0312056,16.7638158 11,17.1282637 11,17.5 C11,17.6408778 11.0044818,17.7807089 11.0133105,17.9193584 C9.53812034,17.6766509 8.21128537,16.8896809 7.28351576,15.7121597 C7.02716611,15.3868018 7.08310832,14.9152347 7.40846617,14.6588851 C7.73382403,14.4025354 8.20539113,14.4584777 8.46174078,14.7838355 Z M9.00044779,8.75115873 C9.69041108,8.75115873 10.2497368,9.3104845 10.2497368,10.0004478 C10.2497368,10.6904111 9.69041108,11.2497368 9.00044779,11.2497368 C8.3104845,11.2497368 7.75115873,10.6904111 7.75115873,10.0004478 C7.75115873,9.3104845 8.3104845,8.75115873 9.00044779,8.75115873 Z M15.0004478,8.75115873 C15.6904111,8.75115873 16.2497368,9.3104845 16.2497368,10.0004478 C16.2497368,10.6904111 15.6904111,11.2497368 15.0004478,11.2497368 C14.3104845,11.2497368 13.7511587,10.6904111 13.7511587,10.0004478 C13.7511587,9.3104845 14.3104845,8.75115873 15.0004478,8.75115873 Z" fill="currentColor"/></svg>`;
        
        const closeSVG = `<svg width="20" height="20" viewBox="0 0 455 455" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M227.5,0C101.761,0,0,101.75,0,227.5C0,353.239,101.75,455,227.5,455C353.239,455,455,353.25,455,227.5C455.001,101.761,353.251,0,227.5,0z M227.5,425.001c-108.902,0-197.5-88.599-197.5-197.5S118.599,30,227.5,30S425,118.599,425,227.5S336.402,425.001,227.5,425.001z"/><path d="M321.366,133.635c-17.587-17.588-46.051-17.589-63.64,0L227.5,163.86l-30.226-30.225c-17.588-17.588-46.051-17.589-63.64,0c-17.544,17.545-17.544,46.094,0,63.64L163.86,227.5l-30.226,30.226c-17.544,17.545-17.544,46.094,0,63.64c17.585,17.586,46.052,17.589,63.64,0l30.226-30.225l30.226,30.225c17.585,17.586,46.052,17.589,63.64,0c17.544-17.545,17.544-46.094,0-63.64L291.141,227.5l30.226-30.226C338.911,179.729,338.911,151.181,321.366,133.635z M300.153,176.062l-40.832,40.832c-2.813,2.813-4.394,6.628-4.394,10.606c0,3.979,1.581,7.793,4.394,10.606l40.832,40.832c5.849,5.849,5.849,15.365,0,21.214c-5.862,5.862-15.351,5.863-21.214,0l-40.832-40.832c-2.929-2.929-6.768-4.394-10.606-4.394s-7.678,1.464-10.606,4.394l-40.832,40.832c-5.861,5.861-15.351,5.863-21.213,0c-5.849-5.849-5.849-15.365,0-21.214l40.832-40.832c2.813-2.813,4.394-6.628,4.394-10.606c0-3.978-1.581-7.793-4.394-10.606l-40.832-40.832c-5.849-5.849-5.849-15.365,0-21.214c5.864-5.863,15.35-5.863,21.214,0l40.832,40.832c5.857,5.858,15.355,5.858,21.213,0l40.832-40.832c5.863-5.862,15.35-5.863,21.213,0C306.001,160.697,306.001,170.213,300.153,176.062z"/></svg>`;
        
        const emojiButton = document.createElement('button');
        emojiButton.type = 'button';
        emojiButton.className = 'emoji-picker-button p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg transition-all';
        emojiButton.innerHTML = emojiSVG;
        emojiButton.style.cssText = `
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.3s ease;
        `;
        
        // Add button to container (left side of input)
        emojiButtonContainer.appendChild(emojiButton);
        
        // Function to toggle icon with smooth animation
        function toggleIcon(isOpen) {
            emojiButton.style.opacity = '0';
            setTimeout(() => {
                emojiButton.innerHTML = isOpen ? closeSVG : emojiSVG;
                emojiButton.style.opacity = '1';
            }, 150);
        }
        
        // Set the toggle callback for the picker
        picker.onToggleCallback = toggleIcon;
        
        // Button click handler
        emojiButton.onclick = (e) => {
            e.preventDefault();
            e.stopPropagation();
            picker.toggle();
        };
        
        // Store picker reference on input
        messageInput.customEmojiPicker = picker;
            }
    
    // Infinite Scroll for Loading Older Messages
    initializeInfiniteScroll();
});

// Initialize Pusher connection
function initializePusher() {
    // You'll need to set these values in your .env file
    const pusherKey = '{{ env("PUSHER_APP_KEY", "your-pusher-key") }}';
    const pusherCluster = '{{ env("PUSHER_APP_CLUSTER", "mt1") }}';
    
    console.log('Initializing Pusher with key:', pusherKey);
    console.log('Pusher cluster:', pusherCluster);
    
    if (pusherKey === 'your-pusher-key') {
        console.warn('Pusher not configured. Using polling fallback.');
        // Fallback to polling if Pusher is not configured
        setInterval(checkForNewMessages, 5000);
        return;
    }
    
    pusher = new Pusher(pusherKey, {
        cluster: pusherCluster,
        encrypted: true
    });
    
    // Add connection event listeners for debugging
    pusher.connection.bind('connected', function() {
        console.log('Pusher connected successfully!');
    });
    
    pusher.connection.bind('disconnected', function() {
        console.log('Pusher disconnected');
    });
    
    pusher.connection.bind('error', function(error) {
        console.error('Pusher connection error:', error);
    });
    
    // Subscribe to the public conversation channel (temporarily for testing)
    channel = pusher.subscribe('conversation.{{ $conversation->id }}');
    console.log('Subscribing to conversation.{{ $conversation->id }}');
    // Add subscription event listeners
    channel.bind('pusher:subscription_succeeded', function() {
        console.log('Successfully subscribed to conversation.{{ $conversation->id }}');
    });
    
    channel.bind('pusher:subscription_error', function(error) {
        console.error('Subscription error:', error);
        console.error('Error details:', error.data);
    });
    
    // Listen for new messages
    channel.bind('MessageSent', function(data) {
        console.log('New message received via Pusher:', data);
        console.log('Message data:', data.message);
        console.log('Current user ID:', {{ auth()->id() }});
        console.log('Message sender ID:', data.message.sender_id);
        
        // Only add message if it's not from the current user
        if (data.message.sender_id != {{ auth()->id() }}) {
            console.log('Adding message from other user');
            addMessageToConversation(data.message);
            scrollToBottom();
        } else {
            console.log('Ignoring own message');
        }
    });
    
    // Listen for message updates
    channel.bind('MessageUpdated', function(data) {
        console.log('Message updated via Pusher:', data);
        console.log('Updated message data:', data.message);
        
        // Update the message in the DOM for all users
        const messageElement = document.querySelector(`[data-message-id="${data.message.id}"]`);
        if (messageElement) {
            const messageTextElement = messageElement.querySelector('.message-text');
            const messageHeader = messageElement.querySelector('.mb-1');
            
            // Update message text
            if (messageTextElement) {
                messageTextElement.innerHTML = data.message.message.replace(/\n/g, '<br>');
            }
            
            // Add or update edited indicator with pen icon
            if (messageHeader && data.message.is_edited) {
                // Check if edited span already exists
                let editedSpan = messageHeader.querySelector('.italic');
                const isOwnMessage = data.message.sender_id == {{ auth()->id() }};
                
                if (!editedSpan) {
                    // Create new edited span with pen icon
                    editedSpan = document.createElement('span');
                    editedSpan.className = `text-xs ${isOwnMessage ? 'text-yellow-200' : 'text-purple-500'} font-semibold italic flex items-center gap-1`;
                    editedSpan.innerHTML = '<span>✏️</span><span>{{ __("messages.other_lang.edited_label") }}</span>';
                    
                    // If header doesn't have flex, add it
                    if (!messageHeader.classList.contains('flex')) {
                        messageHeader.classList.add('flex', 'justify-between', 'items-center');
                    }
                    
                    messageHeader.appendChild(editedSpan);
                } else {
                    // Update existing span to include pen icon if not already present
                    if (!editedSpan.innerHTML.includes('✏️')) {
                        editedSpan.innerHTML = '<span>✏️</span><span>{{ __("messages.other_lang.edited_label") }}</span>';
                        editedSpan.classList.add('flex', 'items-center', 'gap-1');
                    }
                }
            }
            
            console.log('Message updated in DOM');
        } else {
            console.log('Message element not found for update');
        }
    });
    
    // Listen for message deletions
    channel.bind('MessageDeleted', function(data) {
        console.log('Message deleted via Pusher:', data);
        console.log('Deleted message ID:', data.message_id);
        
        // Instead of removing, show "This message was deleted"
        const messageElement = document.querySelector(`[data-message-id="${data.message_id}"]`);
        if (messageElement) {
            const messageTextElement = messageElement.querySelector('.message-text');
            const messageHeader = messageElement.querySelector('.mb-1');
            const isOwnMessage = messageElement.classList.contains('justify-end') || messageElement.querySelector('.justify-end') !== null;
            
            // Hide edited indicator if present
            if (messageHeader) {
                const editedSpan = messageHeader.querySelector('.italic');
                if (editedSpan) {
                    editedSpan.style.display = 'none';
                }
            }
            
            if (messageTextElement) {
                messageTextElement.innerHTML = '{{ __("messages.other_lang.this_message_was_deleted") }}';
                messageTextElement.classList.add('italic');
                if (isOwnMessage) {
                    messageTextElement.classList.add('text-blue-100');
                } else {
                    messageTextElement.style.color = '#6b7280';
                }
            }
            
            // Hide edit/delete buttons
            const actionButtons = messageElement.querySelector('.opacity-0');
            if (actionButtons) {
                actionButtons.style.display = 'none';
            }
            
            console.log('Message marked as deleted in DOM');
        } else {
            console.log('Message element not found for deletion');
        }
    });
    
    // Listen for any event on this channel for debugging
    // channel.bind_all(function(eventName, data) {
    //     console.log('Received event:', eventName, data);
    // });
}

// Fallback polling function (only used if Pusher is not configured)
function checkForNewMessages() {
    const lastMessage = document.querySelector('[data-message-id]');
    const lastMessageId = lastMessage ? lastMessage.getAttribute('data-message-id') : 0;
    
    fetch(`/messages/{{ $conversation->id }}/check-new?last_message_id=${lastMessageId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.messages.length > 0) {
                data.messages.forEach(message => {
                    addMessageToConversation(message);
                });
                scrollToBottom();
            }
        })
        .catch(error => {
            console.error('Error checking for new messages:', error);
        });
}

// Infinite Scroll Implementation
let isLoadingOlderMessages = false;
let hasMoreMessages = true;

function initializeInfiniteScroll() {
    const messagesContainer = document.getElementById('messages-container');
    
    messagesContainer.addEventListener('scroll', function() {
        // Check if user scrolled to top (within 100px from top)
        if (messagesContainer.scrollTop < 100 && !isLoadingOlderMessages && hasMoreMessages) {
            loadOlderMessages();
        }
    });
}

function loadOlderMessages() {
    const messagesContainer = document.getElementById('messages-container');
    const loadingIndicator = document.getElementById('loading-older-messages');
    const firstMessage = document.querySelector('[data-message-id]');
    
    if (!firstMessage) {
        return;
    }
    
    const oldestMessageId = firstMessage.getAttribute('data-message-id');
    const previousScrollHeight = messagesContainer.scrollHeight;
    
    isLoadingOlderMessages = true;
    loadingIndicator.classList.remove('hidden');
    
    fetch(`/messages/{{ $conversation->id }}/load-older?oldest_message_id=${oldestMessageId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.messages.length > 0) {
                // Add older messages at the top
                data.messages.forEach(message => {
                    prependMessageToConversation(message);
                });
                
                // Maintain scroll position
                const newScrollHeight = messagesContainer.scrollHeight;
                messagesContainer.scrollTop = newScrollHeight - previousScrollHeight;
                
                hasMoreMessages = data.has_more;
            } else {
                hasMoreMessages = false;
            }
        })
        .catch(error => {
            console.error('Error loading older messages:', error);
        })
        .finally(() => {
            isLoadingOlderMessages = false;
            loadingIndicator.classList.add('hidden');
        });
}

function prependMessageToConversation(messageData) {
    const messagesContainer = document.getElementById('messages-container');
    const loadingIndicator = document.getElementById('loading-older-messages');
    const currentUserId = {{ auth()->id() }};
    const isOwnMessage = messageData.sender_id == currentUserId;
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `flex ${isOwnMessage ? 'justify-end' : 'justify-start'} mb-2 group`;
    messageDiv.setAttribute('data-message-id', messageData.id);
    
    const messageContainer = document.createElement('div');
    messageContainer.className = 'flex items-end space-x-2 max-w-xs lg:max-w-md';
    
    // Avatar for other user (left side)
    let avatar = null;
    if (!isOwnMessage) {
        avatar = document.createElement('div');
        avatar.className = 'flex-shrink-0 w-8 h-8 rounded-full overflow-hidden';
        
        const avatarImg = document.createElement('img');
        avatarImg.src = isConversationBlocked ? defaultAvatarUrl : (messageData.sender.profile_image || defaultAvatarUrl);
        avatarImg.alt = messageData.sender.username;
        avatarImg.className = 'w-full h-full object-cover';
        avatarImg.onerror = function() {
            this.src = '{{ asset('assets/image/avatar.png') }}';
        };
        
        avatar.appendChild(avatarImg);
    }
    
    // Message Bubble Container
    const bubbleContainer = document.createElement('div');
    bubbleContainer.className = 'relative';
    
    const messageBubble = document.createElement('div');
    messageBubble.className = `px-3 py-2 rounded-2xl ${isOwnMessage ? 'bg-blue-600 text-white rounded-br-md' : 'bg-white text-blue-600 rounded-bl-md'} shadow-sm message-bubble max-w-xs lg:max-w-md`;
    
    // Force colors for messages
    if (isOwnMessage) {
        messageBubble.style.backgroundColor = '#2563eb';
        messageBubble.style.color = 'white';
    } else {
        messageBubble.style.backgroundColor = 'white';
        messageBubble.style.color = '#2563eb';
    }
    
    messageBubble.innerHTML = `
        <!-- Username and Edited indicator at top of message -->
        <div class="mb-1 flex justify-between items-center">
            <span class="text-xs font-bold ${isOwnMessage ? 'text-yellow-300' : 'text-purple-600'}">
                ${isOwnMessage ? '{{ __('messages.other_lang.me') }}' : messageData.sender.username}
            </span>
            ${messageData.is_edited ? `
                <span class="text-xs ${isOwnMessage ? 'text-yellow-200' : 'text-purple-500'} font-semibold italic">{{ __("messages.other_lang.edited_label") }}</span>
            ` : ''}
        </div>
        
        <!-- Message Content -->
        <div class="message-text text-base leading-relaxed break-words" ${!isOwnMessage ? 'style="color: #2563eb !important;"' : ''}>
            ${messageData.message.replace(/\n/g, '<br>')}
        </div>
        
        <!-- Message Footer with Date and Time -->
        <div class="flex items-center justify-between mt-2">
            <span class="text-xs ${isOwnMessage ? 'text-blue-100' : 'text-blue-500'}">
                ${messageData.created_at}
            </span>
            ${isOwnMessage ? `
                <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity ml-2">
                    <button onclick="editMessage(${messageData.id})" class="p-1 rounded-full hover:bg-white hover:bg-opacity-20 transition-all duration-200" title="{{ __('messages.other_lang.edit') }}">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>
                    <button onclick="deleteMessage(${messageData.id})" class="p-1 rounded-full hover:bg-white hover:bg-opacity-20 transition-all duration-200" title="{{ __('messages.other_lang.delete') }}">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            ` : ''}
        </div>
    `;
    
    bubbleContainer.appendChild(messageBubble);
    
    // Message Tail
    const messageTail = document.createElement('div');
    messageTail.className = `absolute ${isOwnMessage ? 'right-0 bottom-0' : 'left-0 bottom-0'} w-0 h-0 ${isOwnMessage ? 'border-l-8 border-l-blue-600 border-b-8 border-b-transparent' : 'border-r-8 border-r-white border-b-8 border-b-transparent'}`;
    
    bubbleContainer.appendChild(messageTail);
    
    // Add avatar before bubble for other user, after bubble for own messages
    if (!isOwnMessage && avatar) {
        messageContainer.appendChild(avatar);
    }
    messageContainer.appendChild(bubbleContainer);
    
    // Add avatar after bubble for own messages
    if (isOwnMessage) {
        const ownAvatar = document.createElement('div');
        ownAvatar.className = 'flex-shrink-0 w-8 h-8 rounded-full overflow-hidden';
        
        const ownAvatarImg = document.createElement('img');
        ownAvatarImg.src = messageData.sender.profile_image || '{{ asset('assets/image/avatar.png') }}';
        ownAvatarImg.alt = messageData.sender.username;
        ownAvatarImg.className = 'w-full h-full object-cover';
        ownAvatarImg.onerror = function() {
            this.src = '{{ asset('assets/image/avatar.png') }}';
        };
        
        ownAvatar.appendChild(ownAvatarImg);
        messageContainer.appendChild(ownAvatar);
    }
    
    messageDiv.appendChild(messageContainer);
    
    // Insert after loading indicator
    messagesContainer.insertBefore(messageDiv, loadingIndicator.nextSibling);
}
</script>

@endsection
