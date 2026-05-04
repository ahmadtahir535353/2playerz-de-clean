<?php

namespace App\Filament\Resources\GameReleaseResource\Pages;

use App\Filament\Resources\GameReleaseResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class EditGameRelease extends EditRecord
{
    protected static string $resource = GameReleaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['name'] = trim((string) ($data['name'] ?? ''));
        $data['slug'] = trim((string) ($data['slug'] ?? '')) ?: Str::slug($data['name']);
        $data['link'] = blank($data['link'] ?? null) ? '' : trim((string) $data['link']);

        if (!blank($data['release_date'] ?? null)) {
            try {
                $data['release_date'] = Carbon::parse($data['release_date'])->toDateString();
            } catch (Throwable $e) {
                $data['release_date'] = null;
            }
        } else {
            $data['release_date'] = null;
        }

        // Ensure release_year and release_month are always saved (normalize and cast)
        $data['release_year'] = isset($data['release_year']) && $data['release_year'] !== '' && $data['release_year'] !== null
            ? (int) $data['release_year']
            : null;
        $data['release_month'] = isset($data['release_month']) && $data['release_month'] !== '' && $data['release_month'] !== null
            ? (int) $data['release_month']
            : null;

        if ($data['release_date']) {
            try {
                $date = Carbon::parse($data['release_date']);
                $data['release_year'] = $date->year;
                $data['release_month'] = $date->month;
            } catch (Throwable $e) {
                // keep manual year/month if date parsing unexpectedly fails
            }
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        try {
            $record->update($data);
            return $record;
        } catch (Throwable $e) {
            Log::error('Failed to update game release', [
                'record_id' => $record->id,
                'data' => $data,
                'error' => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            Notification::make()
                ->title('Game release could not be updated')
                ->body('Please check slug uniqueness, URL format, and required fields. Technical error: ' . $e->getMessage())
                ->danger()
                ->persistent()
                ->send();

            throw $e;
        }
    }
}
