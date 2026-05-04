<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailNotification extends Notification
{
    use Queueable;

    protected $subject;
    protected $mail_body;
    protected $template_name;
    protected $is_admin = false;
    protected $email;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($subject, $mail_body, $template_name = 'review-request', $email = '', $is_admin = false)
    {

        $this->subject = $subject;
        $this->mail_body = $mail_body;
        $this->template_name = $template_name;
        $this->email = $email;

        if ($is_admin == 1) {
            $this->is_admin = true;
        }
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mailMessage = (new MailMessage)
            ->subject($this->subject)
            ->view('emails.' . $this->template_name, ['posts' => $this->mail_body, 'subject' => $this->subject, 'email' => $this->email]);

        // For newsletters, pass unsubscribe URL to template
        if ($this->template_name === 'news-letter' && !empty($this->email)) {
            $unsubscribeUrl = url('/newsletter/unsubscribe?email=' . urlencode($this->email));
            $mailMessage->with([
                'unsubscribeUrl' => $unsubscribeUrl,
            ]);
        }

//        if ($this->is_admin) {
        //    $mailMessage->to('zohaibali890385@gmail.com');  // Send to the specified recipient email
//        }

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
