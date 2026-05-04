<?php

namespace App\Filament\Resources\GalleryImagesResource\Pages;

use App\Filament\Resources\GalleryImagesResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditGalleryImages extends EditRecord
{
    protected static string $resource = GalleryImagesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\ViewAction::make(),
            // Actions\DeleteAction::make(),
            Action::make('back')
                ->label(__('messages.common.back'))
                ->url($this->getResource()::getUrl('index')),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return __('messages.placeholder.gallery_image_updated_successfully');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getTitle(): string
    {
        return __('messages.gallery.edit_images');
    }
}
