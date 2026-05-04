<?php

namespace App\Filament\Exports;

use App\Models\NewsLetter;
use App\Models\Subscriber;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class NewsLetterExporter extends Exporter
{
    protected static ?string $model = Subscriber::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id'),
            ExportColumn::make('email')
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your news letter export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
