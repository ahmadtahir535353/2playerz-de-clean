<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\Setting;
use App\Repositories\SubscriptionRepository;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PaypalController extends AppBaseController
{
    private SubscriptionRepository $subscriptionRepository;

    public function __construct(SubscriptionRepository $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $checkPaypalCreds = Setting::where('key', 'paypal_checkbox_btn')->value('value');
        $paypalKey = Setting::where('key', 'paypal_client_id')->value('value');
        $paypalSecretKey = Setting::where('key', 'paypal_secret')->value('value');
        $mode = Setting::where('key', 'paypal_mode')->value('value');
        $clientId = (isset($checkPaypalCreds) && $checkPaypalCreds == 1) && ! empty($paypalKey) ? $paypalKey : config('paypal.sandbox.client_id');
        $clientSecret = (isset($checkPaypalCreds) && $checkPaypalCreds == 1) && ! empty($paypalSecretKey) ? $paypalSecretKey : config('paypal.sandbox.client_secret');
        $paypalmode = (isset($checkPaypalCreds) && $checkPaypalCreds == 1) && ! empty($mode) ? $mode : config('paypal.mode');
        config([
            'paypal.mode' => $paypalmode,
            'paypal.sandbox.client_id' => $clientId,
            'paypal.sandbox.client_secret' => $clientSecret,
            'paypal.live.client_id' => $clientId,
            'paypal.live.client_secret' => $clientSecret,
        ]);
    }

    public static function getPayPalSupportedCurrencies()
    {
        return [
            'AUD',
            'BRL',
            'CAD',
            'CNY',
            'CZK',
            'DKK',
            'EUR',
            'HKD',
            'HUF',
            'ILS',
            'JPY',
            'MYR',
            'MXN',
            'TWD',
            'NZD',
            'NOK',
            'PHP',
            'PLN',
            'GBP',
            'RUB',
            'SGD',
            'SEK',
            'CHF',
            'THB',
            'USD',
        ];
    }

    public function onBoard(Request $request)
    {
        $plan = json_decode($request->plan);

        $sectiondata = [
            'user_id' => Auth::id(),
            'plan_id' => $plan->id,
        ];


        if ($plan->currency->currency_code != null && ! in_array(strtoupper($plan->currency->currency_code), self::getPayPalSupportedCurrencies())) {
            Notification::make()
                ->danger()
                ->title(__('messages.placeholder.this_currency_is_not_supported'))
                ->send();

            return redirect()->back();
        }
        $data = $this->subscriptionRepository->manageSubscription($plan->id);
        session(['data' => $sectiondata]);

        $subscriptionsPricingPlan = $data['plan'];
        $subscription = $data['subscription'];

        $provider = new PayPalClient();
        $provider->getAccessToken();

        $data = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'reference_id' => $subscription->id,
                    'amount' => [
                        'value' => $data['subscription']['payable_amount'],
                        'currency_code' => $subscription->plan->currency->currency_code,
                    ],
                ],
            ],
            'application_context' => [
                'cancel_url' => route('paypal.failed') . '?error=subscription_failed',
                'return_url' => route('paypal.success'),
            ],
        ];

        $order = $provider->createOrder($data);

        return redirect($order['links'][1]['href']);
    }

    public function success(Request $request)
    {
        $data = session('data');
        $plan = Plan::find($data['plan_id']);


        $provider = new PayPalClient;
        $provider->getAccessToken();
        $token = $request->get('token');
        $orderInfo = $provider->showOrderDetails($token);

        try {
            // Call API with your client and get a response for your call
            $response = $provider->capturePaymentOrder($token);

            $subscriptionId = $response['purchase_units'][0]['reference_id'];
            $subscriptionAmount = $response['purchase_units'][0]['payments']['captures'][0]['amount']['value'];
            $transactionID = $response['id'];     // $response->result->id gives the orderId of the order created above

            Subscription::findOrFail($subscriptionId)->update(['status' => Subscription::ACTIVE]);
            // De-Active all other subscription
            Subscription::whereUserId(getLogInUser()->id)
                ->where('id', '!=', $subscriptionId)
                ->update([
                    'status' => Subscription::INACTIVE,
                ]);

            $transaction = Transaction::create([
                'transaction_id' => $transactionID,
                'type' => Transaction::PAYPAL,
                'amount' => $subscriptionAmount,
                'status' => Subscription::ACTIVE,
                'meta' => json_encode($response),
                'user_id' => getLogInUserId(),
            ]);

            // updating the transaction id on the subscription table
            $subscription = Subscription::findOrFail($subscriptionId);
            $subscription->update(['transaction_id' => $transaction->id]);
            Notification::make()
                ->success()
                ->title($plan->name . ' ' . __('messages.subscription.has_been_subscribed'))
                ->send();
            return redirect()->route('filament.customer.pages.manage-subscription');
            //            return view('sadmin.plans.payment.paymentSuccess');
        } catch (HttpException $ex) {
            print_r($ex->getMessage());
        }
    }

    /**
     * @return Application|Factory|View
     */
    public function failed(Request $request): RedirectResponse
    {
        if (session()->has('subscription_plan_id')) {
            $subscriptionPlanId = session('subscription_plan_id');
            $subscriptionRepo = app(SubscriptionRepository::class);
            $subscriptionRepo->paymentFailed($subscriptionPlanId);
        }
        Notification::make()
            ->danger()
            ->title(__('messages.placeholder.unable_to_process_payment'))
            ->send();
        if ($request->error == 'subscription_failed') {
            return redirect(route('filament.customer.pages.manage-subscription'));
        } else {
            return redirect(route('home'));
        }
    }
}
