<?php

namespace App\Filament\Pages\Auth;
use Exception;
use Filament\Pages\Auth\PasswordReset\RequestPasswordReset;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Contracts\Auth\CanResetPassword;
use App\Notifications\ResetPasswordNotification;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;

class CustomRequestPasswordReset extends RequestPasswordReset
{
    /**
     * @var view-string
     */
    protected static string $view = 'filament.auth.password_reset';
    /**
     * @return array<int | string, string | Form>
     */
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getEmailFormComponent()
                            ->label(__('messages.mails.email_address') . ':')
                            ->validationAttribute(__('messages.mails.email_address'))
                            ->placeholder(__('messages.mails.email_address'))
                            ->default(null), // Will be filled by JavaScript from sessionStorage
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getRequestFormAction()
             ->extraAttributes(['class' => 'w-full flex items-center justify-center space-x-3'])
             ->label(__('messages.send_mail'))
        ];
    }

    public function request(): void
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::pages/auth/password-reset/request-password-reset.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(array_key_exists('body', __('filament-panels::pages/auth/password-reset/request-password-reset.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/password-reset/request-password-reset.notifications.throttled.body', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]) : null)
                ->danger()
                ->send();

            return;
        }

        $data = $this->form->getState();
        $status = Password::broker(Filament::getAuthPasswordBroker())->sendResetLink(
            $data,
            function (CanResetPassword $user, string $token): void {
                if (! method_exists($user, 'notify')) {
                    $userClass = $user::class;
                    throw new Exception("Model [{$userClass}] does not have a [notify()] method.");
                }

                $notification = new ResetPasswordNotification($token);
                $notification->url = Filament::getResetPasswordUrl($token, $user);
                $user->notify($notification);
            },
        );

        if ($status !== Password::RESET_LINK_SENT) {
            Notification::make()
                ->title(__($status))
                ->danger()
                ->send();

            return;
        }

        Notification::make()
            ->title(__($status))
            ->success()
            ->send();

        // Clear email from session and form after sending
        session()->forget('prefill_email');
        $this->form->fill();
    }


}
