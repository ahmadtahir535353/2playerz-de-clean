<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Lang;
use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;

class UserEmailVerification extends BaseVerifyEmail
{
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        return (new MailMessage)
            ->subject(__('messages.mails.verify_email'))
            ->view('emails.verify_email', ['url' => $verificationUrl]);
    }
}
