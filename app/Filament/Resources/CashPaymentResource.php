<?php

namespace App\Filament\Resources;

use App\Enums\Sidebar;
use App\Filament\Resources\CashPaymentResource\Pages;
use App\Filament\Resources\CashPaymentResource\RelationManagers;
use App\Http\Middleware\CheckPaddingSubscription;
use App\Mail\ManualPaymentStatusMail;
use App\Models\CashPayment;
use App\Models\MailSetting;
use App\Models\Subscription;
// use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action as ActionsAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\SelectColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class CashPaymentResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = Sidebar::CASH_PAYMENTS->value;

    protected static string|array $routeMiddleware = [
        CheckPaddingSubscription::class,
    ];

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(false)
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('user.full_name')
                    ->label(__('messages.user.full_name'))
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('plan.name')
                    ->label(__('messages.subscription.plan_name'))
                    ->searchable(),
                TextColumn::make('plan_amount')
                    ->label(__('messages.subscription.plan_price'))
                    ->searchable(),
                TextColumn::make('payable_amount')
                    ->label(__('messages.subscription.payable_amount'))
                    ->searchable(),
                TextColumn::make('starts_at')
                    ->label(__('messages.subscription.start_date'))
                    ->formatStateUsing(function (Subscription $record) {
                        return \Carbon\Carbon::parse($record->starts_at)->isoFormat('DD/MM/YYYY');
                    })
                    ->searchable(),
                TextColumn::make('ends_at')
                    ->label(__('messages.subscription.end_date'))
                    ->formatStateUsing(function (Subscription $record) {
                        return \Carbon\Carbon::parse($record->ends_at)->isoFormat('DD/MM/YYYY');
                    })
                    ->searchable(),
                TextColumn::make('attachment')
                    ->label(__('messages.attachment'))
                    ->default(__('messages.menu.n_a'))
                    ->alignCenter()
                    ->url(function (Subscription $record) {
                        if ($record->attachment) {
                            return route('download.attachment', $record->id);
                        }
                    }, true)
                    ->formatStateUsing(function (Subscription $record) {
                        if ($record->attachment) {
                            return "<span class='inline-block my-2 mx-1 px-3 py-1 text-lg font-semibold text-white bg-primary-600 rounded-full'>&#10515;</span>";
                        } else {
                            return __('messages.menu.n_a');
                        }
                    })
                    ->html(),
                TextColumn::make('notes')
                    ->label(__('messages.notes'))
                    ->default(__('messages.menu.n_a')) // Display 'Null' if the notes field is empty
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('demo')
                    ->button()
                    ->label(function (Subscription $record) {
                        if ($record->status == 2) {
                            return __('messages.common.pending');
                        } elseif ($record->status == 1) {
                            return __('messages.common.approved');
                        } elseif ($record->status == 3) {
                            return __('messages.common.rejected');
                        } elseif ($record->status == 0) {
                            return __('messages.common.deactive');
                        }
                    })
                    ->color(function (Subscription $record) {
                        if ($record->status == 2) {
                            return 'warning';
                        } elseif ($record->status == 1) {
                            return 'success';
                        } elseif ($record->status == 3) {
                            return 'danger';
                        } else {
                            return 'info';
                        }
                    })
                    ->modal('asdasd')
                    ->disabled(false)
                    ->modalHeading('Demo')
                    ->action(function (array $data, Subscription $record) {
                        $input = $data;
                        $input['notes'] = isset($input['notes']) ? $input['notes'] : null;
                        if ($input['status'] == 0) {
                            Subscription::whereId($record->id)->update([
                                'status' => 3,
                                'reject_notes' => $input['notes'],
                                'payment_type' => Subscription::REJECTED,
                            ]);
                        }
                        // Approved Payment
                        if ($input['status'] == 1) {
                            Subscription::whereUserId($record->user->id)
                                ->where('id', '!=', $record->id)
                                ->update(['status' => 0]);

                            Subscription::whereId($record->id)->update([
                                'status' => 1,
                                'reject_notes' => $input['notes'],
                                'payment_type' => Subscription::PAID,
                            ]);
                        }
                        $input['status'] = ($input['status'] == 1) ? 'Approved' : 'Rejected';
                        $super_admin_data = [
                            'super_admin_msg' => __('messages.placeholder.your_manual_payment_request_is') . ' ' . $input['status'] . ' of ' . $record->plan->currency->currency_icon . '' . $record->plan->price,
                            'notes' => $input['notes'] ?? '',
                            'name' => $record->user->full_name,
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

                        Mail::to($record->user->email)
                            ->send(new ManualPaymentStatusMail($super_admin_data, $record->user));

                        Notification::make()
                            ->success()
                            ->title(__('messages.placeholder.payment_received'))
                            ->send();
                    })
                    ->form([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                '1' => 'Approved',
                                '0' => 'Rejected',
                            ])
                            ->selectablePlaceholder(false)
                            ->native(false)
                            ->required(),
                        Textarea::make('notes')
                            ->rows(3)
                            ->label('Notes')
                            ->maxLength(255),
                    ])
                    ->modalWidth('lg')
                    ->modal(function (Subscription $record) {
                        if ($record->status == 2) {
                            return true;
                        }
                        return false;
                    })

            ])
            ->actionsColumnLabel(__('messages.status'))
            ->actionsAlignment(function () {
                return Session::get('locale') == 'ar' ? 'left' : 'right';
            });
        // ->bulkActions([
        //     Tables\Actions\BulkActionGroup::make([
        //         Tables\Actions\DeleteBulkAction::make(),
        //     ]),
        // ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCashPayments::route('/'),
            'create' => Pages\CreateCashPayment::route('/create'),
            'edit' => Pages\EditCashPayment::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.cash_payment');
    }

    public static function getmodelLabel(): string
    {
        return __('messages.cash_payment');
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->hasPermissionTo('cash_payment');
    }
}
