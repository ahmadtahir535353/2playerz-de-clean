<?php

namespace App\Filament\Widgets;

use App\Models\GoogleSearchConsoleData;
use App\Models\GoogleSearchConsoleToken;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GSCStatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Get active token
        $token = GoogleSearchConsoleToken::where('is_active', true)->first();
        
        if (!$token) {
            return [
                Stat::make(__('messages.gsc.google_search_console'), __('messages.gsc.not_connected'))
                    ->description(__('messages.gsc.connect_in_settings'))
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('warning'),
            ];
        }

        // Get last 30 days data
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        $data = GoogleSearchConsoleData::where('token_id', $token->id)
            ->where('data_type', 'overall')
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('
                SUM(clicks) as total_clicks,
                SUM(impressions) as total_impressions,
                AVG(ctr) as avg_ctr,
                AVG(position) as avg_position
            ')
            ->first();

        $totalClicks = $data->total_clicks ?? 0;
        $totalImpressions = $data->total_impressions ?? 0;
        $avgCtr = ($data->avg_ctr ?? 0) * 100; // Convert to percentage
        $avgPosition = $data->avg_position ?? 0;

        // Get previous period for comparison
        $prevStartDate = Carbon::now()->subDays(60);
        $prevData = GoogleSearchConsoleData::where('token_id', $token->id)
            ->where('data_type', 'overall')
            ->whereBetween('date', [$prevStartDate, $startDate])
            ->selectRaw('
                SUM(clicks) as total_clicks,
                SUM(impressions) as total_impressions,
                AVG(ctr) as avg_ctr,
                AVG(position) as avg_position
            ')
            ->first();

        $prevClicks = $prevData->total_clicks ?? 0;
        $prevImpressions = $prevData->total_impressions ?? 0;
        $prevCtr = ($prevData->avg_ctr ?? 0) * 100;
        $prevPosition = $prevData->avg_position ?? 0;

        // Calculate change percentage
        $clicksChange = $prevClicks > 0 ? (($totalClicks - $prevClicks) / $prevClicks) * 100 : 0;
        $impressionsChange = $prevImpressions > 0 ? (($totalImpressions - $prevImpressions) / $prevImpressions) * 100 : 0;
        $ctrChange = $prevCtr > 0 ? (($avgCtr - $prevCtr) / $prevCtr) * 100 : 0;
        $positionChange = $prevPosition > 0 ? (($avgPosition - $prevPosition) / $prevPosition) * 100 : 0;

        return [
            Stat::make(__('messages.gsc.total_clicks'), number_format($totalClicks))
                ->description($clicksChange >= 0 ? '+' . number_format($clicksChange, 1) . '%' : number_format($clicksChange, 1) . '%')
                ->descriptionIcon($clicksChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->descriptionColor($clicksChange >= 0 ? 'success' : 'danger')
                ->icon('heroicon-o-cursor-arrow-rays')
                ->color('primary'),
            
            Stat::make(__('messages.gsc.total_impressions'), number_format($totalImpressions))
                ->description($impressionsChange >= 0 ? '+' . number_format($impressionsChange, 1) . '%' : number_format($impressionsChange, 1) . '%')
                ->descriptionIcon($impressionsChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->descriptionColor($impressionsChange >= 0 ? 'success' : 'danger')
                ->icon('heroicon-o-eye')
                ->color('info'),
            
            Stat::make(__('messages.gsc.average_ctr'), number_format($avgCtr, 2) . '%')
                ->description($ctrChange >= 0 ? '+' . number_format($ctrChange, 1) . '%' : number_format($ctrChange, 1) . '%')
                ->descriptionIcon($ctrChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->descriptionColor($ctrChange >= 0 ? 'success' : 'danger')
                ->icon('heroicon-o-chart-bar')
                ->color('success'),
            
            Stat::make(__('messages.gsc.average_position'), number_format($avgPosition, 1))
                ->description($positionChange <= 0 
                    ? number_format(abs($positionChange), 1) . '% ' . __('messages.gsc.better')
                    : number_format($positionChange, 1) . '% ' . __('messages.gsc.worse'))
                ->descriptionIcon($positionChange <= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->descriptionColor($positionChange <= 0 ? 'success' : 'danger')
                ->icon('heroicon-o-signal')
                ->color('warning'),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin|staff');
    }
}

