@extends('customer-panel.layout.main')
@section('title', __('messages.members_i_follow'))
@section('content')

<div class="bg-[#F5F5F5] dark:bg-[#18171C] mb-4 p-3 text-end rounded-md">
    <h3 class="text-xl opacity-60 tracking-wider">{{ __('messages.members_i_follow')}}</h3>
</div>

<div class="w-full bg-[#F5F5F5] dark:bg-[#18171C] p-6 rounded-md shadow-lg">
    <div class="mb-4">
        <h2 class="text-2xl font-bold text-center mb-2">{{ __('messages.members_i_follow')}}</h2>
        <p class="text-center text-gray-400">{{ __('messages.following_count', ['count' => $followingUsers->total()]) }}</p>
    </div>

    @if($followingUsers->count() > 0)
        <!-- Following Cards Grid -->
        <div id="followingGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @include('customer-panel.partials.following-cards', ['followingUsers' => $followingUsers])
        </div>

        <!-- Load More Button -->
        @if($followingUsers->hasMorePages())
        <div class="mt-8 text-center">
            <button id="loadMoreBtn" 
                    data-page="2"
                    class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-8 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105">
                <i class="fas fa-chevron-down mr-2"></i>
                {{ __('messages.load_more') }}
            </button>
            
            <!-- Loading Spinner (hidden by default) -->
            <div id="loadingSpinner" class="hidden mt-4">
                <div class="inline-block">
                    <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-purple-600"></div>
                </div>
                <p class="text-gray-400 mt-2">{{ __('messages.loading') }}...</p>
            </div>
        </div>
        @endif

        <!-- Activities Section -->
        @if(isset($activities) && $activities->count() > 0)
        <div class="mt-12 w-full bg-[#F5F5F5] dark:bg-[#18171C] p-6 rounded-md shadow-lg">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-center mb-2">{{ __('messages.actions_from_members_i_follow') }}</h2>
                <p class="text-center text-gray-400">{{ __('messages.look_at_what_the_people_are_doing') }}</p>
            </div>

            <div id="activitiesContainer" class="space-y-4">
                @foreach($activities as $activity)
                <div class="bg-white dark:bg-black rounded-lg p-4 shadow-md hover:shadow-lg transition-shadow activity-item">
                    <div class="flex items-start gap-4">
                        <!-- User Avatar -->
                        <div class="flex-shrink-0">
                            <img src="{{ $activity['profile_image'] }}" 
                                 alt="{{ $activity['username'] }}" 
                                 class="w-12 h-12 rounded-full object-cover border-2 border-gray-200 dark:border-gray-700">
                        </div>

                        <!-- Activity Content -->
                        <div class="flex-1 min-w-0">
                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $activity['username'] }}</span>
                                hat kommentiert bei 
                                <a href="{{ route('detailPage', $activity['post_slug']) }}" 
                                   class="text-purple-600 dark:text-purple-400 hover:underline font-semibold">
                                    {{ $activity['post_title'] }}
                                </a>
                                <span class="text-gray-500">vor {{ $activity['created_at_human'] }}</span>
                            </div>
                            
                            <!-- Comment Preview -->
                            <div class="bg-gray-50 dark:bg-gray-900 rounded-md p-3 mb-3">
                                <p class="text-sm text-gray-700 dark:text-gray-300 italic">
                                    "{{ $activity['comment_text'] }}"
                                </p>
                            </div>

                            <!-- View Conversation Button -->
                            <a href="{{ route('detailPage', $activity['post_slug']) }}#comment-{{ $activity['id'] }}" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                Konversation anzeigen
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Load More Activities Button -->
            @if(isset($activitiesHasMore) && $activitiesHasMore)
            <div class="mt-8 text-center">
                <button id="loadMoreActivitiesBtn" 
                        data-page="2"
                        onclick="loadMoreActivities(event)"
                        class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-8 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105">
                    <i class="fas fa-chevron-down mr-2"></i>
                    Mehr Aktivitäten laden
                </button>
                
                <!-- Loading Spinner (hidden by default) -->
                <div id="loadingActivitiesSpinner" class="hidden mt-4">
                    <div class="inline-block">
                        <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-purple-600"></div>
                    </div>
                    <p class="text-gray-400 mt-2">Laden...</p>
                </div>
            </div>
            @endif
        </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <div class="text-6xl mb-4">👥</div>
            <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-400 mb-2">
                {{ __('messages.no_following_yet') }}
            </h3>
            <p class="text-gray-500 dark:text-gray-500 mb-6">
                {{ __('messages.start_following_description') }}
            </p>
            <a href="/" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg transition-colors">
                {{ __('messages.explore_members') }}
            </a>
        </div>
    @endif
