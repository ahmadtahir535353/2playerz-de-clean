<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;

class FcmService
{
    public function __construct(
        private ?string $projectId   = null,
        private ?string $clientEmail = null,
        private ?string $privateKey  = null,
    ) {
        $this->projectId   = $projectId   ?? config('services.fcm.project_id');
        $this->clientEmail = $clientEmail ?? config('services.fcm.client_email');
        // Convert \n to real newlines for PEM
        $this->privateKey  = $privateKey  ?? str_replace('\\n', "\n", config('services.fcm.private_key'));
    }

    protected function accessToken(): string
    {
        $creds = new ServiceAccountCredentials(
            ['https://www.googleapis.com/auth/firebase.messaging'],
            [
                'client_email' => $this->clientEmail,
                'private_key'  => $this->privateKey,
            ]
        );
        $creds->fetchAuthToken();
        return $creds->getLastReceivedToken()['access_token'] ?? '';
    }

    public function sendToToken(string $token, string $title, string $body, array $data = []): bool
    {
        $client = new Client(['base_uri' => 'https://fcm.googleapis.com/']);

        $payload = [
            'message' => [
                'token' => $token,
                'notification' => ['title' => $title, 'body' => $body],
                'data' => array_map('strval', $data), // data strings only
                'webpush' => [
                    'fcm_options' => ['link' => $data['link'] ?? url('/')],
                    'headers' => ['TTL' => '300'],
                    'notification' => [
                        'icon'  => url('/icon-192x192.png'),
                        'badge' => url('/badge-72x72.png'),
                        'vibrate' => [100, 50, 100],
                    ],
                ],
            ],
        ];

        $resp = $client->post("v1/projects/{$this->projectId}/messages:send", [
            'headers' => [
                'Authorization' => 'Bearer '.$this->accessToken(),
                'Content-Type'  => 'application/json',
            ],
            'json' => $payload,
            'timeout' => 15,
        ]);

        return $resp->getStatusCode() === 200;
    }
}
