<?php

// use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FollowersController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\CustomerProfileController;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\CronJobController;
use App\Http\Controllers\ProxyController;
use Filament\Http\Controllers\Auth\EmailVerificationController;
use Filament\Http\Controllers\Auth\LogoutController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
// use App\Http\Controllers\SocialiteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\FcmController;
use App\Models\UserFcmToken;
use App\Services\FcmService;
// use App\Mail\DailyNewsletterMail;
// use App\Models\Post;
// use App\Models\Subscriber;
Route::get('/run-commands', function () {
    try {
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');
        Artisan::call('optimize:clear');
        // composer dump-autoload shared hosting par skip ho jata hai mostly
        return '✅ Laravel optimization commands executed successfully.';
    } catch (\Exception $e) {
        return '⚠️ Error: ' . $e->getMessage();
    }
});
// Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirect'])
//     ->name('socialite.redirect');
// Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback'])
//     ->name('socialite.callback');

Route::get('/customer.profile', function () {
    return redirect('/');
});

Route::post('/set-theme', function (Illuminate\Http\Request $request) {
    session(['theme' => $request->theme]);
    return response()->json([
        'status' => 'ok',
        'theme' => $request->theme
    ]);
})->name('set-theme');

Route::get('/clear', function () {

    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    echo "Cleared";
});



