<?php

namespace App\Notifications;

use Filament\Facades\Filament;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\MailSetting;
use Lang;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private readonly string $token)
    {

    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Setup mail config dynamically (same as other notifications)
        $mailData = MailSetting::first();
        if ($mailData) {
            $protocol = MailSetting::TYPE[$mailData->mail_protocol];
            $host = $mailData->mail_host;

            if ($mailData->mail_protocol == MailSetting::MAIL_LOG) {
                $protocol = 'log';
                $host = 'mailhog';
            }
            if ($mailData->mail_protocol == MailSetting::SMTP) {
                $protocol = 'smtp';
            }
            if ($mailData->mail_protocol == MailSetting::SENDGRID) {
                $protocol = 'sendgrid';
            }

            config([
                'mail.default' => $protocol,
                "mail.mailers.$protocol.transport" => $protocol,
                "mail.mailers.$protocol.host" => $host,
                "mail.mailers.$protocol.port" => $mailData->mail_port,
                "mail.mailers.$protocol.encryption" => MailSetting::ENCRYPTION_TYPE[$mailData->encryption],
                "mail.mailers.$protocol.username" => $mailData->mail_username,
                "mail.mailers.$protocol.password" => $mailData->mail_password,
                'mail.from.address' => $mailData->reply_to,
                'mail.from.name' => $mailData->mail_title,
            ]);
        }
        
        $resetUrl = $this->resetUrl($notifiable);
        
        return (new MailMessage)
            ->subject(__('messages.mails.reset_password_notification'))
            ->view('emails.reset_password_mail', ['url' => $resetUrl]);
    }

    protected function resetUrl(mixed $notifiable): string
    {
        return Filament::getResetPasswordUrl($this->token, $notifiable);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
