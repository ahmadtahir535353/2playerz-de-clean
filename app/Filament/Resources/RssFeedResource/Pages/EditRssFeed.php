<?php

namespace App\Filament\Resources\RssFeedResource\Pages;

use App\Filament\Resources\RssFeedResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditRssFeed extends EditRecord
{
    protected static string $resource = RssFeedResource::class;

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

    public function mutateFormDataBeforeFill(array $data): array
    {

        $data['tags'] = explode(",", $data['tags']);
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data['tags'] = implode(",", $data['tags']);
        return parent::handleRecordUpdate($record, $data);
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return __('messages.placeholder.rss_feed_update_successfully');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getTitle(): string
    {
        return __('messages.common.edit') . ' ' . __('messages.rss-feed');
    }
}
