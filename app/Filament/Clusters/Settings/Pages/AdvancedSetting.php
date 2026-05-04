<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Enums\Sidebar;
use App\Filament\Clusters\Settings;
use App\Models\Language;
use App\Models\Setting;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Pages\SubNavigationPosition;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class AdvancedSetting extends Page
{
    public ?array $data = [];
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
    protected static ?int $navigationSort = Sidebar::ADVANCED_SETTINGS->value;
    protected static string $view = 'filament.clusters.settings.pages.advanced-setting';
    protected static ?string $cluster = Settings::class;

    public ?array $record = [];

    public static function getNavigationLabel(): string
    {
        return __('messages.setting.advanced_setting');
    }

    public function getTitle(): string
    {
        return __('messages.setting.advanced_setting');
    }

    public static function canView(): bool
    {
        return Auth::user()->hasPermissionTo('manage_settings');
    }

    public function mount(): void
    {
        if (!$this->canView()) {
            abort(403); // Unauthorized access
        }

        $settingsRecord = Setting::pluck('value', 'key')->toArray();
        $activeLanguage = Setting::where('key', 'front_language')->first()->value ?? null;
        $currentFrontLanguage = Setting::where('key', 'front_language')->first();

        // Breaking news status ko load karo
        $breakingNewsStatus = $settingsRecord['breaking_news_status'] ?? false;

        $this->form->fill(array_merge($settingsRecord, [
            'registration_system' => $settingsRecord['registration_system'] ?? false,
            'emoji_system' => $settingsRecord['emoji_system'] ?? false,
            'breaking_news_status' => $breakingNewsStatus, // Toggle ke liye
            'selectedLanguage' => $activeLanguage ?? null,
            'show_staff_markers' => $settingsRecord['show_staff_markers'] ?? true, // Naya toggle ke liye
            'show_poll_votes_count' => $settingsRecord['show_poll_votes_count'] ?? true,
        ]));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Toggle::make('registration_system')
                    ->label(__('messages.setting.registration_system') . ':')
                    ->validationAttribute(__('messages.setting.registration_system'))
                    ->inlineLabel(true)
                    ->default(false),

                Toggle::make('emoji_system')
                    ->label(__('messages.setting.emoji_system') . ':')
                    ->validationAttribute(__('messages.setting.emoji_system'))
                    ->inlineLabel(true)
                    ->default(false),

                Toggle::make('breaking_news_status')
                    ->label(__('messages.other_lang.breaking_news_status') . ':')
                    ->validationAttribute(__('messages.other_lang.breaking_news_status'))
                    ->inlineLabel(true)
                    ->default(false),

                Select::make('selectedLanguage')
                    ->label(__('messages.other_lang.active_language'))
                    ->options(Language::pluck('name', 'id')->toArray())
                    ->nullable()
                    ->placeholder(__('messages.other_lang.active_language')),

                Toggle::make('show_staff_markers')
                    ->label(__('messages.other_lang.staff_marker'))
                    ->helperText(__('messages.other_lang.helper_text'))
                    ->inlineLabel(true)
                    ->default(true),

                Toggle::make('show_poll_votes_count')
                    ->label(__('messages.poll.show_votes_count') . ':')
                    ->helperText(__('messages.poll.show_votes_count_help'))
                    ->inlineLabel(true)
                    ->default(true),
            ])->statePath('data');
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            // Sab settings ko save karo
            foreach (['registration_system', 'emoji_system', 'breaking_news_status', 'show_staff_markers', 'show_poll_votes_count'] as $key) {
                $setting = Setting::where('key', $key)->first();
                if ($setting) {
                    $setting->update(['value' => $data[$key] ?? false]);
                } else {
                    Setting::create(['key' => $key, 'value' => $data[$key] ?? false]);
                }
            }

            if ($data['selectedLanguage']) {
                $selectedLanguage = Language::find($data['selectedLanguage']);
                if ($selectedLanguage) {
                    $frontLanguageSetting = Setting::updateOrCreate(
                        ['key' => 'front_language'],
                        ['value' => $selectedLanguage->id]
                    );
                    Language::where('front_language_status', 1)->update(['front_language_status' => 0]);
                    $selectedLanguage->update(['front_language_status' => 1]);
                    App::setLocale($selectedLanguage->iso_code);
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