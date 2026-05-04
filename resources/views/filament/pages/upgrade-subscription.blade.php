<div>
    <div class="py-4">
        <div class="flex">
            <nav class="flex max-w-full p-2 mx-auto overflow-x-auto bg-white shadow-sm fi-tabs gap-x-1 rounded-xl ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
                role="tablist">
                @foreach ($plans as $tab => $tab_plans)
                    <button type="button"
                        class="fi-tabs-item group flex items-center gap-x-2 rounded-lg px-6 py-2 text-sm font-medium outline-none transition duration-75 {{ $loop->first ? 'fi-tabs-item-active bg-gray-50 dark:bg-white/5 text-primary-400 dark:text-primary-400' : 'hover:bg-gray-50 focus-visible:bg-gray-50 dark:hover:bg-white/5 dark:focus-visible:bg-white/5' }}"
                        role="tab"
                        href="#{{ str_replace(' ', '', \App\Enums\PlanFrequency::from($tab)->getLabel()) }}">
                        {{ \App\Enums\PlanFrequency::from($tab)->getLabel() }}
                    </button>
                @endforeach
            </nav>
        </div>
        <div class="flex w-full py-5">
            @foreach ($plans as $tab => $tab_plans)
                <div id="{{ str_replace(' ', '', \App\Enums\PlanFrequency::from($tab)->getLabel()) }}"
                    class="tab-content w-full flex justify-center flex-wrap {{ $loop->first ? '' : 'hidden' }}">
                    @foreach ($tab_plans as $plan)
                        <div
                            class="w-full max-w-md p-4 mx-1 my-2 text-center text-gray-600 bg-white rounded-lg shadow-lg dark:bg-gray-900 dark:text-gray-200">
                            <h1 class="font-bold" style="font-size: 2rem">{{ $plan['name'] }}</h1>
                            <h1 class="pt-1 text-md">{{ $plan['currency_icon'] }}{{ $plan['price'] }}</h1>
                            <div class="py-5 pt-3">
                                <div class="flex items-center py-1">
                                    <h4 class="w-1/3 font-bold text-start  text-gray-600 dark:text-gray-200">
                                        {{ __('messages.subscription.what_in_startup_plan') }}</h4>
                                </div>
                                <div class="flex items-center py-1">
                                    <h4 class="w-1/2 font-bold text-start text-gray-600 dark:text-gray-200">
                                        <span class="w-1/2 text-gray-600 dark:text-gray-400">
                                            @if ($plan['frequency'] == 1)
                                                {{ __('messages.subscription.duration') . ' ' . __('messages.plans.monthly') }}
                                            @elseif($plan['frequency'] == 2)
                                                {{ __('messages.subscription.duration') . ' ' . __('messages.plans.yearly') }}
                                            @else
                                                {{ __('messages.subscription.duration') . ' ' . __('messages.plans.unlimited') }}
                                            @endif
                                        </span>
                                    </h4>
                                </div>
                                <div class="flex items-center py-1">
                                    <small class="font-semibold text-start text-gray-600 dark:text-gray-200">
                                        {{ __('messages.plans.no_of_posts') . ' : ' . $plan['post_count'] }}</small>
                                </div>
                            </div>

                            @if (
                                !empty(getCurrentSubscription()) &&
                                    $plan['id'] == getCurrentSubscription()->plan_id &&
                                    !getCurrentSubscription()->isExpired())
                                @if ($plan['price'] != 0)
                                    <button
                                        style="--c-400:var(--success-400);--c-500:var(--success-500);--c-600:var(--success-600);"
                                        class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-success fi-color-success fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 focus-visible:ring-custom-500/50 dark:bg-custom-400 dark:hover:bg-custom-600 dark:focus-visible:ring-custom-400/50 fi-ac-action fi-ac-btn-action cursor-default">
                                        <span
                                            class="fi-btn-label">{{ __('messages.subscription.currently_active') }}</span>
                                    </button>
                                @else
                                    <button type="button"
                                        class="btn btn-info rounded-pill mx-auto d-block cursor-remove-plan">
                                        {{ __('messages.subscription.renew_free_plan') }}
                                    </button>
                                @endif
                            @else
                                @php
                                    $hasPlan = App\Models\Plan::where('id', $plan['id'])->first();
                                @endphp
                                @if (
                                    !empty(getCurrentSubscription()) &&
                                        !getCurrentSubscription()->isExpired() &&
                                        ($plan['price'] == 0 || $plan['price'] != 0))
                                    @if ($hasPlan->hasZeroPlan->count() == 0)
                                        {{-- @if ($plan['is_default'])
                                            <button
                                                style="--c-400:var(--info-300);--c-500:var(--info-400);--c-600:var(--info-500);"
                                                class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-info fi-color-info fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white focus-visible:ring-custom-500/50 dark:bg-custom-400 dark:focus-visible:ring-custom-400/50 fi-ac-action fi-ac-btn-action cursor-default">
                                                <span
                                                    class="fi-btn-label">Is Trial plan</span>
                                            </button>
                                        @else --}}
                                            <a href="{{ route('filament.customer.pages.choose-pyment-type', ['plan' => $plan['id']]) }}"
                                                style="--c-400:var(--primary-400);--c-500:var(--primary-500);--c-600:var(--primary-600);"
                                                class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary fi-color-primary fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 focus-visible:ring-custom-500/50 dark:bg-custom-500 dark:hover:bg-custom-400 dark:focus-visible:ring-custom-400/50 fi-ac-action fi-ac-btn-action">
                                                {{ __('messages.subscription.switch_plan') }}</a>
                                        {{-- @endif --}}
                                    @else
                                        <button type="button"
                                            class="btn btn-info rounded-pill mx-auto d-block cursor-remove-plan">
                                            {{ __('messages.subscription.renew_free_plan') }}
                                        </button>
                                    @endif
                                @else
                                    @if ($plan['price'] != 0 && $hasPlan->hasZeroPlan->count() == 0)
                                        <a href="{{ route('filament.customer.pages.choose-pyment-type', ['plan' => $plan['id']]) }}"
                                            style="--c-400:var(--primary-400);--c-500:var(--primary-500);--c-600:var(--primary-600);"
                                            class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary fi-color-primary fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 focus-visible:ring-custom-500/50 dark:bg-custom-500 dark:hover:bg-custom-400 dark:focus-visible:ring-custom-400/50 fi-ac-action fi-ac-btn-action">
                                            {{ __('messages.subscription.choose_plan') }}</a>
                                    @else
                                        <button type="button"
                                            class="btn btn-info rounded-pill mx-auto d-block cursor-remove-plan">
                                            {{ __('messages.subscription.renew_free_plan') }}
                                        </button>
                                    @endif
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
        @if ($plans->count() <= 0)
            <div class="p-4 text-center border border-gray-200 rounded-lg dark:border-white/10">
                <h1 class="text-2xl font-bold text-gray-950 dark:text-white">{{ __('messages.plan.no_plans') }}</h1>
            </div>
        @endif
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabLinks = document.querySelectorAll('.fi-tabs-item');
        tabLinks.forEach(function(tabLink) {
            tabLink.addEventListener('click', function(event) {
                event.preventDefault();
                tabLinks.forEach(function(link) {
                    link.classList.remove('fi-tabs-item-active', 'bg-gray-50',
                        'dark:bg-white/5', 'text-primary-400',
                        'dark:text-primary-400');
                    link.classList.add('hover:bg-gray-50', 'focus-visible:bg-gray-50',
                        'dark:hover:bg-white/5', 'dark:focus-visible:bg-white/5');
                });
                tabLink.classList.add('fi-tabs-item-active', 'bg-gray-50', 'dark:bg-white/5',
                    'text-primary-400', 'dark:text-primary-400');
                tabLink.classList.remove('hover:bg-gray-50', 'focus-visible:bg-gray-50',
                    'dark:hover:bg-white/5', 'dark:focus-visible:bg-white/5');
                const tabContents = document.querySelectorAll('.tab-content');
                tabContents.forEach(function(tabContent) {
                    tabContent.classList.add('hidden');
                });
                const targetId = tabLink.getAttribute('href').substring(1);
                const targetContent = document.getElementById(targetId);
                if (targetContent) {
                    targetContent.classList.remove('hidden');
                }
            });
        });
    });
</script>
