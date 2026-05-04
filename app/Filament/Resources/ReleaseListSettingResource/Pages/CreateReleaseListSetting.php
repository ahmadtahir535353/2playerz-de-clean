<?php

namespace App\Filament\Resources\ReleaseListSettingResource\Pages;

use App\Filament\Resources\ReleaseListSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateReleaseListSetting extends CreateRecord
{
    protected static string $resource = ReleaseListSettingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }
}
