@extends('customer-panel.layout.main')
@section('title', __('messages.wishlist.my_wishlist'))
@section('content')

<div class="bg-[#F5F5F5] dark:bg-[#18171C] mb-4 p-3 text-end rounded-md">
    <h3 class="text-xl opacity-60 tracking-wider">{{ __('messages.wishlist.my_wishlist') }}</h3>
</div>

<div class="w-full bg-[#F5F5F5] dark:bg-[#161618] p-6 rounded-md shadow-lg">
    @if(session('success'))
        <p class="text-green-600 dark:text-green-400 mb-4">{{ session('success') }}</p>
    @endif

    @if($wishlistPaginator->isEmpty())
        <p class="text-gray-600 dark:text-gray-400">{{ __('messages.wishlist.no_games') }}</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-300 dark:border-gray-600">
                        <th class="py-2 pr-4 font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.wishlist.release') }}</th>
                        <th class="py-2 pr-4 font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.wishlist.name') }}</th>
                        <th class="py-2 pr-4 font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.release_calendar.platforms') ?? 'Platform' }}</th>
                        <th class="py-2 font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.wishlist.remove_from_list') }}</th>
                    </tr>
                </thead>
                <tbody id="wishlist-tbody">
                    @include('customer-panel.partials.wishlist-rows', ['items' => $wishlistPaginator->getCollection(), 'badgeColors' => $badgeColors])
                </tbody>
            </table>
        </div>

        @if($wishlistPaginator->hasMorePages())
            <div class="text-center border-t border-gray-200 dark:border-gray-700 mt-5 pt-4" id="loadMoreWrapper">
                <button type="button" id="loadMoreWishlistBtn" data-next-page="{{ $wishlistPaginator->currentPage() + 1 }}"
                    class="w-4/5 mx-auto bg-black hover:bg-[#313131] dark:hover:bg-[#dadada] dark:bg-white transition-all mt-4 font-medium px-6 py-2 rounded-md text-white dark:text-black">
                    {{ __('messages.customer_profile.load_more') }}
                </button>
            </div>
        @endif
    @endif
</div>

<style>
    .wishlist-row-highlighted td:nth-child(2) {
        color: #dc2626 !important;
    }
    .dark .wishlist-row-highlighted td:nth-child(2) {
        color: #f87171 !important;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var csrf = document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var clearHighlightUrl = '{{ route("wishlist.clear-highlight") }}';
    var releaseCalendarUrl = '{{ route("release-calendar.all") }}';

    document.getElementById('wishlist-tbody') && document.getElementById('wishlist-tbody').addEventListener('click', function(e) {
        var btn = e.target.closest('.wishlist-clear-highlight-btn');
        if (!btn) return;
        e.preventDefault();
        var id = btn.getAttribute('data-id');
        var row = btn.closest('tr');
        var gameName = btn.textContent.trim();
        var formData = new FormData();
        formData.append('_token', csrf || '');
        formData.append('id', id);
        fetch(clearHighlightUrl, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData
        }).then(function() {
            row.classList.remove('wishlist-row-highlighted');
            var nameCell = row.querySelector('td:nth-child(2)');
            nameCell.innerHTML = '<a href="' + releaseCalendarUrl + '" class="font-semibold text-[#B051B0] hover:underline">' + gameName + '</a>';
        });
    });

    var $loadMoreBtn = $('#loadMoreWishlistBtn');
    var $wishlistTbody = $('#wishlist-tbody');
    var $loadMoreWrapper = $('#loadMoreWrapper');
    var originalText = $loadMoreBtn.length ? $loadMoreBtn.text() : '';

    function loadMoreWishlist() {
        var nextPage = $loadMoreBtn.data('next-page');
        $loadMoreBtn.text("{{ __('messages.customer_profile.loading') }}");
        $.ajax({
            url: '{{ route("wishlist.index") }}?page=' + nextPage,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf || ''
            },
            success: function(data) {
                $loadMoreBtn.text(originalText);
                if (data.html) {
                    $wishlistTbody.append(data.html);
                }
                if (data.hasMore) {
                    $loadMoreBtn.data('next-page', data.nextPage);
                } else {
                    $loadMoreWrapper.remove();
                }
            },
            error: function() {
                $loadMoreBtn.text(originalText);
            }
        });
    }

    if ($loadMoreBtn.length) {
        $loadMoreBtn.on('click', function() {
            loadMoreWishlist();
        });
    }
});
</script>
@endsection
