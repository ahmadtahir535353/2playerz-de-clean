<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Scopes\AuthoriseUserActivePostScope;
use App\Scopes\LanguageScope;
use App\Scopes\PostDraftScope;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Subscriber;
use App\Mail\DailyNewsletterMail;
use Illuminate\Support\Facades\Mail;
 use App\Models\MailSetting;
use Illuminate\Support\Facades\Config;
use App\Notifications\EmailNotification;
use Illuminate\Support\Facades\DB;
use App\Models\Language;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use App\Models\GameRelease;
use App\Services\WishlistNotificationService;

class CronJobController extends Controller
{
    
   

public function sendNewsLetters()
{
    // Step 1: Setup mail config for newsletter (using newsletter@2playerz.de)
    $mailData = MailSetting::first();
    
    if (!$mailData) {
        return response()->json(['status' => 'error', 'message' => 'Mail settings not found.']);
    }
    
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
    
    // Use newsletter@2playerz.de as from address
    $fromAddress = 'newsletter@2playerz.de';
    $fromName = '2Playerz Newsletter';
    
    config([
        'mail.default' => $protocol,
        "mail.mailers.$protocol.transport" => $protocol,
        "mail.mailers.$protocol.host" => $host,
        "mail.mailers.$protocol.port" => $mailData->mail_port,
        "mail.mailers.$protocol.encryption" => MailSetting::ENCRYPTION_TYPE[$mailData->encryption],
        "mail.mailers.$protocol.username" => $mailData->mail_username,
        "mail.mailers.$protocol.password" => $mailData->mail_password,

        'mail.from.address' => $fromAddress,
        'mail.from.name' => $fromName,
    ]);


    // Step 2: Get posts (only today's posts)
    $todayPosts = Post::where('visibility', 1)
        ->whereIn('post_types', ['1', '6']) // adjust types as needed
        ->whereDate('created_at', Carbon::today()->toDateString())
        ->latest()
        ->get();


    if ($todayPosts->isEmpty()) {
        return response()->json(['status' => 'no-posts', 'message' => 'No posts found today.']);
    }

    // Step 3: Set locale for translations
    $langId = getSettingValue()['front_language'] ?? 5; // Default to German (id: 5) if not set
    $language = Language::find($langId);
    if ($language) {
        App::setLocale($language->iso_code);
    } else {
        // Fallback to German if language not found
        App::setLocale('de');
    }

    // Step 4: Get subscribers
    $subscribers = Subscriber::all();

    if ($subscribers->isEmpty()) {
        return response()->json(['status' => 'no-subscribers', 'message' => 'No subscribers found.']);
    }

    // Step 5: Send to all subscribers with delays to avoid spam filters and SMTP limits
    $sentCount = 0;
    $failedCount = 0;
    $totalSubscribers = $subscribers->count();

    foreach ($subscribers as $subscriber) {
        try {
            Mail::to($subscriber->email)->send(new DailyNewsletterMail($todayPosts, $subscriber->email));
            $sentCount++;
            \Log::info('Newsletter sent successfully to: ' . $subscriber->email);

            if ($sentCount < $totalSubscribers) {
                sleep(3); // 3 second delay between emails
            }
        } catch (\Exception $e) {
            $failedCount++;
            \Log::error('Failed to send newsletter to ' . $subscriber->email . ': ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());
            continue;
        }
    }

    return response()->json([
        'status' => 'success',
        'message' => "Newsletter sent successfully. Sent: {$sentCount}, Failed: {$failedCount}",
        'sent' => $sentCount,
        'failed' => $failedCount,
        'total' => $totalSubscribers,
    ]);
}

    public function PublishScheduledPosts(){
        \Log::info('PublishScheduledPosts called at ' . Carbon::now());
        $posts = Post::withoutGlobalScope(AuthoriseUserActivePostScope::class)
            ->withoutGlobalScope(LanguageScope::class)
            ->withoutGlobalScope(PostDraftScope::class)
            ->whereStatus(0)
            ->where('scheduled_post', 1)
            ->where('scheduled_post_time', '!=', null)
            ->get();

        $updated = 0;
        foreach ($posts as $post) {
            \Log::info('Checking post ID: ' . $post->id . ' with scheduled time: ' . $post->scheduled_post_time);
            if (Carbon::parse($post->scheduled_post_time) <= Carbon::now()) {
                $post->update(['status' => 1, 'scheduled_post' => 0, 'scheduled_post_time' => null, 'visibility' => 1]);
                
                // Update user's last_seen_at and last_activity_at when scheduled post is published
                $user = \App\Models\User::find($post->created_by);
                if ($user) {
                    $user->update([
                        'last_seen_at' => now('Europe/Berlin'),
                        'last_activity_at' => now('Europe/Berlin')
                    ]);
                }
                
                \Log::info('Post ID: ' . $post->id . ' published successfully');
                $updated++;
            }
        }
        return response()->json(['message' => "$updated posts updated successfully"]);
    }

    public function disableExpiredLiveIndicators()
    {
        $now = now('Europe/Berlin');

        // Agar DB me UTC store karte ho, to:
        // $now = now('UTC');

        $updated = DB::table('liveticker_contents')
            ->where('live_indicator_enabled', 1)
            ->where('live_indicator_until', '<=', $now)
            ->update([
                'live_indicator_enabled' => 0,
                'updated_at' => now(), // optional
            ]);

        \Log::info('[cron] live-indicator disable', [
            'now' => $now->toDateTimeString(),
            'updated' => $updated,
        ]);

        return response()->json([
            'ok'      => true,
            'updated' => $updated,
            'now'     => $now->toDateTimeString(),
        ]);
    }

    /**
     * Fetch Google Search Console data via cron job
     */
    public function fetchGSCData()
    {
        \Log::info('GSC Data Fetch Cron Job started at ' . now());

        try {
            // Run the artisan command
            Artisan::call('gsc:fetch-data', [
                '--days' => 90, // Fetch last 90 days of data
            ]);

            $output = Artisan::output();
            
            \Log::info('GSC Data Fetch completed', [
                'output' => $output,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Google Search Console data fetched successfully',
                'output' => $output,
                'timestamp' => now()->toDateTimeString(),
            ]);
        } catch (\Exception $e) {
            \Log::error('GSC Data Fetch Cron Job Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch GSC data: ' . $e->getMessage(),
                'timestamp' => now()->toDateTimeString(),
            ], 500);
        }
    }

