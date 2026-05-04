<?php

namespace App\Filament\Resources\GalleryImagesResource\Pages;

use App\Filament\Resources\GalleryImagesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGalleryImages extends ListRecords
{
    protected static string $resource = GalleryImagesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label(__('messages.gallery.add_images')),
        ];
    }
}
