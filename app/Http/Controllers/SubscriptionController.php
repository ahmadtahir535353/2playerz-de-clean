<?php

namespace App\Http\Controllers;

use App\Enums\SubscriptionStatus;
use App\Http\Requests\UpdateSubscriptionPlanRequest;
use App\Mail\ManualPaymentGuideMail;
use App\Mail\ManualPaymentStatusMail;
use App\Mail\SuperAdminManualPaymentMail;
use App\Models\MailSetting;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Repositories\SubscriptionRepository;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Stripe\Checkout\Session;
use Laracasts\Flash\Flash;
use Stripe\Stripe;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class SubscriptionController extends AppBaseController
{
    protected SubscriptionRepository $subscriptionRepo;

    public function __construct(SubscriptionRepository $subscriptionRepo)
    {
        $this->subscriptionRepo = $subscriptionRepo;
        Stripe::setApiKey(getStripeSecretKey());
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View|Factory|Application
    {
        $currentPlan = getCurrentSubscription();

        $days = $remainingDay = '';

        if ($currentPlan->ends_at > Carbon::now()) {
            $days = Carbon::parse($currentPlan->ends_at)->diffInDays();
            $remainingDay = $days . ' Days';
        }

        if ($days >= 30 && $days <= 365) {
            $remainingDay = '';
            $months = floor($days / 30);
            $extraDays = $days % 30;
            if ($extraDays > 0) {
                $remainingDay .= $months . ' Month ' . $extraDays . ' Days';
            } else {
                $remainingDay .= $months . ' Month ';
            }
        }

        if ($days >= 365) {
            $remainingDay = '';
            $years = floor($days / 365);
            $extraMonths = floor($days % 365 / 30);
            $extraDays = floor($days % 365 % 30);
            if ($extraMonths > 0 && $extraDays < 1) {
                $remainingDay .= $years . ' Years ' . $extraMonths . ' Month ';
            } elseif ($extraDays > 0 && $extraMonths < 1) {
                $remainingDay .= $years . ' Years ' . $extraDays . ' Days';
            } elseif ($years > 0 && $extraDays > 0 && $extraMonths > 0) {
                $remainingDay .= $years . ' Years ' . $extraMonths . ' Month ' . $extraDays . ' Days';
            } else {
                $remainingDay .= $years . ' Years ';
            }
        }

        return view('subscription.index', compact('currentPlan', 'remainingDay'));
    }

    public function upgrade(): Factory|View|Application
    {
        $plans = Plan::with(['currency'])
            ->get();

        $monthlyPlans = $plans->where('frequency', Plan::MONTHLY);
        $yearlyPlans = $plans->where('frequency', Plan::YEARLY);
        $unLimitedPlans = $plans->where('frequency', Plan::UNLIMITED);

        return view('subscription.upgrade', compact('monthlyPlans', 'yearlyPlans', 'unLimitedPlans'));
    }

    public function choosePaymentType($planId, $context = null, $fromScreen = null): Factory|View|Application
    {
        // code for checking the current plan is active or not, if active then it should not allow to choose that plan
        $subscriptionsPricingPlan = Plan::findOrFail($planId);
        $paymentTypes = getPaymentGateway();

        return view('subscription.payment_for_plan', compact('subscriptionsPricingPlan', 'paymentTypes'));
    }

    public static function zeroDecimalCurrencies(): array
    {
        return [
            'BIF',
            'CLP',
            'DJF',
            'GNF',
            'JPY',
            'KMF',
            'KRW',
            'MGA',
            'PYG',
            'RWF',
            'UGX',
            'VND',
            'VUV',
            'XAF',
            'XOF',
            'XPF',
        ];
    }

    /**
     * @return mixed|string|string[]
     */
    public static function removeCommaFromNumbers($number)
    {
        return (gettype($number) == 'string' && ! empty($number)) ? str_replace(',', '', $number) : $number;
    }

    public function purchaseSubscription(Request $request)
    {
        //start
        $plan = json_decode($request->plan);

        $subscriptionPlan = Plan::findOrFail($plan->id);

        if ($subscriptionPlan->frequency == Plan::MONTHLY) {
            $newPlanDays = 30;
        } else {
            if ($subscriptionPlan->frequency == Plan::YEARLY) {
                $newPlanDays = 365;
            } else {
                $newPlanDays = 36500;
            }
        }
        $startsAt = Carbon::now();
        $endsAt = $startsAt->copy()->addDays($newPlanDays);

        $usedTrialBefore = Subscription::whereUserId(getLogInUser()->id)->whereNotNull('trial_ends_at')->exists();

        // if the user did not have any trial plan then give them a trial
        if (! $usedTrialBefore && $subscriptionPlan->trial_days > 0) {
            $endsAt = $startsAt->copy()->addDays($subscriptionPlan->trial_days);
        }

        $amountToPay = $subscriptionPlan->price;

        /** @var Subscription $currentSubscription */
        $currentSubscription = getCurrentSubscription();

        $usedDays = Carbon::parse($currentSubscription->starts_at)->diffInDays($startsAt);
        $planIsInTrial = checkIfPlanIsInTrial($currentSubscription);

        // switching the plan -- Manage the pro-rating
        if (! $currentSubscription->isExpired() && $amountToPay != 0 && ! $planIsInTrial) {
            $usedDays = Carbon::parse($currentSubscription->starts_at)->diffInDays($startsAt);

            $currentSubsTotalDays = Carbon::parse($currentSubscription->starts_at)->diffInDays($currentSubscription->ends_at);

            $currentPlan = $currentSubscription->plan; // TODO: take fields from subscription

            // checking if the current active subscription plan has the same price and frequency in order to process the calculation for the proration
            $planPrice = $currentPlan->price;
            $planFrequency = $currentPlan->frequency;
            if ($planPrice != $currentSubscription->plan_amount || $planFrequency != $currentSubscription->plan_frequency) {
                $planPrice = $currentSubscription->plan_amount;
                $planFrequency = $currentSubscription->plan_frequency;
            }

            //            $frequencyDays = $planFrequency == Plan::MONTHLY ? 30 : 365;
            $perDayPrice = round($planPrice / $currentSubsTotalDays, 2);
            $isJPYCurrency = ! empty($subscriptionPlan->currency) && isJPYCurrency($subscriptionPlan->currency->currency_code);

            $remainingBalance = $planPrice - ($perDayPrice * $usedDays);
            $remainingBalance = $isJPYCurrency
                ? round($remainingBalance) : $remainingBalance;

            if ($remainingBalance < $subscriptionPlan->price) { // adjust the amount in plan i.e. you have to pay for it
                $amountToPay = $isJPYCurrency
                    ? round($subscriptionPlan->price - $remainingBalance)
                    : round($subscriptionPlan->price - $remainingBalance, 2);
            } else {
                $perDayPriceOfNewPlan = round($subscriptionPlan->price / $newPlanDays, 2);
                if ($subscriptionPlan->frequency == Plan::UNLIMITED) {
                    $totalDays = $newPlanDays;
                } else {
                    $totalDays = round($newPlanDays / $perDayPriceOfNewPlan);
                }
                $endsAt = Carbon::now()->addDays($totalDays);
                $amountToPay = 0;
            }
        }

        // check that if try to switch the plan
        if (! $currentSubscription->isExpired()) {
            if ((checkIfPlanIsInTrial($currentSubscription) || ! checkIfPlanIsInTrial($currentSubscription)) && $subscriptionPlan->price <= 0) {
                return ['status' => false, 'subscriptionPlan' => $subscriptionPlan];
            }
        }

        if ($usedDays <= 0) {
            $startsAt = $currentSubscription->starts_at;
        }

        $input = [
            'user_id' => getLogInUser()->id,
            'plan_id' => $subscriptionPlan->id,
            'plan_amount' => $subscriptionPlan->price,
            'payable_amount' => $amountToPay,
            'plan_frequency' => $subscriptionPlan->frequency,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => SubscriptionStatus::PENDING,
            'no_of_post' => $subscriptionPlan->post_count,
        ];
        $subscription = Subscription::create($input);

        if ($subscriptionPlan->price <= 0 || $amountToPay == 0) {
            Subscription::whereUserId(getLogInUserId())->where('id', '!=', $subscription->id)
                ->update([
                    'status' => Subscription::INACTIVE,
                ]);

            Subscription::findOrFail($subscription->id)->update(['status' => Subscription::ACTIVE]);

            return ['status' => true, 'subscriptionPlan' => $subscriptionPlan];
        }

        $user = Auth::user();

        $data = [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ];

        $unit_amount = ! in_array($plan->currency->currency_code, self::zeroDecimalCurrencies()) ? self::removeCommaFromNumbers($subscription->payable_amount) * 100 : self::removeCommaFromNumbers($subscription->payable_amount);

        // dd($subscription->payable_amount, $unit_amount);
        $session = Session::create([
            'payment_method_types' => ['card'],
            'customer_email' => $user->email,
            'line_items' => [[
                'price_data' => [
                    'currency' => $plan->currency->currency_code,
                    'unit_amount' => $unit_amount,
                    'product_data' => [
                        'name' => $plan->name,
                    ],
                ],
                'quantity' => '1',
            ]],
            'mode' => 'payment',
            'client_reference_id' => $subscription->id,
            'success_url' => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('stripe.failed') . '?error=subscription_failed',
        ]);

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');
        if (empty($sessionId)) {
            throw new UnprocessableEntityHttpException('session_id required');
        }

        try {
            $subscriptionRepo = app(SubscriptionRepository::class);
            $subscription = $subscriptionRepo->paymentUpdate($request);

            Notification::make()
                ->success()
                ->title($subscription->plan->name . ' ' . __('messages.subscription.has_been_subscribed'))
                ->send();

            return redirect()->route('filament.customer.pages.manage-subscription');
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function failed(Request $request)
    {
        if (session()->has('subscription_plan_id')) {
            $subscriptionPlanId = session('subscription_plan_id');
            $subscriptionRepo = app(SubscriptionRepository::class);
            $subscriptionRepo->paymentFailed($subscriptionPlanId);
        }
        if ($request->error == 'subscription_failed') {
            return redirect(route('filament.customer.pages.manage-subscription'));
        } else {
            return redirect(route('home'));
        }
    }

    public function manualPay(Request $request): JsonResponse
    {
        $input = $request->all();

        $data['attachment'] = $this->data['attachment'] ?? null;
        $data['notes'] = $this->data['notes'] ?? null;

        $subscription = $this->subscriptionRepo->manageSubscriptionForManualPayment($request->get('planId'), $input);

        $data = Subscription::whereUserId(getLogInUserId())->orderBy('created_at', 'desc')->first();

        $asds = Subscription::whereId($data->id)->update(['payment_type' => Subscription::MANUALLY]);

        $manual_payment_guide_step = Setting::where('key', 'manual_payment_guide')->first();

        $user = \Illuminate\Support\Facades\Auth::user();
        $super_admin_data = [
            'super_admin_msg' => $user->full_name . __('messages.placeholder.created_request_for_payment_of') . $data->plan->currency->currency_icon . '' . $data->payable_amount,
            'attachment' => $data->attachment ?? '',
            'notes' => $data->notes ?? '',
            'id' => $data->id,
        ];

        $mailData = MailSetting::first();
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

        config(
            [
                'mail.default' => $protocol,
                "mail.mailers.$protocol.transport" => $protocol,
                "mail.mailers.$protocol.host" => $host,
                "mail.mailers.$protocol.port" => $mailData->mail_port,
                "mail.mailers.$protocol.encryption" => MailSetting::ENCRYPTION_TYPE[$mailData->encryption],
                "mail.mailers.$protocol.username" => $mailData->mail_username,
                "mail.mailers.$protocol.password" => $mailData->mail_password,
                'mail.from.address' => $mailData->reply_to,
                'mail.from.name' => $mailData->mail_title,
            ]
        );

        Mail::to($user['email'])
            ->send(new ManualPaymentGuideMail($manual_payment_guide_step['value'], $user));

        Mail::to('sadmin@vcard.com')
            ->send(new SuperAdminManualPaymentMail($super_admin_data, 'sadmin@vcard.com'));

        return $this->sendSuccess($subscription['plan']->name . ' ' . __('messages.subscription.has_been_subscribed'));
    }

    public function downloadAttachment($id): Response|Application|ResponseFactory
    {

        $subscription = Subscription::whereId($id)->first();

        [$file, $headers] = $this->subscriptionRepo->downloadAttachment($subscription);
        // dd($file, $headers);

        return response($file, 200, $headers);
    }

    public function planStatus(Request $request)
    {
        $data = Subscription::with('user', 'plan.currency')->whereId($request->id)->first();
        $input = $request->all();
        $input['notes'] = isset($input['notes']) ? $input['notes'] : null;
        if ($input['status'] == 'Rejected') {
            Subscription::whereId($request->id)->update([
                'status' => 0,
                'reject_notes' => $input['notes'],
                'payment_type' => Subscription::REJECTED,
            ]);
        }
        // Approved Payment
        if ($input['status'] == 'Manually') {
            Subscription::whereUserId($data->user->id)
                ->where('id', '!=', $request->id)
                ->update(['status' => 0]);

            Subscription::whereId($request->id)->update([
                'status' => 1,
                'reject_notes' => $input['notes'],
                'payment_type' => Subscription::PAID,
            ]);
        }
        $input['status'] = ($input['status'] == 'Manually') ? 'Approved' : $input['status'];
        $super_admin_data = [
            'super_admin_msg' => __('messages.placeholder.your_manual_payment_request_is') . ' ' . $input['status'] . ' of ' . $data->plan->currency->currency_icon . '' . $data->plan->price,
            'notes' => $input['notes'] ?? '',
            'name' => $data->user->full_name,
        ];
        Mail::to($data->user->email)
            ->send(new ManualPaymentStatusMail($super_admin_data, $data->user));

        return $this->sendSuccess(__('messages.placeholder.payment_received'));
    }

    /**
     * @return Application|Factory|View
     */
    public function subscribedUserPlans(): \Illuminate\View\View
    {
        return view('subscribed_user_plans.index');
    }

    public function userSubscribedPlanEdit(Request $request): JsonResponse
    {
        $subscription = Subscription::whereId($request->id)->first();

        return $this->sendResponse($subscription, __('messages.placeholder.subscription_successfully_retrieved'));
    }

    public function userSubscribedPlanUpdate(Request $request): JsonResponse
    {
        $subscription = Subscription::where('id', $request->id)->update([
            'ends_at' => $request->end_date,
            'status' => Subscription::ACTIVE,
        ]);

        return $this->sendResponse($subscription, __('messages.placeholder.subscription_date_successfully_updated'));
    }
}
