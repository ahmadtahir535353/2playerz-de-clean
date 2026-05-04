@extends('customer-panel.layout.main')
@section('title', __('messages.customer_profile.notifications'))  {{-- "Notifications" --}}
@section('content')

<div class="bg-[#F5F5F5] dark:bg-[#18171C] mb-4 p-3 text-end rounded-md">
    <h3 class="text-xl opacity-60 tracking-wider">{{ __('messages.customer_profile.notifications')}}</h3>
</div>

<div class="w-full bg-[#F5F5F5] dark:bg-[#18171C] p-6 rounded-md shadow-lg">
    <h2 class="text-center text-lg font-semibold mb-4">
        {{ __('messages.customer_profile.notification_text')}}
    </h2>

    <!-- Top Bar with Delete All Button and Filter Dropdown -->
    <div class="mb-4 flex justify-between items-center">
        <!-- Delete All Button (Left Side) -->
        <button type="button" 
                id="delete-all-notifications-btn"
                class="flex items-center gap-2 px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-md transition-all"
                onclick="deleteAllNotifications()"
                title="{{ __('messages.customer_profile.delete_all_notifications_title') }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
            </svg>
            <span>{{ __('messages.customer_profile.delete_all') }}</span>
        </button>
        
        <!-- Filter Dropdown (Right Side) -->
        <div class="relative">
            <select id="notificationFilter" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-[#252525] text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-[#B051B0] focus:border-transparent cursor-pointer" style="min-width: 200px;">
                <option value="all" {{ (isset($filter) && $filter == 'all') ? 'selected' : '' }}>Alle Benachrichtigungen</option>
                <option value="comments" {{ (isset($filter) && $filter == 'comments') ? 'selected' : '' }}>Kommentare</option>
                <option value="likes" {{ (isset($filter) && $filter == 'likes') ? 'selected' : '' }}>Likes</option>
                <option value="following" {{ (isset($filter) && $filter == 'following') ? 'selected' : '' }}>Aktionen von Mitgliedern, denen ich folge</option>
            </select>
        </div>
    </div>

    <ul class="space-y-4 max-h-[70vh] overflow-y-auto" id="notifications-list">
        @forelse($notifications as $notification)
            @php
                $data = json_decode($notification->data, true);
                $commentId = $data['comment_id'] ?? null;
                $postId = $data['post_id'] ?? null;
                $conversationId = $data['conversation_id'] ?? null;
                $message = $data['message'] ?? 'You have a notification';
                $post = \App\Models\Post::find($postId);
                $slug = $post?->slug ?? '#';
                
                // Determine icon and link based on notification type
                if ($conversationId) {
                    // Private message notification
                    $icon = '💬';
                    $link = route('messages.show', $conversationId);
                } elseif (str_contains(strtolower($message), 'gefällt')) {
                    // Like notification
                    $icon = '👍';
                    $link = route('notifications.read', $notification->id);
                } elseif (str_contains(strtolower($message), 'replied') || str_contains(strtolower($message), 'antwortet')) {
                    // Reply notification
                    $icon = '💬';
                    $link = route('notifications.read', $notification->id);
                } elseif (str_contains(strtolower($message), 'folgt') || str_contains(strtolower($message), 'follow')) {
                    // Follow notification
                    $icon = '👥';
                    $fromUsername = $data['sender_username'] ?? $data['from_username'] ?? $notification->from_user_id ?? $data['from_user_id'] ?? null;
                    $link = $fromUsername ? route('user.public.profile', $fromUsername) : route('members.following');
                } elseif (str_contains(strtolower($message), 'kommentar') || str_contains(strtolower($message), 'comment')) {
                    // Comment notification
                    $icon = '💬';
                    $link = route('notifications.read', $notification->id);
                } else {
                    // Default
                    $icon = '🔔';
                    $link = route('notifications.read', $notification->id);
                }
            @endphp
            <li class="flex items-start gap-4 bg-white dark:bg-black p-3 rounded-md cursor-pointer dark:hover:bg-[#252525] hover:bg-[#e7e7e7] transition-all {{ is_null($notification->read_at) ? 'bg-light' : '' }}" data-notification-id="{{ $notification->id }}">
                <div class="text-xl flex-shrink-0">{{ $icon }}</div>
                <div class="flex-1 text-sm min-w-0">
                    <a href="{{ $link }}" target="_blank"
                       class="text-dark dark:text-white">
                        {{ $message }}
                    </a>
                    <span class="text-gray-400 text-xs">
                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                    </span>
                </div>
                <!-- Delete icon -->
                <button type="button" 
                        class="delete-notification-btn bg-transparent border-0 cursor-pointer p-2 flex-shrink-0" 
                        onclick="deleteNotification('{{ $notification->id }}', this)"
                        title="Delete notification"
                        style="cursor: pointer; transition: all 0.2s;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        <line x1="10" y1="11" x2="10" y2="17"></line>
                        <line x1="14" y1="11" x2="14" y2="17"></line>
                    </svg>
                </button>
            </li>
        @empty
            <li class="flex items-start gap-4 bg-white dark:bg-black p-3 rounded-md">
                <div class="flex-1 text-sm text-muted">{{ __('messages.customer_profile.no_notifications')}}</div>
            </li>
        @endforelse
    </ul>

    <div class="text-center border-t mt-5" id="load-more-container">
        @if($notifications->hasMorePages())
            <a href="{{ $notifications->nextPageUrl() }}"
               class="w-4/5 mx-auto bg-black hover:bg-[#313131] dark:hover:bg-[#dadada] dark:bg-white transition-all mt-4 font-medium px-6 py-2 rounded-md text-white dark:text-black load-more">
                {{ __('messages.customer_profile.load_more')}}
            </a>
        @endif
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://momentjs.com/downloads/moment.min.js"></script>
<script>
// Translation variables - Global scope
const loadMoreText = '{{ __("messages.customer_profile.load_more") }}';
const loadingText = '{{ __("messages.customer_profile.loading") }}';
const deleteAllTitle = '{{ __("messages.customer_profile.delete_all_notifications_title") }}';
const deleteAllMessage = '{{ __("messages.customer_profile.delete_all_notifications_message") }}';
const cancelText = '{{ __("messages.customer_profile.cancel") }}';
const deleteAllText = '{{ __("messages.customer_profile.delete_all") }}';

$(document).ready(function() {
    let currentFilter = '{{ isset($filter) ? $filter : "all" }}';
    
    // Handle filter change
    $('#notificationFilter').on('change', function() {
        const filter = $(this).val();
        currentFilter = filter;
        
        // Reload page with filter parameter
        const url = new URL(window.location.href);
        url.searchParams.set('filter', filter);
        window.location.href = url.toString();
    });
    
    $(document).on('click', '.load-more', function(e) {
        e.preventDefault();
        let url = $(this).attr('href');
        // Add filter parameter to load more URL
        if (currentFilter && currentFilter !== 'all') {
            url += (url.includes('?') ? '&' : '?') + 'filter=' + currentFilter;
        }
        let container = $('#load-more-container');
        let list = $('#notifications-list');
        let button = $(this);

        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json',
            beforeSend: function() {
                button.text(loadingText);
                container.addClass('loading');
            },

            success: function(response) {
                if (response.data && response.data.length > 0) {
                    // Append new notifications
                    $.each(response.data, function(index, notification) {
                        let data = JSON.parse(notification.data);
                        let commentId = data.comment_id ?? null;
                        let postId = data.post_id ?? null;
                        let conversationId = data.conversation_id ?? null;
                        let message = data.message ?? '{{ __("messages.customer_profile.notify")}}';
                        
                        // Determine icon and link based on notification type
                        let icon, link;
                        if (conversationId) {
                            // Private message notification
                            icon = '💬';
                            link = `/messages/${conversationId}`;
                        } else if (message.toLowerCase().includes('gefällt')) {
                            // Like notification
                            icon = '👍';
                            link = `/notifications/${notification.id}/read`;
                        } else if (message.toLowerCase().includes('replied') || message.toLowerCase().includes('antwortet')) {
                            // Reply notification
                            icon = '💬';
                            link = `/notifications/${notification.id}/read`;
                        } else if (message.toLowerCase().includes('folgt') || message.toLowerCase().includes('follow')) {
                            // Follow notification
                            icon = '👥';
                            let fromUsername = data.sender_username || data.from_username || notification.from_user_id || data.from_user_id;
                            link = fromUsername ? `/user/${fromUsername}/profile` : `/customers/members-following`;
                        } else if (message.toLowerCase().includes('kommentar') || message.toLowerCase().includes('comment')) {
                            // Comment notification
                            icon = '💬';
                            link = `/notifications/${notification.id}/read`;
                        } else {
                            // Default
                            icon = '🔔';
                            link = `/notifications/${notification.id}/read`;
                        }
                        
                        let timeAgo = moment(notification.created_at).fromNow();
                        let isUnread = notification.read_at === null ? 'bg-light' : '';

                        let html = `
                            <li class="flex items-start gap-4 bg-white dark:bg-black p-3 rounded-md cursor-pointer dark:hover:bg-[#252525] hover:bg-[#e7e7e7] transition-all ${isUnread}" data-notification-id="${notification.id}">
                                <div class="text-xl flex-shrink-0">${icon}</div>
                                <div class="flex-1 text-sm min-w-0">
                                    <a href="${link}" target="_blank" class="text-dark dark:text-white">
                                        ${message}
                                    </a>
                                    <span class="text-gray-400 text-xs">${timeAgo}</span>
                                </div>
                                <button type="button" 
                                        class="delete-notification-btn bg-transparent border-0 cursor-pointer p-2 flex-shrink-0" 
                                        onclick="deleteNotification('${notification.id}', this)"
                                        title="Delete notification"
                                        style="cursor: pointer; transition: all 0.2s;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                    </svg>
                                </button>
                            </li>
                        `;
                        list.append(html);
                    });

                    if (response.next_page_url) {
                        $('.load-more').attr('href', response.next_page_url);
                    } else {
                        container.html('<p class="text-muted">{{ __("messages.customer_profile.no_more")}}</p>');
                    }
                } else {
                    container.html('<p class="text-muted">{{ __("messages.customer_profile.no_more")}}</p>');
                }
            },
            error: function() {
                container.html('<p class="text-danger">Error loading notifications</p>');
            },
            complete: function() {
                setTimeout(function() {
                    button.text(loadMoreText);
                    container.removeClass('loading');
                }, 2000);
            }
        });
    });
});

