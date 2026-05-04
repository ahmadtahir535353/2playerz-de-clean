<?php

namespace App\Http\Controllers;

use App\Models\GoogleSearchConsoleToken;
use App\Services\GoogleSearchConsoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class GoogleSearchConsoleController extends Controller
{
    public function handleCallback(Request $request)
    {
        try {
            Log::info('GSC Callback Hit', [
                'all_params' => $request->all(),
                'code' => $request->get('code'),
                'error' => $request->get('error'),
                'session_state' => session('gsc_oauth_state'),
                'session_user_id' => session('gsc_user_id'),
                'auth_user_id' => Auth::id(),
            ]);

            $code = $request->get('code');
            $error = $request->get('error');

            if ($error) {
                Log::error('GSC OAuth Error: ' . $error);
                return redirect()->route('filament.admin.settings.pages.google-search-console')
                    ->with('error', 'Google OAuth error: ' . $error);
            }

            if (!$code) {
                Log::warning('GSC Callback: No authorization code received');
                return redirect()->route('filament.admin.settings.pages.google-search-console')
                    ->with('error', 'No authorization code received.');
            }

            // Get user ID - try session first, then auth
            $userId = session('gsc_user_id');
            if (!$userId && Auth::check()) {
                $userId = Auth::id();
            }
            
            if (!$userId) {
                Log::error('GSC Callback: No user ID found');
                return redirect()->route('filament.admin.settings.pages.google-search-console')
                    ->with('error', 'User not authenticated. Please try again.');
            }

            Log::info('GSC Callback: Processing for user', ['user_id' => $userId]);

            // Get credentials from settings
            $clientId = \App\Models\Setting::where('key', 'gsc_client_id')->first();
            $clientSecret = \App\Models\Setting::where('key', 'gsc_client_secret')->first();
            $redirectUrl = \App\Models\Setting::where('key', 'gsc_redirect_url')->first();

            if (!$clientId || !$clientSecret) {
                Notification::make()
                    ->danger()
                    ->title('Connection Failed')
                    ->body('Google credentials not configured. Please save credentials first.')
                    ->send();
                return redirect()->route('filament.admin.settings.pages.google-search-console');
            }

            $service = new GoogleSearchConsoleService();
            $service->setCredentials(
                $clientId->value,
                \Illuminate\Support\Facades\Crypt::decryptString($clientSecret->value),
                $redirectUrl->value ?? url('/gsc/callback')
            );

            // Exchange code for tokens
            Log::info('GSC Callback: Exchanging code for tokens');
            $tokens = $service->exchangeCodeForTokens($code);
            Log::info('GSC Callback: Tokens received', ['has_access_token' => !empty($tokens['access_token']), 'has_refresh_token' => !empty($tokens['refresh_token'])]);

            // Get sites to determine property URL
            Log::info('GSC Callback: Getting sites');
            $tempToken = new GoogleSearchConsoleToken([
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'],
                'expires_at' => $tokens['expires_at'],
            ]);
            
            $sites = $service->getSites($tempToken);
            Log::info('GSC Callback: Sites retrieved', ['sites_count' => count($sites)]);

            // Use first site or let user select
            $propertyUrl = !empty($sites) ? $sites[0]['siteUrl'] : null;
            Log::info('GSC Callback: Property URL', ['property_url' => $propertyUrl]);

            // Deactivate existing tokens for this user
            GoogleSearchConsoleToken::where('user_id', $userId)
                ->update(['is_active' => false]);

            // Create new token
            Log::info('GSC Callback: Creating token in database');
            $token = GoogleSearchConsoleToken::create([
                'user_id' => $userId,
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'],
                'expires_at' => $tokens['expires_at'],
                'property_url' => $propertyUrl,
                'is_active' => true,
            ]);

            Log::info('GSC Token created successfully', [
                'token_id' => $token->id, 
                'user_id' => $userId,
                'property_url' => $propertyUrl
            ]);

            // Clear session
            session()->forget(['gsc_oauth_state', 'gsc_user_id']);

            return redirect()->route('filament.admin.settings.pages.google-search-console')
                ->with('success', 'Google Search Console connected successfully!');
        } catch (\Exception $e) {
            Log::error('GSC Callback Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('filament.admin.settings.pages.google-search-console')
                ->with('error', 'Failed to connect: ' . $e->getMessage());
        }
    }
}

