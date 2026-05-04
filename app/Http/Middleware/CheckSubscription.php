<?php

namespace App\Http\Middleware;

use App\Models\Post;
use App\Models\Subscription;
use Closure;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $excludedRoutes = [
            'filament.customer.pages.choose-pyment-type',
            'filament.customer.pages.upgrade-subscription',
        ];

        if (in_array($request->route()->getName(), $excludedRoutes)) {
            return $next($request);
        }

        if ($request->routeIs('filament.customer.pages.manage-subscription')) {
            return $next($request);
        }

        $request = $next($request);

        if (auth()->user() && auth()->user()->hasRole('customer')) {
            $subscription = Subscription::with('plan')
                ->where('status', Subscription::ACTIVE)
                ->where('user_id', getLogInUser()->id)
                ->latest()
                ->first();

                $newsubscription = Subscription::with('plan')
                ->where('user_id', getLogInUser()->id)
                ->latest()->first();
            if (! $subscription || $subscription->isExpired() && $newsubscription->status !== 2) {
                Post::where('created_by', getLogInUser()->id)->update([
                    'status' => 0,
                ]);

                Notification::make()
                    ->danger()
                    ->title(__('messages.placeholder.your_plan_is_expired_Please_choose_a_plan_to_continue_the_services'))
                    ->send();

                return redirect()->route('filament.customer.pages.manage-subscription');
            }
        }

        return $request;
    }
}
