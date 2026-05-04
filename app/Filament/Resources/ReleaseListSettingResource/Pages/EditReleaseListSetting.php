<?php

namespace App\Filament\Resources\ReleaseListSettingResource\Pages;

use App\Filament\Resources\ReleaseListSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReleaseListSetting extends EditRecord
{
    protected static string $resource = ReleaseListSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

}
