<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\RegistrationResponse;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class CustomRegistrationResponse extends RegistrationResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        return redirect('/register')->with([
            'filament.notifications' => [
                [
                    'status' => 'success',
                    'title' => __('messages.placeholder.registered_success'),
                ]
            ]
        ]);

    }
}
