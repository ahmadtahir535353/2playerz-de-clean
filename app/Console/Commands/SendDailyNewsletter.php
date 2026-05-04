<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\Subscriber;
use App\Mail\DailyNewsletterMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDailyNewsletter extends Command
{
    protected $signature = 'newsletter:send';
    protected $description = 'Send daily newsletter to all subscribers';

    public function handle()
    {
        $cutoff = now()->subMinutes(4);
        $todayPosts = Post::where('visibility', 1)
            ->whereDate('created_at', now()->toDateString())
            ->where('created_at', '<=', $cutoff)
            ->whereIn('post_types', ['1', '6']) // adjust as needed
            ->latest()
            ->get();


        if ($todayPosts->isEmpty()) {
            $this->info('No posts for today.');
            return;
        }

        $subscribers = Subscriber::all();

        foreach ($subscribers as $subscriber) {
            Mail::to($subscriber->email)->send(new DailyNewsletterMail($todayPosts, $subscriber->email));
        }

        $this->info('Newsletter sent to all subscribers.');
    }
}

