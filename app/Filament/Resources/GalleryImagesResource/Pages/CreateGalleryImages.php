<?php

namespace App\Filament\Resources\GalleryImagesResource\Pages;

use App\Filament\Resources\GalleryImagesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateGalleryImages extends CreateRecord
{
    protected static string $resource = GalleryImagesResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return __('messages.placeholder.gallery_image_created_successfully');
    }

    public function getTitle(): string
    {
        return __('messages.gallery.add_images');
    }
}
