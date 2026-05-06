<?php

namespace App\Filament\Resources\StaffResource\Pages;

use App\Filament\Resources\StaffResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class EditStaff extends EditRecord
{
    protected static string $resource = StaffResource::class;

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
        return __('messages.placeholder.staff_updated_successfully');
    }

    // protected function getSavedNotification(): ?Notification
    // {
    //     return Notification::make()
    //     ->success()
    //     ->title($this->getSavedNotificationTitle())
    //     ->body("Staff has been updated successfully.");
    // }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getTitle(): string
    {
        return __('messages.common.edit').' '.__('messages.staff.staff');
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (isset($data['password']) && !empty($data['password'])
            && !preg_match('/^\$2[ayb]\$/', $data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return parent::handleRecordUpdate($record, $data);
    }
}
