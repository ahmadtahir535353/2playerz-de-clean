<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Enums\Sidebar;
use App\Filament\Clusters\Settings;
use App\Models\GoogleSearchConsoleToken;
use App\Models\Setting;
use App\Services\GoogleSearchConsoleService;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Pages\SubNavigationPosition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class GoogleSearchConsole extends Page
{
    public ?array $data = [];

    protected static string $view = 'filament.clusters.settings.pages.google-search-console';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?int $navigationSort = 10;

    protected static ?string $cluster = Settings::class;

    public ?array $record = [];

    public $token = null;
    public $isConnected = false;
    public $hasCredentials = false;

    public static function getNavigationLabel(): string
    {
        return __('messages.gsc.google_search_console');
    }

    public function getTitle(): string
    {
        return __('messages.gsc.google_search_console');
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

        // Check if credentials are saved
        $clientId = Setting::where('key', 'gsc_client_id')->first();
        $clientSecret = Setting::where('key', 'gsc_client_secret')->first();
        $this->hasCredentials = $clientId && $clientSecret && !empty($clientId->value) && !empty($clientSecret->value);

        // Check if token exists
        $this->token = GoogleSearchConsoleToken::where('user_id', Auth::id())
            ->where('is_active', true)
            ->first();

        $this->isConnected = $this->token !== null;

        // Fill form with existing values
        $redirectUrl = Setting::where('key', 'gsc_redirect_url')->first();
        $decryptedSecret = '';
        if ($clientSecret && !empty($clientSecret->value)) {
            try {
                $decryptedSecret = Crypt::decryptString($clientSecret->value);
            } catch (\Exception $e) {
                // If decryption fails, leave empty
                $decryptedSecret = '';
            }
        }
        
        $this->form->fill([
            'client_id' => $clientId->value ?? '',
            'client_secret' => $decryptedSecret,
            'redirect_url' => $redirectUrl->value ?? url('/gsc/callback'),
            'property_url' => $this->token->property_url ?? '',
            'is_active' => $this->token->is_active ?? false,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('messages.gsc.google_credentials'))
                    ->description(__('messages.gsc.google_credentials_description'))
                    ->schema([
                        TextInput::make('client_id')
                            ->label(__('messages.gsc.client_id'))
                            ->required(fn () => !$this->hasCredentials)
                            ->helperText(__('messages.gsc.client_id_helper'))
                            ->placeholder('Enter your Google Client ID'),
                        TextInput::make('client_secret')
                            ->label(__('messages.gsc.client_secret'))
                            ->required(fn () => !$this->hasCredentials)
                            ->password()
                            ->helperText(__('messages.gsc.client_secret_helper'))
                            ->placeholder('Enter your Google Client Secret'),
                        TextInput::make('redirect_url')
                            ->label(__('messages.gsc.redirect_url'))
                            ->required()
                            ->disabled()
                            ->default(url('/gsc/callback'))
                            ->helperText(__('messages.gsc.redirect_url_helper')),
                    ])
                    ->visible(fn () => !$this->isConnected),

                Section::make(__('messages.gsc.connection_status'))
                    ->description($this->isConnected 
                        ? __('messages.gsc.connected_description')
                        : __('messages.gsc.connect_account_description'))
                    ->schema([
                        Toggle::make('is_connected')
                            ->label(__('messages.gsc.connection_status'))
                            ->disabled()
                            ->default($this->isConnected)
                            ->dehydrated(false),
                    ])
                    ->visible(fn () => $this->isConnected),

                Section::make(__('messages.gsc.property_settings_description'))
                    ->description(__('messages.gsc.property_settings_description'))
                    ->schema([
                        TextInput::make('property_url')
                            ->label(__('messages.gsc.property_url'))
                            ->placeholder('e.g., https://example.com or sc-domain:example.com')
                            ->helperText(__('messages.gsc.site_url_helper'))
                            ->required(fn () => $this->isConnected)
                            ->disabled(fn () => !$this->isConnected),
                    ])
                    ->visible(fn () => $this->isConnected),

                Section::make(__('messages.gsc.connect_google_search_console'))
                    ->description(__('messages.gsc.connect_button_description'))
                    ->schema([])
                    ->visible(fn () => !$this->isConnected),
            ])
            ->statePath('data');
    }

    public function connectGSC()
    {
        try {
            if (!$this->hasCredentials) {
                Notification::make()
                    ->warning()
                    ->title(__('messages.error'))
                    ->body(__('messages.gsc.credentials_required'))
                    ->send();
                return;
            }

            $clientId = Setting::where('key', 'gsc_client_id')->first();
            $clientSecret = Setting::where('key', 'gsc_client_secret')->first();
            $redirectUrl = Setting::where('key', 'gsc_redirect_url')->first();

            if (!$clientId || !$clientSecret) {
                Notification::make()
                    ->warning()
                    ->title(__('messages.error'))
                    ->body(__('messages.gsc.credentials_required'))
                    ->send();
                return;
            }

            $service = new GoogleSearchConsoleService();
            $service->setCredentials(
                $clientId->value,
                Crypt::decryptString($clientSecret->value),
                $redirectUrl->value ?? url('/gsc/callback')
            );

            $authUrl = $service->getAuthUrl();
            
            // Store state in session for verification
            session(['gsc_oauth_state' => 'pending', 'gsc_user_id' => Auth::id()]);

            return redirect($authUrl);
        } catch (\Exception $e) {
                Notification::make()
                    ->danger()
                    ->title(__('messages.error'))
                    ->body(__('messages.gsc.connection_failed') . ': ' . $e->getMessage())
                    ->send();

            Log::error('GSC Connect Error: ' . $e->getMessage());
        }
    }

    public function disconnectGSC()
    {
        try {
            if ($this->token) {
                $this->token->update(['is_active' => false]);
                
                Notification::make()
                    ->success()
                    ->title(__('messages.gsc.disconnect'))
                    ->body(__('messages.gsc.disconnected_successfully'))
                    ->send();

                $this->mount(); // Refresh page state
            }
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Failed to disconnect: ' . $e->getMessage())
                ->send();

            Log::error('GSC Disconnect Error: ' . $e->getMessage());
        }
    }

    public function saveCredentials(): void
    {
        try {
            $data = $this->form->getState();

            if (empty($data['client_id']) || empty($data['client_secret'])) {
                Notification::make()
                    ->warning()
                    ->title(__('messages.error'))
                    ->body(__('messages.gsc.credentials_required'))
                    ->send();
                return;
            }

            // Save client ID
            Setting::updateOrCreate(
                ['key' => 'gsc_client_id'],
                ['value' => $data['client_id']]
            );

            // Save client secret (encrypted)
            Setting::updateOrCreate(
                ['key' => 'gsc_client_secret'],
                ['value' => Crypt::encryptString($data['client_secret'])]
            );

            // Save redirect URL
            Setting::updateOrCreate(
                ['key' => 'gsc_redirect_url'],
                ['value' => $data['redirect_url'] ?? url('/gsc/callback')]
            );

            $this->hasCredentials = true;

            Notification::make()
                ->success()
                ->title(__('messages.common.save'))
                ->body(__('messages.gsc.credentials_saved'))
                ->send();

            $this->mount(); // Refresh page state
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title(__('messages.error'))
                ->body('Failed to save credentials: ' . $e->getMessage())
                ->send();

            Log::error('GSC Credentials Save Error: ' . $e->getMessage());
        }
    }

    public function save(): void
    {
        try {
            if (!$this->token) {
                Notification::make()
                    ->warning()
                    ->title(__('messages.gsc.not_connected'))
                    ->body(__('messages.gsc.please_connect_first'))
                    ->send();
                return;
            }

            $data = $this->form->getState();
            
            $this->token->update([
                'property_url' => $data['property_url'] ?? $this->token->property_url,
            ]);

                Notification::make()
                    ->success()
                    ->title(__('messages.common.save'))
                    ->body(__('messages.gsc.settings_updated'))
                    ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Failed to update settings: ' . $e->getMessage())
                ->send();

            Log::error('GSC Save Error: ' . $e->getMessage());
        }
    }
}

