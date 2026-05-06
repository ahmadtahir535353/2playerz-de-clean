<?php

namespace App\Http\Controllers;

use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class VerifyEmailController extends Controller
{
    public function verify(Request $request)
    {
        $user = User::findOrFail($request->route('id'));

        if ($user->hasVerifiedEmail()) {
            Notification::make()
                ->success()
                ->title(__('messages.placeholder.email_already_verified'))
                ->send();
            return redirect()->route('filament.auth.auth.login');
        }

        if ($user->markEmailAsVerified()) {
            Notification::make()
                ->success()
                ->title(__('messages.placeholder.your_email_verified_success'))
                ->send();
           return redirect()
                ->route('filament.auth.auth.login')
                ->with([
                    'prefill_email' => $user->email,
                ]);
        }

        Notification::make()
            ->danger()
            ->title(__('messages.placeholder.your_email_verification_failed'))
            ->send();
        return redirect()->route('filament.auth.auth.login');
    }
}
