<?php

namespace App\Http\Middleware;

use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use Closure;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPaddingSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request = $next($request);

        if(Auth::user()->hasRole('customer')){
            $paddingSubscription = Subscription::where('user_id', auth()->id())->where('status', SubscriptionStatus::PENDING->value)->first();
            // dd($paddingSubscription);

            if($paddingSubscription !== null && !empty($paddingSubscription)){
                Notification::make()
                ->danger()
                ->title(__('Your Manual Transaction Request Is Pending.'))
                ->send();
                return redirect()->back();
            }
        }

        return $request;
    }
}
