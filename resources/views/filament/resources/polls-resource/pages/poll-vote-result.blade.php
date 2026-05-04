<x-filament-panels::page>
    <div class="bg-white p-6 shadow rounded-lg dark:bg-gray-800">
        <div class="py-4">
            @if ($optionAns)
                <div class="flex justify-center">
                    <nav class="fi-tabs flex max-w-full gap-x-1 overflow-x-auto mx-auto rounded-xl bg-gray-100 p-2 dark:bg-gray-900 font-semibold"
                        role="tablist">
                        {{ __('messages.poll.total_vote') }}: {{ $totalPollResults }}
                    </nav>
                </div>
                <div class="flex w-full py-5">
                    <div class="flex flex-col w-full space-y-4">
                        @foreach ($optionAns as $pollName => $statistic)
                            <div class="w-full">
                                <p class="w-full mb-2">{{ $pollName }}</p>
                                <div class="w-full bg-gray-200 rounded dark:bg-gray-700 text-end relative text-sm pe-3">
                                    <div class="bg-primary-600 text-xs font-medium text-blue-100 text-center leading-none rounded absolute top-0 bottom-0"
                                        style="width: {{ $statistic }}%">

                                    </div>
                                    <div class="relative z-10 font-extrabold">
                                        {{ $statistic }}%
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="flex w-full py-5 justify-center items-center">
                    <div class="flex flex-col w-full space-y-4 overflow-x-auto">
                        <div class="w-full text-center">
                            <p class="w-full">{{ __('messages.poll.no_result_found') }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
