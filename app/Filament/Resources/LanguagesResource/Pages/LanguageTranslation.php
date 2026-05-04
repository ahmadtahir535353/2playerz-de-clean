<?php

namespace App\Filament\Resources\LanguagesResource\Pages;

use App\Filament\Resources\LanguagesResource;
use App\Models\Language;
use Filament\Actions\Action;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use File;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\App;

class LanguageTranslation extends Page
{
    public ?array $data = [];

    public ?string $selectedFile = 'messages.php';

    public ?string $selectedLang = 'en';

    protected static string $resource = LanguagesResource::class;

    protected static string $view = 'filament.resources.languages-resource.pages.language-translation';


    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label(__('messages.common.save'))
                ->action('save'),
            Action::make('back')
                ->label(__('messages.common.back'))
                ->url($this->getResource()::getUrl('index')),
        ];
    }

    public function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('save'),
        ];
    }

    public function mount(?string $selectedFile = 'messages.php'): void
    {
        $id = request()->route('record');
        $langCode = Language::where('id', $id)->first();
        $this->selectedLang = $langCode->iso_code;

        $this->selectedFile = $selectedFile;
        $this->updateDataFromFile($this->selectedFile);
    }

//    protected function updateDataFromFile(string $fileName): void
//    {
//
//        $langPath = base_path('lang/' . $this->selectedLang . '/' . $fileName);
//
//        if (File::exists($langPath)) {
//            $translations = include($langPath);
//            $flattenedTranslations = $this->flattenArray($translations);
////            dd($flattenedTranslations);
//
//            $this->data = [
//                'language_id' => $fileName,
//                ...$flattenedTranslations
//            ];
//        } else {
//            $this->data = [
//                'language_id' => $fileName, // Ensure 'language_id' is still present even if file does not exist
//            ];
//        }
//    }

    protected function updateDataFromFile(string $fileName): void
    {
        $langPath = base_path('lang/' . $this->selectedLang . '/' . $fileName);

        if (File::exists($langPath)) {
            $translations = include($langPath);

            $this->data = [
                'language_id' => $fileName,
                ...$translations, // keep as nested array, no flattening!
            ];
        } else {
            $this->data = [
                'language_id' => $fileName,
            ];
        }
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('language_id')
                            ->label('Language')
                            ->preload()
                            ->native(false)
                            ->live()
                            ->options(function () {
                                $id = request()->route('record');

                                $langPath = App::langPath() . '/' . $this->selectedLang . '/';

                                $data['allFiles'] = [];
                                try {
                                    if (!File::exists($langPath)) {
                                        throw new \Exception("The directory '$langPath' does not exist.");
                                    }

                                    $files = File::allFiles($langPath);
                                    foreach ($files as $file) {
                                        $data['allFiles'][basename($file)] = ucfirst(basename($file));
                                    }
                                    return $data['allFiles'];
                                } catch (\Exception $e) {
                                    throw new \Exception($e->getMessage());
                                }
                            })
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                try {
                                    $this->selectedFile = $state;
                                    $this->refreshFormFields($state);
                                } catch (\Exception $e) {
                                    throw new \Exception($e->getMessage());
                                }
                            }),
                        Group::make()
                            ->columns(3)
                            ->schema(fn() => $this->selectedFile ? $this->getTextInputFields($this->selectedFile) : []),
                    ])

            ])->statePath('data');
    }

    protected function refreshFormFields(string $selectedFile): void
    {
        // Update the selected file and data
        $this->selectedFile = $selectedFile;
        $this->updateDataFromFile($selectedFile);

        // Rebuild the form schema
        $this->form->schema([
            Section::make()
                ->schema([
                    Select::make('language_id')
                        ->label('Language')
                        ->preload()
                        ->native(false)
                        ->live()
                        ->options(function () {
                            $langPath = App::langPath() . '/' . $this->selectedLang . '/';
                            $data['allFiles'] = [];

                            try {
                                if (!File::exists($langPath)) {
                                    throw new \Exception("The directory '$langPath' does not exist.");
                                }

                                $files = File::allFiles($langPath);
                                foreach ($files as $file) {
                                    $data['allFiles'][basename($file)] = ucfirst(basename($file));
                                }
                                return $data['allFiles'];
                            } catch (\Exception $e) {
                                throw new \Exception($e->getMessage());
                            }
                        })
                        ->afterStateUpdated(function ($state) {
                            $this->refreshFormFields($state);
                        }),
                    Group::make()
                        ->columns(3)
                        ->schema(fn() => $this->getTextInputFields($this->selectedFile)),
                ])
        ]);
    }


