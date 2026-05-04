<?php

namespace App\Filament\Pages;

use App\Enums\Sidebar;
use App\Http\Middleware\CheckPaddingSubscription;
use App\Models\AdSpaces as ModelsAdSpaces;
use Filament\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use NunoMaduro\Collision\Adapters\Phpunit\State;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

class AdSpaces extends Page
{
    protected static ?string $model = ModelsAdSpaces::class;

    protected $listeners = ['content-changed' => '$refresh'];

    public ?array $data = [];

    public ?string $id = null;

    public ?int $modalId = null;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.ad-spaces';

    protected static ?int $navigationSort = Sidebar::AD_SPACE->value;

    protected ?ModelsAdSpaces $record = null;

    public $ad_space;
    public $ad_url1;
    public $ad_code1;
    public $ad_url2;
    public $ad_code2;
    public $ad_banner1;
    public $ad_banner2;

    public static function getNavigationLabel(): string
    {
        return __('messages.ad_space.ad_space');
    }

    public function getTitle(): string
    {
        return __('messages.ad_space.ad_space');
    }

    public static function canAccess(): bool
    {
        return Auth::user()->hasPermissionTo('manage_ad');
    }

    protected static string|array $routeMiddleware = [
        CheckPaddingSubscription::class,
    ];

    public function mount(): void
    {
        $adBanner = ModelsAdSpaces::first();
        $this->modalId = $adBanner->id;

        if ($adBanner !== null) {
            $mediaItems = $adBanner->getMedia(ModelsAdSpaces::IMAGE_POST);
            if ($mediaItems) {
                $mediaUrls = $mediaItems->map(function ($media) {
                    return $media->getUrl();
                });
            }

            $this->dispatch('content-changed');
            $this->ad_space = $adBanner->ad_spaces;
            $this->ad_url1 = $adBanner->ad_url;
            $this->ad_code1 = $adBanner->code;
            $this->ad_banner1 = isset($mediaUrls[0]) ? $mediaUrls[0] : null;

            $this->form->fill([
                'ad_space' => $this->ad_space,
                'ad_url1' => $this->ad_url1,
                'ad_code1' => $this->ad_code1,
                'ad_banner1' => $this->ad_banner1,
            ]);
        }
    }

    public function updatedId($value)
    {
        $adBanner = ModelsAdSpaces::where('ad_spaces', $value)->get();

        if ($adBanner->isNotEmpty()) {
            $mediaItems = $adBanner[0]->getMedia(ModelsAdSpaces::IMAGE_POST);
            if ($mediaItems) {
                $mediaUrls = $mediaItems->map(function ($media) {
                    return $media->getUrl();
                });
            }

            if (isset($adBanner[1])) {
                $mediaItems1 = $adBanner[1]->getMedia(ModelsAdSpaces::IMAGE_POST);
                if ($mediaItems1) {
                    $mediaUrls1 = $mediaItems1->map(function ($media) {
                        return $media->getUrl();
                    });
                }
            }

            $this->ad_space = $adBanner[0]->ad_spaces;
            $this->ad_url1 = $adBanner[0]->ad_url;
            $this->ad_code1 = $adBanner[0]->code;
            $this->ad_url2 = isset($adBanner[1]) ? $adBanner[1]->ad_url : null;
            $this->ad_code2 = isset($adBanner[1]) ? $adBanner[1]->code : null;
            $this->ad_banner1 = isset($mediaUrls[0]) ? $mediaUrls[0] : null;
            $this->ad_banner2 = isset($mediaUrls1[0]) ? $mediaUrls1[0] : null;

            if ($adBanner[0]->ad_spaces == ModelsAdSpaces::ALL_DETAILS_SIDE) {
                $this->form->fill([
                    'ad_space' => $this->ad_space,
                    'ad_url2' => $this->ad_url1,
                    'ad_code2' => $this->ad_code1,
                    'ad_banner2' => $this->ad_banner1,
                ]);
            } else {
                $this->form->fill([
                    'ad_space' => $this->ad_space,
                    'ad_url1' => $this->ad_url1,
                    'ad_code1' => $this->ad_code1,
                    'ad_url2' => $this->ad_url2,
                    'ad_code2' => $this->ad_code2,
                    'ad_banner1' => $this->ad_banner1,
                    'ad_banner2' => $this->ad_banner2,
                ]);
            }
        }
    }

