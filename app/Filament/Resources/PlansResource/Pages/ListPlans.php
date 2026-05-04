<?php

namespace App\Filament\Resources\PlansResource\Pages;

use App\Filament\Resources\PlansResource;
use App\Models\Plan;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListPlans extends ListRecords
{
    protected static string $resource = PlansResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label(__('messages.common.add') . ' ' . __('messages.plans.plan')),
        ];
    }

    public function toggleStatus($planId)
    {
        Plan::where('is_default', true)->update(['is_default' => false]);
        Plan::where('id', $planId)->update(['is_default' => true]);

        Notification::make()
            ->title(__('messages.placeholder.default_plan_changed_successfully'))
            ->success()
            ->send();

        $this->resetTable();
    }
}
