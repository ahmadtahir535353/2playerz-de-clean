<?php

namespace App\Filament\Resources\MenusResource\Pages;

use App\Filament\Resources\MenusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use PhpParser\Node\Stmt\Label;

class ListMenuses extends ListRecords
{
    protected static string $resource = MenusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->Label(__('messages.menu.add_menu')),
        ];
    }
}
