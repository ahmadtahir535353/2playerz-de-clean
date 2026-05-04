<?php

namespace App\Filament\Resources\MenusResource\Pages;

use App\Filament\Resources\MenusResource;
use App\Models\Menu;
use App\Models\Navigation;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateMenus extends CreateRecord
{
    protected static string $resource = MenusResource::class;

    protected static bool $canCreateAnother = false;

    protected function getCreatedNotificationTitle(): ?string
    {
        return __('messages.placeholder.menu_created_successfully');
    }


    protected function handleRecordCreation(array $input): Model
    {
        $menu = Menu::create($input);

        if (isset($menu['parent_menu_id'])) {
            $navigationOrder = Navigation::whereNavigationableType(Menu::class)
                ->whereParentId($menu['parent_menu_id'])->count() + 1;
        } else {
            $navigationOrder = Navigation::whereNull('parent_id')->count() + 1;
        }

        Navigation::create([
            'navigationable_type' => Menu::class,
            'navigationable_id' => $menu['id'],
            'order_id' => $navigationOrder,
            'parent_id' => $menu['parent_menu_id'] ?? null,
        ]);

        return $menu;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getTitle(): string
    {
        return __('messages.common.add') . ' ' . __('messages.menu.menu');
    }
}
