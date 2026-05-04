@extends('customer-panel.layout.main')
@section('title', __('messages.other_lang.my_messages'))
@section('content')

<div class="bg-[#F5F5F5] dark:bg-[#18171C] mb-4 p-3 text-end rounded-md">
    <h3 class="text-xl opacity-60 tracking-wider">{{ __('messages.other_lang.my_messages')}}</h3>
</div>

<div class="w-full bg-[#F5F5F5] dark:bg-[#18171C] p-6 rounded-md shadow-lg">
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 rounded-md">{{ session('error') }}</div>
    @endif
    @if($conversations->count() > 0)
        <!-- Scrollable Container with Dynamic Height -->
        <div id="conversationsContainer" class="space-y-4 overflow-y-auto pr-2" style="max-height: calc(100vh - 250px);">
            @include('customer-panel.partials.conversation-items', ['conversations' => $conversations])
            
            <!-- Loading Indicator -->
            <div id="loadingIndicator" class="hidden text-center py-4">
                <div class="inline-block">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                </div>
                <p class="text-gray-400 text-sm mt-2">{{ __('messages.other_lang.loading_conversations') }}...</p>
            </div>
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('messages.other_lang.no_messages')}}</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('messages.other_lang.no_messages_description')}}</p>
        </div>
    @endif
</div>

<script>
// Infinite Scroll for Conversations
let isLoadingConversations = false;
let hasMoreConversations = true;

document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('conversationsContainer');
    const loadingIndicator = document.getElementById('loadingIndicator');
    
    if (container) {
        container.addEventListener('scroll', function() {
            // Check if scrolled near bottom (within 100px)
            const scrollPosition = container.scrollTop + container.clientHeight;
            const scrollHeight = container.scrollHeight;
            
            if (scrollPosition >= scrollHeight - 100 && !isLoadingConversations && hasMoreConversations) {
                loadOlderConversations();
            }
        });
    }
});

