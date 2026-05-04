<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Enums\Sidebar;
use App\Filament\Clusters\Settings;
use App\Models\Setting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Pages\SubNavigationPosition;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Auth;

class AdManagement extends Page
{
    public ?array $data = [];


    // protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.clusters.settings.pages.ad-management';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?int $navigationSort = Sidebar::AD_MANAGEMENT->value;

    protected static ?string $cluster = Settings::class;

    public ?array  $record = [];

    public static function getNavigationLabel(): string
    {
        return __('messages.ad_space.ad_management');
    }

    public function getTitle(): string
    {
        return __('messages.ad_space.ad_management');
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
        //     'header' => $this->record['header'],
        //     'index_top' => $this->record['index_top'],
        //     'index_bottom' => $this->record['index_bottom'],
        //     'post_details' => $this->record['post_details'],
        //     'details_side' => $this->record['details_side'],
        //     'categories' => $this->record['categories'],
        //     'trending_post' => $this->record['trending_post'],
        //     'popular_news' => $this->record['popular_news'],
        //     'trending_post_index_page' => $this->record['trending_post_index_page'],
        //     'popular_news_index_page' => $this->record['popular_news_index_page'],
        //     'recommended_post_index_page' => $this->record['recommended_post_index_page'],
        // ]);
        $this->form->fill($this->record);

    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('messages.ad_space.ad_management'))
                    ->schema([
                        Toggle::make('header')
                            ->label(__('messages.ad_space.header').':')
                            ->validationAttribute(__('messages.ad_space.header'))
                            ->inlineLabel(true),
                        Toggle::make('index_top')
                            ->label(__('messages.ad_space.index_top').':')
                            ->validationAttribute(__('messages.ad_space.index_top'))
                            ->inlineLabel(true),
                        Toggle::make('index_bottom')
                            ->label(__('messages.ad_space.index_bottom').':')
                            ->validationAttribute(__('messages.ad_space.index_bottom'))
                            ->inlineLabel(true),
                        Toggle::make('post_details')
                            ->label(__('messages.ad_space.post_details').':')
                            ->validationAttribute(__('messages.ad_space.post_details'))
                            ->inlineLabel(true),
                        Toggle::make('details_side')
                            ->label(__('messages.ad_space.details_side').':')
                            ->validationAttribute(__('messages.ad_space.details_side'))
                            ->inlineLabel(true),
                        Toggle::make('categories')
                            ->label(__('messages.ad_space.categories').':')
                            ->validationAttribute(__('messages.ad_space.categories'))
                            ->inlineLabel(true),
                        Toggle::make('trending_post')
                            ->label(__('messages.ad_space.trending_post').':')
                            ->validationAttribute(__('messages.ad_space.trending_post'))
                            ->inlineLabel(true),
                        Toggle::make('popular_news')
                            ->label(__('messages.ad_space.popular_news').':')
                            ->validationAttribute(__('messages.ad_space.popular_news'))
                            ->inlineLabel(true),
                        Toggle::make('trending_post_index_page')
                            ->label(__('messages.ad_space.trending_post_index_page').':')
                            ->validationAttribute(__('messages.ad_space.trending_post_index_page'))
                            ->inlineLabel(true),
                        Toggle::make('popular_news_index_page')
                            ->label(__('messages.ad_space.popular_news_index_page').':')
                            ->validationAttribute(__('messages.ad_space.popular_news_index_page'))
                            ->inlineLabel(true),
                        Toggle::make('recommended_post_index_page')
                            ->label(__('messages.ad_space.recommended_post_index_page').':')
                            ->validationAttribute(__('messages.ad_space.recommended_post_index_page'))
                            ->inlineLabel(true),
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
