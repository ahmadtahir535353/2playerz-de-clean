<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use App\Scopes\LanguageScope;
use App\Scopes\PostDraftScope;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CustomerDashboardCardOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $posts = Post::withoutGlobalScope(LanguageScope::class)->withoutGlobalScope(PostDraftScope::class)->count();
        $postsDraft = Post::withoutGlobalScope(LanguageScope::class)->withoutGlobalScope(PostDraftScope::class)->where('status', Post::STATUS_DRAFT)->count();
        return [
            Stat::make(__('messages.post.posts'), $posts)
                ->icon('heroicon-o-document')
                // ->iconBackgroundColor('success')
                ->chartColor('success')
                ->descriptionIcon('heroicon-o-chevron-up', 'before')
                ->descriptionColor('success'),
            Stat::make(__('messages.dashboard_show.drafts'), $postsDraft)
                ->icon('heroicon-o-newspaper')
                ->descriptionIcon('heroicon-o-chevron-up', 'before')
                ->descriptionColor('primary'),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole('customer');
    }
}
