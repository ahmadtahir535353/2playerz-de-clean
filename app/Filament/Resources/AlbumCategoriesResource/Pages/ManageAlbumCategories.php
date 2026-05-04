<?php

namespace App\Filament\Resources\AlbumCategoriesResource\Pages;

use App\Filament\Resources\AlbumCategoriesResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAlbumCategories extends ManageRecords
{
    protected static string $resource = AlbumCategoriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label(__('messages.album_category.add_album_category'))
            ->modalWidth('md')
            ->createAnother(false)
            ->modalHeading(__('messages.album_category.add_album_category'))
            ->successNotificationTitle(__('messages.placeholder.album_category_created_successfully')),
        ];
    }
}
