@forelse($comments as $comment)
    <div class="flex gap-4 border-b border-gray-700 pb-4">
        <img src="{{ isset($comment->users->profile_image) ? $comment->users->profile_image : asset('web/media/avatars/150-2.jpg') }}" alt="Avatar" class="w-12 h-12 rounded object-cover" />

        <div class="flex-1">
            <p class="text-sm">
                <span class="opacity-70">{{ __('messages.customer_profile.you_commented')}}</span>
                <a href="{{ route('detailPage', [$comment->post->slug ?? '']) }}" class="text-purple-400 hover:underline font-medium">
                    {{ $comment->post->title ?? 'Untitled Post' }}
                </a>
                <span class="text-gray-500">&middot; {{ $comment->created_at->diffForHumans() }}</span>
            </p>
            <p class="text-sm mt-1 whitespace-pre-line">{{ $comment->comment }}</p>
            <a 
                            href="{{ route('detailPage', [$comment->post->slug ?? '']) }}#comment-{{ $comment->id }}" target="_blank"
                            class="mt-2 text-sm inline-block px-3 py-1 bg-black hover:bg-[#313131] dark:hover:bg-[#dadada] dark:bg-white transition-all dark:text-black text-white rounded">
                            {{ __('messages.customer_profile.show_conversation')}}
                        </a>
        </div>
    </div>
@empty
    <p class="text-sm text-gray-400">{{ __('messages.customer_profile.empty_message')}}</p>
@endforelse
