<?php

namespace App\Filament\Resources\PostCommentsResource\Pages;

use App\Filament\Resources\PostCommentsResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePostComments extends ManageRecords
{
    protected static string $resource = PostCommentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
