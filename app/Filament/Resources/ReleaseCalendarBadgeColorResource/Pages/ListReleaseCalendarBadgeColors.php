<?php

namespace App\Filament\Resources\ReleaseCalendarBadgeColorResource\Pages;

use App\Filament\Resources\ReleaseCalendarBadgeColorResource;
use Filament\Resources\Pages\ListRecords;

class ListReleaseCalendarBadgeColors extends ListRecords
{
    protected static string $resource = ReleaseCalendarBadgeColorResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
