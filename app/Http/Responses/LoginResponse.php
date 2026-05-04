<?php

namespace App\Http\Responses;

use App\Models\User;
use Carbon\Carbon;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Filament\Notifications\Notification;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user) {
            $role = $user->roles()->first();

            $user->last_seen_at = Carbon::now();
            $user->save();

            if ($role && $role->name === 'admin') {
                return redirect()->route('filament.admin.pages.dashboard');
            }

            if ($role && $role->name === 'customer') {
                return redirect()->intended('customer.profile');
            }
            return redirect()->route('filament.admin.pages.dashboard');
        }

        // return parent::toResponse($request);
    }
}
