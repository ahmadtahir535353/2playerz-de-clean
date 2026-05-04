<?php

namespace App\Filament\Resources\PollsResource\Pages;

use App\Filament\Resources\PollsResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditPolls extends EditRecord
{
    protected static string $resource = PollsResource::class;

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
        return __('messages.placeholder.poll_updated_successfully');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getTitle(): string
    {
        return __('messages.common.edit').' '.__('messages.poll.poll');
    }
}
