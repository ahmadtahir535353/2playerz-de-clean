<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AggregatedFollowingActivityNotification extends Notification
{
    use Queueable;

    protected $count;
    protected $memberIds;
    protected $activityType;

    /**
     * Create a new notification instance.
     */
    public function __construct($count, $memberIds, $activityType = 'comment')
    {
        $this->count = $count;
        $this->memberIds = $memberIds;
        $this->activityType = $activityType;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        $message = "{$this->count} Mitglieder, denen du folgst, haben neue Kommentare gepostet.";
        
        return [
            'message' => $message,
            'count' => $this->count,
            'member_ids' => $this->memberIds,
            'activity_type' => $this->activityType,
        ];
    }
}

