<section class="flex flex-col gap-y-8 py-8">
    <div class="rounded-lg border border-gray-200 dark:border-white/10 p-4">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="">
                <h1
                    class="fi-header-heading text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">
                    {{ $currentPlan->plan->name }}
                </h1>

                <h5 class="mb-12">
                    @if (\Carbon\Carbon::now() > $currentPlan->ends_at)
                        <p class="text-sm text-primary-600 dark:text-gray-400">
                            {{ __('messages.subscription.expired') . ' ' . \Carbon\Carbon::parse($currentPlan->ends_at)->isoFormat('DD/MM/YYYY') }}
                        </p>
                    @else
                        <p class="text-sm text-primary-600 dark:text-gray-400">
                            {{ __('messages.subscription.active_until') . ' ' . \Carbon\Carbon::parse($currentPlan->ends_at)->isoFormat('DD/MM/YYYY') }}
                        </p>
                    @endif
                </h5>
                <div class="mb-12 gap-y-8 py-8">
                    <p class="text-sm text-gray-950 dark:text-white font-extrabold">
                        {{ currencyFormat($currentPlan->plan_amount, $currentPlan->plan->currency->currency_code) . '/ ' . \App\Models\Plan::DURATION[$currentPlan->plan_frequency] }}
                    </p>
                    @if (!empty($currentPlan->trial_ends_at))
                        @php
                            $startsAt = \Carbon\Carbon::now();
                            $totalDays = \Carbon\Carbon::parse($currentPlan->starts_at)->diffInDays(
                                $currentPlan->ends_at,
                            );
                            $usedDays = \Carbon\Carbon::parse($currentPlan->starts_at)->diffInDays($startsAt);
                            $remainingDays = number_format($totalDays - $usedDays, 0);
                        @endphp
                        <div class="">
                            <small class="text-sm text-gray-950 dark:text-white font-medium">
                                @if ($remainingDays > 0)
                                    {{ __('messages.plans.trial_days') }}
                                    :
                                    {{ $remainingDays . ' ' . __('messages.subscription.days') . ' ' . __('messages.subscription.remaining') }}
                                @endif
                            </small>
                        </div>
                    @endif
                    <div class="text-sm text-gray-950 dark:text-white font-medium">
                        {{ __('messages.subscription.subscribed_date').': '.\Carbon\Carbon::parse($currentPlan->starts_at)->isoFormat('DD/MM/YYYY') }}
                    </div>
                    <div class="text-sm text-gray-950 dark:text-white font-medium">
                        {{ __('messages.subscription.number_of_post').': ' .$currentPlan->no_of_post }}
                    </div>
                </div>
            </div>
            <div class="fi-ac gap-3 flex flex-wrap items-center justify-start shrink-0">
                <a href="{{ route('filament.customer.pages.upgrade-subscription') }}"
                    style="--c-400:var(--primary-400);--c-500:var(--primary-500);--c-600:var(--primary-600);"
                    class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary fi-color-primary fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 focus-visible:ring-custom-500/50 dark:bg-custom-500 dark:hover:bg-custom-400 dark:focus-visible:ring-custom-400/50 fi-ac-action fi-ac-btn-action">
                    <span class="fi-btn-label">{{ __('messages.subscription.upgrade_plan') }}</span>
                </a>
            </div>
        </div>
    </div>
    <div class="rounded-lg border border-gray-200 dark:border-white/10 p-4">
        {{ $this->table }}
    </div>
</section>
