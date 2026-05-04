<?php

namespace App\Filament\Resources\RssFeedResource\Pages;

use App\Filament\Resources\RssFeedResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRssFeed extends ViewRecord
{
    protected static string $resource = RssFeedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
