<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProfileVisitNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $visitor;
    protected $profileOwner;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $visitor, User $profileOwner)
    {
        $this->visitor = $visitor;
        $this->profileOwner = $profileOwner;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('messages.notifications.profile_visit_subject'))
            ->greeting(__('messages.notifications.hello', ['name' => $notifiable->full_name]))
            ->line(__('messages.notifications.profile_visit_message', [
                'visitor' => $this->visitor->full_name,
                'profile' => $this->profileOwner->full_name
            ]))
            ->action(__('messages.notifications.view_profile'), route('user.profile', $this->visitor->username))
            ->line(__('messages.notifications.thank_you'));
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'profile_visit',
            'visitor_id' => $this->visitor->id,
            'visitor_name' => $this->visitor->full_name,
            'visitor_username' => $this->visitor->username,
            'visitor_profile_image' => $this->visitor->profile_image,
            'profile_owner_id' => $this->profileOwner->id,
            'message' => __('messages.notifications.profile_visit_message', [
                'visitor' => $this->visitor->full_name,
                'profile' => $this->profileOwner->full_name
            ]),
            'action_url' => route('user.profile', $this->visitor->username),
        ];
    }

    /**
     * Get the notification's database type.
     */
    public function databaseType(object $notifiable): string
    {
        return 'profile_visit';
    }
}