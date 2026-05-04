@extends('customer-panel.layout.main')
@section('title', __('messages.block.blocked_members'))
@section('content')

<div class="bg-[#F5F5F5] dark:bg-[#18171C] mb-4 p-3 text-end rounded-md">
    <h3 class="text-xl opacity-60 tracking-wider">{{ __('messages.block.blocked_members') }}</h3>
</div>

<div class="w-full bg-[#F5F5F5] dark:bg-[#18171C] p-6 rounded-md shadow-lg">
    <div class="mb-4">
        <h2 class="text-2xl font-bold text-center mb-2">{{ __('messages.block.blocked_members') }}</h2>
        <p class="text-center text-gray-400">{{ __('messages.block.blocked_members_description') }}</p>
    </div>

    @if($blockedUsers->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($blockedUsers as $userBlock)
                @php $user = $userBlock->blocked; @endphp
                <div class="bg-white dark:bg-black rounded-lg shadow-md p-4 hover:shadow-lg transition-shadow blocked-card">
                    <div class="text-center mb-4">
                        <img src="{{ $user->profile_image ?: asset('web/media/avatars/150-2.jpg') }}"
                             alt="{{ $user->username }}"
                             class="w-20 h-20 rounded-full mx-auto object-cover border-2 border-gray-200 dark:border-gray-700">
                    </div>
                    <div class="text-center">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                            {{ $user->username ?: 'Anonymous' }}
                        </h3>
                        <form action="{{ route('user.unblock', $user->username ?? $user->id) }}" method="POST" class="mt-3 inline-block">
                            @csrf
                            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold py-2 px-4 rounded transition-colors">
                                {{ __('messages.block.unblock') }}
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        @if($blockedUsers->hasMorePages())
            <div class="mt-8 flex justify-center">
                {{ $blockedUsers->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-12 text-gray-500 dark:text-gray-400">
            <p class="text-lg">{{ __('messages.block.no_blocked_members') }}</p>
        </div>
    @endif
</div>
@if(session('success'))
<script>document.addEventListener('DOMContentLoaded', function() { showSuccessToast(@json(session('success'))); });</script>
@endif
@endsection
