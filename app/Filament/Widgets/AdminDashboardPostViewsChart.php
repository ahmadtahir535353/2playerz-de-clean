<?php

namespace App\Filament\Widgets;

use App\Models\DailyStat;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AdminDashboardPostViewsChart extends ChartWidget
{
    // protected static ?string $heading = 'post views';

    protected int | string | array $columnSpan = 'full';

    public ?string $filter = 'thismonth';

    protected function getData(): array
    {
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
            $start_date = Carbon::now()->subYears()->startOfYear();
            $end_date = Carbon::now()->subYears()->endOfYear();
        }

        $startDate = isset($start_date) ? Carbon::parse($start_date) : Carbon::now()->subMonth();
        $endDate = isset($end_date) ? Carbon::parse($end_date) : Carbon::now();
        $result = ['data' => [], 'labels' => []];

        $period = CarbonPeriod::create($startDate, $endDate);
        $rowsByDate = DailyStat::whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get()
            ->keyBy(fn ($r) => $r->date->format('Y-m-d'));

        foreach ($period as $date) {
            $key = $date->format('Y-m-d');
            $result['data'][] = $rowsByDate->get($key)?->post_views ?? 0;
            $result['labels'][] = $date->format('d-m-Y');
        }

        return [
            'datasets' => [
                [
                    'label' => __('messages.details.views'),
                    'data' => $result['data'],
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
        return __('messages.dashboard_show.post_views');
    }

}