    public function form(Form $form): Form
    {
        $form->model = ModelsAdSpaces::with('media')->find($this->modalId);
        return $form
            ->schema([
                Select::make('ad_space')
                    ->label(__('messages.ad_space.select_ad_space') . ':')
                    ->validationAttribute(__('messages.ad_space.select_ad_space'))
                    ->placeholder(__('messages.ad_space.select_ad_space'))
                    ->options(ModelsAdSpaces::AD_SPACE)
                    ->live()
                    ->required()
                    ->default(ModelsAdSpaces::HEADER)
                    ->afterStateUpdated(function ($state, $record, $operation) use ($form) {
                        $adBanner = ModelsAdSpaces::where('ad_spaces', $state)->get();
                        $this->modalId = $adBanner[0]->id;
                        $form->model = ModelsAdSpaces::with('media')->find($this->modalId);
                        $this->id = $state;
                        $this->updatedId($state);
                    }),
                Group::make()
                    ->schema([
                        Section::make(__('messages.ad_space.desktop'))
                            ->columns(2)
                            ->schema([
                                Group::make()
                                    ->schema([
                                        TextInput::make('ad_url1')
                                            ->label(__('messages.ad_space.ad_url') . ':')
                                            ->validationAttribute(__('messages.ad_space.ad_url'))
                                            ->placeholder(__('messages.ad_space.ad_url'))
                                            ->required(),
                                        SpatieMediaLibraryFileUpload::make('ad_banner1')
                                            ->label(__('messages.allowed_file_size') . ' 800 X 130' . ':')
                                            ->validationAttribute(__('messages.allowed_file_size') . ' 800 X 130')
                                            ->rules(['image', 'dimensions:width=800,height=130'])
                                            ->saveUploadedFileUsing(function (SpatieMediaLibraryFileUpload $component, TemporaryUploadedFile $file, ?Model $record) {
                                                $rec = ModelsAdSpaces::where('ad_spaces', $this->id ?? 1)->get();
                                                $rec[0]->addMedia($file)->toMediaCollection(ModelsAdSpaces::IMAGE_POST);
                                            })
                                            ->collection(ModelsAdSpaces::IMAGE_POST)
                                            ->image()
                                            ->preserveFilenames()
                                            ->hidden(function (Get $get) {
                                                return  in_array($get('ad_space'), [12, 13, 14, 15, 16, 17]) ? true : false;
                                            }),
                                        SpatieMediaLibraryFileUpload::make('ad_banner1')
                                            ->label(__('messages.allowed_file_size') . ' 1280 X 150' . ':')
                                            ->validationAttribute(__('messages.allowed_file_size') . ' 1280 X 150')
                                            ->rules(['image', 'dimensions:width=1280,height=150'])
                                            ->saveUploadedFileUsing(function (SpatieMediaLibraryFileUpload $component, TemporaryUploadedFile $file, ?Model $record) {
                                                $rec = ModelsAdSpaces::where('ad_spaces', $this->id)->get();
                                                $rec[0]->addMedia($file)->toMediaCollection(ModelsAdSpaces::IMAGE_POST);
                                            })
                                            ->collection(ModelsAdSpaces::IMAGE_POST)
                                            ->image()
                                            ->preserveFilenames()
                                            ->hidden(function (Get $get) {
                                                return  in_array($get('ad_space'), [12, 13, 14, 15, 16, 17]) ? false : true;
                                            }),
                                    ])->columns(1),
                                Textarea::make('ad_code1')
                                    ->label(__('messages.ad_space.ad_code') . ':')
                                    ->validationAttribute(__('messages.ad_space.ad_code'))
                                    ->placeholder(__('messages.ad_space.ad_code'))
                                    ->required()
                                    ->rows(7)
                                    ->columns(1)->columnSpan(1),
                            ])
                            ->visible(function (Get $get) {
                                return $get('ad_space') != ModelsAdSpaces::ALL_DETAILS_SIDE && $get('ad_space') != ModelsAdSpaces::ALL_DETAILS_SIDE_THEME_1 ? true : false;
                            }),
                        Section::make(__('messages.ad_space.mobile'))
                            ->columns(2)
                            ->schema([
                                Group::make()
                                    ->schema([
                                        TextInput::make('ad_url2')
                                            ->label(__('messages.ad_space.ad_url') . ':')
                                            ->validationAttribute(__('messages.ad_space.ad_url'))
                                            ->placeholder(__('messages.ad_space.ad_url'))
                                            ->required(),
                                        SpatieMediaLibraryFileUpload::make('ad_banner2')
                                            ->label(__('messages.allowed_file_size') . ' 350 X 290' . ':')
                                            ->validationAttribute(__('messages.allowed_file_size') . ' 350 X 290')
                                            ->rules(['image', 'dimensions:width=350,height=290'])
                                            ->getUploadedFileUsing(static function (SpatieMediaLibraryFileUpload $component, string $file): ?array {
                                                if (!$component->getRecord()) {
                                                    return null;
                                                }

                                                /** @var ?Media $media */
                                                $record = ModelsAdSpaces::with('media')->where('ad_spaces', $component->getRecord()->ad_spaces)->where('ad_view', ModelsAdSpaces::MOBILE)->first();
                                                $media = $record->getRelationValue('media')->firstWhere('uuid', $record->media[0]->uuid);
                                                // $media = $component->getRecord()->getRelationValue('media')->firstWhere('uuid', $file);

                                                $url = null;

                                                if ($component->getVisibility() === 'private') {
                                                    $conversion = $component->getConversion();

                                                    try {
                                                        $url = $media?->getTemporaryUrl(
                                                            now()->addMinutes(5),
                                                            (filled($conversion) && $media->hasGeneratedConversion($conversion)) ? $conversion : '',
                                                        );
                                                    } catch (Throwable $exception) {
                                                        // This driver does not support creating temporary URLs.
                                                    }
                                                }

                                                if ($component->getConversion() && $media?->hasGeneratedConversion($component->getConversion())) {
                                                    $url ??= $media->getUrl($component->getConversion());
                                                }

                                                $url ??= $media?->getUrl();

                                                return [
                                                    'name' => $media?->getAttributeValue('name') ?? $media?->getAttributeValue('file_name'),
                                                    'size' => $media?->getAttributeValue('size'),
                                                    'type' => $media?->getAttributeValue('mime_type'),
                                                    'url' => $url,
                                                ];
                                            })
                                            ->saveUploadedFileUsing(function (SpatieMediaLibraryFileUpload $component, TemporaryUploadedFile $file, ?Model $record) {
                                                $rec = ModelsAdSpaces::where('ad_spaces', $this->id)->get();
                                                $rec[1]->addMedia($file)->toMediaCollection(ModelsAdSpaces::IMAGE_POST);
                                            })
                                            ->collection(ModelsAdSpaces::IMAGE_POST)
                                            ->image()
                                            ->preserveFilenames()
                                            ->hidden(function (Get $get) {
                                                return  in_array($get('ad_space'), [12, 13, 14, 15, 16, 17]) ? true : false;
                                            }),
                                        SpatieMediaLibraryFileUpload::make('ad_banner2')
                                            ->label(__('messages.allowed_file_size') . ' 407 X 340' . ':')
                                            ->validationAttribute(__('messages.allowed_file_size') . ' 407 X 340')
                                            ->rules(['image', 'dimensions:width=407,height=340'])
                                            ->getUploadedFileUsing(static function (SpatieMediaLibraryFileUpload $component, string $file): ?array {
                                                if (!$component->getRecord()) {
                                                    return null;
                                                }

                                                /** @var ?Media $media */
                                                $record = ModelsAdSpaces::with('media')->where('ad_spaces', $component->getRecord()->ad_spaces)->where('ad_view', ModelsAdSpaces::MOBILE)->first();
                                                $media = $record->getRelationValue('media')->firstWhere('uuid', $record->media[0]->uuid);
                                                // $media = $component->getRecord()->getRelationValue('media')->firstWhere('uuid', $file);

                                                $url = null;

                                                if ($component->getVisibility() === 'private') {
                                                    $conversion = $component->getConversion();

                                                    try {
                                                        $url = $media?->getTemporaryUrl(
                                                            now()->addMinutes(5),
                                                            (filled($conversion) && $media->hasGeneratedConversion($conversion)) ? $conversion : '',
                                                        );
                                                    } catch (Throwable $exception) {
                                                        // This driver does not support creating temporary URLs.
                                                    }
                                                }

                                                if ($component->getConversion() && $media?->hasGeneratedConversion($component->getConversion())) {
                                                    $url ??= $media->getUrl($component->getConversion());
                                                }

                                                $url ??= $media?->getUrl();

                                                return [
                                                    'name' => $media?->getAttributeValue('name') ?? $media?->getAttributeValue('file_name'),
                                                    'size' => $media?->getAttributeValue('size'),
                                                    'type' => $media?->getAttributeValue('mime_type'),
                                                    'url' => $url,
                                                ];
                                            })
                                            ->saveUploadedFileUsing(function (SpatieMediaLibraryFileUpload $component, TemporaryUploadedFile $file, ?Model $record) {
                                                $rec = ModelsAdSpaces::where('ad_spaces', $this->id)->get();
                                                $rec[1]->addMedia($file)->toMediaCollection(ModelsAdSpaces::IMAGE_POST);
                                            })
                                            ->collection(ModelsAdSpaces::IMAGE_POST)
                                            ->image()
                                            ->preserveFilenames()
                                            ->hidden(function (Get $get) {
                                                return  in_array($get('ad_space'), [12, 13, 14, 15, 16, 17]) ? false : true;
                                            }),
                                    ])->columns(1),
                                Textarea::make('ad_code2')
                                    ->label(__('messages.ad_space.ad_code') . ':')
                                    ->validationAttribute(__('messages.ad_space.ad_code'))
                                    ->placeholder(__('messages.ad_space.ad_code'))
                                    ->required()
                                    ->rows(7)
                                    ->columns(1)->columnSpan(1),
                            ])
                            ->visible(function (Get $get) {
                                return $get('ad_space') != ModelsAdSpaces::HEADER && $get('ad_space') != ModelsAdSpaces::ALL_DETAILS_SIDE && $get('ad_space') != ModelsAdSpaces::ALL_DETAILS_SIDE_THEME_1 ? true : false;
                            }),
                    ]),
                Group::make()
                    ->schema([
                        Section::make(__('messages.ad_space.mobile'))
                            ->columns(2)
                            ->schema([
                                Group::make()
                                    ->schema([
                                        TextInput::make('ad_url2')
                                            ->label(__('messages.ad_space.ad_url') . ':')
                                            ->validationAttribute(__('messages.ad_space.ad_url'))
                                            ->placeholder(__('messages.ad_space.ad_url'))
                                            ->required(),
                                        SpatieMediaLibraryFileUpload::make('ad_banner2')
                                            ->image()
                                            ->label(__('messages.allowed_file_size') . ' ' . '350 X 290' . ':')
                                            ->validationAttribute(__('messages.allowed_file_size') . ' ' . '350 X 290')
                                            ->rules(['image', 'dimensions:width=350,height=290'])
                                            ->collection(ModelsAdSpaces::IMAGE_POST),
                                    ])->columns(1),
                                Textarea::make('ad_code2')
                                    ->label(__('messages.ad_space.ad_code') . ':')
                                    ->validationAttribute(__('messages.ad_space.ad_code'))
                                    ->placeholder(__('messages.ad_space.ad_code'))
                                    ->required()
                                    ->rows(7)
                                    ->columns(1)->columnSpan(1),
                            ]),
                    ])
                    ->visible(function (Get $get) {
                        return $get('ad_space') == ModelsAdSpaces::ALL_DETAILS_SIDE ? true : false;
                    }),
                Group::make()
                    ->schema([
                        Section::make(__('messages.ad_space.mobile'))
                            ->columns(2)
                            ->schema([
                                Group::make()
                                    ->schema([
                                        TextInput::make('ad_url2')
                                            ->label(__('messages.ad_space.ad_url') . ':')
                                            ->validationAttribute(__('messages.ad_space.ad_url'))
                                            ->placeholder(__('messages.ad_space.ad_url'))
                                            ->required(),
                                        SpatieMediaLibraryFileUpload::make('ad_banner2')
                                            ->image()
                                            ->label(__('messages.allowed_file_size') . ' ' . '407 X 340' . ':')
                                            ->validationAttribute(__('messages.allowed_file_size') . ' ' . '407 X 340')
                                            ->rules(['image', 'dimensions:width=407,height=340'])
                                            ->collection(ModelsAdSpaces::IMAGE_POST),
                                    ])->columns(1),
                                Textarea::make('ad_code2')
                                    ->label(__('messages.ad_space.ad_code') . ':')
                                    ->validationAttribute(__('messages.ad_space.ad_code'))
                                    ->placeholder(__('messages.ad_space.ad_code'))
                                    ->required()
                                    ->rows(7)
                                    ->columns(1)->columnSpan(1),
                            ]),
                    ])
                    ->visible(function (Get $get) {
                        return $get('ad_space') == ModelsAdSpaces::ALL_DETAILS_SIDE_THEME_1 ? true : false;
                    }),
            ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        try {
            $model = ModelsAdSpaces::where('ad_spaces', $data['ad_space'])->get();

            if ($data['ad_space'] == ModelsAdSpaces::ALL_DETAILS_SIDE || $data['ad_space'] == ModelsAdSpaces::ALL_DETAILS_SIDE_THEME_1) {
                $model[0]->update([
                    'ad_url' => $data['ad_url2'],
                    'code' => $data['ad_code2'],
                ]);
            } else {
                $model[0]->update([
                    'ad_url' => $data['ad_url1'],
                    'code' => $data['ad_code1'],
                ]);
                if (isset($model[1])) {
                    $model[1]->update([
                        'ad_url' => $data['ad_url2'],
                        'code' => $data['ad_code2'],
                    ]);
                }
            }

            Notification::make()
                ->success()
                ->title(__('messages.placeholder.adSpaces_updated_successfully'))
                ->send();
        } catch (Halt $exception) {
            $this->notify('error', $exception->getMessage());
        }
    }

    public function getFormAction(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('save'),
        ];
    }
}
