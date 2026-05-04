<?php

namespace App\Filament\Resources\AlbumResource\Pages;

use App\Filament\Resources\AlbumResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAlbums extends ManageRecords
{
    protected static string $resource = AlbumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label(__('messages.album.add_album'))
            ->modalWidth('md')
            ->createAnother(false)
            ->modalHeading(__('messages.album.add_album'))
            ->successNotificationTitle(__('messages.placeholder.album_created_successfully')),
        ];
    }
}
