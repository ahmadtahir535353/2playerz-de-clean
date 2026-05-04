<?php

namespace App\Filament\Resources\LanguagesResource\Pages;

use App\Filament\Resources\LanguagesResource;
use App\Models\Language;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;
use File;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class ManageLanguages extends ManageRecords
{
    protected static string $resource = LanguagesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label(__('messages.common.add').' '.__('messages.common.language'))
            ->modalWidth('lg')
            ->createAnother(false)
            ->modalHeading(__('messages.common.add').' '.__('messages.common.language'))
            ->successNotificationTitle(__('messages.placeholder.language_saved_successfully'))
            ->action(function ($data) {
                // Create the language in the database
                $language = Language::create([
                    'name' => $data['name'],
                    'iso_code' => $data['iso_code'],
                ]);

                $allLanguagesArr = [];
                $languages = File::directories(base_path('lang'));

                foreach ($languages as $dir) {
                    $allLanguagesArr[] = basename($dir); // Extract directory name, which is the ISO code
                }

                if (in_array($language->iso_code, $allLanguagesArr)) {
                    throw new UnprocessableEntityHttpException($language->iso_code . ' language already exists.');
                }

                try {
                    if (!empty($language->iso_code)) {
                        // Make directory in lang folder
                        File::makeDirectory(lang_path() . '/' . $language->iso_code);

                        // Copy all 'en' folder files to the new folder
                        $filesInFolder = File::files(App::langPath() . '/en');

                        foreach ($filesInFolder as $path) {
                            $file = basename($path);
                            File::copy(App::langPath() . '/en/' . $file, App::langPath() . '/' . $language->iso_code . '/' . $file);
                        }
                    }

                    return true;
                } catch (\Exception $e) {
                    throw new UnprocessableEntityHttpException($e->getMessage());
                }
            }),
        ];
    }
}
