@forelse($items as $item)
    @php
        $game = $item->gameRelease;
        $bc = $badgeColors ?? [];
    @endphp
    <tr class="border-b border-gray-200 dark:border-gray-700 wishlist-row {{ $item->highlighted ? 'wishlist-row-highlighted' : '' }}"
        data-wishlist-id="{{ $item->id }}">
        <td class="py-3 pr-4 text-gray-700 dark:text-gray-300">
            @if($game->release_date)
                {{ $game->release_date->format('d.m.Y') }}
            @elseif($game->release_month && $game->release_year)
                {{ \Carbon\Carbon::create($game->release_year, $game->release_month, 1)->locale('de')->monthName }} {{ $game->release_year }}
            @elseif($game->release_year)
                {{ __('messages.release_calendar.date_tba') }}
            @else
                –
            @endif
        </td>
        <td class="py-3 pr-4">
            @if($item->highlighted)
                <button type="button" class="wishlist-clear-highlight-btn text-red-600 dark:text-red-400 font-semibold hover:underline bg-transparent border-none cursor-pointer p-0" data-id="{{ $item->id }}">
                    {{ $game->name }}
                </button>
            @else
                @if(!empty(trim($game->link ?? '')))
                    <a href="{{ $game->link }}" class="font-semibold text-[#B051B0] hover:underline">{{ $game->name }}</a>
                @else
                    <span class="font-semibold">{{ $game->name }}</span>
                @endif
            @endif
        </td>
        <td class="py-3 pr-4">
            @if($game->playstation || $game->xbox || $game->nintendo)
                <span class="text-gray-500 dark:text-gray-400">(</span>
                @if($game->playstation)
                    <span class="platform-badge inline-block px-2 py-0.5 rounded text-sm" style="background-color: {{ $bc['playstation']['bg'] ?? '#2563eb' }}; color: {{ $bc['playstation']['text'] ?? '#fff' }};">PlayStation</span>
                @endif
                @if($game->xbox)
                    @if($game->playstation) <span class="text-gray-400">, </span> @endif
                    <span class="platform-badge inline-block px-2 py-0.5 rounded text-sm" style="background-color: {{ $bc['xbox']['bg'] ?? '#16a34a' }}; color: {{ $bc['xbox']['text'] ?? '#fff' }};">Xbox</span>
                @endif
                @if($game->nintendo)
                    @if($game->playstation || $game->xbox) <span class="text-gray-400">, </span> @endif
                    <span class="platform-badge inline-block px-2 py-0.5 rounded text-sm" style="background-color: {{ $bc['nintendo']['bg'] ?? '#dc2626' }}; color: {{ $bc['nintendo']['text'] ?? '#fff' }};">Nintendo</span>
                @endif
                <span class="text-gray-500 dark:text-gray-400">)</span>
            @else
                –
            @endif
        </td>
        <td class="py-3">
            <form action="{{ route('wishlist.remove') }}" method="POST" class="inline-block wishlist-remove-form">
                @csrf
                <input type="hidden" name="id" value="{{ $item->id }}">
                <button type="submit" class="text-red-600 dark:text-red-400 font-bold text-lg hover:text-red-700 dark:hover:text-red-300" title="{{ __('messages.wishlist.remove_from_list') }}">✕</button>
            </form>
        </td>
    </tr>
@empty
@endforelse
