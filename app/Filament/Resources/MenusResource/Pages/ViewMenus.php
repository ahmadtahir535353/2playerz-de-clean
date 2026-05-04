<?php

namespace App\Filament\Resources\MenusResource\Pages;

use App\Filament\Resources\MenusResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMenus extends ViewRecord
{
    protected static string $resource = MenusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
