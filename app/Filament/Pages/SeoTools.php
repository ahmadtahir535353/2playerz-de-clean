<?php

namespace App\Filament\Pages;

use App\Enums\Sidebar;
use App\Http\Middleware\CheckPaddingSubscription;
use App\Models\Language;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\SeoTool;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Auth;

class SeoTools extends Page
{
    public ?array $data = [];

    protected static ?string $navigationIcon = 'heroicon-o-wrench';

    protected static string $view = 'filament.pages.seo-tools';

    protected static ?int $navigationSort = Sidebar::SEO_TOOLS->value;

    protected ?SeoTool $record = null;

    public static function getNavigationLabel(): string
    {
        return __('messages.seo-tools');
    }

    public function getTitle(): string
    {
        return __('messages.seo-tools');
    }

    public static function canAccess(): bool
    {
        return Auth::user()->hasPermissionTo('manage_seo_tools');
    }

    protected static string|array $routeMiddleware = [
        CheckPaddingSubscription::class,
    ];

    public function mount(): void
    {
        $this->record = SeoTool::first();


        // $this->form->fill([
        //     'lang_id' => $this->record->lang_id,
        //     'site_title' => $this->record->site_title,
        //     'home_title' => $this->record->home_title,
        //     'site_description' => $this->record->site_description,
        //     'keyword' => $this->record->keyword,
        //     'google_analytics' => $this->record->google_analytics,
        // ]);

        $this->form->fill($this->record->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('lang_id')
                            ->label(__('messages.seo-tool.language') . ':')
                            ->validationAttribute(__('messages.seo-tool.language'))
                            ->placeholder(__('messages.seo-tool.language'))
                            ->required()
                            ->searchable()
                            ->native(false)
                            ->inlineLabel(true)
                            ->options(Language::pluck('name', 'id')),
                        TextInput::make('site_title')
                            ->label(__('messages.seo-tool.site_title') . ':')
                            ->validationAttribute(__('messages.seo-tool.site_title'))
                            ->placeholder(__('messages.seo-tool.site_title'))
                            ->required()
                            ->inlineLabel(true)
                            ->maxLength(255),
                        TextInput::make('home_title')
                            ->label(__('messages.seo-tool.home_title') . ':')
                            ->validationAttribute(__('messages.seo-tool.home_title'))
                            ->placeholder(__('messages.seo-tool.home_title'))
                            ->required()
                            ->inlineLabel(true)
                            ->maxLength(255),
                        Textarea::make('site_description')
                            ->label(__('messages.seo-tool.site_description') . ':')
                            ->validationAttribute(__('messages.seo-tool.site_description'))
                            ->placeholder(__('messages.seo-tool.site_description'))
                            ->required()
                            ->inlineLabel(true)
                            ->maxLength(255),
                        Textarea::make('keyword')
                            ->label(__('messages.seo-tool.keyword') . ':')
                            ->validationAttribute(__('messages.seo-tool.keyword'))
                            ->placeholder(__('messages.seo-tool.keyword'))
                            ->required()
                            ->inlineLabel(true)
                            ->maxLength(255),
                        Textarea::make('google_analytics')
                            ->label(__('messages.seo-tool.google_analytics') . ':')
                            ->validationAttribute(__('messages.seo-tool.google_analytics'))
                            ->placeholder(__('messages.seo-tool.google_analytics'))
                            ->inlineLabel(true)
                            ->maxLength(255),
                    ])->columns(1)
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        // SeoTool::first()->update($this->form->getState());

        try {
            SeoTool::first()->update($this->form->getState());
            Notification::make()
                ->success()
                ->title(__('messages.placeholder.seo_tools_updated_successfully'))
                ->send();
        } catch (Halt $exception) {
            $this->notify('error', $exception->getMessage());
        }
    }
}
