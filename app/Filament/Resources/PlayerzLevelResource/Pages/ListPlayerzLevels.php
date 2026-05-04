<?php

namespace App\Filament\Resources\PlayerzLevelResource\Pages;

use App\Filament\Resources\PlayerzLevelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlayerzLevels extends ListRecords
{
    protected static string $resource = PlayerzLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->disableCreateAnother(),
        ];
    }
}
