<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use App\Models\RssFeed;
use App\Models\User;
use App\Scopes\LanguageScope;
use App\Scopes\PostDraftScope;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminDashboardCardOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $posts = Post::withoutGlobalScope(LanguageScope::class)->withoutGlobalScope(PostDraftScope::class)->count();
        $postsDraft = Post::withoutGlobalScope(LanguageScope::class)->withoutGlobalScope(PostDraftScope::class)->where('status', Post::STATUS_DRAFT)->count();
        $rss = RssFeed::count();
        $rssPost = Post::withoutGlobalScope(LanguageScope::class)->withoutGlobalScope(PostDraftScope::class)->whereIsRss(true)->count();
        return [
            Stat::make(__('messages.post.posts'), $posts)
                ->icon('heroicon-o-document')
                ->chartColor('success')
                ->descriptionIcon('heroicon-o-chevron-up', 'before')
                ->descriptionColor('success')
                ->url(route('filament.admin.resources.posts.index')),
            Stat::make(__('messages.dashboard_show.drafts'), $postsDraft)
                ->icon('heroicon-o-newspaper')
                ->descriptionIcon('heroicon-o-chevron-up', 'before')
                ->descriptionColor('primary'),
            Stat::make(__('messages.rss-feed'), $rss)
                ->icon('heroicon-o-rss')
                ->descriptionIcon('heroicon-o-chevron-down', 'before')
                ->descriptionColor('danger'),
            Stat::make(__('messages.on_rss_feed'), $rssPost)
                ->icon('heroicon-o-rss')
                ->descriptionIcon('heroicon-o-chevron-down', 'before')
                ->descriptionColor('danger')
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin|staff');
    }
}
