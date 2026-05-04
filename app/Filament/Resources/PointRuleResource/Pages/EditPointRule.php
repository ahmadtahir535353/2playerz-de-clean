<?php

namespace App\Filament\Resources\PointRuleResource\Pages;

use App\Filament\Resources\PointRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditPointRule extends EditRecord
{
    protected static string $resource = PointRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return __('messages.other_lang.points_updated_successfully');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getTitle(): string
    {
        return 'Edit ' . $this->record->label . ' Points';
    }
}