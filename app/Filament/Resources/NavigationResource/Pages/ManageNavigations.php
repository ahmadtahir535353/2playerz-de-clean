<?php

namespace App\Filament\Resources\NavigationResource\Pages;

use App\Filament\Resources\NavigationResource;
use App\Models\Language;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Session;

class ManageNavigations extends ManageRecords
{
    protected static string $resource = NavigationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('select')
                ->modalWidth('sm')
                ->label(__('messages.common.select_language'))
                ->form([
                    Select::make('option')
                        ->label('')
                        ->native(false)
                        ->searchable()
                        ->preload()
                        ->options(Language::all()->pluck('name', 'id'))
                        ->default(function () {
                            $lag = Language::where('iso_code', Session::get('languageChange') == null ? 'en' : Session::get('languageChange'))->first()->id;
                            return $lag;
                        })
                        ->required(),
                ])
                ->action(function (array $data) {
                    $selectedOption = $data['option'];
                    $language = Language::whereId($selectedOption)->first()->iso_code;
                    Session::put('languageChange', $language);
                    return Notification::make()
                        ->success()
                        ->title(__('messages.placeholder.language_updated_successfully'))
                        ->send();
                }),
        ];
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return __('messages.placeholder.post_created_successfully');
    }

    public function getTitle(): string
    {
        return __('messages.navigation');
    }
}
