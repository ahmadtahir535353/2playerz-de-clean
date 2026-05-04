<?php

namespace App\Filament\Resources\PlayerzLevelResource\Pages;

use App\Filament\Resources\PlayerzLevelResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePlayerzLevel extends CreateRecord
{
    protected static string $resource = PlayerzLevelResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function hasCreateAnother(): bool
    {
        return false;
    }

}
