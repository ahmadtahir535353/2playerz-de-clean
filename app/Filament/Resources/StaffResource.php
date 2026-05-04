<?php

namespace App\Filament\Resources;

use App\Enums\Sidebar;
use App\Filament\Resources\StaffResource\Pages;
use App\Filament\Resources\StaffResource\RelationManagers;
use App\Http\Middleware\CheckPaddingSubscription;
use App\Models\MailSetting;
use App\Models\Role;
use App\Models\Staff;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class StaffResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = Sidebar::STAFFS->value;

    // protected static ?string $navigationGroup = 'User Management';

    // protected static ?string $navigationLabel = 'Staffs';

    protected static string|array $routeMiddleware = [
        CheckPaddingSubscription::class,
    ];

    public static function form(Form $form): Form
    {

        return $form
            ->schema(User::getForm($form));
    }

    public static function table(Table $table): Table
    {
        $table = $table->modifyQueryUsing(function ($query) {
            $query->where('type', User::STAFF)
                ->with(['roles', 'subscription.plan']);
        });
        return $table
            ->recordUrl(false)
            ->defaultSort('id', 'desc')
            ->columns([
                SpatieMediaLibraryImageColumn::make('profile')
                    ->label(__('messages.staff.profile'))
                    ->circular()
                    ->collection(Staff::PROFILE)
                    ->simpleLightbox()
                    ->defaultImageUrl(asset('images/avatar.png')),
                TextColumn::make('full_name')
                    ->label(__('messages.user.full_name'))
                    ->description(function (User $record) {
                        return $record->email;
                    })
                    ->sortable(['first_name'])
                    ->searchable(['first_name', 'last_name', 'email']),
                TextColumn::make('subscription.plan.name')->label(__('messages.subscription.current_plan'))->default(__('messages.menu.n_a')),
                TextColumn::make('username')
                    ->label(__('messages.staff.username'))
                    ->searchable()
                    ->sortable()
                    ->default(__('messages.menu.n_a')),
                TextColumn::make('roles.name')
                    ->label(__('messages.staff.role'))
                    ->sortable(),
                ToggleColumn::make('email_verified_at')
                    ->label(__('messages.staff.email_verified'))
                    ->sortable()
                    ->disabled(function (User $record) {
                        return $record->email_verified_at !== null;
                    })
                    ->updateStateUsing(function ($record, $state) {
                        $record->email_verified_at = $state ? now() : null;
                        $record->save();

                        return $state;
                    })
                    ->afterStateUpdated(function ($record, $state) {
                        Notification::make()
                            ->success()
                            ->title(__('messages.staff.email_verified') . ' ' . __('messages.successfylly'))
                            ->duration(2000)
                            ->send();
                    }),
                ToggleColumn::make('status')
                    ->label(__('messages.status'))
                    ->sortable()
                    ->afterStateUpdated(function ($state) {
                        Notification::make()
                            ->success()
                            ->title(__('messages.placeholder.status_updated_successfully'))
                            ->duration(2000)
                            ->send();
                    }),

                    TextColumn::make('comment_points')
                        ->label('Points')
                        ->sortable()
                        ->searchable(),

                TextColumn::make('level')
                    ->label('Level')
                    ->formatStateUsing(function ($state, $record) {
                        $levelObj = $record->level_object;
                        $badgeBgColor = ($levelObj && !empty($levelObj->badge_color)) ? $levelObj->badge_color : '#1e40af';
                        $badgeTextColor = ($levelObj && !empty($levelObj->badge_text_color)) ? $levelObj->badge_text_color : '#93c5fd';
                        return '<span style="display: inline-block; background-color: ' . $badgeBgColor . '; color: ' . $badgeTextColor . '; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 500; line-height: 1.4;">' . ($state ?: 'Newbie') . '</span>';
                    })
                    ->html()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('password_plain')
                    ->label(__('messages.staff.password'))
                    ->searchable()
                    ->copyable()
                    ->copyMessage(__('messages.placeholder.password_copied'))
                    ->copyMessageDuration(1500)
                    ->default(__('messages.menu.n_a')),


            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('demo')
                    ->icon('heroicon-o-envelope')
                    ->iconButton()
                    ->hidden(function (User $record) {
                        return $record->email_verified_at !== null;
                    })
                    ->action(function (User $record) {
                        $user = User::whereId($record->id)->first();
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
                        $user->sendEmailVerificationNotification();

                        Notification::make()
                            ->success()
                            ->title(__('messages.placeholder.email_send_successfully'))
                            ->send();
                    }),
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->successNotificationTitle(__('messages.placeholder.staff_updated_successfully'))
                    ->visible(function (User $record) {
                        return $record->email !== 'customer@infynews.com' && $record->email !== 'staff@infynews.com';
                    }),
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->modalHeading(__('messages.delete') . ' ' . __('messages.staff.staff'))
                    ->modalDescription(__('messages.common.are_you_sure'))
                    ->modalCancelActionLabel(__('messages.common.cancel'))
                    ->modalSubmitActionLabel(__('messages.common.confirm'))
                    ->successNotificationTitle(__('messages.placeholder.staff_deleted_successfully'))
                    ->visible(function (User $record) {
                        return $record->email !== 'customer@infynews.com' && $record->email !== 'staff@infynews.com';
                    }),

            ])
            ->actionsColumnLabel(__('messages.common.action'))
            ->actionsAlignment(function () {
                return Session::get('locale') == 'ar' ? 'left' : 'right';
            })
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->tooltip(__('messages.delete'))
                        ->modalHeading(__('messages.delete') . ' ' . __('messages.selected') . ' ' . __('messages.staffs'))
                        ->modalDescription(__('messages.common.are_you_sure'))
                        ->modalCancelActionLabel(__('messages.common.cancel'))
                        ->modalSubmitActionLabel(__('messages.common.confirm'))
                        ->successNotificationTitle(__('messages.placeholder.staff_deleted_successfully')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStaff::route('/'),
            'create' => Pages\CreateStaff::route('/create'),
            'view' => Pages\ViewStaff::route('/{record}'),
            'edit' => Pages\EditStaff::route('/{record}/edit'),
        ];
    }


    public static function getNavigationLabel(): string
    {
        return __('messages.staffs');
    }

    public static function getmodelLabel(): string
    {
        return __('messages.staffs');
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->hasPermissionTo('manage_staff');
    }
}
