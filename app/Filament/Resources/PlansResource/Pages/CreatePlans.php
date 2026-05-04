<?php

namespace App\Filament\Resources\PlansResource\Pages;

use App\Filament\Resources\PlansResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePlans extends CreateRecord
{
    protected static string $resource = PlansResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return __('messages.placeholder.plan_created_successfully');
    }

    public function getTitle(): string
    {
        return __('messages.common.add').' '.__('messages.plans.plan');
    }
}
