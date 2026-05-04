<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Enums\Sidebar;
use App\Filament\Clusters\Settings;
use App\Models\Setting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Pages\SubNavigationPosition;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Auth;

class ContactInformation extends Page
{
    public ?array $data = [];

    // protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.clusters.settings.pages.contact-information';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?int $navigationSort = Sidebar::CONTENT_INTERACTION->value;

    protected static ?string $cluster = Settings::class;

    public ?array  $record = [];

    public static function getNavigationLabel(): string
    {
        return __('messages.setting.contact_information');
    }

    public function getTitle(): string
    {
        return __('messages.setting.contact_information');
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
        //     'contact_address' => $this->record['contact_address'],
        //     'about_text' => $this->record['about_text'],
        // ]);

        $this->form->fill($this->record);

    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('messages.setting.contact_information'))
                    ->schema([
                        Textarea::make('contact_address')
                            ->label(__('messages.setting.contact_address').':')
                            ->validationAttribute(__('messages.setting.contact_address'))
                            ->placeholder(__('messages.setting.contact_address'))
                            ->rows(3)
                            ->inlineLabel(true)
                            ->required(),

                        Textarea::make('about_text')
                            ->label(__('messages.setting.about_text').':')
                            ->validationAttribute(__('messages.setting.about_text'))
                            ->placeholder(__('messages.setting.about_text'))
                            ->rows(3)
                            ->inlineLabel(true)
                            ->required(),
                    ])
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
