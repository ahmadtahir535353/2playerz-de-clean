<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\GSCStatsOverviewWidget;
use App\Filament\Widgets\GSCPerformanceChartWidget;
use App\Filament\Widgets\GSCTopQueriesWidget;
use App\Filament\Widgets\GSCTopPagesWidget;
use App\Filament\Widgets\BingStatsOverviewWidget;
use App\Filament\Widgets\BingPerformanceChartWidget;
use App\Filament\Widgets\BingTopQueriesWidget;
use App\Filament\Widgets\BingTopPagesWidget;
use Filament\Pages\Page;
use Filament\Pages\Concerns\InteractsWithHeaderActions;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;

class SearchAnalytics extends Page
{
    use InteractsWithHeaderActions;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationLabel = null;

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.search-analytics';

    public $activeTab = 'gsc';

    public static function getNavigationLabel(): string
    {
        return __('messages.search_analytics.title');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('fetchGSCData')
                ->label(__('messages.gsc.fetch_data_now'))
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->visible(fn () => $this->activeTab === 'gsc')
                ->requiresConfirmation()
                ->modalHeading(__('messages.gsc.fetch_data_heading'))
                ->modalDescription(__('messages.gsc.fetch_data_description'))
                ->modalSubmitActionLabel(__('messages.gsc.fetch_data'))
                ->action(function () {
                    try {
                        $this->fetchGSCData();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->danger()
                            ->title(__('messages.error'))
                            ->body(__('messages.gsc.fetch_data_failed') . ': ' . $e->getMessage())
                            ->send();
                    }
                }),
            Action::make('fetchBingData')
                ->label(__('messages.bing.fetch_data_now'))
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->visible(fn () => $this->activeTab === 'bing')
                ->requiresConfirmation()
                ->modalHeading(__('messages.bing.fetch_data_heading'))
                ->modalDescription(__('messages.bing.fetch_data_description'))
                ->modalSubmitActionLabel(__('messages.bing.fetch_data'))
                ->action(function () {
                    try {
                        $this->fetchBingData();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->danger()
                            ->title(__('messages.error'))
                            ->body(__('messages.bing.fetch_data_failed') . ': ' . $e->getMessage())
                            ->send();
                    }
                }),
        ];
    }

    public function getGSCWidgets(): array
    {
        return [
            GSCStatsOverviewWidget::class,
            GSCPerformanceChartWidget::class,
            GSCTopQueriesWidget::class,
            GSCTopPagesWidget::class,
        ];
    }

    public function getBingWidgets(): array
    {
        return [
            BingStatsOverviewWidget::class,
            BingPerformanceChartWidget::class,
            BingTopQueriesWidget::class,
            BingTopPagesWidget::class,
        ];
    }

    protected function getColumns(): int | string | array
    {
        return 12;
    }

    public static function canView(): bool
    {
        return Auth::user()->hasRole('admin|staff');
    }

    public function getTitle(): string
    {
        return __('messages.search_analytics.title');
    }

    public function fetchGSCData(): void
    {
        try {
            Log::info('GSC Manual Data Fetch started by user: ' . Auth::id());

            // Run the artisan command
            Artisan::call('gsc:fetch-data', [
                '--days' => 90, // Fetch last 90 days of data
            ]);

            $output = Artisan::output();

            Log::info('GSC Manual Data Fetch completed', [
                'user_id' => Auth::id(),
                'output' => $output,
            ]);

            Notification::make()
                ->success()
                ->title(__('messages.gsc.fetch_data_success'))
                ->body(__('messages.gsc.fetch_data_success_message'))
                ->send();

            // Refresh widgets to show new data
            $this->dispatch('$refresh');
        } catch (\Exception $e) {
            Log::error('GSC Manual Data Fetch Error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            Notification::make()
                ->danger()
                ->title(__('messages.error'))
                ->body(__('messages.gsc.fetch_data_failed') . ': ' . $e->getMessage())
                ->send();
        }
    }

    public function fetchBingData(): void
    {
        try {
            Log::info('Bing Manual Data Fetch started by user: ' . Auth::id());

            // Run the artisan command
            Artisan::call('bing:fetch-data', [
                '--days' => 90, // Fetch last 90 days of data
            ]);

            $output = Artisan::output();

            Log::info('Bing Manual Data Fetch completed', [
                'user_id' => Auth::id(),
                'output' => $output,
            ]);

            Notification::make()
                ->success()
                ->title(__('messages.bing.fetch_data_success'))
                ->body(__('messages.bing.fetch_data_success_message'))
                ->send();

            // Refresh widgets to show new data
            $this->dispatch('$refresh');
        } catch (\Exception $e) {
            Log::error('Bing Manual Data Fetch Error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            Notification::make()
                ->danger()
                ->title(__('messages.error'))
                ->body(__('messages.bing.fetch_data_failed') . ': ' . $e->getMessage())
                ->send();
        }
    }
}