    /**
     * Fetch Bing Webmaster data via cron job (same pattern as fetch-gsc-data).
     * Call via cron: GET /fetch-bing-data
     */
    public function fetchBingData()
    {
        \Log::info('Bing Data Fetch Cron Job started at ' . now());

        try {
            Artisan::call('bing:fetch-data', [
                '--days' => 90,
            ]);

            $output = Artisan::output();

            \Log::info('Bing Data Fetch completed', [
                'output' => $output,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Bing Webmaster data fetched successfully',
                'output' => $output,
                'timestamp' => now()->toDateTimeString(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Bing Data Fetch Cron Job Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch Bing data: ' . $e->getMessage(),
                'timestamp' => now()->toDateTimeString(),
            ], 500);
        }
    }

    /**
     * Wishlist release-day notifications: notify users when a game from their wishlist releases today.
     * Call via cron e.g. daily at 08:00: GET /wishlist-notify-releases
     */
    public function wishlistNotifyReleases(WishlistNotificationService $service)
    {
        \Log::info('Wishlist notify-releases cron started at ' . now());

        try {
            $today = now()->toDateString();
            $games = GameRelease::whereDate('release_date', $today)->get();

            foreach ($games as $game) {
                $service->notifyReleaseToday($game);
            }

            \Log::info('Wishlist notify-releases completed', ['games_count' => $games->count()]);

            return response()->json([
                'status' => 'success',
                'message' => 'Wishlist release notifications processed.',
                'games_count' => $games->count(),
                'timestamp' => now()->toDateTimeString(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Wishlist notify-releases cron error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'timestamp' => now()->toDateTimeString(),
            ], 500);
        }
    }

}
