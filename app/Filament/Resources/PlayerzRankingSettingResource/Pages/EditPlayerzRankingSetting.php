<?php

namespace App\Filament\Resources\PlayerzRankingSettingResource\Pages;

use App\Filament\Resources\PlayerzRankingSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlayerzRankingSetting extends EditRecord
{
    protected static string $resource = PlayerzRankingSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}