// Optional: Include Moment.js for timeAgo formatting if not already included
// <script src="https://momentjs.com/downloads/moment.min.js">

// Function to show custom popup
function showPopup(message, type = 'confirm', onConfirm = null) {
    // Create overlay
    const overlay = document.createElement('div');
    overlay.style.cssText = 'position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center; animation: fadeIn 0.2s;';
    
    // Create popup
    const popup = document.createElement('div');
    popup.style.cssText = 'background: white; border-radius: 12px; padding: 24px; max-width: 400px; width: 90%; box-shadow: 0 4px 20px rgba(0,0,0,0.3); animation: slideIn 0.3s; position: relative;';
    
    // Dark mode support
    const isDark = document.documentElement.classList.contains('dark') || document.body.classList.contains('dark');
    if (isDark) {
        popup.style.background = '#1f1f1f';
        popup.style.color = '#ffffff';
    }
    
    if (type === 'confirm') {
        popup.innerHTML = `
            <div style="text-align: center;">
                <div style="margin-bottom: 16px;">
                    <svg style="width: 64px; height: 64px; margin: 0 auto; color: #dc3545;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <h3 style="font-size: 20px; font-weight: bold; margin-bottom: 8px; color: ${isDark ? '#fff' : '#333'};">Benachrichtigung löschen?</h3>
                <p style="color: ${isDark ? '#ccc' : '#666'}; margin-bottom: 24px;">Diese Aktion kann nicht rückgängig gemacht werden.</p>
                <div style="display: flex; gap: 12px; justify-content: center;">
                    <button id="cancelBtn" style="padding: 10px 24px; border-radius: 8px; border: 1px solid #ddd; background: ${isDark ? '#333' : '#f5f5f5'}; color: ${isDark ? '#fff' : '#333'}; cursor: pointer; font-weight: 500; transition: all 0.2s;">
                        Abbrechen
                    </button>
                    <button id="confirmBtn" style="padding: 10px 24px; border-radius: 8px; border: none; background: #dc3545; color: white; cursor: pointer; font-weight: 500; transition: all 0.2s;">
                        Löschen
                    </button>
                </div>
            </div>
        `;
    } else if (type === 'success') {
        popup.innerHTML = `
            <div style="text-align: center;">
                <div style="margin-bottom: 16px;">
                    <svg style="width: 64px; height: 64px; margin: 0 auto; color: #28a745;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 style="font-size: 20px; font-weight: bold; margin-bottom: 8px; color: ${isDark ? '#fff' : '#333'};">Erfolgreich!</h3>
                <p style="color: ${isDark ? '#ccc' : '#666'}; margin-bottom: 24px;">${message}</p>
                <button id="okBtn" style="padding: 10px 32px; border-radius: 8px; border: none; background: #28a745; color: white; cursor: pointer; font-weight: 500; transition: all 0.2s;">
                    OK
                </button>
            </div>
        `;
    } else if (type === 'error') {
        popup.innerHTML = `
            <div style="text-align: center;">
                <div style="margin-bottom: 16px;">
                    <svg style="width: 64px; height: 64px; margin: 0 auto; color: #dc3545;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 style="font-size: 20px; font-weight: bold; margin-bottom: 8px; color: ${isDark ? '#fff' : '#333'};">Fehler!</h3>
                <p style="color: ${isDark ? '#ccc' : '#666'}; margin-bottom: 24px;">${message}</p>
                <button id="okBtn" style="padding: 10px 32px; border-radius: 8px; border: none; background: #dc3545; color: white; cursor: pointer; font-weight: 500; transition: all 0.2s;">
                    OK
                </button>
            </div>
        `;
    }
    
    overlay.appendChild(popup);
    document.body.appendChild(overlay);
    
    // Add animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideIn { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        #confirmBtn:hover, #okBtn:hover { transform: scale(1.05); }
        #cancelBtn:hover { background: ${isDark ? '#444' : '#e5e5e5'}; }
    `;
    document.head.appendChild(style);
    
    // Handle button clicks
    const confirmBtn = popup.querySelector('#confirmBtn');
    const cancelBtn = popup.querySelector('#cancelBtn');
    const okBtn = popup.querySelector('#okBtn');
    
    const closePopup = () => {
        overlay.style.animation = 'fadeIn 0.2s reverse';
        setTimeout(() => {
            document.body.removeChild(overlay);
            document.head.removeChild(style);
        }, 200);
    };
    
    if (confirmBtn) {
        confirmBtn.addEventListener('click', () => {
            closePopup();
            if (onConfirm) onConfirm();
        });
    }
    
    if (cancelBtn) {
        cancelBtn.addEventListener('click', closePopup);
    }
    
    if (okBtn) {
        okBtn.addEventListener('click', closePopup);
    }
    
    // Close on overlay click
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) closePopup();
    });
}

// Function to delete notification
function deleteNotification(notificationId, buttonElement) {
    showPopup('', 'confirm', () => {
        fetch(`/notifications/${notificationId}/delete`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the notification item from the list
                const notificationItem = buttonElement.closest('li');
                if (notificationItem) {
                    notificationItem.style.transition = 'opacity 0.3s';
                    notificationItem.style.opacity = '0';
                    setTimeout(() => {
                        notificationItem.remove();
                        // Check if list is empty
                        const list = document.getElementById('notifications-list');
                        if (list && list.children.length === 0) {
                            list.innerHTML = '<li class="flex items-start gap-4 bg-white dark:bg-black p-3 rounded-md"><div class="flex-1 text-sm text-muted">{{ __("messages.customer_profile.no_notifications")}}</div></li>';
                        }
                        // Show success popup
                        showPopup('Die Benachrichtigung wurde erfolgreich gelöscht.', 'success');
                    }, 300);
                }
            } else {
                showPopup('Die Benachrichtigung konnte nicht gelöscht werden.', 'error');
            }
        })
        .catch(error => {
            console.error('Failed to delete notification:', error);
            showPopup('Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.', 'error');
        });
    });
}

// Function to delete all notifications
function deleteAllNotifications() {
    // Check if there are any notifications
    const list = document.getElementById('notifications-list');
    const notificationItems = list.querySelectorAll('li[data-notification-id]');
    
    if (notificationItems.length === 0) {
        showPopup('Es gibt keine Benachrichtigungen zum Löschen.', 'error');
        return;
    }
    
    // Show confirmation popup with custom message for delete all
    const overlay = document.createElement('div');
    overlay.style.cssText = 'position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center; animation: fadeIn 0.2s;';
    
    const popup = document.createElement('div');
    popup.style.cssText = 'background: white; border-radius: 12px; padding: 24px; max-width: 400px; width: 90%; box-shadow: 0 4px 20px rgba(0,0,0,0.3); animation: slideIn 0.3s; position: relative;';
    
    const isDark = document.documentElement.classList.contains('dark') || document.body.classList.contains('dark');
    if (isDark) {
        popup.style.background = '#1f1f1f';
        popup.style.color = '#ffffff';
    }
    
    popup.innerHTML = `
        <div style="text-align: center;">
            <div style="margin-bottom: 16px;">
                <svg style="width: 64px; height: 64px; margin: 0 auto; color: #dc3545;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            <h3 style="font-size: 20px; font-weight: bold; margin-bottom: 8px; color: ${isDark ? '#fff' : '#333'};">${deleteAllTitle}</h3>
            <p style="color: ${isDark ? '#ccc' : '#666'}; margin-bottom: 24px;">${deleteAllMessage}</p>
            <div style="display: flex; gap: 12px; justify-content: center;">
                <button id="cancelDeleteAllBtn" style="padding: 10px 24px; border-radius: 8px; border: 1px solid #ddd; background: ${isDark ? '#333' : '#f5f5f5'}; color: ${isDark ? '#fff' : '#333'}; cursor: pointer; font-weight: 500; transition: all 0.2s;">
                    ${cancelText}
                </button>
                <button id="confirmDeleteAllBtn" style="padding: 10px 24px; border-radius: 8px; border: none; background: #dc3545; color: white; cursor: pointer; font-weight: 500; transition: all 0.2s;">
                    ${deleteAllText}
                </button>
            </div>
        </div>
    `;
    
    overlay.appendChild(popup);
    document.body.appendChild(overlay);
    
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideIn { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        #confirmDeleteAllBtn:hover { transform: scale(1.05); }
        #cancelDeleteAllBtn:hover { background: ${isDark ? '#444' : '#e5e5e5'}; }
    `;
    document.head.appendChild(style);
    
    const closePopup = () => {
        overlay.style.animation = 'fadeIn 0.2s reverse';
        setTimeout(() => {
            if (document.body.contains(overlay)) {
                document.body.removeChild(overlay);
            }
            if (document.head.contains(style)) {
                document.head.removeChild(style);
            }
        }, 200);
    };
    
    popup.querySelector('#confirmDeleteAllBtn').addEventListener('click', () => {
        closePopup();
        
        // Disable button during deletion
        const deleteBtn = document.getElementById('delete-all-notifications-btn');
        if (deleteBtn) {
            deleteBtn.disabled = true;
            deleteBtn.style.opacity = '0.5';
            deleteBtn.style.cursor = 'not-allowed';
        }
        
        fetch('/notifications/delete-all', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Clear the entire list
                const list = document.getElementById('notifications-list');
                if (list) {
                    list.innerHTML = '<li class="flex items-start gap-4 bg-white dark:bg-black p-3 rounded-md"><div class="flex-1 text-sm text-muted">{{ __("messages.customer_profile.no_notifications")}}</div></li>';
                }
                
                // Hide load more button if exists
                const loadMoreContainer = document.getElementById('load-more-container');
                if (loadMoreContainer) {
                    loadMoreContainer.innerHTML = '';
                }
                
                // Show success popup
                showPopup('Alle Benachrichtigungen wurden erfolgreich gelöscht.', 'success');
            } else {
                showPopup('Die Benachrichtigungen konnten nicht gelöscht werden.', 'error');
            }
        })
        .catch(error => {
            console.error('Failed to delete all notifications:', error);
            showPopup('Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.', 'error');
        })
        .finally(() => {
            // Re-enable button
            if (deleteBtn) {
                deleteBtn.disabled = false;
                deleteBtn.style.opacity = '1';
                deleteBtn.style.cursor = 'pointer';
            }
        });
    });
    
    popup.querySelector('#cancelDeleteAllBtn').addEventListener('click', closePopup);
    
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) closePopup();
    });
}

</script>

@endsection
