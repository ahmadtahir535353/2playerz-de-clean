<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Enums\Sidebar;
use App\Filament\Clusters\Settings;
use App\Models\Setting;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Pages\SubNavigationPosition;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Auth;

class Cms extends Page
{
    public ?array $data = [];

    // protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?int $navigationSort = Sidebar::CMS->value;

    protected static string $view = 'filament.clusters.settings.pages.cms';

    protected static ?string $cluster = Settings::class;

    public ?array  $record = [];

    public static function getNavigationLabel(): string
    {
        return __('messages.setting.cms');
    }

    public function getTitle(): string
    {
        return __('messages.setting.cms');
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
        //     'terms&conditions' => $this->record['terms&conditions'],
        //     'support' => $this->record['support'],
        //     'privacy' => $this->record['privacy'],
        //     'manual_payment_guide' => $this->record['manual_payment_guide'],
        // ]);

        $this->form->fill($this->record);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('messages.setting.cms'))
                    ->schema([
                        RichEditor::make('terms&conditions')
                            ->label(__('messages.setting.terms-conditions').':')
                            ->validationAttribute(__('messages.setting.terms-conditions'))
                            ->placeholder(__('messages.setting.terms-conditions'))
                            ->columnSpanFull()
                            ->required(),
                        RichEditor::make('support')
                            ->label(__('messages.setting.support').':')
                            ->validationAttribute(__('messages.setting.support'))
                            ->placeholder(__('messages.setting.support'))
                            ->columnSpanFull()
                            ->required(),
                        RichEditor::make('privacy')
                            ->label(__('messages.setting.privacy').':')
                            ->validationAttribute(__('messages.setting.privacy'))
                            ->placeholder(__('messages.setting.privacy'))
                            ->columnSpanFull()
                            ->required(),
                        RichEditor::make('manual_payment_guide')
                            ->label(__('messages.setting.manual_payment_guide').':')
                            ->validationAttribute(__('messages.setting.manual_payment_guide'))
                            ->placeholder(__('messages.setting.manual_payment_guide'))
                            ->columnSpanFull(),
                    ]),
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
