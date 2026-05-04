<?php

namespace App\Filament\Resources\PlayerzLevelResource\Pages;

use App\Filament\Resources\PlayerzLevelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlayerzLevel extends EditRecord
{
    protected static string $resource = PlayerzLevelResource::class;

     protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
