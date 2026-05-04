<?php

namespace App\Filament\Resources\PollsResource\Pages;

use App\Filament\Resources\PollsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePolls extends CreateRecord
{
    protected static string $resource = PollsResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return __('messages.placeholder.poll_created_successfully');
    }

    public function getTitle(): string
    {
        return __('messages.common.add').' '.__('messages.poll.poll');
    }
}
