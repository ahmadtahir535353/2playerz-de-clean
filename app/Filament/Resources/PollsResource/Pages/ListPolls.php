<?php

namespace App\Filament\Resources\PollsResource\Pages;

use App\Filament\Resources\PollsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPolls extends ListRecords
{
    protected static string $resource = PollsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label(__('messages.common.add').' '.__('messages.poll.poll')),
        ];
    }
}
