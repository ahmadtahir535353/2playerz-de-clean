<?php

namespace App\Filament\Resources\StaffResource\Pages;

use App\Filament\Resources\StaffResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStaff extends ListRecords
{
    protected static string $resource = StaffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label(__('messages.common.add').' '.__('messages.staff.staff')),
        ];
    }

    public function getTitle(): string
    {
        return __('messages.staffs');
    }
    // public static function getNavigationLabel(): string
    // {
    //     return 'messages.subscription.subscribed_plans';
    // }
}
