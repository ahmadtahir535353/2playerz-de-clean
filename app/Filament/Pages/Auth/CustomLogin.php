<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Models\Contracts\FilamentUser;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;

class CustomLogin extends BaseLogin
{

    /**
     * @var view-string
     */
    protected static string $view = 'filament.auth.login';


    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        TextInput::make('email')
                            ->label(__('messages.mails.email_address_or_username') . ':')
                            ->validationAttribute(__('messages.mails.email_address_or_username'))
                            ->placeholder(__('messages.mails.email_address_or_nickname'))
                            ->required()
                            ->default(function() {
                                // Only auto-fill if coming from register page (has prefill_email in session)
                                $email = session('prefill_email');
                                if ($email) {
                                    // Clear after use so it doesn't persist
                                    session()->forget('prefill_email');
                                    return $email;
                                }
                                return null;
                            })
                            ->autocomplete('username'),
                        $this->getPasswordFormComponent()->label(__('messages.staff.password') . ':')->validationAttribute(__('messages.staff.password'))->placeholder(__('messages.staff.password'))->default(session('prefill_password'))
                            ->hint(filament()->hasPasswordReset() ? new HtmlString(Blade::render('
                                <x-filament::link 
                                    :href="filament()->getRequestPasswordResetUrl()" 
                                    onclick="
                                        event.preventDefault();
                                        const emailInput = document.querySelector(\'input[name=\'data.email\']\') || document.querySelector(\'input[type=email]\') || document.querySelector(\'input[name*=\'email\']\');
                                        const resetUrl = \'' . filament()->getRequestPasswordResetUrl() . '\';
                                        if (emailInput && emailInput.value) {
                                            sessionStorage.setItem(\'forgot_password_email\', emailInput.value);
                                            window.location.href = resetUrl;
                                        } else {
                                            window.location.href = resetUrl;
                                        }
                                    "
                                    tabindex="3"> 
                                    {{ __("messages.forgot_password") }}
                                </x-filament::link>
                            ')) : null),
                        $this->getRememberFormComponent()->label(__('messages.common.remember_me')),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    /**
     * Override to support both email and username login
     */
    protected function getCredentialsFromFormData(array $data): array
    {
        $login = $data['email'] ?? null;
        
        if (!$login) {
            return [];
        }

        // Check if input is email or username
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        return [
            $field => $login,
            'password' => $data['password'] ?? null,
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getAuthenticateFormAction()
                ->extraAttributes(['class' => 'w-full flex items-center justify-center space-x-3'])
                ->label(__('messages.common.sign_in')),
        ];
    }


    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();

        if (isset($data['email']) && !empty($data['email'])) {
            $login = $data['email'];
            
            // Check if input is email or username
            $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            
            // Find user by email or username
            $user = $field === 'email' 
                ? User::whereEmail($login)->first()
                : User::where('username', $login)->first();
            
            if ($user) {
                if ($user->email_verified_at == null) {
                    Notification::make()
                        ->title(__('messages.placeholder.email_not_verified'))
                        ->danger()
                        ->send();

                    return null;
                }
                if ($user->status == false) {
                    Notification::make()
                        ->title(__('messages.placeholder.your_account_is_currently_disabled_please_contact_to_administrator'))
                        ->danger()
                        ->send();

                    return null;
                }
            }
        }

        $credentials = $this->getCredentialsFromFormData($data);
        
        // If username is used, manually authenticate
        if (isset($credentials['username'])) {
            $user = User::where('username', $credentials['username'])->first();
            
            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                $this->throwFailureValidationException();
            }
            
            Filament::auth()->login($user, $data['remember'] ?? false);
        } else {
            // Use default email authentication
            if (! Filament::auth()->attempt($credentials, $data['remember'] ?? false)) {
                $this->throwFailureValidationException();
            }
        }

        $user = Filament::auth()->user();

        if (
            ($user instanceof FilamentUser) &&
            (! $user->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::auth()->logout();

            $this->throwFailureValidationException();
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }
}
