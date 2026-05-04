<?php

namespace App\Filament\Resources\ReleaseCalendarBadgeColorResource\Pages;

use App\Filament\Resources\ReleaseCalendarBadgeColorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReleaseCalendarBadgeColor extends EditRecord
{
    protected static string $resource = ReleaseCalendarBadgeColorResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
