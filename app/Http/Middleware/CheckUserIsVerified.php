<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;
use Symfony\Component\HttpFoundation\Response;

class CheckUserIsVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user() && $request->user()->email_verified_at !== null) {
            return $next($request);
        }

        Auth::logout();
        Notification::make()
            ->title(__('messages.placeholder.your_account_is_currently_disabled_please_contact_to_administrator'))
            ->danger()
            ->send();

        return redirect()->route('filament.auth.auth.login');
    }
}
