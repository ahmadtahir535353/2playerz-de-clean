<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Enums\Sidebar;
use App\Filament\Clusters\Settings;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Pages\SubNavigationPosition;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class GenerateSitemap extends Page
{
    // protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.clusters.settings.pages.generate-sitemap';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?int $navigationSort = Sidebar::GENERAL_SETTINGS->value;

    protected static ?string $cluster = Settings::class;

    public static function getNavigationLabel(): string
    {
        return __('messages.setting.generate_sitemap');
    }

    public function getTitle(): string
    {
        return __('messages.setting.generate_sitemap');
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

    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('messages.setting.generate_sitemap')),
            ]);
    }

    public function save(): void
    {
        Artisan::call('generate:sitemap');
        Notification::make()
                ->success()
                ->title(__('messages.placeholder.settings_updated_successfully'))
                ->send();
    }
}
