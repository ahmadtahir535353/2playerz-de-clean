<?php

namespace App\Filament\Resources\SubCategoriesResource\Pages;

use App\Filament\Resources\SubCategoriesResource;
use App\Models\Navigation;
use App\Models\SubCategory;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManageSubCategories extends ManageRecords
{
    protected static string $resource = SubCategoriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('messages.sub_category.add'))
                ->modalWidth('lg')
                ->createAnother(false)
                ->modalHeading(__('messages.sub_category.add'))
                ->action(function (array $data) {
                    $subCategory = SubCategory::create($data);
                    $navigationOrder = Navigation::whereNavigationableType(SubCategory::class)
                        ->whereParentId($subCategory['parent_category_id'])->count() + 1;
                    Navigation::create([
                        'navigationable_type' => SubCategory::class,
                        'navigationable_id' => $subCategory['id'],
                        'order_id' => $navigationOrder,
                        'parent_id' => $subCategory['parent_category_id'] ?? null,
                    ]);

                    return Notification::make()
                        ->title(__('messages.placeholder.sub_categories_saved_successfully'))
                        ->success()
                        ->send();
                }),
        ];
    }
}
