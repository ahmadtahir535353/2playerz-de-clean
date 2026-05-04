<?php

namespace App\Filament\Resources\PollsResource\Pages;

use App\Filament\Resources\PollsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPolls extends ViewRecord
{
    protected static string $resource = PollsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
