<?php

namespace App\Filament\Pages\Auth;

use App\Actions\Subscription\CreateSubscription;
use App\Models\Plan;
use App\Models\PointRule;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Events\Auth\Registered;
use Filament\Facades\Filament;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Get;
use Illuminate\Validation\Rule;
use Illuminate\Support\HtmlString;
use Filament\Http\Responses\Auth\RegistrationResponse;
use App\Http\Responses\CustomRegistrationResponse;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Support\Facades\Schema;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;

class Register extends BaseRegister
{
    /**
     * @var view-string
     */
    protected static string $view = 'filament.auth.register';

    /**
     * @return array<int | string, string | Form>
     */
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        // Group::make([
                        //     TextInput::make('first_name')
                        //         ->label(__('messages.staff.first_name') . ':')
                        //         ->validationAttribute(__('messages.staff.first_name'))
                        //         ->placeholder(__('messages.staff.first_name'))
                        //         ->required()
                        //         ->maxLength(255)
                        //         ->autofocus(),
                        //     TextInput::make('last_name')
                        //         ->label(__('messages.staff.last_name') . ':')
                        //         ->validationAttribute(__('messages.staff.last_name'))
                        //         ->placeholder(__('messages.staff.last_name'))
                        //         ->required()
                        //         ->maxLength(255),
                        // ])->columns(2),
                        $this->getEmailFormComponent()->label(__('messages.staff.email') . ':')->validationAttribute(__('messages.staff.email'))->placeholder(__('messages.staff.email')),
                        TextInput::make('username')
                            ->label(__('messages.staff.username') . ':')
                            ->validationAttribute(__('messages.staff.username'))
                            ->placeholder(__('messages.staff.username'))
                            ->required()
                            ->maxLength(255)
                            ->rule(Rule::unique(User::class, 'username')),
                            TextInput::make('password')
                                ->label(__('filament-panels::pages/auth/register.form.password.label'))
                                ->password()
                                ->revealable(filament()->arePasswordsRevealable())
                                ->placeholder(__('messages.staff.password'))
                                ->required()
                                ->rule(Password::default())
                                ->same('passwordConfirmation')
                                ->hint(__('messages.placeholder.password_must_be_at_least_8_characters'))
                                ->validationAttribute(__('filament-panels::pages/auth/register.form.password.validation_attribute')),
                                            // $this->getPasswordFormComponent()->label(__('messages.staff.password') . ':')->validationAttribute(__('messages.staff.password'))->placeholder(__('messages.staff.password')),
                                            $this->getPasswordConfirmationFormComponent()
                                                ->label(__('messages.staff.confirm_password') . ':')
                                                ->validationAttribute(__('messages.staff.confirm_password'))
                                                ->placeholder(__('messages.staff.confirm_password')),
                                            Placeholder::make('terms_agreement')
                                                ->label('')
                                                ->content(new HtmlString('<p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Mit deiner Anmeldung stimmst du unseren <a href="' . route('page.Terms') . '" target="_blank" class="text-primary-600 dark:text-primary-400 hover:underline font-medium">Nutzungsbedingungen</a> zu.</p>'))
                                                ->extraAttributes(['class' => 'mt-1']),
                        ])
                        ->statePath('data'),
            ),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getRegisterFormAction()
                ->extraAttributes(['class' => 'w-full flex items-center justify-center space-x-3'])
                ->label(__('messages.common.submit')),
        ];
    }

    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        // Check if email already exists in database (before validation)
        if (isset($this->data['email']) && !empty($this->data['email'])) {
            if (User::where('email', $this->data['email'])->exists()) {
                Notification::make()
                    ->warning()
                    ->title(__('messages.placeholder.email_already_exists'))
                    ->body(__('messages.placeholder.please_login_with_your_existing_account'))
                    ->send();
                
                // Store email in session to auto-fill on login page
                session()->put('prefill_email', $this->data['email']);
                
                $this->redirect(route('filament.auth.auth.login'));
                return null;
            }
        }

        // Validate form and get state
        $data = $this->form->getState();

        $user = $this->wrapInDatabaseTransaction(function () use ($data) {

            $plainPassword = (string) $data['password'];
            $data['contact'] = '1234567890'; // Replace with the actual contact value
            $data['gender'] = 0;
            if (Schema::hasColumn('users', 'is_default')) {
                $data['is_default'] = false;
            }

            $data['password'] = Hash::make($plainPassword); // for auth
            $data['type'] = User::STAFF;

            $data = $this->mutateFormDataBeforeRegister($data);

            $user = $this->handleRegistration($data);

            $user->assignRole('customer');

            $this->form->model($user)->saveRelationships();

            return $user;
        });

        $plan = Plan::whereIsDefault(true)->first();
        Subscription::create([
            'plan_id' => $plan->id,
            'plan_amount' => $plan->price,
            'payable_amount' => $plan->price,
            'plan_frequency' => Plan::MONTHLY,
            'starts_at' => Carbon::now(),
            'ends_at' => Carbon::now()->addDays($plan->trial_days),
            'trial_ends_at' => Carbon::now()->addDays($plan->trial_days),
            'status' => Subscription::ACTIVE,
            'user_id' => $user->id,
            'no_of_post' => $plan->post_count,
        ]);

        // Award registration points
        $registerPoints = PointRule::where('key', 'register')->value('points') ?? 100;
        $user->increment('comment_points', $registerPoints);

        event(new Registered($user));

        $user->sendEmailVerificationNotification();
        Notification::make()
            ->success()
            ->title(__('messages.placeholder.registered_success'))
            ->send();

        return app(CustomRegistrationResponse::class);
    }
}
