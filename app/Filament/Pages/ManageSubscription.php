<?php

namespace App\Filament\Pages;

use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ManageSubscription extends Page implements HasTable
{
    use InteractsWithTable;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.manage-subscription';

    public ?array $data = [];

    public function getModel(): string
    {
        return Subscription::class;
    }

    protected function getViewData(): array
    {
        $data = [];

        $data['currentPlan'] = Subscription::with(['plan.currency'])
            ->whereUserId(auth()->user()->id)
            ->where('status', Subscription::ACTIVE)->latest()->first();

        $days = $data['remainingDay'] = '';
        
        if ($data['currentPlan']){
            if ($data['currentPlan']->ends_at > Carbon::now()) {
                $days = Carbon::parse($data['currentPlan']->ends_at)->diffInDays();
                $data['remainingDay'] = $days . ' Days';
            }   
        }else{
            $data['remainingDay'] = 0 . ' Days';
        }

        if ($days >= 30 && $days <= 365) {
            $data['remainingDay'] = '';
            $months = floor($days / 30);
            $extraDays = $days % 30;
            if ($extraDays > 0) {
                $data['remainingDay'] .= $months . ' Month ' . $extraDays . ' Days';
            } else {
                $data['remainingDay'] .= $months . ' Month ';
            }
        }

        if ($days >= 365) {
            $data['remainingDay'] = '';
            $years = floor($days / 365);
            $extraMonths = floor($days % 365 / 30);
            $extraDays = floor($days % 365 % 30);
            if ($extraMonths > 0 && $extraDays < 1) {
                $data['remainingDay'] .= $years . ' Years ' . $extraMonths . ' Month ';
            } elseif ($extraDays > 0 && $extraMonths < 1) {
                $data['remainingDay'] .= $years . ' Years ' . $extraDays . ' Days';
            } elseif ($years > 0 && $extraDays > 0 && $extraMonths > 0) {
                $data['remainingDay'] .= $years . ' Years ' . $extraMonths . ' Month ' . $extraDays . ' Days';
            } else {
                $data['remainingDay'] .= $years . ' Years ';
            }
        }

        return $data;
    }

    public function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading(__('messages.no') . ' ' . __('messages.subscriptions'))
            ->defaultSort('id', 'desc')
            ->query(Subscription::query()->where('user_id', auth()->id()))
            ->columns([
                TextColumn::make('plan.name')
                    ->label(__('messages.subscription.plan_name'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('plan_amount')
                    ->label(__('messages.subscription.amount'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('starts_at')
                    ->label(__('messages.subscription.subscribed_date'))
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function (Subscription $record) {
                        return \Carbon\Carbon::parse($record->starts_at)->isoFormat('DD/MM/YYYY');
                    }),
                TextColumn::make('ends_at')
                    ->label(__('messages.subscription.expired_date'))
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function (Subscription $record) {
                        return \Carbon\Carbon::parse($record->ends_at)->isoFormat('DD/MM/YYYY');
                    }),
                TextColumn::make('status')
                    ->label(__('messages.status'))
                    ->formatStateUsing(function (int $state, Subscription $record) {
                        if ($record->ends_at < Carbon::now()) {
                            return __('messages.subscription.expired');
                        } elseif (SubscriptionStatus::PENDING->value == $state) {
                            return __('messages.comment.pending');
                        } elseif (SubscriptionStatus::ACTIVE->value == $state) {
                            return __('messages.common.active');
                        } elseif (SubscriptionStatus::REJECTED->value == $state) {
                            return __('messages.common.rejected');
                        } else {
                            return __('messages.common.closed');
                        }
                    })
                    ->badge()
                    ->color(function (int $state, Subscription $record) {
                        if ($record->ends_at < Carbon::now()) {
                            return 'danger';
                        } elseif (SubscriptionStatus::PENDING->value == $state) {
                            return 'warning';
                        } elseif (SubscriptionStatus::ACTIVE->value == $state) {
                            return 'success';
                        } elseif (SubscriptionStatus::REJECTED->value == $state) {
                            return 'danger';
                        } else {
                            return 'primary';
                        }
                    }),
            ]);
    }
}
