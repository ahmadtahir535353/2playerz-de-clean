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

