<?php

namespace App\Filament\Pages;

use App\Http\Middleware\CheckPaddingSubscription;
use App\Models\Plan;
use App\Models\Subscription;
use Filament\Pages\Page;

class UpgradeSubscription extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.upgrade-subscription';

    protected static string|array $routeMiddleware = [
        CheckPaddingSubscription::class,
    ];

    protected function getViewData(): array
    {
        $data = [];

        $data['tabs'] = [
            'monthly' => 'Monthly',
            'yearly' => 'Yearly',
            'unlimited' => 'Unlimited',
        ];

        // $plans = Plan::with(['currency'])->get();

        // $data['monthlyPlans'] = $plans->where('frequency', Plan::MONTHLY);
        // $data['yearlyPlans'] = $plans->where('frequency', Plan::YEARLY);
        // $data['unLimitedPlans'] = $plans->where('frequency', Plan::UNLIMITED);

        // $data['currentActivePlan'] = Subscription::with('plan')->where('user_id', auth()->id())->where('status', SubscriptionStatus::ACTIVE->value)->first();

        // dd($data['plans'] = Plan::with(['currency'])->get());
        $data['plans'] = Plan::with(['currency'])
            ->get()->groupBy('frequency')->map(function ($plans) {
            return $plans->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'post_count' => $plan->post_count,
                    'price' => $plan->price,
                    'currency_icon' => $plan->currency->currency_icon,
                    'trial_days' => $plan->trial_days,
                    'frequency' => $plan->frequency,
                    'is_default' => $plan->is_default,
                ];
            });
        });

        return $data;
    }
}
