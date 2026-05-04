<?php

namespace App\Filament\Pages;

use App\Enums\Sidebar;
use App\Http\Middleware\CheckPaddingSubscription;
use App\Mail\TestMail;
use App\Models\MailSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class Mails extends Page
{
    public ?array $data = [];

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static string $view = 'filament.pages.mails';

    protected static ?int $navigationSort = Sidebar::MAILS->value;

    protected MailSetting $record;

    public static function getNavigationLabel(): string
    {
        return __('messages.mails.mail');
    }

    public function getTitle(): string
    {
        return __('messages.mails.mail');
    }

    public static function canAccess(): bool
    {
        return Auth::user()->hasPermissionTo('manage_mail_setting');
    }

    protected static string|array $routeMiddleware = [
        CheckPaddingSubscription::class,
    ];

    public function mount(): void
    {
        $this->record = MailSetting::first();

        // $this->getFormSchemaPart1->fill([
        //     'mail_protocol' => $this->record->mail_protocol,
        //     'mail_library' => $this->record->mail_library,
        //     'encryption' => $this->record->encryption,
        //     'mail_host' => $this->record->mail_host,
        //     'mail_port' => $this->record->mail_port,
        //     'mail_username' => $this->record->mail_username,
        //     'mail_password' => $this->record->mail_password,
        //     'mail_title' => $this->record->mail_title,
        //     'reply_to' => $this->record->reply_to,
        //     'contact_messages' => $this->record->contact_messages,
        //     'contact_mail' => $this->record->contact_mail,
        // ]);

        $this->getFormSchemaPart1->fill($this->record->toArray());
    }

    protected function getForms(): array
    {
        return [
            'getFormSchemaPart1',
            'getFormSchemaPart2',
            'getFormSchemaPart3',
        ];
    }

    public function getFormSchemaPart1(Form $form): Form
    {
        $form->model = MailSetting::first();
        return $form
            ->schema([
                Section::make('')
                    ->schema([
                        Select::make('mail_protocol')
                            ->label(__('messages.mails.mail_protocol') . ':')
                            ->validationAttribute(__('messages.mails.mail_protocol'))
                            ->placeholder(__('messages.mails.mail_protocol'))
                            ->options([
                                MailSetting::TYPE
                            ])
                            ->searchable()
                            ->native(true)
                            ->required(),
                        Select::make('mail_library')
                            ->label(__('messages.mails.mail_library') . ':')
                            ->validationAttribute(__('messages.mails.mail_library'))
                            ->placeholder(__('messages.mails.mail_library'))
                            ->options([
                                MailSetting::LIBRARY_TYPE
                            ])
                            ->searchable()
                            ->native(true)
                            ->required(),
                        Select::make('encryption')
                            ->label(__('messages.mails.encryption') . ':')
                            ->validationAttribute(__('messages.mails.encryption'))
                            ->placeholder(__('messages.mails.encryption'))
                            ->options([
                                MailSetting::ENCRYPTION_TYPE
                            ])
                            ->searchable()
                            ->native(true)
                            ->required(),
                        TextInput::make('mail_host')
                            ->label(__('messages.mails.mail_host') . ':')
                            ->validationAttribute(__('messages.mails.mail_host'))
                            ->placeholder(__('messages.mails.mail_host'))
                            ->required(),
                        TextInput::make('mail_port')
                            ->label(__('messages.mails.mail_port') . ':')
                            ->validationAttribute(__('messages.mails.mail_port'))
                            ->placeholder(__('messages.mails.mail_port'))
                            ->required(),
                        TextInput::make('mail_username')
                            ->label(__('messages.mails.mail_user_name') . ':')
                            ->validationAttribute(__('messages.mails.mail_user_name'))
                            ->placeholder(__('messages.mails.mail_user_name'))
                            ->required(),
                        TextInput::make('mail_password')
                            ->label(__('messages.mails.mail_password') . ':')
                            ->validationAttribute(__('messages.mails.mail_password'))
                            ->placeholder(__('messages.mails.mail_password'))
                            ->required(),
                        TextInput::make('mail_title')
                            ->label(__('messages.mails.mail_title') . ':')
                            ->validationAttribute(__('messages.mails.mail_title'))
                            ->placeholder(__('messages.mails.mail_title'))
                            ->required(),
                        TextInput::make('reply_to')
                            ->label(__('messages.mails.reply_to') . ':')
                            ->validationAttribute(__('messages.mails.reply_to'))
                            ->placeholder(__('messages.mails.reply_to'))
                            ->required(),
                    ]),
            ])->statePath('data');
    }

    public function getFormSchemaPart2(Form $form): Form
    {

        return $form
            ->schema([
                Section::make(__('messages.mails.contact_messages'))
                    ->collapsible()
                    ->schema([
                        Toggle::make('contact_messages')
                            ->label(__('messages.mails.send_contact-messages_to_email_address') . ':')
                            ->validationAttribute(__('messages.mails.send_contact-messages_to_email_address'))
                            ->inline(false),
                        TextInput::make('contact_mail')
                            ->label(__('messages.mails.mail') . ':')
                            ->validationAttribute(__('messages.mails.mail'))
                            ->placeholder(__('messages.mails.mail')),
                    ]),
            ])->statePath('data');
    }


    public function getFormSchemaPart3(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('messages.mails.test_mail'))
                    ->schema([
                        TextInput::make('test_mails')
                            ->label(__('messages.emails.email') . ':')
                            ->validationAttribute(__('messages.emails.email'))
                            ->required()
                            ->email()
                            ->placeholder(__('messages.emails.email')),
                    ]),
            ])->statePath('data');
    }


    public function savePart1()
    {
        // return Notification::make()
        // ->danger()
        // ->title('This functionality not allowed in demo.')
        // ->send();
        try {
            $this->record = MailSetting::first();
            $data = $this->getFormSchemaPart1->getState();
            if (isset($data['email_setting'])) {
                $data['email_verification'] = (isset($data['email_verification'])) ? MailSetting::EMAIL_VERIFICATION_ACTIVE : MailSetting::EMAIL_VERIFICATION_DEACTIVE;
            }

            if (isset($data['contact_setting'])) {
                $data['contact_messages'] = (isset($data['contact_messages'])) ? MailSetting::CONTACT_MESSAGES_ACTIVE : MailSetting::CONTACT_MESSAGES_DEACTIVE;

                $this->record->contact_mail = $data['contact_mail'];
            }

            $this->record->update([
                'mail_protocol' => $data['mail_protocol'],
                'mail_library' => $data['mail_library'],
                'encryption' => $data['encryption'],
                'mail_host' => $data['mail_host'],
                'mail_port' => $data['mail_port'],
                'mail_username' => $data['mail_username'],
                'mail_password' => $data['mail_password'],
                'mail_title' => $data['mail_title'],
                'reply_to' => $data['reply_to'],
            ]);

            Notification::make()
                ->success()
                ->title(__('messages.placeholder.mail_updated_successfully'))
                ->send();
        } catch (ModelNotFoundException $exception) {
            Notification::make()
                ->error()
                ->title('post not found')
                ->send();
        }
    }

    public function savePart2()
    {
        // return Notification::make()
        //     ->danger()
        //     ->title('This functionality not allowed in demo.')
        //     ->send();
        try {
            $this->record = MailSetting::first();
            $data2 = $this->getFormSchemaPart2->getState();

            if (isset($data2['email_setting'])) {
                $data2['email_verification'] = (isset($data2['email_verification'])) ? MailSetting::EMAIL_VERIFICATION_ACTIVE : MailSetting::EMAIL_VERIFICATION_DEACTIVE;
            }

            if (isset($data2['contact_setting'])) {
                $data2['contact_messages'] = (isset($data2['contact_messages'])) ? MailSetting::CONTACT_MESSAGES_ACTIVE : MailSetting::CONTACT_MESSAGES_DEACTIVE;

                $this->record->contact_mail = $data2['contact_mail'];
            }

            $this->record->update([
                'contact_messages' => $data2['contact_messages'],
                'contact_mail' => $data2['contact_mail'],
            ]);

            Notification::make()
                ->success()
                ->title(__('messages.placeholder.mail_updated_successfully'))
                ->send();
        } catch (ModelNotFoundException $exception) {
            Notification::make()
                ->error()
                ->title('post not found')
                ->send();
        }
    }

    public function savePart3()
    {
        // return Notification::make()
        //     ->danger()
        //     ->title('This functionality not allowed in demo.')
        //     ->send();
        try {
            $data3 = $this->getFormSchemaPart3->getState();
            $id = Auth::id();

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

            Mail::to($data3['test_mails'])
                ->send(new TestMail($data3['test_mails'], $id));

            Notification::make()
                ->success()
                ->title(__('messages.placeholder.test_mail_send_successfully'))
                ->send();
        } catch (ModelNotFoundException $exception) {
            Notification::make()
                ->error()
                ->title('Test Mail Not Send')
                ->send();
        }
    }

    public function getFormAction1(): array
    {
        return [
            Action::make('savePart1')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('savePart1'),
        ];
    }

    public function getFormAction3(): array
    {
        return [
            Action::make('savePart3')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('savePart3'),
        ];
    }

    public function getFormAction2(): array
    {
        return [
            Action::make('savePart2')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('savePart2'),
        ];
    }
}