//    protected function getTextInputFields($selectedFile): array
//    {
//        if ($selectedFile) {
//
//
//            $langPath = base_path('lang/' . $this->selectedLang . '/' . $selectedFile);
//
//            if (!File::exists($langPath)) {
//                throw new \Exception("The file '$langPath' does not exist.");
//            }
//
//            $translations = include($langPath);
//
//            $flattenedTranslations = $this->flattenArray($translations);
//
//            $inputs = [];
//            foreach ($flattenedTranslations as $key => $value) {
//                // $this->data[$key] = $value;
//                $inputs[] = TextInput::make($key)
//                    ->label(str_replace('_', ' ', ucfirst($key)));
//            }
//            return $inputs;
//        }
//        return [];
//    }
    protected function getTextInputFields($selectedFile): array
    {
        if (!$selectedFile) {
            return [];
        }

        $langPath = base_path('lang/' . $this->selectedLang . '/' . $selectedFile);

        if (!File::exists($langPath)) {
            throw new \Exception("The file '$langPath' does not exist.");
        }

        $translations = include($langPath);
        $flattenedTranslations = $this->flattenArray($translations);
        $inputs = [];

        foreach ($flattenedTranslations as $key => $value) {
            // Create readable label
            $label = ucwords(str_replace(['_', '.'], [' ', ' → '], $key));

            $inputs[] = TextInput::make("{$key}") // Keep dot notation for statePath binding
            ->label($label)
                ->default($value)
                ->lazy(); // Improves performance on large forms
        }
        return $inputs;
    }

//    protected function flattenArray(array $array, string $prefix = ''): array
//    {
//        $result = [];
//        foreach ($array as $key => $value) {
//            if (is_array($value)) {
//                $result = array_merge($result, $this->flattenArray($value, $prefix . $key . '.'));
//            } else {
//                $result[$key] = $value;
//            }
//        }
//        return $result;
//    }
    protected function flattenArray(array $array, string $prefix = ''): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $fullKey = $prefix . $key;
            if (is_array($value)) {
                $result += $this->flattenArray($value, $fullKey . '.');
            } else {
                $result[$fullKey] = $value;
            }
        }
        return $result;
    }

    public function save(): void
    {
        // Get the form state data
        $data = $this->form->getState();

        // Filter out 'language_id'
        $filteredData = array_filter($data, function ($key) {
            return $key !== 'language_id';
        }, ARRAY_FILTER_USE_KEY);

        // Define the path for the language file
        // $selectedLang = 'en'; // Adjust as needed
        $langPath = base_path('lang/' . $this->selectedLang . '/' . $this->selectedFile);

        // Check if the file exists
        if (File::exists($langPath)) {
            // Format the data for file
            $formattedData = $this->formatArrayForFile($filteredData);

            // Write the formatted data to the file
            $this->writeArrayToFile($langPath, $formattedData);
        } else {
            throw new \Exception("The file '$langPath' does not exist.");
        }
    }

    protected function formatArrayForFile(array $data): string
    {
        $formattedData = "<?php\n\nreturn [\n";

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $formattedValue = var_export($value, true);
            } else {
                $formattedValue = addslashes($value);
                $formattedValue = "'$formattedValue'";
            }

            $formattedData .= sprintf("    '%s' => %s,\n", addslashes($key), $formattedValue);
        }

        $formattedData .= "];\n";

        return $formattedData;
    }

    protected function writeArrayToFile(string $filePath, string $data): void
    {
        File::put($filePath, $data);
    }

    protected function unflattenArray(array $flat): array
    {
        $result = [];

        foreach ($flat as $key => $value) {
            $keys = explode('.', $key);
            $temp = &$result;

            foreach ($keys as $innerKey) {
                if (!isset($temp[$innerKey]) || !is_array($temp[$innerKey])) {
                    $temp[$innerKey] = [];
                }
                $temp = &$temp[$innerKey];
            }

            $temp = $value;
        }

        return $result;
    }

}
