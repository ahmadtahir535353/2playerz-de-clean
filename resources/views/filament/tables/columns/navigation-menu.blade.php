<style>
    .max-h-0 {
        max-height: 0;
    }
</style>
@php
    $navigationType =
        $getRecord()->navigationable_type == App\Models\Category::class
            ? App\Models\SubCategory::class
            : $getRecord()->navigationable_type;
    $navigationSubs = App\Models\Navigation::with('navigationable')
        ->whereHas('navigationable', function ($q) {
            $q->where('show_in_menu', 1);
        })
        ->where('navigationable_type', $navigationType)
        ->where('parent_id', $getRecord()->navigationable_id)
        ->orderBy('order_id')
        ->get();

@endphp
@if ($navigationSubs->isNotEmpty())
    <div class="bg-gray-100  flex justify-center pt-10 w-full">
        <div x-data="{ selected: 0 }" class="w-full">
            <div class="bg-white max-w-full mx-auto">
                <ul class="shadow-box">
                    <li class="relative">
                        <button type="button" class="w-full py-3 text-left"
                            @click="selected !== 1 ? selected = 1 : selected = null"
                            x-bind:style="selected == 1 ? 'border-bottom-width: 1px; border-color: #e5e7eb;' : ''">
                            <div
                                class="flex items-center justify-between fi-ta-text-item-label text-sm leading-6 text-gray-950 dark:text-white  ">
                                <span>
                                    {{ $getRecord()->navigationable->name ?? $getRecord()->navigationable->title }}
                                </span>
                                <svg :class="{ 'transform rotate-180': selected == 1 }" class="w-5 h-5 text-gray-500"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </button>

                        {{-- @dump($navigationSubs) --}}
                        <div class="relative overflow-hidden transition-all max-h-0 duration-700" style=""
                            x-ref="container1"
                            x-bind:style="selected == 1 ? 'max-height: ' + $refs.container1.scrollHeight + 'px' : ''">
                            @foreach ($navigationSubs as $sub)
                                <div
                                    class="p-3 fi-ta-text-item-label text-sm leading-6 text-gray-950 dark:text-white  ">
                                    <p>{{ $sub->navigationable->name ?? $sub->navigationable->title }}</p>
                                </div>
                            @endforeach

                        </div>
                    </li>

                </ul>
            </div>


        </div>
    </div>
@else
    <div class="py-3 text-sm leading-6 text-gray-950 dark:text-white">
        {{ $getRecord()->navigationable->name ?? $getRecord()->navigationable->title }}
    </div>
@endif