</div>

<script>
// Load More Functionality
document.addEventListener('DOMContentLoaded', function() {
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const followingGrid = document.getElementById('followingGrid');

    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            const page = parseInt(this.getAttribute('data-page'));
            
            // Show loading spinner, hide button
            loadMoreBtn.classList.add('hidden');
            loadingSpinner.classList.remove('hidden');

            // Fetch next page
            fetch(`{{ route('members.following') }}?page=${page}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Hide loading spinner
                loadingSpinner.classList.add('hidden');

                if (data && data.html) {
                    // Create temporary div to parse HTML
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = data.html;
                    
                    // Append each card with animation
                    const cards = tempDiv.querySelectorAll('.following-card');
                    
                    if (cards.length > 0) {
                        cards.forEach((card, index) => {
                            // Add fade-in animation
                            card.style.opacity = '0';
                            card.style.transform = 'translateY(20px)';
                            followingGrid.appendChild(card);
                            
                            // Trigger animation after a small delay
                            setTimeout(() => {
                                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                                card.style.opacity = '1';
                                card.style.transform = 'translateY(0)';
                            }, index * 50);
                        });
                    }

                    // If there are more pages, show button again with updated page
                    if (data.hasMore) {
                        loadMoreBtn.setAttribute('data-page', data.nextPage);
                        loadMoreBtn.classList.remove('hidden');
                    } else {
                        // No more pages, button stays hidden
                        console.log('No more users to load');
                    }
                } else {
                    // No data or invalid response
                    console.log('No data received or invalid response');
                    loadingSpinner.classList.add('hidden');
                    loadMoreBtn.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error loading more users:', error);
                loadingSpinner.classList.add('hidden');
                loadMoreBtn.classList.remove('hidden');
                
                // Show error message
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '{{ __("messages.error") }}',
                        text: '{{ __("messages.error_loading_more") }}',
                        icon: 'error',
                        confirmButtonColor: '#7c3aed',
                    });
                } else {
                    alert('Error loading more users');
                }
            });
        });
    }
});

function unfollowUser(userId, username) {
    Swal.fire({
        title: '{{ __("messages.confirm_unfollow_title") }}',
        text: '{{ __("messages.confirm_unfollow") }}'.replace(':username', username),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '{{ __("messages.yes_unfollow") }}',
        cancelButtonText: '{{ __("messages.cancel") }}',
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
            // Show loading state
            const button = event.target;
            if (!button) {
                console.error('Button element not found');
                return;
            }
            
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>{{ __("messages.unfollowing") }}...';
            
            fetch(`/user/${userId}/unfollow`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the user card from the page
                    const userCard = button.closest('.bg-white, .bg-black') || 
                                   button.closest('[class*="bg-white"], [class*="bg-black"]') ||
                                   button.closest('.rounded-lg');
                    
                    if (userCard) {
                        userCard.style.transition = 'opacity 0.3s ease';
                        userCard.style.opacity = '0';
                        
                        setTimeout(() => {
                            userCard.remove();
                            
                            // Check if there are any more users left
                            const remainingCards = document.querySelectorAll('.bg-white, .bg-black, [class*="bg-white"], [class*="bg-black"]');
                            if (remainingCards.length === 0) {
                                // Reload the page to show empty state
                                location.reload();
                            }
                        }, 300);
                    } else {
                        // If card not found, just reload the page
                        console.log('User card not found, reloading page');
                        location.reload();
                    }
                    
                    // Show success message using SweetAlert2
                    Swal.fire({
                        title: '{{ __("messages.unfollowed_successfully_title") }}',
                        text: data.message,
                        icon: 'success',
                        confirmButtonColor: '#7c3aed',
                        timer: 2000,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'swal2-popup-custom',
                            title: 'swal2-title-custom',
                            content: 'swal2-content-custom'
                        }
                    });
                } else {
                    // Show error message using SweetAlert2
                    Swal.fire({
                        title: '{{ __("messages.error") }}',
                        text: data.message || '{{ __("messages.error_occurred") }}',
                        icon: 'error',
                        confirmButtonColor: '#dc2626',
                        customClass: {
                            popup: 'swal2-popup-custom',
                            title: 'swal2-title-custom',
                            content: 'swal2-content-custom',
                            confirmButton: 'swal2-confirm-custom'
                        }
                    });
                    
                    // Restore button state if button exists
                    if (button) {
                        button.innerHTML = originalText;
                        button.disabled = false;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Show error message using SweetAlert2
                Swal.fire({
                    title: '{{ __("messages.error") }}',
                    text: '{{ __("messages.error_occurred") }}',
                    icon: 'error',
                    confirmButtonColor: '#dc2626',
                    customClass: {
                        popup: 'swal2-popup-custom',
                        title: 'swal2-title-custom',
                        content: 'swal2-content-custom',
                        confirmButton: 'swal2-confirm-custom'
                    }
                });
                
                // Restore button state if button exists
                if (button) {
                    button.innerHTML = originalText;
                    button.disabled = false;
                }
            });
        }
    });
}
</script>

<script>
// Load More Activities Functionality - Global function
function loadMoreActivities(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    const loadMoreActivitiesBtn = document.getElementById('loadMoreActivitiesBtn');
    const loadingActivitiesSpinner = document.getElementById('loadingActivitiesSpinner');
    const activitiesContainer = document.getElementById('activitiesContainer');
    
    if (!loadMoreActivitiesBtn || !activitiesContainer) {
        console.error('Load more activities button or container not found!');
        return;
    }
    
    const page = parseInt(loadMoreActivitiesBtn.getAttribute('data-page')) || 2;
    
    // Show loading spinner, hide button
    loadMoreActivitiesBtn.classList.add('hidden');
    if (loadingActivitiesSpinner) {
        loadingActivitiesSpinner.classList.remove('hidden');
    }

    // Fetch next page of activities
    fetch(`{{ route('members.following') }}?activities_page=${page}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        // Hide loading spinner
        if (loadingActivitiesSpinner) {
            loadingActivitiesSpinner.classList.add('hidden');
        }

        if (data && data.html) {
            // Create temporary div to parse HTML
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = data.html;
            
            // Append each activity with animation
            const activities = tempDiv.querySelectorAll('.activity-item');
            
            if (activities.length > 0) {
                activities.forEach((activity, index) => {
                    // Add fade-in animation
                    activity.style.opacity = '0';
                    activity.style.transform = 'translateY(20px)';
                    activitiesContainer.appendChild(activity);
                    
                    // Trigger animation after a small delay
                    setTimeout(() => {
                        activity.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                        activity.style.opacity = '1';
                        activity.style.transform = 'translateY(0)';
                    }, index * 50);
                });
            }

            // If there are more pages, show button again with updated page
            if (data.hasMore) {
                loadMoreActivitiesBtn.setAttribute('data-page', data.nextPage);
                loadMoreActivitiesBtn.classList.remove('hidden');
            }
        } else {
            // No more data or error
            if (loadingActivitiesSpinner) {
                loadingActivitiesSpinner.classList.add('hidden');
            }
        }
    })
    .catch(error => {
        console.error('Error loading more activities:', error);
        if (loadingActivitiesSpinner) {
            loadingActivitiesSpinner.classList.add('hidden');
        }
        if (loadMoreActivitiesBtn) {
            loadMoreActivitiesBtn.classList.remove('hidden');
        }
        
        // Show error message
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '{{ __("messages.error") }}',
                text: 'Fehler beim Laden weiterer Aktivitäten',
                icon: 'error',
                confirmButtonColor: '#dc2626',
            });
        } else {
            alert('Fehler beim Laden weiterer Aktivitäten');
        }
    });
}
</script>

<style>
/* Custom SweetAlert2 Styling */
.swal2-popup-custom {
    border-radius: 12px !important;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
}

.swal2-title-custom {
    font-size: 1.25rem !important;
    font-weight: 600 !important;
    color: #1f2937 !important;
}

.swal2-content-custom {
    font-size: 0.875rem !important;
    color: #6b7280 !important;
    line-height: 1.5 !important;
}

.swal2-confirm-custom {
    border-radius: 8px !important;
    font-weight: 500 !important;
    padding: 0.5rem 1.5rem !important;
}

.swal2-cancel-custom {
    border-radius: 8px !important;
    font-weight: 500 !important;
    padding: 0.5rem 1.5rem !important;
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
