<?php

namespace App\Filament\Resources\EmojiResource\Pages;

use App\Filament\Resources\EmojiResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEmoji extends ViewRecord
{
    protected static string $resource = EmojiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
