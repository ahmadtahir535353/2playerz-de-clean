<?php

namespace App\Filament\Pages\Auth;

use App\Http\Middleware\CheckPaddingSubscription;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;

class EditProfile extends BaseEditProfile
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.edit-profile';

    protected static string|array $routeMiddleware = [
        CheckPaddingSubscription::class,
    ];

    public static function getLabel(): string
    {
        return __('messages.user.profile_details');
    }

    protected function getForms(): array
{
    return [
        'form' => $this->form(
            $this->makeForm()
                ->schema([
                    Section::make()
                        ->columns(4)
                        ->schema([
                            Group::make([
                                SpatieMediaLibraryFileUpload::make('profile')
                                    ->label(__('messages.user.avatar') . ':')
                                    ->validationAttribute(__('messages.user.avatar'))
                                    ->disk(config('app.media_disk'))
                                    ->collection(User::PROFILE)
                                    ->image()
                                    ->imagePreviewHeight(150)
                                    ->imageEditor('cropper')
                                    ->inlineLabel(false)
                                    ->required(),
                            ]),
                            Group::make([
                                TextInput::make('first_name')
                                    ->label(__('messages.staff.first_name') . ':')
                                    ->validationAttribute(__('messages.staff.first_name'))
                                    ->placeholder(__('messages.staff.first_name'))
                                    ->required()
                                    ->maxLength(255),
                                    
                                TextInput::make('last_name')
                                    ->label(__('messages.staff.last_name') . ':')
                                    ->validationAttribute(__('messages.staff.last_name'))
                                    ->placeholder(__('messages.staff.last_name'))
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('email')
                                    ->label(__('messages.user.email') . ':')
                                    ->validationAttribute(__('messages.user.email'))
                                    ->placeholder(__('messages.user.email'))
                                    ->email()
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('contact')
                                    ->tel()
                                    ->rules(['required', 'regex:/^[0-9]{10}$/'])
                                    ->label(__('messages.user.contact_number') . ':')
                                    ->validationAttribute(__('messages.user.contact_number'))
                                    ->placeholder(__('messages.user.contact_number'))
                                    ->required(),

                                Textarea::make('about_us')
                                    ->label(__('messages.staff.about_us') . ':')
                                    ->validationAttribute(__('messages.staff.about_us'))
                                    ->placeholder(__('messages.staff.about_us'))
                                    ->columnSpanFull()
                                    ->rows(3),

                                DatePicker::make('dob')
                                    ->label('Date of Birth')
                                    ->required(),

                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        '1' => 'Active',
                                        '0' => 'Inactive',
                                    ])
                                    ->required(),

                                Select::make('blood_group')
                                    ->label('Blood Group')
                                    ->options([
                                        'A+' => 'A+',
                                        'A-' => 'A-',
                                        'B+' => 'B+',
                                        'B-' => 'B-',
                                        'AB+' => 'AB+',
                                        'AB-' => 'AB-',
                                        'O+' => 'O+',
                                        'O-' => 'O-',
                                    ])
                                    ->required(),

                                TextInput::make('interest')
                                    ->label('Interest')
                                    ->maxLength(255),

                                TextInput::make('favorite_game')
                                    ->label('Favorite Game')
                                    ->maxLength(255),

                                // ✅ Newly added fields:
                                TextInput::make('city')
                                    ->label('City')
                                    ->maxLength(255),

                                TextInput::make('hardware')
                                    ->label('Hardware')
                                    ->maxLength(255),

                                // DateTimePicker::make('last_seen_at')
                                //     ->label('Last Seen At'),
                            ])->columnSpan(3)->columns(2),
                        ]),
                        
                        // Private Message Settings Section
                        Section::make('Private Message Settings')
                            ->schema([
                                Select::make('who_can_send_messages')
                                    ->label('Who can send me private messages')
                                    ->options([
                                        'all' => 'All members',
                                        'following' => 'Members I follow',
                                        'nobody' => 'Nobody',
                                    ])
                                    ->default('all')
                                    ->required(),
                                    
                                Select::make('message_notification_preference')
                                    ->label('How I want to be notified about new messages')
                                    ->options([
                                        'notification_only' => 'Only notification on website',
                                        'email_and_notification' => 'Email and notification',
                                    ])
                                    ->default('email_and_notification')
                                    ->required(),
                            ])->columnSpan(3)->columns(1),
                ])
                ->operation('edit')
                ->model($this->getUser())
                ->statePath('data'),
        ),
    ];
}



    public function getFormAction(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('save'),
        ];
    }

    protected function afterSave(): void
    {
        $this->js('window.location.reload()');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return __('messages.placeholder.User_profile_updated_successfully');
    }

    // protected function getActions(): array
    // {
    //     return [
    //         Action::make('back')
    //             ->label(__('messages.common.back'))
    //             ->url(function (Request $request) {
    //                 return session()->get('previous_url', URL::previous());
    //             }),
    //     ];
    // }
}
