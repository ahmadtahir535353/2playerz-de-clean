@extends('customer-panel.layout.main')
@section('title', __('messages.customer_profile.my_comments'))  {{-- "My Comments" --}}
@section('content')

<div class="bg-[#F5F5F5] dark:bg-[#18171C] mb-4 p-3 text-end rounded-md">
    <h3 class="text-xl tracking-wider">{{ __('messages.customer_profile.my_comments')}}</h3>
</div>

<div class="w-full bg-[#F5F5F5] dark:bg-[#18171C] p-6 rounded-md shadow-lg">
    <div class="w-full max-w-3xl">
        <!-- Comment List -->
        <div class="space-y-6" id="comments-list">
            @forelse($comments as $comment)
                <div class="flex gap-4 border-b border-gray-700 pb-4">
                    <img src="{{ isset($comment->users->profile_image) ? $comment->users->profile_image : asset('web/media/avatars/150-2.jpg') }}" alt="Avatar" class="w-12 h-12 rounded object-cover" />

                    <div class="flex-1">
                        <p class="text-sm">
                            <span class="opacity-70">
                                {{ __('messages.customer_profile.you_commented')}}
                            </span>
                            <a href="{{ route('detailPage', [$comment->post->slug ?? '']) }}" class="text-purple-400 hover:underline font-medium">
                                {{ $comment->post->title ?? __("messages.customer_profile.un_titled") }}
                            </a>
                            <span class="text-gray-500">&middot; {{ $comment->created_at->diffForHumans() }}</span>
                        </p>
                        <p class="text-sm mt-1 whitespace-pre-line">
                            {{ $comment->comment }}
                        </p>
                        <a
                            href="{{ route('detailPage', [$comment->post->slug ?? '']) }}#comment-{{ $comment->id }}"
                            class="mt-2 text-sm inline-block px-3 py-1 bg-black hover:bg-[#313131] dark:hover:bg-[#dadada] dark:bg-white transition-all dark:text-black text-white rounded">
                            {{ __('messages.customer_profile.show_conversation')}}
                        </a>

                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-400">{{ __('messages.customer_profile.empty_message')}}</p>
            @endforelse
        </div>

                <!-- Load More Button -->
        @if($comments->hasMorePages())
                <div class="text-center border-t mt-5" id="loadMoreWrapper">
                        <button
                            id="loadMoreBtn"
                            data-next-page="{{ $comments->currentPage() + 1 }}"
                            class="w-4/5 mx-auto bg-black hover:bg-[#313131] dark:hover:bg-[#dadada] dark:bg-white transition-all mt-4 font-medium px-6 py-2 rounded-md text-white dark:text-black">
                            {{ __('messages.customer_profile.load_more')}}
                        </button>
            </div>
        @endif
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function () {
        const $loadMoreBtn = $('#loadMoreBtn');
        const $commentsList = $('#comments-list');
        const $loadMoreWrapper = $('#loadMoreWrapper');
        const originalText = $loadMoreBtn.length ? $loadMoreBtn.text() : '';

        function loadComments() {
            let nextPage = $loadMoreBtn.data('next-page');

            $loadMoreBtn.text("{{ __('messages.customer_profile.loading')}}");
                setTimeout(function () {
                    $loadMoreBtn.text(originalText);
                }, 2000);

            $.ajax({
                url: `{{ route('customer.profile.comments') }}?page=${nextPage}`,
                type: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                success: function (data) {
                    if (data.html) {
                        $commentsList.append(data.html);
                    }

                    if (data.hasMore) {
                        $loadMoreBtn.data('next-page', data.nextPage);
                    } else {
                        $loadMoreWrapper.remove();
                    }
                },
                error: function (xhr) {
                    console.error('Error loading comments:', xhr);
                    $loadMoreBtn.text(originalText);
                }
            });
        }

        if ($loadMoreBtn.length) {
        $loadMoreBtn.on('click', function () {
            loadComments();
        });
        }
    });
</script>





@endsection
