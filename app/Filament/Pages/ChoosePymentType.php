<?php

namespace App\Filament\Pages;

use App\Enums\PlanFrequency;
use App\Http\Middleware\CheckPaddingSubscription;
use App\Mail\ManualPaymentGuideMail;
use App\Mail\SuperAdminManualPaymentMail;
use App\Models\MailSetting;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\Subscription;
use App\Repositories\SubscriptionRepository;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ChoosePymentType extends Page implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'choose-pyment-type/{plan}';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.choose-pyment-type';

    protected static string|array $routeMiddleware = [
        CheckPaddingSubscription::class,
    ];

    public Plan $plan;

    public $paymentAmount = 0;

    public $paymentType = 0;

    private SubscriptionRepository $subscriptionRepository;

    public function boot(SubscriptionRepository $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    protected function getViewData(): array
    {
        // New Plan
        $plan = $this->plan;
        $plan->payable_amount = $plan->price > 0 ? $plan->price : 0;
        $this->paymentAmount = $plan->price > 0 ? $plan->price : 0;

        $newPlan = getProratedPlanData($plan->id);

        return compact('plan', 'newPlan');
    }

    public static function getRelativeRouteName(): string
    {
        return (string) 'choose-pyment-type';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('payment_type')
                    ->label('')
                    ->live()
                    ->options(getPaymentGateway())
                    ->afterStateUpdated(function (Get $get) {
                        $this->paymentType = (int) $get('payment_type');
                    }),
                SpatieMediaLibraryFileUpload::make('attachment')
                    ->label(__('messages.attachment'))
                    ->disk(config('app.media_disk'))
                    ->collection(Subscription::ATTACHMENT_PATH)
                    // Manual payment proofs only (no executable uploads)
                    ->acceptedFileTypes([
                        'application/pdf',
                        'image/jpeg',
                        'image/png',
                        'image/webp',
                    ])
                    ->rules(['file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,webp'])
                    ->visible(fn(Get $get) => $get('payment_type') == Subscription::MANUALLY),
                Textarea::make('notes')
                    ->label(__('messages.notes'))
                    ->visible(fn(Get $get) => $get('payment_type') == Subscription::MANUALLY),
            ])
            ->statePath('data');
    }

    public function getFormAction(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('save'),
        ];
    }

    public function save(Request $request): void
    {
        $input = $this->form->getState();
        $newPlan = getProratedPlanData($this->plan->id);

        if ($input['payment_type'] == Subscription::MANUALLY) {
            $input['attachment'] = $this->data['attachment'] ?? null;
            $input['notes'] = $this->data['notes'] ?? null;
        }

        $subscription = $this->subscriptionRepository->manageSubscriptionForManualPayment($newPlan['id'], $input);

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

        Notification::make()
            ->success()
            ->title(isset($subscription['plan']) ? $subscription['plan']->name . ' ' . __('messages.subscription.has_been_subscribed') : $subscription['subscriptionPlan']->name . ' ' . __('messages.subscription.has_been_subscribed'))
            ->send();

        $this->redirect(route('filament.customer.pages.manage-subscription'));
    }
}
