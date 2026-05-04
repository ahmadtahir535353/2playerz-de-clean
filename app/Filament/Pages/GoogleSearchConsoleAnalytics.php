<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\GSCStatsOverviewWidget;
use App\Filament\Widgets\GSCPerformanceChartWidget;
use App\Filament\Widgets\GSCTopQueriesWidget;
use App\Filament\Widgets\GSCTopPagesWidget;
use Filament\Pages\Page;
use Filament\Pages\Concerns\InteractsWithHeaderActions;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;

class GoogleSearchConsoleAnalytics extends Page
{
    use InteractsWithHeaderActions;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationLabel = null;

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.google-search-console-analytics';

    public static function getNavigationLabel(): string
    {
        return __('messages.gsc.analytics');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('fetchData')
                ->label(__('messages.gsc.fetch_data_now'))
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
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
        ];
    }

    protected function getWidgets(): array
    {
        return [
            GSCStatsOverviewWidget::class,
            GSCPerformanceChartWidget::class,
            GSCTopQueriesWidget::class,
            GSCTopPagesWidget::class,
        ];
    }

    protected function getColumns(): int | string | array
    {
        return 12;
    }

    public static function canView(): bool
    {
        // Hide this page - replaced by SearchAnalytics page
        return false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        // Don't show in navigation - replaced by SearchAnalytics page
        return false;
    }

    public function getTitle(): string
    {
        return __('messages.gsc.google_search_console_analytics');
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
}
