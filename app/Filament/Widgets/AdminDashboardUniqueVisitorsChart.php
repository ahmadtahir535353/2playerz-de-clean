<?php

namespace App\Filament\Widgets;

use App\Models\DailyStat;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Widgets\ChartWidget;

class AdminDashboardUniqueVisitorsChart extends ChartWidget
{
    protected int | string | array $columnSpan = 'full';

    public ?string $filter = 'thismonth';

    protected function getData(): array
    {
        $start_date = match ($this->filter) {
            'thisweek' => Carbon::now()->startOfWeek(),
            'lastweek' => Carbon::now()->subWeek()->startOfWeek(),
            'thismonth' => Carbon::now()->startOfMonth(),
            'lastmonth' => Carbon::now()->subMonth()->startOfMonth(),
            'thisyear' => Carbon::now()->startOfYear(),
            'lastyear' => Carbon::now()->subYear()->startOfYear(),
            default => Carbon::now()->startOfMonth(),
        };

        $end_date = match ($this->filter) {
            'thisweek' => Carbon::now()->endOfWeek(),
            'lastweek' => Carbon::now()->subWeek()->endOfWeek(),
            'thismonth' => Carbon::now()->endOfMonth(),
            'lastmonth' => Carbon::now()->subMonth()->endOfMonth(),
            'thisyear' => Carbon::now()->endOfYear(),
            'lastyear' => Carbon::now()->subYear()->endOfYear(),
            default => Carbon::now(),
        };

        $result = ['data' => [], 'labels' => []];
        $period = CarbonPeriod::create($start_date, $end_date);

        $rowsByDate = DailyStat::whereBetween('date', [$start_date->toDateString(), $end_date->toDateString()])
            ->get()
            ->keyBy(fn ($r) => $r->date->format('Y-m-d'));

        foreach ($period as $date) {
            $key = $date->format('Y-m-d');
            $result['data'][] = $rowsByDate->get($key)?->unique_visitors ?? 0;
            $result['labels'][] = $date->format('d-m-Y');
        }

        return [
            'datasets' => [
                [
                    'label' => __('messages.other_lang.unique_visitors'),
                    'data' => $result['data'],
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

    public function getHeading(): string
    {
        return __('messages.other_lang.unique_website_visitors');
    }

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

}
