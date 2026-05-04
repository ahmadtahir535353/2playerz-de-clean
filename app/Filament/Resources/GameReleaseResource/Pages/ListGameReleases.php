<?php

namespace App\Filament\Resources\GameReleaseResource\Pages;

use App\Filament\Resources\GameReleaseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGameReleases extends ListRecords
{
    protected static string $resource = GameReleaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
