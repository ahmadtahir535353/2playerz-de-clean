<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Enums\Sidebar;
use App\Filament\Clusters\Settings;
use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Pages\SubNavigationPosition;
use Illuminate\Support\Facades\Auth;

class ThemeConfiguration extends Page
{
    // protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.clusters.settings.pages.theme-configuration';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?int $navigationSort = Sidebar::THEME_CONFIGURATION->value;

    protected static ?string $cluster = Settings::class;

    public $theme_id = 1;

    public static function getNavigationLabel(): string
    {
        return __('messages.setting.theme_configuration');
    }

    public function getTitle(): string
    {
        return __('messages.setting.theme_configuration');
    }

    public static function canView(): bool
    {
        return Auth::user()->hasPermissionTo('manage_settings');
    }

    public function mount(): void
    {
        if (! $this->canView()) {
            abort(403); // Unauthorized access
        }

        $setting = Setting::where('key', 'theme_configuration')->first();
        if ($setting != null) {
            $this->theme_id = $setting->value;
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('save'),
        ];
    }

    public function save()
    {
        $setting = Setting::where('key', 'theme_configuration')->first();
        if ($setting == null) {
            $setting = Setting::create([
                'key' => 'theme_configuration',
                'value' => $this->theme_id
            ]);
        } else {
            $setting->value = $this->theme_id;
            $setting->save();
        }
        Notification::make()
        ->success()
        ->title(__('messages.placeholder.settings_updated_successfully'))
        ->send();
    }
}
