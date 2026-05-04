<?php

namespace App\Filament\Resources\SubscribedUserPlansResource\Pages;

use App\Filament\Resources\SubscribedUserPlansResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSubscribedUserPlans extends ManageRecords
{
    protected static string $resource = SubscribedUserPlansResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
