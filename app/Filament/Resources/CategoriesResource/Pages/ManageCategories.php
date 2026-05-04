<?php

namespace App\Filament\Resources\CategoriesResource\Pages;

use App\Filament\Resources\CategoriesResource;
use App\Models\Category;
use App\Models\Navigation;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManageCategories extends ManageRecords
{
    protected static string $resource = CategoriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('messages.category.add_category'))
                ->modalWidth('lg')
                ->createAnother(false)
                ->modalHeading(__('messages.category.add_category'))
                ->after(function ($record) {
                    $navigationOrder = Navigation::whereNull('parent_id')->count() + 1;

                    Navigation::create([
                        'navigationable_type' => Category::class,
                        'navigationable_id' => $record['id'],
                        'order_id' => $navigationOrder,
                    ]);
                })->successNotificationTitle(__('messages.placeholder.category_created_successfully'))
        ];
    }
}
