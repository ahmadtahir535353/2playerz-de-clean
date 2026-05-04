<?php

namespace App\Filament\Resources\EmojiResource\Pages;

use App\Filament\Resources\EmojiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmoji extends ListRecords
{
    protected static string $resource = EmojiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
