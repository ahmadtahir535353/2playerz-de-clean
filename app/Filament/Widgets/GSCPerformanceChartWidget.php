<?php

namespace App\Filament\Widgets;

use App\Models\GoogleSearchConsoleData;
use App\Models\GoogleSearchConsoleToken;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Widgets\ChartWidget;

class GSCPerformanceChartWidget extends ChartWidget
{
    protected int | string | array $columnSpan = 'full';

    public ?string $filter = 'thismonth';

    protected function getData(): array
    {
        $token = GoogleSearchConsoleToken::where('is_active', true)->first();
        
        if (!$token) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $activeFilter = $this->filter;
        $start_date = null;
        $end_date = null;

        if ($activeFilter == 'thisweek') {
            $start_date = Carbon::now()->startOfWeek();
            $end_date = Carbon::now()->endOfWeek();
        } elseif ($activeFilter == 'lastweek') {
            $start_date = Carbon::now()->subWeek()->startOfWeek();
            $end_date = Carbon::now()->subWeek()->endOfWeek();
        } elseif ($activeFilter == 'thismonth') {
            $start_date = Carbon::now()->startOfMonth();
            $end_date = Carbon::now()->endOfMonth();
        } elseif ($activeFilter == 'lastmonth') {
            $start_date = Carbon::now()->subMonth()->startOfMonth();
            $end_date = Carbon::now()->subMonth()->endOfMonth();
        } elseif ($activeFilter == 'thisyear') {
            $start_date = Carbon::now()->startOfYear();
            $end_date = Carbon::now()->endOfYear();
        } elseif ($activeFilter == 'lastyear') {
            $start_date = Carbon::now()->subYear()->startOfYear();
            $end_date = Carbon::now()->subYear()->endOfYear();
        }

        $startDate = isset($start_date) ? Carbon::parse($start_date) : Carbon::now()->subMonth();
        $endDate = isset($end_date) ? Carbon::parse($end_date) : Carbon::now();
        
        $result = [
            'clicks' => [],
            'impressions' => [],
            'labels' => [],
        ];
        
        $period = CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $date) {
            $dayData = GoogleSearchConsoleData::where('token_id', $token->id)
                ->where('data_type', 'overall')
                ->whereDate('date', $date)
                ->selectRaw('SUM(clicks) as clicks, SUM(impressions) as impressions')
                ->first();

            $result['clicks'][] = $dayData->clicks ?? 0;
            $result['impressions'][] = $dayData->impressions ?? 0;
            $result['labels'][] = $date->format('d-m-Y');
        }

        return [
            'datasets' => [
                [
                    'label' => __('messages.gsc.clicks'),
                    'data' => $result['clicks'],
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'lineTension' => 0.4,
                ],
                [
                    'label' => __('messages.gsc.impressions'),
                    'data' => $result['impressions'],
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'lineTension' => 0.4,
                ],
            ],
            'labels' => $result['labels'],
        ];
    }

    protected function getFilters(): ?array
    {
        return [
            'thisweek' => __('messages.days.this_week'),
            'lastweek' => __('messages.days.last_week'),
            'thismonth' => __('messages.days.this_month'),
            'lastmonth' => __('messages.days.last_month'),
            'thisyear' => __('messages.days.this_year'),
            'lastyear' => __('messages.days.last_year'),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin|staff');
    }

    public function getHeading(): string
    {
        return __('messages.gsc.performance');
    }
}

