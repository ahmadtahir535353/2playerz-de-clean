<?php

namespace App\Filament\Resources\EmojiResource\Pages;

use App\Filament\Resources\EmojiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmoji extends EditRecord
{
    protected static string $resource = EmojiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
