<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Filament\Clusters\Settings;
use App\Models\BingWebmasterToken;
use App\Models\Setting;
use App\Services\BingWebmasterService;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Pages\SubNavigationPosition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class BingWebmaster extends Page
{
    public ?array $data = [];

    protected static string $view = 'filament.clusters.settings.pages.bing-webmaster';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?int $navigationSort = 11;

    protected static ?string $cluster = Settings::class;

    public ?array $record = [];

    public $token = null;
    public $isConnected = false;

    public static function getNavigationLabel(): string
    {
        return __('messages.bing.bing_webmaster');
    }

    public function getTitle(): string
    {
        return __('messages.bing.bing_webmaster');
    }

    public function getHeading(): string
    {
        return __('messages.bing.bing_webmaster');
    }

    public static function canView(): bool
    {
        return Auth::user()->hasPermissionTo('manage_settings');
    }

    public function mount(): void
    {
        if (!$this->canView()) {
            abort(403);
        }

        // Check if token exists
        $this->token = BingWebmasterToken::where('user_id', Auth::id())
            ->where('is_active', true)
            ->first();

        $this->isConnected = $this->token !== null;

        $decryptedApiKey = '';
        if ($this->token && !empty($this->token->api_key)) {
            try {
                $decryptedApiKey = Crypt::decryptString($this->token->api_key);
            } catch (\Exception $e) {
                $decryptedApiKey = '';
            }
        }
        
        $this->form->fill([
            'api_key' => $decryptedApiKey,
            'site_url' => $this->token->site_url ?? '',
            'is_active' => $this->token->is_active ?? true,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('messages.bing.api_credentials'))
                    ->description(__('messages.bing.api_credentials_description'))
                    ->schema([
                        TextInput::make('api_key')
                            ->label(__('messages.bing.api_key'))
                            ->required()
                            ->password()
                            ->helperText(__('messages.bing.api_key_helper'))
                            ->placeholder('Enter your Bing Webmaster API Key'),
                        TextInput::make('site_url')
                            ->label(__('messages.bing.site_url'))
                            ->required()
                            ->url()
                            ->placeholder('https://example.com')
                            ->helperText(__('messages.bing.site_url_helper')),
                        Toggle::make('is_active')
                            ->label(__('messages.bing.is_active'))
                            ->default(true)
                            ->helperText(__('messages.bing.is_active_helper')),
                    ]),

                Section::make(__('messages.bing.connection_status'))
                    ->description($this->isConnected 
                        ? __('messages.bing.connected_description')
                        : __('messages.bing.not_connected_description'))
                    ->schema([
                        Toggle::make('is_connected')
                            ->label(__('messages.bing.connection_status'))
                            ->disabled()
                            ->default($this->isConnected)
                            ->dehydrated(false),
                    ]),

                Section::make(__('messages.bing.instructions'))
                    ->description(__('messages.bing.instructions_description'))
                    ->schema([])
                    ->collapsible()
                    ->collapsed(),
            ])
            ->statePath('data');
    }

    public function testConnection(): void
    {
        try {
            $data = $this->form->getState();

            if (empty($data['api_key']) || empty($data['site_url'])) {
                Notification::make()
                    ->warning()
                    ->title(__('messages.error'))
                    ->body(__('messages.bing.api_key_and_site_required'))
                    ->send();
                return;
            }

            // Create temporary token for testing
            $tempToken = new BingWebmasterToken([
                'api_key' => $data['api_key'],
                'site_url' => $data['site_url'],
            ]);

            $service = new BingWebmasterService();
            $sites = $service->getSites($tempToken);

            if (!empty($sites)) {
                Notification::make()
                    ->success()
                    ->title(__('messages.bing.connection_success'))
                    ->body(__('messages.bing.connection_test_successful'))
                    ->send();
            } else {
                Notification::make()
                    ->warning()
                    ->title(__('messages.bing.connection_warning'))
                    ->body(__('messages.bing.no_sites_found'))
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title(__('messages.error'))
                ->body(__('messages.bing.connection_failed') . ': ' . $e->getMessage())
                ->send();

            Log::error('Bing Connection Test Error: ' . $e->getMessage());
        }
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            if (empty($data['api_key']) || empty($data['site_url'])) {
                Notification::make()
                    ->warning()
                    ->title(__('messages.error'))
                    ->body(__('messages.bing.api_key_and_site_required'))
                    ->send();
                return;
            }

            // Encrypt API key
            $encryptedApiKey = Crypt::encryptString($data['api_key']);

            if ($this->token) {
                // Update existing token
                $this->token->update([
                    'api_key' => $encryptedApiKey,
                    'site_url' => $data['site_url'],
                    'is_active' => $data['is_active'] ?? true,
                ]);
            } else {
                // Create new token
                BingWebmasterToken::create([
                    'user_id' => Auth::id(),
                    'api_key' => $encryptedApiKey,
                    'site_url' => $data['site_url'],
                    'is_active' => $data['is_active'] ?? true,
                ]);
            }

            Notification::make()
                ->success()
                ->title(__('messages.common.save'))
                ->body(__('messages.bing.settings_saved'))
                ->send();

            $this->mount(); // Refresh page state
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title(__('messages.error'))
                ->body(__('messages.bing.save_failed') . ': ' . $e->getMessage())
                ->send();

            Log::error('Bing Save Error: ' . $e->getMessage());
        }
    }

    public function disconnect(): void
    {
        try {
            if ($this->token) {
                $this->token->update(['is_active' => false]);
                
                Notification::make()
                    ->success()
                    ->title(__('messages.bing.disconnect'))
                    ->body(__('messages.bing.disconnected_successfully'))
                    ->send();

                $this->mount(); // Refresh page state
            }
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title(__('messages.error'))
                ->body(__('messages.bing.disconnect_failed') . ': ' . $e->getMessage())
                ->send();

            Log::error('Bing Disconnect Error: ' . $e->getMessage());
        }
    }
}
