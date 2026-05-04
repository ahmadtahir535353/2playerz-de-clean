<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Plan;
use App\Models\PointRule;
use App\Models\Role;
use App\Models\User;
use Laracasts\Flash\Flash;
use Illuminate\Support\Str;
use App\Models\Subscription;
use App\Models\SocialAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Filament\Notifications\Notification;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class SocialAuthController extends Controller
{
    public function redirectToSocial($provider): RedirectResponse
    {
        return Socialite::driver($provider)
            ->redirect();
    }

    public function handleSocialCallback($provider): \Illuminate\Http\RedirectResponse
    {
        if (Auth::check()) {
            return redirect('/');
        }

        if(!$provider){
            return redirect(route('filament.auth.auth.login'));
        }

        if (request()->has('error') || request()->get('error_code') == 200) {
            Notification::make()
                ->title(__('messages.Login_cancelled_or_denied_by_user'))
                ->danger()
                ->send();
            Flash::error('Login cancelled or denied by user.');
            return redirect()->route('filament.auth.auth.login');
        }

        $socialUser = Socialite::driver($provider)->user();

        if (empty($socialUser['email'])) {
            Notification::make()
                ->title(__('messages.your_facebook_account_does_not_have_attached_email'))
                ->danger()
                ->send();
            Flash::error(__('messages.placeholder.we_could_not_fb_id'));
            return redirect(route('filament.auth.auth.register'));
        }

        try {
            DB::beginTransaction();

            /** @var User $user */
            $user = User::whereRaw('lower(email) = ?', strtolower($socialUser['email']))->first();
            $existingAccount = null;

            if (!empty($user)) {
                /** @var SocialAccount $existingProfile */
                $existingAccount = SocialAccount::where('provider_id', $socialUser->id)->first();
            } else {
                $fullName = trim($socialUser['name']);
                $nameParts = explode(' ', $fullName);

                $userData['first_name'] = $nameParts[0] ?? 'User'; // Default to 'User' if empty
                $userData['last_name'] = isset($nameParts[1]) ? implode(' ', array_slice($nameParts, 1)) : '';

                $baseUsername = Str::slug($fullName, '');
                $username = $baseUsername;
                $counter = 1;

                while (User::where('username', $username)->exists()) {
                    $username = $baseUsername . $counter;
                    $counter++;
                }

                $userData['username'] = $username;
                $userData['email'] = $socialUser['email'];
                $userData['email_verified_at'] = Carbon::now();
                $userData['password'] = bcrypt(Str::random(40));
                $userData['type'] = User::STAFF;
                $userData['contact'] = '9874563210';
                $userData['gender'] = 0;

                $adminRole = Role::whereName('customer')->first();

                /** @var User $user */
                $user = User::create($userData)->assignRole($adminRole);
                $plan = Plan::whereIsDefault(true)->first();

                $subscription = new Subscription();
                $subscription->plan_id = $plan->id;
                $subscription->starts_at = Carbon::now();
                $subscription->ends_at = Carbon::now()->addDays($plan->trial_days);
                $subscription->plan_amount = $plan->price;
                $subscription->payable_amount = $plan->price;
                $subscription->plan_frequency = $plan->frequency;
                $subscription->trial_ends_at = Carbon::now()->addDays($plan->trial_days);
                $subscription->no_of_post = $plan->post_count;
                $subscription->user_id = $user['id'];
                $subscription->status = Subscription::ACTIVE;
                $subscription->saveQuietly();

                // Award registration points for new user
                $registerPoints = PointRule::where('key', 'register')->value('points') ?? 100;
                $user->increment('comment_points', $registerPoints);
            }

            if (empty($existingAccount)) {
                $existingAccount = SocialAccount::where('provider_id', $socialUser->id)->first();
                if (empty($existingAccount)) {
                    $socialAccount = new SocialAccount();
                    $socialAccount->user_id = $user->id;
                    $socialAccount->provider = $provider;
                    $socialAccount->provider_id = $socialUser->id;
                    $socialAccount->save();
                }
            }

            DB::commit();
            Auth::login($user);

            return redirect(route('customer.profile'));
        } catch (Exception $e) {
            DB::rollBack();
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }
}
