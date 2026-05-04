<?php

namespace App\Filament\Clusters\Settings\Pages;

use Throwable;
use App\Enums\Sidebar;
use App\Models\Setting;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Support\Collection;
use App\Filament\Clusters\Settings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Support\Exceptions\Halt;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Filament\Forms\Components\ToggleButtons;
use League\Flysystem\UnableToCheckFileExistence;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class General extends Page
{
    public ?array $data = [];

    // protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.clusters.settings.pages.general';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?int $navigationSort = Sidebar::GENERAL->value;

    protected static ?string $cluster = Settings::class;

    public ?array  $record = [];


    public static function getNavigationLabel(): string
    {
        return __('messages.setting.general');
    }

    public function getTitle(): string
    {
        return __('messages.setting.general');
    }

    public static function canView(): bool
    {
        return Auth::user()->hasPermissionTo('manage_settings');
    }

    public function mount(): void
    {
        if (! $this->canView()) {
            abort(403); // Unauthorized access
        }

        $this->record = Setting::pluck('value', 'key')->toArray();

        $logoMedia = Setting::where('key', 'logo')->first()->getFirstMediaUrl('logo');
        $faviconMedia = Setting::where('key', 'favicon')->first()->getFirstMediaUrl('favicon');

        $this->getFormSchemaPart1->fill([
            'application_name' => isset($this->record['application_name']) ? $this->record['application_name'] : null,
            'contact_no' => isset($this->record['contact_no']) ? $this->record['contact_no'] : null,
            'email' => isset($this->record['email']) ? $this->record['email'] : null,
            'copy_right_text' => isset($this->record['copy_right_text']) ? $this->record['copy_right_text'] : null,
            'front_language' => isset($this->record['front_language']) ? $this->record['front_language'] : null,
            'rss_feed_update_time' => isset($this->record['rss_feed_update_time']) ? $this->record['rss_feed_update_time'] : null,
            // 'logo' => $logoMedia ?? null,
            // 'favicon' => $faviconMedia ?? null,

            'stripe_checkbox_btn' => isset($this->record['stripe_checkbox_btn']) ? ($this->record['stripe_checkbox_btn'] ? true : false) : false,
            'stripe_key' => isset($this->record['stripe_key']) ? $this->record['stripe_key'] : null,
            'stripe_secret' => isset($this->record['stripe_secret']) ? $this->record['stripe_secret'] : null,
            'paypal_checkbox_btn' => isset($this->record['paypal_checkbox_btn']) ? ($this->record['paypal_checkbox_btn'] ? true : false) : false,
            'paypal_client_id' => isset($this->record['paypal_client_id']) ? $this->record['paypal_client_id'] : null,
            'paypal_secret' => isset($this->record['paypal_secret']) ? $this->record['paypal_secret'] : null,
            'paypal_mode' => isset($this->record['paypal_mode']) ? $this->record['paypal_mode'] : null,
            'manually_checkbox_btn' => isset($this->record['manually_checkbox_btn']) ? ($this->record['manually_checkbox_btn'] ? true : false) : false,

            'show_captcha_on_registration' => isset($this->record['show_captcha_on_registration']) ? ($this->record['show_captcha_on_registration'] ? true : false) : false,
            'show_captcha' => isset($this->record['show_captcha']) ? ($this->record['show_captcha'] ? true : false) : false,
            'site_key' => isset($this->record['site_key']) ? $this->record['site_key'] : null,
            'secret_key' => isset($this->record['secret_key']) ? $this->record['secret_key'] : null,

            'open_AI_key' => isset($this->record['open_AI_key']) ? $this->record['open_AI_key'] : null,

            'whatsapp' => isset($this->record['whatsapp']) ? ($this->record['whatsapp'] ? true : false) : false,
            'linkedin' => isset($this->record['linkedin']) ? ($this->record['linkedin'] ? true : false) : false,
            'twitter' => isset($this->record['twitter']) ? ($this->record['twitter'] ? true : false) : false,
            'facebook' => isset($this->record['facebook']) ? ($this->record['facebook'] ? true : false) : false,
            'reddit' => isset($this->record['reddit']) ? ($this->record['reddit'] ? true : false) : false,

        ]);

        // $this->getFormSchemaPart1->fill($this->record);
    }

    protected function getForms(): array
    {
        return [
            'getFormSchemaPart1',
            'getFormSchemaPart2',
            'getFormSchemaPart3',
            'getFormSchemaPart4',
            'getFormSchemaPart5',
        ];
    }

    public function getFormSchemaPart1(Form $form): Form
    {
        $form->model = Setting::first();
        return $form
            ->schema([
                Section::make(__('messages.setting.general_details'))
                    ->schema([
                        TextInput::make('application_name')
                            ->label(__('messages.setting.app_name') . ':')
                            ->validationAttribute(__('messages.setting.app_name'))
                            ->placeholder(__('messages.setting.app_name'))
                            ->required()
                            ->placeholder('Application Name')
                            ->inlineLabel(true),
                        TextInput::make('contact_no')
                            ->label(__('messages.user.contact_number') . ':')
                            ->validationAttribute(__('messages.user.contact_number'))
                            ->placeholder(__('messages.user.contact_number'))
                            ->tel()
                            ->required()
                            ->placeholder('Contact Number')
                            ->inlineLabel(true),
                        TextInput::make('email')
                            ->label(__('messages.user.email') . ':')
                            ->validationAttribute(__('messages.user.email'))
                            ->placeholder(__('messages.user.email'))
                            ->email()
                            ->required()
                            ->placeholder('Email')
                            ->inlineLabel(true),
                        TextInput::make('copy_right_text')
                            ->label(__('messages.setting.copy_right_text') . ':')
                            ->validationAttribute(__('messages.setting.copy_right_text'))
                            ->placeholder(__('messages.setting.copy_right_text'))
                            ->required()
                            ->placeholder('Copyright Text')
                            ->inlineLabel(true),
                        Select::make('front_language')
                            ->label(__('messages.setting.front_language') . ':')
                            ->validationAttribute(__('messages.setting.front_language'))
                            ->placeholder(__('messages.setting.front_language'))
                            ->required()
                            ->options(getLanguage())
                            ->searchable()
                            ->native(false)
                            ->inlineLabel(true),
                        Select::make('rss_feed_update_time')
                            ->label(__('messages.setting.rss_feed_auto_update') . ':')
                            ->validationAttribute(__('messages.setting.rss_feed_auto_update'))
                            ->placeholder(__('messages.setting.rss_feed_auto_update'))
                            ->required()
                            ->options(Setting::AUTO_UPDATE_RSS_FEED)
                            ->searchable()
                            ->native(false)
                            ->inlineLabel(true),
                        SpatieMediaLibraryFileUpload::make('logo')
                            // ->rules(['image', 'dimensions:width=90,height=60'])
                            // ->saveUploadedFileUsing(function (SpatieMediaLibraryFileUpload $component, TemporaryUploadedFile $file, ?Model $record) {
                            //     $rec = Setting::first();
                            //     $media = $rec->addMedia($file)->toMediaCollection(Setting::LOGO);
                            //     $rec->where('key', '=', 'logo')->update(['value' => $media->getUrl()]);
                            // })
                            ->disk(config('app.media_disk'))
                            ->image()
                            ->loadStateFromRelationshipsUsing(static function (SpatieMediaLibraryFileUpload $component, HasMedia $record): void {
                                /** @var Model&HasMedia $record */
                                $record = Setting::with('media')->where('key', '=', 'logo')->first();
                                $media = $record->load('media')->getMedia($component->getCollection() ?? 'default')
                                    ->when(
                                        $component->hasMediaFilter(),
                                        fn(Collection $media) => $component->filterMedia($media)
                                    )
                                    ->when(
                                        ! $component->isMultiple(),
                                        fn(Collection $media): Collection => $media->take(1),
                                    )
                                    ->mapWithKeys(function (Media $media): array {
                                        $uuid = $media->getAttributeValue('uuid');
                                        return [$uuid => $uuid];
                                    })
                                    ->toArray();
                                $component->state($media);
                            })
                            ->getUploadedFileUsing(static function (SpatieMediaLibraryFileUpload $component, string $file): ?array {
                                if (! $component->getRecord()) {
                                    return null;
                                }
                                $record = Setting::with('media')->where('key', '=', 'logo')->first();
                                $media = $record->getRelationValue('media')->firstWhere('uuid', $file);

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
                            ->saveUploadedFileUsing(static function (SpatieMediaLibraryFileUpload $component, TemporaryUploadedFile $file, ?Model $record): ?string {
                                $record = Setting::where('key', '=', 'logo')->first();
                                if (! $record) {
                                    $record = Setting::create([
                                        'key' => 'logo',
                                        'value' => null,
                                    ]);
                                }

                                if (! method_exists($record, 'addMediaFromString')) {
                                    return $file;
                                }

                                try {
                                    if (! $file->exists()) {
                                        return null;
                                    }
                                } catch (UnableToCheckFileExistence $exception) {
                                    return null;
                                }

                                $record->getMedia($component->getCollection() ?? 'default')
                                    ->whereNotIn('uuid', array_keys($component->getState() ?? []))
                                    ->when($component->hasMediaFilter(), fn(Collection $media): Collection => $component->filterMedia($media))
                                    ->each(fn(Media $media) => $media->delete());

                                /** @var FileAdder $mediaAdder */
                                $mediaAdder = $record->addMediaFromString($file->get());

                                $filename = $component->getUploadedFileNameForStorage($file);

                                $media = $mediaAdder
                                    ->addCustomHeaders($component->getCustomHeaders())
                                    ->usingFileName($filename)
                                    ->usingName($component->getMediaName($file) ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                                    ->storingConversionsOnDisk($component->getConversionsDisk() ?? '')
                                    ->withCustomProperties($component->getCustomProperties())
                                    ->withManipulations($component->getManipulations())
                                    ->withResponsiveImagesIf($component->hasResponsiveImages())
                                    ->withProperties($component->getProperties())
                                    ->toMediaCollection($component->getCollection() ?? 'default', $component->getDiskName());

                                $record->update(['value' => $media->getUrl()]);
                                return $media->getAttributeValue('uuid');
                            })
                            ->beforeStateDehydrated(function (array $state) {
                                if (empty($state)) {
                                    Setting::where('key', 'logo')->update(['value' => '']);
                                }
                                return null;
                            })
                            ->label(__('messages.setting.logo') . ':')
                            ->validationAttribute(__('messages.setting.logo'))
                            // ->placeholder(__('messages.placeholder.best_resolution_for_this_logo_will_be_90x60'))
                            ->reorderable()
                            ->collection(Setting::LOGO)
                            ->inlineLabel(true)
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: fn() => __('messages.placeholder.best_resolution_for_this_logo_will_be_90x60')),
                        SpatieMediaLibraryFileUpload::make('favicon')
                            // ->rules(['image', 'dimensions:width=32,height=32'])
                            // ->saveUploadedFileUsing(function (SpatieMediaLibraryFileUpload $component, TemporaryUploadedFile $file, ?Model $record) {
                            //     $rec = Setting::first();
                            //     $media = $rec->addMedia($file)->toMediaCollection(Setting::FAVICON);
                            //     $rec->where('key', '=', 'favicon')->update(['value' => $media->getUrl()]);
                            // })
                            ->image()
                            ->disk(config('app.media_disk'))
                            ->loadStateFromRelationshipsUsing(static function (SpatieMediaLibraryFileUpload $component, HasMedia $record): void {
                                /** @var Model&HasMedia $record */
                                $record = Setting::with('media')->where('key', '=', 'favicon')->first();
                                $media = $record->load('media')->getMedia($component->getCollection() ?? 'default')
                                    ->when(
                                        $component->hasMediaFilter(),
                                        fn(Collection $media) => $component->filterMedia($media)
                                    )
                                    ->when(
                                        ! $component->isMultiple(),
                                        fn(Collection $media): Collection => $media->take(1),
                                    )
                                    ->mapWithKeys(function (Media $media): array {
                                        $uuid = $media->getAttributeValue('uuid');
                                        return [$uuid => $uuid];
                                    })
                                    ->toArray();
                                $component->state($media);
                            })
                            ->getUploadedFileUsing(static function (SpatieMediaLibraryFileUpload $component, string $file): ?array {
                                if (! $component->getRecord()) {
                                    return null;
                                }
                                $record = Setting::with('media')->where('key', '=', 'favicon')->first();
                                $media = $record->getRelationValue('media')->firstWhere('uuid', $file);

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
                            ->saveUploadedFileUsing(static function (SpatieMediaLibraryFileUpload $component, TemporaryUploadedFile $file, ?Model $record): ?string {
                                $record = Setting::where('key', '=', 'favicon')->first();
                                if (! $record) {
                                    $record = Setting::create([
                                        'key' => 'favicon',
                                        'value' => null,
                                    ]);
                                }

                                if (! method_exists($record, 'addMediaFromString')) {
                                    return $file;
                                }

                                try {
                                    if (! $file->exists()) {
                                        return null;
                                    }
                                } catch (UnableToCheckFileExistence $exception) {
                                    return null;
                                }

                                $record->getMedia($component->getCollection() ?? 'default')
                                    ->whereNotIn('uuid', array_keys($component->getState() ?? []))
                                    ->when($component->hasMediaFilter(), fn(Collection $media): Collection => $component->filterMedia($media))
                                    ->each(fn(Media $media) => $media->delete());

                                /** @var FileAdder $mediaAdder */
                                $mediaAdder = $record->addMediaFromString($file->get());

                                $filename = $component->getUploadedFileNameForStorage($file);

                                $media = $mediaAdder
                                    ->addCustomHeaders($component->getCustomHeaders())
                                    ->usingFileName($filename)
                                    ->usingName($component->getMediaName($file) ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                                    ->storingConversionsOnDisk($component->getConversionsDisk() ?? '')
                                    ->withCustomProperties($component->getCustomProperties())
                                    ->withManipulations($component->getManipulations())
                                    ->withResponsiveImagesIf($component->hasResponsiveImages())
                                    ->withProperties($component->getProperties())
                                    ->toMediaCollection($component->getCollection() ?? 'default', $component->getDiskName());

                                $record->update(['value' => $media->getUrl()]);
                                return $media->getAttributeValue('uuid');
                            })
                            ->beforeStateDehydrated(function (array $state) {
                                if (empty($state)) {
                                    Setting::where('key', 'favicon')->update(['value' => '']);
                                }
                                return null;
                            })
                            ->label(__('messages.setting.favicon') . ':')
                            ->validationAttribute(__('messages.setting.favicon'))
                            // ->placeholder(__('messages.placeholder.best_resolution_for_this_favicon_will_be_32X32'))
                            ->reorderable()
                            ->collection(Setting::FAVICON)
                            ->inlineLabel(true)
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: fn() => __('messages.placeholder.best_resolution_for_this_favicon_will_be_32X32')),
                    ])
            ])->statePath('data');
    }

    public function getFormSchemaPart2(Form $form): Form
    {
        $form->model = Setting::first();
        return $form
            ->schema([
                Section::make(__('messages.payment_method'))
                    ->schema([
                        Toggle::make('stripe_checkbox_btn')
                            ->inlineLabel(true)
                            ->live()
                            ->label(__('messages.setting.Stripe') . ':')
                            ->validationAttribute(__('messages.setting.Stripe')),
                        TextInput::make('stripe_key')
                            ->label(__('messages.setting.stripe_key') . ':')
                            ->validationAttribute(__('messages.setting.stripe_key'))
                            ->placeholder(__('messages.setting.stripe_key'))
                            ->inlineLabel(true)
                            ->required()
                            ->visible(fn($get) => $get('stripe_checkbox_btn')),
                        TextInput::make('stripe_secret')
                            ->label(__('messages.setting.stripe_secret_key') . ':')
                            ->validationAttribute(__('messages.setting.stripe_secret_key'))
                            ->placeholder(__('messages.setting.stripe_secret_key'))
                            ->inlineLabel(true)
                            ->required()
                            ->visible(fn($get) => $get('stripe_checkbox_btn')),

                        Toggle::make('paypal_checkbox_btn')
                            ->inlineLabel(true)
                            ->live()
                            ->label(__('messages.setting.Paypal') . ':')
                            ->validationAttribute(__('messages.setting.Paypal')),
                        TextInput::make('paypal_client_id')
                            ->label(__('messages.setting.paypal_client_id') . ':')
                            ->validationAttribute(__('messages.setting.paypal_client_id'))
                            ->placeholder(__('messages.setting.paypal_client_id'))
                            ->inlineLabel(true)
                            ->required()
                            ->visible(fn($get) => $get('paypal_checkbox_btn')),
                        TextInput::make('paypal_secret')
                            ->label(__('messages.setting.paypal_secret') . ':')
                            ->validationAttribute(__('messages.setting.paypal_secret'))
                            ->placeholder(__('messages.setting.paypal_secret'))
                            ->inlineLabel(true)
                            ->required()
                            ->visible(fn($get) => $get('paypal_checkbox_btn')),
                        TextInput::make('paypal_mode')
                            ->label(__('messages.setting.paypal_mode') . ':')
                            ->validationAttribute(__('messages.setting.paypal_mode'))
                            ->placeholder(__('messages.setting.paypal_mode'))
                            ->inlineLabel(true)
                            ->required()
                            ->visible(fn($get) => $get('paypal_checkbox_btn')),

                        Toggle::make('manually_checkbox_btn')
                            ->label(__('messages.setting.Manually') . ':')
                            ->validationAttribute(__('messages.setting.Manually'))
                            ->inlineLabel(true)
                    ])
            ])->statePath('data');
    }

    public function getFormSchemaPart3(Form $form): Form
    {
        $form->model = Setting::first();
        return $form
            ->schema([
                Section::make(__('messages.setting.google_recaptcha'))
                    ->schema([
                        Toggle::make('show_captcha_on_registration')
                            ->inlineLabel(true)
                            ->live()
                            ->label(__('messages.setting.show_capcha_register') . ':')
                            ->validationAttribute(__('messages.setting.show_capcha_register')),
                        Toggle::make('show_captcha')
                            ->inlineLabel(true)
                            ->live()
                            ->label(__('messages.setting.show_captcha') . ':')
                            ->validationAttribute(__('messages.setting.show_captcha')),
                        TextInput::make('site_key')
                            ->label(__('messages.setting.site_key') . ':')
                            ->validationAttribute(__('messages.setting.site_key'))
                            ->placeholder(__('messages.setting.site_key'))
                            ->inlineLabel(true)
                            ->required()
                            ->visible(fn($get) => $get('show_captcha_on_registration') || $get('show_captcha')),
                        TextInput::make('secret_key')
                            ->label(__('messages.setting.secret_key') . ':')
                            ->validationAttribute(__('messages.setting.secret_key'))
                            ->placeholder(__('messages.setting.secret_key'))
                            ->inlineLabel(true)
                            ->required()
                            ->visible(fn($get) => $get('show_captcha_on_registration') || $get('show_captcha')),
                    ])
            ])->statePath('data');
    }

    public function getFormSchemaPart4(Form $form): Form
    {
        $form->model = Setting::first();
        return $form
            ->schema([
                Section::make(__('messages.post.open_ai'))
                    ->schema([
                        TextInput::make('open_AI_key')
                            ->label(__('messages.setting.open_ai_key') . ':')
                            ->validationAttribute(__('messages.setting.open_ai_key'))
                            ->placeholder(__('messages.setting.open_ai_key'))
                            ->inlineLabel(true)
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                $set('open_AI_key', str_replace(' ', '', $state));
                            })
                    ])
            ])->statePath('data');
    }

    public function getFormSchemaPart5(Form $form): Form
    {
        $form->model = Setting::first();
        return $form
            ->schema([
                Section::make(__('messages.setting.social_media_sharing'))
                    ->schema([
                        Toggle::make('whatsapp')
                            ->label(__('messages.setting.whatsapp') . ':')
                            ->validationAttribute(__('messages.setting.whatsapp'))
                            ->inlineLabel(true),
                        Toggle::make('linkedin')
                            ->label(__('messages.setting.linkedIn') . ':')
                            ->validationAttribute(__('messages.setting.linkedIn'))
                            ->inlineLabel(true),
                        Toggle::make('twitter')
                            ->label(__('messages.setting.twitter') . ':')
                            ->validationAttribute(__('messages.setting.twitter'))
                            ->inlineLabel(true),
                        Toggle::make('facebook')
                            ->label(__('messages.setting.facebook') . ':')
                            ->validationAttribute(__('messages.setting.facebook'))
                            ->inlineLabel(true),
                        Toggle::make('reddit')
                            ->label(__('messages.setting.reddit') . ':')
                            ->validationAttribute(__('messages.setting.reddit'))
                            ->inlineLabel(true),
                    ])
            ])->statePath('data');
    }


    public function savePart1()
    {
        // return Notification::make()
        // ->danger()
        // ->title('This functionality not allowed in demo.')
        // ->send();
        try {
            $data = $this->getFormSchemaPart1->getState();
            foreach ($data as $key => $value) {
                $setting = Setting::where('key', $key)->first();
                if ($setting) {
                    $setting->update(['value' => $value]);
                } else {
                    Setting::create(['key' => $key, 'value' => $value]);
                }
            }

            if (auth()->user()->hasRole('customer')) {
                redirect()->route('filament.customer.settings.pages.general');
            } else {
                redirect()->route('filament.admin.settings.pages.general');
            }
            Notification::make()
                ->success()
                ->title(__('messages.placeholder.settings_updated_successfully'))
                ->send();
        } catch (Halt $exception) {
            $this->notify('error', $exception->getMessage());
        }
    }

    public function savePart2()
    {
        // return Notification::make()
        //     ->danger()
        //     ->title('This functionality not allowed in demo.')
        //     ->send();
        try {
            $data = $this->getFormSchemaPart2->getState();
            foreach ($data as $key => $value) {
                $setting = Setting::where('key', $key)->first();
                if ($setting) {
                    $setting->update(['value' => $value]);
                } else {
                    Setting::create(['key' => $key, 'value' => $value]);
                }
            }
            Notification::make()
                ->success()
                ->title(__('messages.placeholder.settings_updated_successfully'))
                ->send();
        } catch (Halt $exception) {
            $this->notify('error', $exception->getMessage());
        }
    }

    public function savePart3()
    {
        // return Notification::make()
        //     ->danger()
        //     ->title('This functionality not allowed in demo.')
        //     ->send();
        try {
            $data = $this->getFormSchemaPart3->getState();
            foreach ($data as $key => $value) {
                $setting = Setting::where('key', $key)->first();
                if ($setting) {
                    $setting->update(['value' => $value]);
                } else {
                    Setting::create(['key' => $key, 'value' => $value]);
                }
            }
            Notification::make()
                ->success()
                ->title(__('messages.placeholder.settings_updated_successfully'))
                ->send();
        } catch (Halt $exception) {
            $this->notify('error', $exception->getMessage());
        }
    }

    public function savePart4()
    {
        // return Notification::make()
        //     ->danger()
        //     ->title('This functionality not allowed in demo.')
        //     ->send();
        try {
            $data = $this->getFormSchemaPart4->getState();
            foreach ($data as $key => $value) {
                $setting = Setting::where('key', $key)->first();
                if ($setting) {
                    $setting->update(['value' => $value ?? '']);
                } else {
                    Setting::create(['key' => $key, 'value' => $value]);
                }
            }
            Notification::make()
                ->success()
                ->title(__('messages.placeholder.settings_updated_successfully'))
                ->send();
        } catch (Halt $exception) {
            $this->notify('error', $exception->getMessage());
        }
    }

    public function savePart5()
    {
        // return Notification::make()
        //     ->danger()
        //     ->title('This functionality not allowed in demo.')
        //     ->send();
        try {
            $data = $this->getFormSchemaPart5->getState();
            foreach ($data as $key => $value) {
                $setting = Setting::where('key', $key)->first();
                if ($setting) {
                    $setting->update(['value' => $value]);
                } else {
                    Setting::create(['key' => $key, 'value' => $value]);
                }
            }
            Notification::make()
                ->success()
                ->title(__('messages.placeholder.settings_updated_successfully'))
                ->send();
        } catch (Halt $exception) {
            $this->notify('error', $exception->getMessage());
        }
    }

    public function getFormAction1(): array
    {
        return [
            Action::make('savePart1')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('savePart1'),
        ];
    }

    public function getFormAction2(): array
    {
        return [
            Action::make('savePart2')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('savePart2'),
        ];
    }

    public function getFormAction3(): array
    {
        return [
            Action::make('savePart3')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('savePart3'),
        ];
    }

    public function getFormAction4(): array
    {
        return [
            Action::make('savePart4')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('savePart4'),
        ];
    }

    public function getFormAction5(): array
    {
        return [
            Action::make('savePart5')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('savePart5'),
        ];
    }
}
