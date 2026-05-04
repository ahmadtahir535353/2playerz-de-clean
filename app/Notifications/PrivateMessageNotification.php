<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class PrivateMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $sender;
    protected $message;
    protected $conversation;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $sender, Message $message)
    {
        $this->sender = $sender;
        $this->message = $message;
        $this->conversation = $message->conversation;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        // Check user's notification preference
        if ($notifiable->getMessageNotificationPreference() === 'email_and_notification') {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        \Log::info('PrivateMessageNotification toMail called', [
            'recipient_id' => $notifiable->id,
            'recipient_email' => $notifiable->email,
            'message_id' => $this->message->id,
            'preference' => $notifiable->getMessageNotificationPreference()
        ]);

        // Setup mail config dynamically (same as newsletter system)
        // This is needed because notification might be queued and config might be reset
        $mailData = \App\Models\MailSetting::first();
        if ($mailData) {
            $protocol = \App\Models\MailSetting::TYPE[$mailData->mail_protocol];
            $host = $mailData->mail_host;

            if ($mailData->mail_protocol == \App\Models\MailSetting::MAIL_LOG) {
                $protocol = 'log';
                $host = 'mailhog';
            }
            if ($mailData->mail_protocol == \App\Models\MailSetting::SMTP) {
                $protocol = 'smtp';
            }
            if ($mailData->mail_protocol == \App\Models\MailSetting::SENDGRID) {
                $protocol = 'sendgrid';
            }

            config([
                'mail.default' => $protocol,
                "mail.mailers.$protocol.transport" => $protocol,
                "mail.mailers.$protocol.host" => $host,
                "mail.mailers.$protocol.port" => $mailData->mail_port,
                "mail.mailers.$protocol.encryption" => \App\Models\MailSetting::ENCRYPTION_TYPE[$mailData->encryption],
                "mail.mailers.$protocol.username" => $mailData->mail_username,
                "mail.mailers.$protocol.password" => $mailData->mail_password,
                'mail.from.address' => $mailData->reply_to,
                'mail.from.name' => $mailData->mail_title,
            ]);

            \Log::info('Mail config set in toMail', [
                'protocol' => $protocol,
                'host' => $host,
                'from_address' => $mailData->reply_to
            ]);
        } else {
            \Log::warning('MailSetting not found in toMail');
        }

        try {
            $mailMessage = (new MailMessage)
                ->subject(__('messages.other_lang.private_message_subject', ['sender' => $this->sender->username ?? $this->sender->full_name]))
                ->view('emails.private_message', [
                    'sender' => $this->sender,
                    'message' => $this->message,
                    'notifiable' => $notifiable,
                    'subject' => __('messages.other_lang.private_message_subject', ['sender' => $this->sender->username ?? $this->sender->full_name])
                ]);

            \Log::info('MailMessage created successfully', ['message_id' => $this->message->id]);
            return $mailMessage;
        } catch (\Exception $e) {
            \Log::error('Error creating MailMessage', [
                'error' => $e->getMessage(),
                'message_id' => $this->message->id,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->sender->username . ' sent you a private message: "' . \Str::limit($this->message->message, 50) . '"',
            'message_id' => $this->message->id,
            'conversation_id' => $this->conversation->id,
            'sender_name' => $this->sender->full_name,
            'sender_username' => $this->sender->username,
            'sender_profile_image' => $this->sender->profile_image,
        ];
    }

    /**
     * Store notification in the custom notifications table
     */
    public function toDatabase($notifiable)
    {
        return [
            'type' => 'App\\Notifications\\PrivateMessageNotification',
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id' => $notifiable->id,
            'to_user_id' => $notifiable->id,
            'from_user_id' => $this->sender->id,
            'post_id' => null, // No post for private messages
            'data' => json_encode([
                'message' => $this->sender->username . ' sent you a private message: "' . \Str::limit($this->message->message, 50) . '"',
                'message_id' => $this->message->id,
                'conversation_id' => $this->conversation->id,
                'sender_name' => $this->sender->full_name,
                'sender_username' => $this->sender->username,
                'sender_profile_image' => $this->sender->profile_image,
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Get the notification's database type.
     */
    public function databaseType(object $notifiable): string
    {
        return 'private_message';
    }
}