function loadOlderConversations() {
    if (isLoadingConversations || !hasMoreConversations) return;
    
    isLoadingConversations = true;
    const loadingIndicator = document.getElementById('loadingIndicator');
    const container = document.getElementById('conversationsContainer');
    
    // Get the oldest conversation date
    const conversationItems = document.querySelectorAll('.conversation-item');
    if (conversationItems.length === 0) {
        hasMoreConversations = false;
        return;
    }
    
    const oldestConversation = conversationItems[conversationItems.length - 1];
    const oldestDate = oldestConversation.getAttribute('data-conversation-date');
    
    // Show loading indicator
    if (loadingIndicator) {
        loadingIndicator.classList.remove('hidden');
    }
    
    // Fetch older conversations
    fetch(`{{ route('messages.conversations.load-older') }}?oldest_conversation_date=${encodeURIComponent(oldestDate)}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        // Hide loading indicator
        if (loadingIndicator) {
            loadingIndicator.classList.add('hidden');
        }
        
        if (data.html && data.html.trim() !== '') {
            // Create temporary div to parse HTML
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = data.html;
            
            // Insert new conversations before loading indicator
            const newConversations = tempDiv.querySelectorAll('.conversation-item');
            newConversations.forEach((conversation, index) => {
                // Add fade-in animation
                conversation.style.opacity = '0';
                conversation.style.transform = 'translateY(20px)';
                
                // Insert before loading indicator
                if (loadingIndicator) {
                    container.insertBefore(conversation, loadingIndicator);
                } else {
                    container.appendChild(conversation);
                }
                
                // Trigger animation
                setTimeout(() => {
                    conversation.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    conversation.style.opacity = '1';
                    conversation.style.transform = 'translateY(0)';
                }, index * 50);
            });
            
            // Update hasMore flag
            hasMoreConversations = data.hasMore;
        } else {
            hasMoreConversations = false;
        }
        
        isLoadingConversations = false;
    })
    .catch(error => {
        console.error('Error loading older conversations:', error);
        if (loadingIndicator) {
            loadingIndicator.classList.add('hidden');
        }
        isLoadingConversations = false;
    });
}

function deleteConversation(conversationId) {
    Swal.fire({
        title: '{{ __("messages.other_lang.delete_conversation") }}',
        text: '{{ __("messages.other_lang.delete_conversation_confirm") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '{{ __("messages.other_lang.delete") }}',
        cancelButtonText: '{{ __("messages.other_lang.cancel") }}',
        reverseButtons: true,
        customClass: {
            popup: 'swal2-popup-custom',
            title: 'swal2-title-custom',
            content: 'swal2-content-custom',
            confirmButton: 'swal2-confirm-custom',
            cancelButton: 'swal2-cancel-custom'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/messages/conversation/${conversationId}/delete`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the conversation from the DOM
                    const conversationElement = document.querySelector(`[data-conversation-id="${conversationId}"]`);
                    if (conversationElement) {
                        conversationElement.remove();
                    }
                    
                    // Show success message
                    Swal.fire({
                        title: '{{ __("messages.other_lang.deleted") }}',
                        text: '{{ __("messages.other_lang.conversation_deleted_successfully") }}',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'swal2-popup-custom',
                            title: 'swal2-title-custom',
                            content: 'swal2-content-custom'
                        }
                    });
                } else {
                    Swal.fire({
                        title: '{{ __("messages.other_lang.error") }}',
                        text: '{{ __("messages.other_lang.delete_failed") }}',
                        icon: 'error',
                        confirmButtonColor: '#dc2626',
                        customClass: {
                            popup: 'swal2-popup-custom',
                            title: 'swal2-title-custom',
                            content: 'swal2-content-custom',
                            confirmButton: 'swal2-confirm-custom'
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: '{{ __("messages.other_lang.error") }}',
                    text: '{{ __("messages.other_lang.delete_failed") }}',
                    icon: 'error',
                    confirmButtonColor: '#dc2626',
                    customClass: {
                        popup: 'swal2-popup-custom',
                        title: 'swal2-title-custom',
                        content: 'swal2-content-custom',
                        confirmButton: 'swal2-confirm-custom'
                    }
                });
            });
        }
    });
}
</script>

<style>
/* Custom Scrollbar Styling */
#conversationsContainer {
    scrollbar-width: thin;
    scrollbar-color: #7c3aed #e5e7eb;
}

#conversationsContainer::-webkit-scrollbar {
    width: 8px;
}

#conversationsContainer::-webkit-scrollbar-track {
    background: #e5e7eb;
    border-radius: 10px;
}

#conversationsContainer::-webkit-scrollbar-thumb {
    background: #7c3aed;
    border-radius: 10px;
    transition: background 0.3s ease;
}

#conversationsContainer::-webkit-scrollbar-thumb:hover {
    background: #6d28d9;
}

/* Dark mode scrollbar */
@media (prefers-color-scheme: dark) {
    #conversationsContainer {
        scrollbar-color: #7c3aed #374151;
    }
    
    #conversationsContainer::-webkit-scrollbar-track {
        background: #374151;
    }
}

/* SweetAlert Styling */
.swal2-popup-custom {
    border-radius: 12px !important;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
}

.swal2-title-custom {
    color: #1f2937 !important;
    font-weight: 600 !important;
}

.swal2-content-custom {
    color: #6b7280 !important;
}

.swal2-confirm-custom {
    background-color: #ef4444 !important;
    border: none !important;
    border-radius: 8px !important;
    font-weight: 500 !important;
    padding: 8px 16px !important;
}

.swal2-cancel-custom {
    background-color: #6b7280 !important;
    border: none !important;
    border-radius: 8px !important;
    font-weight: 500 !important;
    padding: 8px 16px !important;
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .swal2-popup-custom {
        background-color: #1f2937 !important;
        color: #f9fafb !important;
    }
    
    .swal2-title-custom {
        color: #f9fafb !important;
    }
    
    .swal2-content-custom {
        color: #d1d5db !important;
    }
}
</style>

@endsection
