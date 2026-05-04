<?php

namespace App\Filament\Resources\PostsResource\Pages;

use App\Filament\Resources\PostsResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListPosts extends ListRecords
{
    protected static string $resource = PostsResource::class;
    
    protected static string $view = 'filament.resources.posts-resource.pages.list-posts';
    
    public $customFilters = [
        'fromDate' => null,
        'toDate' => null,
        'metrics' => [],
        'timePeriod' => 'all',
    ];

    public function mount(): void
    {
        parent::mount();
        
        // Load filters from session
        $savedFilters = session('custom_post_filters', []);
        $this->customFilters = array_merge($this->customFilters, $savedFilters);
    }
    
    public function updatedCustomFilters(): void
    {
        // Save filters to session
        session(['custom_post_filters' => $this->customFilters]);
        
        // Refresh the table
        $this->dispatch('$refresh');
    }
    
    public function clearFilters(): void
    {
        $this->customFilters = [
            'fromDate' => null,
            'toDate' => null,
            'metrics' => [],
            'timePeriod' => 'all',
        ];
        
        session()->forget('custom_post_filters');
        $this->dispatch('$refresh');
    }
    
    public function hasActiveFilters(): bool
    {
        return !empty($this->customFilters['fromDate']) || 
               !empty($this->customFilters['toDate']) || 
               !empty($this->customFilters['metrics']) || 
               ($this->customFilters['timePeriod'] !== 'all');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('format')
                ->label(__('messages.post.add_post'))
                ->url(PostsResource::getUrl('format'))
        ];
    }
    
        protected function getTableQuery(): ?\Illuminate\Database\Eloquent\Builder
        {
            $query = parent::getTableQuery();
            
            // Apply custom filters
            if (!empty($this->customFilters['fromDate'])) {
                $query->whereDate('created_at', '>=', $this->customFilters['fromDate']);
            }
            
            if (!empty($this->customFilters['toDate'])) {
                $query->whereDate('created_at', '<=', $this->customFilters['toDate']);
            }
            
            // Apply time period filter (this overrides date filters if set)
            if (!empty($this->customFilters['timePeriod']) && $this->customFilters['timePeriod'] !== 'all') {
                $now = now();
                switch ($this->customFilters['timePeriod']) {
                    case 'today':
                        $query->whereDate('created_at', $now->toDateString());
                        break;
                    case 'last_week':
                        $query->where('created_at', '>=', $now->subWeek());
                        break;
                    case 'last_month':
                        $query->where('created_at', '>=', $now->subMonth());
                        break;
                    case 'last_year':
                        $query->where('created_at', '>=', $now->subYear());
                        break;
                }
            }
            
            // Apply metrics filters (ordering)
            if (!empty($this->customFilters['metrics'])) {
                $orderByClauses = [];
                
                if (in_array('most_read', $this->customFilters['metrics'])) {
                    // Use cached views_count column instead of withCount('analytics')
                    // This is MUCH faster even with millions of analytics records
                    $orderByClauses[] = 'views_count desc';
                }
                if (in_array('most_liked', $this->customFilters['metrics'])) {
                    $query->withCount('likes');
                    $orderByClauses[] = 'likes_count desc';
                }
                if (in_array('most_commented', $this->customFilters['metrics'])) {
                    $query->withCount('comments');
                    $orderByClauses[] = 'comments_count desc';
                }
                
                // Apply ordering if any metrics are selected
                if (!empty($orderByClauses)) {
                    $query->orderByRaw(implode(', ', $orderByClauses));
                }
            }
            
            return $query;
        }

}
