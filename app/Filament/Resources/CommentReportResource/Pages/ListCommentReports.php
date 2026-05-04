<?php

namespace App\Filament\Resources\CommentReportResource\Pages;

use App\Filament\Resources\CommentReportResource;
use App\Models\CommentReport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCommentReports extends ListRecords
{
    protected static string $resource = CommentReportResource::class;

    public function mount(): void
    {
        parent::mount();
        
        // Mark all unread reports as viewed when admin opens the page
        CommentReport::whereNull('viewed_at')
            ->update(['viewed_at' => now()]);
    }

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
