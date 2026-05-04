<?php

namespace App\Filament\Resources\MenusResource\Pages;

use App\Filament\Resources\MenusResource;
use App\Models\Menu;
use App\Models\Navigation;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditMenus extends EditRecord
{
    protected static string $resource = MenusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label(__('messages.common.back'))
                ->url($this->getResource()::getUrl('index')),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return __('messages.placeholder.menu_update_successfully');
    }

    protected function handleRecordUpdate(Model $record, array $input): Model
    {
        $record = Menu::find($record->id);
        $oldParentId = $record->parent_menu_id;
        $newParentId = $input['parent_menu_id'];

        $changeParentMenu = $newParentId != $oldParentId;

        $record->update($input);

        if ($changeParentMenu) {
            if ($newParentId) {
                $navigationOrder = Navigation::whereNavigationableType(Menu::class)
                    ->whereParentId($newParentId)
                    ->count() + 1;
            } else {
                $navigationOrder = Navigation::whereNull('parent_id')->count() + 1;
            }

            $record->navigation->update([
                'order_id' => $navigationOrder,
                'parent_id' => $newParentId,
            ]);
            if ($oldParentId) {
                $subsNavigation = Navigation::whereNavigationableType(Menu::class)
                    ->whereParentId($oldParentId)
                    ->orderBy('order_id')
                    ->get();

                foreach ($subsNavigation as $key => $navigation) {
                    $navigation->update([
                        'order_id' => $key + 1,
                    ]);
                }
            }
        }

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getTitle(): string
    {
        return __('messages.common.edit') . ' ' . __('messages.menu.menu');
    }
}
