<?php

namespace App\Filament\Resources\RolesResource\Pages;

use App\Filament\Resources\RolesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRoles extends CreateRecord
{
    protected static string $resource = RolesResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return __('messages.placeholder.role_created_successfully');
    }

    public function getTitle(): string
    {
        return __('messages.role.add_role');
    }
}