Route::middleware(['auth', 'setLanguage', 'xss'])->group(function () {
    Route::get('/customers', [CustomerProfileController::class, 'showProfile'])->name('customer.profile');
    Route::get('/customers/profile/edit', [CustomerProfileController::class, 'edit'])->name('customer.profile.edit');
    Route::put('/customers/profile/update', [CustomerProfileController::class, 'update'])->name('customer.profile.update');
    Route::get('/customers/my-comments', [CustomerProfileController::class, 'myComments'])->name('customer.profile.comments');
    Route::get('/customers/notifications', [CustomerProfileController::class, 'notification'])->name('customer.profile.notification');
    Route::get('/customers/notifications', [CustomerProfileController::class, 'notification'])->name('notifications');
    Route::get('/customers/notifications/{id}/read', [CustomerProfileController::class, 'markAsRead'])->name('notifications.read');
    Route::delete('/notifications/{id}/delete', [CustomerProfileController::class, 'deleteNotification'])->name('notifications.delete');
    Route::delete('/notifications/delete-all', [CustomerProfileController::class, 'deleteAllNotifications'])->name('notifications.delete-all');
    Route::get('/customers/members-following', [CustomerProfileController::class, 'membersFollowing'])->name('members.following');
    
    // Profile Visitor Routes
    Route::get('/customers/my-profile-visitors', [App\Http\Controllers\ProfileVisitorController::class, 'index'])
        ->name('profile.visitors');
    Route::get('/customers/api/visitor-count', [App\Http\Controllers\ProfileVisitorController::class, 'getVisitorCount'])
        ->name('api.visitor-count');
    Route::get('/customers/api/recent-visitors', [App\Http\Controllers\ProfileVisitorController::class, 'getRecentVisitors'])
        ->name('api.recent-visitors');

    // Messaging Routes
    Route::get('/messages', [App\Http\Controllers\MessagingController::class, 'index'])->name('messages.index');
    Route::post('/messages', [App\Http\Controllers\MessagingController::class, 'store'])->name('messages.store');
    Route::get('/messages/user/{user}', [App\Http\Controllers\MessagingController::class, 'getConversationWithUser'])->name('messages.user');
    Route::get('/messages/{conversation}', [App\Http\Controllers\MessagingController::class, 'show'])->name('messages.show');
    Route::put('/messages/{message}', [App\Http\Controllers\MessagingController::class, 'update'])->name('messages.update');
    Route::delete('/messages/{message}', [App\Http\Controllers\MessagingController::class, 'destroy'])->name('messages.destroy');
    Route::delete('/messages/conversation/{conversation}/delete', [App\Http\Controllers\MessagingController::class, 'deleteConversation'])->name('messages.conversation.delete');
    Route::get('/messages/{conversation}/check-new', [App\Http\Controllers\MessagingController::class, 'checkNewMessages'])->name('messages.check-new');
    Route::get('/messages/{conversation}/load-older', [App\Http\Controllers\MessagingController::class, 'loadOlderMessages'])->name('messages.load-older');
    Route::get('/messages/conversations/load-older', [App\Http\Controllers\MessagingController::class, 'loadOlderConversations'])->name('messages.conversations.load-older');

    // Wishlist (Meine Wunschliste)
    Route::get('/customers/wishlist', [App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/remove', [App\Http\Controllers\WishlistController::class, 'remove'])->name('wishlist.remove');
    Route::post('/wishlist/clear-highlight', [App\Http\Controllers\WishlistController::class, 'clearHighlight'])->name('wishlist.clear-highlight');
});
Route::post('/wishlist/toggle', [App\Http\Controllers\WishlistController::class, 'toggle'])->name('wishlist.toggle')->middleware('setLanguage');
// Route::get('/user/{id}/profile', [CustomerProfileController::class, 'publicProfile'])->name('user.public.profile')->middleware('setLanguage');
// Route::get('/user/{id}/comments', [CustomerProfileController::class, 'publicComments'])->name('user.public.comments')->middleware('setLanguage');
Route::get('/user/{identifier}/profile', [CustomerProfileController::class, 'publicProfile'])->name('user.public.profile')->middleware('setLanguage');
Route::get('/user/{identifier}/comments', [CustomerProfileController::class, 'publicComments'])->name('user.public.comments')->middleware('setLanguage');
// News Sitemap Route - View only (read-only) - Exclude middleware that might inject scripts
Route::get('/news-sitemap.xml', [App\Http\Controllers\SitemapController::class, 'news'])
    ->name('sitemap.news')
    ->withoutMiddleware([\App\Http\Middleware\XSS::class, \App\Http\Middleware\Analytics::class]);

// Google News Sitemap Generation Route for Cronjob
Route::get('/news-sitemap', [App\Http\Controllers\SitemapController::class, 'generateNewsSitemap'])
    ->name('sitemap.generate.news');

// Regular Sitemap Generation Route for Cronjob
Route::get('/generate-sitemap', [App\Http\Controllers\SitemapController::class, 'generate'])
    ->name('sitemap.generate');

// Follow/Unfollow and Block/Unblock routes for user profiles
Route::middleware(['auth', 'setLanguage'])->group(function () {
    // Route::get('/user/{id}/follow', [CustomerProfileController::class, 'followUser'])->name('user.follow');
    // Route::get('/user/{id}/unfollow', [CustomerProfileController::class, 'unfollowUser'])->name('user.unfollow');
    // Route::post('/user/{id}/block', [CustomerProfileController::class, 'blockUser'])->name('user.block');
    // Route::post('/user/{id}/unblock', [CustomerProfileController::class, 'unblockUser'])->name('user.unblock');
    Route::get('/user/{identifier}/follow', [CustomerProfileController::class, 'followUser'])->name('user.follow');
    Route::get('/user/{identifier}/unfollow', [CustomerProfileController::class, 'unfollowUser'])->name('user.unfollow');
    Route::post('/user/{identifier}/block', [CustomerProfileController::class, 'blockUser'])->name('user.block');
    Route::post('/user/{identifier}/unblock', [CustomerProfileController::class, 'unblockUser'])->name('user.unblock');
 
    Route::get('/blocked-members', [CustomerProfileController::class, 'blockedMembers'])->name('blocked.members');
});
Route::post('/logout', function (Request $request) {
    if (Auth::check()) {
        $user = Auth::user();
        $timeZone = $request->input('time_zone', 'UTC'); // Default UTC agar time zone nahi milta
        $user->last_seen_at = now()->setTimezone($timeZone);
        $user->save();
    }
    Auth::logout();
    return redirect('/');
})->name('logout');

Route::get('/customer-panel/notifications', function () {
    return view('customer-panel.notifications');
});



Route::get('/customer-panel/user-profile', function () {
    return view('customer-panel.user-profile');
});


// Route::get('/', function () {
//     return view('welcome');
// });
// Route::get('/login', function () {
//     return redirect('admin/login');
// })->name('login');
// Route::get('/logout', [LogoutController::class, '__invoke'])->name('logout');

// Route::get('email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('filament.admin.auth.email-verification.verify');
// Route::get('email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify');


// subscription controler

Route::post('stripe/subscription-purchase', [StripeController::class, 'purchase'])->name('stripe.purchase');
Route::post(
    'purchase-subscription',
    [SubscriptionController::class, 'purchaseSubscription']
)->name('purchase-subscription');
Route::get('stripe-success', [SubscriptionController::class, 'success'])->name('stripe.success');
Route::get('stripe-failed', [SubscriptionController::class, 'failed'])->name('stripe.failed');

//paypal
Route::post('paypal-onboard', [PaypalController::class, 'onBoard'])->name('paypal.init');
Route::get('paypal-payment-success', [PaypalController::class, 'success'])->name('paypal.success');
Route::get('paypal-payment-failed', [PaypalController::class, 'failed'])->name('paypal.failed');

//manual
Route::post(
    'subscription-purchase/{plan}/manual',
    [SubscriptionController::class, 'manualPay']
)->name('subscription.manual');

Route::get('/download-attachment/{id}', [SubscriptionController::class, 'downloadAttachment'])->name('download.attachment');


//front landing ui

Route::middleware('xss', 'setLanguage')->group(function () {
    Route::get('/notification/mark-as-read/{notification}', [LandingPageController::class, 'markAsRead'])
        ->name('notification.markAsRead');
    Route::get('/notification/unread-count', [LandingPageController::class, 'getUnreadCount'])
        ->name('notification.unreadCount')
        ->middleware('auth');
    Route::get('/', [LandingPageController::class, 'index'])->name('front.home');
    Route::post('/like-toggle', [LandingPageController::class, 'toggleLike'])->name('like-toggle');
    Route::post('/comments', [LandingPageController::class, 'saveCommentsUser'])->name('comment.store');
    Route::delete('/comments/{comment}', [LandingPageController::class, 'destroyComment'])->name('comment.destroy');
    Route::post('/comment/{id}/edit', [LandingPageController::class, 'editComment']);
    Route::post('/comment/report', [LandingPageController::class, 'reportComment'])->name('comment.report');
    Route::post('subscribe', [LandingPageController::class, 'saveSubscribeUser'])->name('subscribe.store');
    Route::post('language-change-home', [LandingPageController::class, 'detailPage'])->name('language.change.home');

    Route::get('p', [LandingPageController::class, 'allPosts'])->name('allPosts');
    Route::get('p/{data}', [LandingPageController::class, 'detailPage'])->name('detailPage')->middleware('analytic');
    Route::get('p/{data}/{id}', [LandingPageController::class, 'detailPage'])->name('detailPage.gallery');
    Route::get('c/{category?}/{slug?}', [LandingPageController::class, 'categoryPage'])->name('categoryPage');
    Route::get('t/{tag}', [LandingPageController::class, 'popularTagPage'])->name('popularTagPage');
    Route::get('/g/{id?}', [LandingPageController::class, 'galleryPage'])->name('galleryPage');
    Route::post('audio-detail-page', [LandingPageController::class, 'audioDetails'])->name('audioDetailPage');

    Route::get('/terms-conditions', [LandingPageController::class, 'displayTerms'])->name('page.Terms');
    Route::get('/support', [LandingPageController::class, 'displayTerms'])->name('page.support');
    Route::get('/privacy', [LandingPageController::class, 'displayTerms'])->name('page.privacy');

    Route::get('/contact-save', [ContactController::class, 'store'])->name('contact.store');
    Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');

    Route::get('profile/{user}', [LandingPageController::class, 'profileDashboard'])->name('userDetails');

    Route::get('follow/{user}', [FollowersController::class, 'store'])->name('followUser');
    Route::get('nu-follow/{user}', [FollowersController::class, 'unFollow'])->name('UnFollowUser');

    //vote poll route
    Route::post('vote-poll', [PollController::class, 'votePoll'])->name('vote.poll');

    //pages
    Route::get('page/{slug}', [PageController::class, 'showPageSlug'])->name('pages.show-page-slug');

    // Release Calendar Routes
    Route::get('releasekalender', [App\Http\Controllers\ReleaseCalendarController::class, 'index'])->name('release-calendar.all');
    Route::get('playstation/release-liste', [App\Http\Controllers\ReleaseCalendarController::class, 'playstation'])->name('release-calendar.playstation');
    Route::get('xbox/release-liste', [App\Http\Controllers\ReleaseCalendarController::class, 'xbox'])->name('release-calendar.xbox');
    Route::get('nintendo/release-liste', [App\Http\Controllers\ReleaseCalendarController::class, 'nintendo'])->name('release-calendar.nintendo');

    // Player Rankings Page
    Route::get('playerz-ranking', [LandingPageController::class, 'playerzRanking'])->name('playerz.ranking');

    //reaction
    Route::post('post-reaction', [LandingPageController::class, 'postReaction'])->name('post-reaction');


    Route::get('cookie', [LandingPageController::class, 'declineCookie'])->name('declineCookie');

    Route::get('change-theme', [LandingPageController::class, 'themeChange'])->name('themeChange');
    Route::post('p/fetch', [LandingPageController::class, 'getComments'])->name('comments.page');

});

Route::get('news-letter', function(){
 return view('emails.news-letter');
});



// Route::get('/send-test-newsletter', function () {
//     $posts = Post::where('status', 1)
//         ->whereDate('published_at', now()->toDateString())
//         ->whereIn('post_types', ['1', '6']) // Adjust based on your DB values
//         ->latest()
//         ->take(5)
//         ->get();

//     if ($posts->isEmpty()) {
//         return "⚠️ No posts found for today!";
//     }

//     $subscriber = Subscriber::first(); // Test for first subscriber

//     if (!$subscriber) {
//         return "⚠️ No subscriber found! Please add one in DB.";
//     }

//     Mail::to($subscriber->email)->send(new DailyNewsletterMail($posts, $subscriber->email));

//     return "✅ Newsletter sent to: {$subscriber->email}";
// });

Route::fallback(function () {
    return view('errors/404');
});



Route::get('/login/{provider}', [SocialAuthController::class, 'redirectToSocial'])->name('social.login')->middleware('setLanguage');
Route::get('/login/{provider}/callback', [SocialAuthController::class, 'handleSocialCallback'])->middleware('setLanguage');

// Google Search Console OAuth
Route::get('/gsc/callback', [\App\Http\Controllers\GoogleSearchConsoleController::class, 'handleCallback'])
    ->middleware(['web', 'auth'])
    ->name('gsc.callback');


//cron job 
Route::get('/publish-schedule-posts', [CronJobController::class, 'PublishScheduledPosts'])->withoutMiddleware([Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
Route::get('/send-news-letters', [CronJobController::class, 'sendNewsLetters'])->withoutMiddleware([Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
Route::get('/cron/live-indicator', [CronJobController::class, 'disableExpiredLiveIndicators']);
Route::get('/fetch-gsc-data', [CronJobController::class, 'fetchGSCData'])->withoutMiddleware([Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
Route::get('/fetch-bing-data', [CronJobController::class, 'fetchBingData'])->withoutMiddleware([Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
Route::get('/wishlist-notify-releases', [CronJobController::class, 'wishlistNotifyReleases'])->withoutMiddleware([Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);


Route::get('/newsletter/unsubscribe', function (\Illuminate\Http\Request $request) {
    $email = $request->query('email');

    if ($email) {
        Subscriber::where('email', $email)->delete();

        return redirect('/')
            ->with('message', __('messages.other_lang.unsubscribed_success_message'));
    }

    return redirect('/')
        ->with('message', 'Invalid unsubscribe request.');
});




include 'auth.php';
require __DIR__ . '/upgrade.php';
// Route::get('/p/{slug}/comments', [LandingPageController::class, 'getComments'])->name('post.comments');
Route::post('/notifications/mark-all-seen', function () {
    DB::table('notifications')
        ->where('to_user_id', auth()->id())
        ->whereNull('read_at')
        ->update(['read_at' => now()]);
    
    return response()->json(['success' => true]);
})->middleware('auth');



Route::post('/proxy-fetch', [ProxyController::class, 'fetchUrl']);

Route::middleware('auth')->group(function () {
    Route::post('/fcm/save-token', [FcmController::class, 'saveToken'])->name('fcm.save-token');
    Route::delete('/fcm/delete-token', [FcmController::class, 'deleteToken'])->name('fcm.delete-token');

    // Quick test sender
    Route::get('/fcm/test', function () {
        $tokens = UserFcmToken::where('user_id', auth()->id())->pluck('token');
        $svc = app(FcmService::class);
        foreach ($tokens as $t) {
            $svc->sendToToken($t, 'Hello 👋', 'This is a test push', [
                'link' => url('/'),
                'type' => 'test',
            ]);
        }
        return 'OK';
    })->name('fcm.test');
});

Route::post('/save-fcm-token', function (Request $request) {
    $request->validate([
        'token' => 'required|string',
        'device' => 'nullable|string',
        'user_id' => 'nullable|integer',
    ]);

    UserFcmToken::updateOrCreate(
        ['token' => $request->token],
        [
            'user_id' => $request->user_id, // null bhi ho sakta hai
            'device'  => $request->device ?? $request->header('User-Agent'),
        ]
    );

    return response()->json(['success' => true]);
});
Route::post('/vote', function () {
    $user = auth()->user();
    if ($user) {
        $user->increment('comment_points', 5);
        return response()->json(['message' => '5 points added!']);
    }
    return response()->json(['message' => 'Not logged in'], 401);
})->name('user.vote');

Route::middleware(['auth'])->group(function () {
    Route::get('/posts/{slug}', [LandingPageController::class, 'postPreview'])
    ->name('admin.posts.preview');
    
        
// Broadcasting routes
Route::post('/broadcasting/auth', function () {
    return response()->json(['success' => true]);
});

// Test route for notifications (remove after testing)
Route::get('/test-notification', function () {
    $user = \App\Models\User::first();
    if ($user) {
        \Illuminate\Support\Facades\DB::table('notifications')->insert([
            'type' => 'App\\Notifications\\PrivateMessageNotification',
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id' => $user->id,
            'to_user_id' => $user->id,
            'from_user_id' => 1,
            'post_id' => null,
            'data' => json_encode([
                'message' => 'Test user sent you a private message: "This is a test message"',
                'message_id' => 999,
                'conversation_id' => 1,
                'sender_name' => 'Test User',
                'sender_username' => 'testuser',
                'sender_profile_image' => null,
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return 'Test notification created for user: ' . $user->username;
    }
    return 'No users found';
});
});