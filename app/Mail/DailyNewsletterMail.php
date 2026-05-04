<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyNewsletterMail extends Mailable
{
    use Queueable, SerializesModels;
    
     public $posts;

    public $email;

    public $subject;

    /**
     * Create a new message instance.
     */
    public function __construct($posts, $email) {
        $this->posts = $posts;
        $this->email = $email;
        
    }

    public function build()
    {
        $unsubscribeUrl = url('/newsletter/unsubscribe?email=' . urlencode($this->email));
        $baseUrl = config('app.url', 'https://2playerz.de');
        
        $mailable = $this->subject('2Playerz – Täglicher Newsletter')
            ->from('newsletter@2playerz.de', '2Playerz Newsletter')
            ->replyTo('redaktion@2playerz.email', '2Playerz Redaktion')
            ->view('emails.news-letter')->with([
                'posts' => $this->posts,
                'email' => $this->email,
                'unsubscribeUrl' => $unsubscribeUrl,
            ]);
        
        // Add comprehensive headers to avoid spam/dangerous warnings
        $mailable->withSymfonyMessage(function ($message) use ($unsubscribeUrl, $baseUrl) {
            // Force set from address to ensure it's correct
            $message->from(new \Symfony\Component\Mime\Address('newsletter@2playerz.de', '2Playerz Newsletter'));
            $message->replyTo(new \Symfony\Component\Mime\Address('redaktion@2playerz.email', '2Playerz Redaktion'));
            
            $headers = $message->getHeaders();
            
            // Required unsubscribe headers (Gmail requirement - MUST have)
            $headers->add(new \Symfony\Component\Mime\Header\UnstructuredHeader('List-Unsubscribe', '<' . $unsubscribeUrl . '>'));
            $headers->add(new \Symfony\Component\Mime\Header\UnstructuredHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click'));
            
            // Bulk email indicators
            $headers->add(new \Symfony\Component\Mime\Header\UnstructuredHeader('Precedence', 'bulk'));
            $headers->add(new \Symfony\Component\Mime\Header\UnstructuredHeader('X-Auto-Response-Suppress', 'All'));
            
            // Mailer identification
            $headers->add(new \Symfony\Component\Mime\Header\UnstructuredHeader('X-Mailer', '2Playerz Newsletter System v1.0'));
            
            // Organization and entity identification
            $headers->add(new \Symfony\Component\Mime\Header\UnstructuredHeader('X-Entity-Ref-ID', '2playerz-newsletter-' . time()));
            $headers->add(new \Symfony\Component\Mime\Header\UnstructuredHeader('Organization', '2Playerz - Gaming Magazine'));
        });
        
        return $mailable;
    }
}
