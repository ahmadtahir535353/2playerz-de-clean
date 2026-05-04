<?php

namespace App\Observers;

use App\Models\Page;
use App\Services\IndexNowService;

class PageObserver
{
    protected $indexNowService;

    public function __construct(IndexNowService $indexNowService)
    {
        $this->indexNowService = $indexNowService;
    }

    public function created(Page $page)
    {
        if ($page->visibility == Page::VISIBILITY_ACTIVE) {
            $this->submitToIndexNow($page);
        }
    }

    public function updated(Page $page)
    {
        // Submit to IndexNow when page becomes visible
        if ($page->isDirty('visibility') && $page->visibility == Page::VISIBILITY_ACTIVE) {
            $this->submitToIndexNow($page);
        }
    }

    protected function submitToIndexNow(Page $page)
    {
        if (empty($page->slug)) {
            return;
        }

        try {
            $url = $this->indexNowService->getPageUrl($page->slug);
            $this->indexNowService->submitUrl($url);
        } catch (\Exception $e) {
            \Log::error('IndexNow: Failed to submit page URL', [
                'page_id' => $page->id,
                'slug' => $page->slug,
                'error' => $e->getMessage()
            ]);
        }
    }
}

