<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Enums\Sidebar;
use App\Filament\Clusters\Settings;
use App\Models\Setting;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Pages\SubNavigationPosition;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Auth;

class CookieWarning extends Page
{
    public ?array $data = [];

    // protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.clusters.settings.pages.cookie-warning';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?int $navigationSort = Sidebar::COOKIE_WARNING->value;

    protected static ?string $cluster = Settings::class;

    public ?array  $record = [];

    public static function getNavigationLabel(): string
    {
        return __('messages.setting.cookie_warning');
    }

    public function getTitle(): string
    {
        return __('messages.setting.cookie_warning');
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

        $this->record = Setting::pluck('value', 'key')->toArray();

        // $this->form->fill([
        //     'show_cookie' => $this->record['show_cookie'],
        //     'cookie_warning' => $this->record['cookie_warning'],
        // ]);

        $this->form->fill($this->record);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Radio::make('show_cookie')
                    ->label(__('messages.setting.show_cookie_warning').':')
                    ->validationAttribute(__('messages.setting.show_cookie_warning'))
                    ->options([
                        Setting::Yes => __('messages.page.yes'),
                        Setting::No => __('messages.page.no'),
                    ])
                    ->inlineLabel(true)
                    ->columns(7),
                Textarea::make('cookie_warning')
                    ->label(__('messages.setting.cookie_warning').':')
                    ->validationAttribute(__('messages.setting.cookie_warning'))
                    ->placeholder(__('messages.setting.cookie_warning'))
                    ->rows(5)
                    ->inlineLabel(true),
            ])->statePath('data');
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();
            foreach ($data as $key => $value) {
                $setting = Setting::where('key', $key)->first();
                if ($setting) {
                    $setting->update(['value' => $value]);
                } else {
                    Setting::create(['key' => $key, 'value' => $value]);
                }
            }
            Notification::make()
                ->success()
                ->title(__('messages.placeholder.settings_updated_successfully'))
                ->send();
        } catch (Halt $exception) {
            $this->notify('error', $exception->getMessage());
        }
    }
}
