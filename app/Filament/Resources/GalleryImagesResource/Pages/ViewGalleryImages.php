<?php

namespace App\Filament\Resources\GalleryImagesResource\Pages;

use App\Filament\Resources\GalleryImagesResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewGalleryImages extends ViewRecord
{
    protected static string $resource = GalleryImagesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
