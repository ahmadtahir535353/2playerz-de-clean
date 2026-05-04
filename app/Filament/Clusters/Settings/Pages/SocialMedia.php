<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Enums\Sidebar;
use App\Filament\Clusters\Settings;
use App\Models\Setting;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Pages\SubNavigationPosition;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Auth;

class SocialMedia extends Page
{
    public ?array $data = [];

    // protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.clusters.settings.pages.social-media';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?int $navigationSort = Sidebar::SOCIAL_MEDIA->value;

    protected static ?string $cluster = Settings::class;

    public ?array  $record = [];

    public static function getNavigationLabel(): string
    {
        return __('messages.setting.social_media_setting');
    }

    public function getTitle(): string
    {
        return __('messages.setting.social_media_setting');
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
        //     'facebook_url' => $this->record['facebook_url'],
        //     'twitter_url' => $this->record['twitter_url'],
        //     'instagram_url' => $this->record['instagram_url'],
        //     'pinterest_url' => $this->record['pinterest_url'],
        //     'linkedin_url' => $this->record['linkedin_url'],
        //     'vk_url' => $this->record['vk_url'],
        //     'telegram_url' => $this->record['telegram_url'],
        //     'youtube_url' => $this->record['youtube_url'],
        // ]);

        $this->form->fill($this->record);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('facebook_url')
                    ->label(__('messages.setting.facebook_url').':')
                    ->validationAttribute(__('messages.setting.facebook_url'))
                    ->placeholder(__('messages.setting.facebook_url'))
                    ->required()
                    ->inlineLabel(true)
                    ->url(),
                TextInput::make('twitter_url')
                    ->label(__('messages.setting.twitter_url').':')
                    ->validationAttribute(__('messages.setting.twitter_url'))
                    ->placeholder(__('messages.setting.twitter_url'))
                    ->required()
                    ->inlineLabel(true)
                    ->url(),
                TextInput::make('instagram_url')
                    ->label(__('messages.setting.instagram_url').':')
                    ->validationAttribute(__('messages.setting.instagram_url'))
                    ->placeholder(__('messages.setting.instagram_url'))
                    ->required()
                    ->inlineLabel(true)
                    ->url(),
                TextInput::make('pinterest_url')
                    ->label(__('messages.setting.pinterest_url').':')
                    ->validationAttribute(__('messages.setting.pinterest_url'))
                    ->placeholder(__('messages.setting.pinterest_url'))
                    ->required()
                    ->inlineLabel(true)
                    ->url(),
                TextInput::make('linkedin_url')
                    ->label(__('messages.setting.linkedin_url').':')
                    ->validationAttribute(__('messages.setting.linkedin_url'))
                    ->placeholder(__('messages.setting.linkedin_url'))
                    ->required()
                    ->inlineLabel(true)
                    ->url(),
                TextInput::make('vk_url')
                    ->label(__('messages.setting.vk_url').':')
                    ->validationAttribute(__('messages.setting.vk_url'))
                    ->placeholder(__('messages.setting.vk_url'))
                    ->required()
                    ->inlineLabel(true)
                    ->url(),
                TextInput::make('telegram_url')
                    ->label(__('messages.setting.telegram_url').':')
                    ->validationAttribute(__('messages.setting.telegram_url'))
                    ->placeholder(__('messages.setting.telegram_url'))
                    ->required()
                    ->inlineLabel(true)
                    ->url(),
                TextInput::make('youtube_url')
                    ->label(__('messages.setting.youtube_url').':')
                    ->validationAttribute(__('messages.setting.youtube_url'))
                    ->placeholder(__('messages.setting.youtube_url'))
                    ->required()
                    ->inlineLabel(true)
                    ->url(),
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
