<?php

namespace App\Filament\Resources\ReleaseListSettingResource\Pages;

use App\Filament\Resources\ReleaseListSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReleaseListSettings extends ListRecords
{
    protected static string $resource = ReleaseListSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
