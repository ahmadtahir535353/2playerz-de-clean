@extends('customer-panel.layout.main')

@section('title', __('messages.profile.my_profile_visitors'))

@section('content')
<style>
    /* Custom Scrollbar Styles */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #734E96;
        border-radius: 10px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #B051B0;
    }
    
    .dark .custom-scrollbar::-webkit-scrollbar-track {
        background: #374151;
    }
    
    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #B051B0;
    }
    
    .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #734E96;
    }
    
</style>
<div class="bg-[#F5F5F5] dark:bg-[#18171C] p-3 md:p-6 rounded-md shadow-md">
    <div class="mb-4 md:mb-6">
        <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white mb-2">
            <!-- <i class="fas fa-eye me-2"></i> -->
            {{ __('messages.profile.my_profile_visitors') }}
        </h2>
        <p class="text-sm md:text-base text-gray-700 dark:text-gray-400">{{ __('messages.profile.visitors_description') }}</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-4 mb-4 md:mb-6">
        <div class="bg-white dark:bg-gray-800 p-3 md:p-4 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-full flex-shrink-0">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
                <div class="ml-3 min-w-0 flex-1">
                    <p class="text-xs md:text-sm font-medium text-gray-700 dark:text-gray-400 truncate">{{ __('messages.profile.total_visitors') }}</p>
                    <p class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['visitor_count'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 p-3 md:p-4 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 dark:bg-green-900 rounded-full flex-shrink-0">
                    <i class="fas fa-user-check text-green-600"></i>
                </div>
                <div class="ml-3 min-w-0 flex-1">
                    <p class="text-xs md:text-sm font-medium text-gray-700 dark:text-gray-400 truncate">{{ __('messages.profile.member_visits') }}</p>
                    <p class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['logged_in_visits'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 p-3 md:p-4 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-full flex-shrink-0">
                    <i class="fas fa-user-secret text-yellow-600"></i>
                </div>
                <div class="ml-3 min-w-0 flex-1">
                    <p class="text-xs md:text-sm font-medium text-gray-700 dark:text-gray-400 truncate">{{ __('messages.profile.guest_visits') }}</p>
                    <p class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['guest_visits'] }}</p>
                </div>
            </div>
        </div>
        
        <!-- <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-full">
                    <i class="fas fa-chart-line text-purple-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.profile.total_visits') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_visits'] }}</p>
                </div>
            </div>
        </div> -->
    </div>

    <!-- Visitors List -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="p-3 md:p-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-white">{{ __('messages.profile.recent_visitors') }}</h3>
        </div>
        <div class="p-2 md:p-4">
            @if($paginatedVisitors->count() > 0)
                <div class="space-y-3 md:space-y-4 {{ $paginatedVisitors->count() > 4 ? 'max-h-[600px] overflow-y-auto pr-2 custom-scrollbar' : '' }}" id="visitorsList">
                    @foreach($paginatedVisitors as $visitor)
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between p-3 md:p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors gap-3 md:gap-4">
                            <div class="flex items-center space-x-3 md:space-x-4 flex-1 min-w-0">
                                <!-- Profile Image -->
                                <div class="flex-shrink-0">
                                    @if($visitor->visitor->profile_image)
                                        <img src="{{ $visitor->visitor->profile_image }}" 
                                             alt="{{ $visitor->visitor->full_name }}" 
                                             class="w-10 h-10 md:w-12 md:h-12 rounded-full object-cover">
                                    @else
                                        <div class="w-10 h-10 md:w-12 md:h-12 bg-gray-400 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-white text-sm md:text-base"></i>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Visitor Info -->
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-base md:text-lg font-semibold text-gray-900 dark:text-white truncate">
                                        <a href="{{ route('user.public.profile', $visitor->visitor->username ?? $visitor->visitor->id) }}"
                                           class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                            {{ $visitor->visitor->username }}
                                        </a>
                                    </h4>
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-2 md:space-x-4 text-xs md:text-sm text-gray-700 dark:text-gray-300 mt-1 gap-1 sm:gap-0">
                                        <span class="whitespace-nowrap">
                                            <i class="fas fa-coins text-yellow-500 mr-1"></i>
                                            {{ $visitor->visitor->comment_points ?? 0 }} {{ __('messages.other_lang.player_points') }}
                                        </span>
                                        <span class="hidden sm:inline">•</span>
                                        <span class="whitespace-nowrap">
                                            <i class="fas fa-trophy text-purple-500 mr-1"></i>
                                            {{ $visitor->visitor->level ?? 'Newbie' }}
                                        </span>
                                        <span class="hidden sm:inline">•</span>
                                        <span class="whitespace-nowrap">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $visitor->visited_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- View Profile Button -->
                            <div class="flex-shrink-0 w-full md:w-auto">
                                <a href="{{ route('user.public.profile', $visitor->visitor->username ?? $visitor->visitor->id) }}"
                                   class="inline-flex items-center justify-center w-full md:w-auto px-3 md:px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs md:text-sm font-medium rounded-lg transition-colors">
                                    <i class="fas fa-eye mr-2"></i>
                                    {{ __('messages.profile.view_profile') }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-4 md:mt-6 overflow-x-auto">
                    <div class="min-w-0">
                        {{ $paginatedVisitors->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-8 md:py-12 px-4">
                    <div class="mx-auto w-16 h-16 md:w-24 md:h-24 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 md:w-12 md:h-12 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-base md:text-lg font-medium text-gray-900 dark:text-white mb-2">{{ __('messages.profile.no_visitors_yet') }}</h3>
                    <p class="text-sm md:text-base text-gray-500 dark:text-gray-400">{{ __('messages.profile.no_visitors_description') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection