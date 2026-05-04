<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AdminDashboardCardOverview;
use App\Filament\Widgets\AdminDashboardPostViewsChart;
use App\Filament\Widgets\AdminDashboardRecentUserTable;
use App\Filament\Widgets\AdminDashboardUniqueVisitorsChart;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            AdminDashboardCardOverview::class,
            AdminDashboardUniqueVisitorsChart::class,
            AdminDashboardPostViewsChart::class,
            AdminDashboardRecentUserTable::class,
        ];
    }
}
