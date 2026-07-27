<?php

namespace App\Services;

use App\Models\DeviceToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    /**
     * Send push notification to a single user.
     */
    public static function sendToUser(int $userId, string $title, string $body, array $data = []): void
    {
        $tokens = DeviceToken::where('user_id', $userId)->pluck('token')->toArray();

        Log::channel('fcm')->info("FCM: Sending to user {$userId} | Title: {$title} | Body: {$body} | Tokens: " . count($tokens));

        if (empty($tokens)) {
            Log::channel('fcm')->warning("FCM: No device tokens found for user {$userId}");
            return;
        }

        self::sendToTokens($tokens, $title, $body, $data);
    }

    /**
     * Send push notification to multiple users.
     */
    public static function sendToUsers(array $userIds, string $title, string $body, array $data = []): void
    {
        $tokens = DeviceToken::whereIn('user_id', $userIds)->pluck('token')->toArray();

        if (empty($tokens)) return;

        self::sendToTokens($tokens, $title, $body, $data);
    }

    /**
     * Send FCM notification to given device tokens.
     */
    private static function sendToTokens(array $tokens, string $title, string $body, array $data = []): void
    {
        $accessToken = self::getAccessToken();

        if (!$accessToken) {
            Log::channel('fcm')->error('FCM: Failed to get access token');
            return;
        }

        $projectId = config('services.fcm.project_id');
        Log::channel('fcm')->info("FCM: Using project ID: {$projectId} | Sending to " . count($tokens) . " token(s)");

        foreach ($tokens as $token) {
            try {
                Log::channel('fcm')->info("FCM: Sending to token: " . substr($token, 0, 20) . "...");

                $response = Http::withToken($accessToken)
                    ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
                        'message' => [
                            'token' => $token,
                            'notification' => [
                                'title' => $title,
                                'body' => $body,
                            ],
                            'data' => array_map('strval', $data),
                            'android' => [
                                'priority' => 'high',
                                'notification' => [
                                    'sound' => 'default',
                                    'channel_id' => 'crm_notifications',
                                ],
                            ],
                            'apns' => [
                                'payload' => [
                                    'aps' => [
                                        'sound' => 'default',
                                        'badge' => 1,
                                    ],
                                ],
                            ],
                        ],
                    ]);

                if ($response->successful()) {
                    Log::channel('fcm')->info("FCM: SUCCESS | Token: " . substr($token, 0, 20) . "... | Response: " . $response->body());
                } else {
                    $error = $response->json('error.details.0.errorCode', $response->body());

                    // Remove invalid/expired tokens
                    if (in_array($error, ['UNREGISTERED', 'INVALID_ARGUMENT'])) {
                        DeviceToken::where('token', $token)->delete();
                        Log::channel('fcm')->warning("FCM: Removed invalid token: " . substr($token, 0, 20) . "... | Error: {$error}");
                    } else {
                        Log::channel('fcm')->error("FCM: FAILED | Token: " . substr($token, 0, 20) . "... | Error: {$error} | Full: " . $response->body());
                    }
                }
            } catch (\Exception $e) {
                Log::channel('fcm')->error("FCM: EXCEPTION | " . $e->getMessage());
            }
        }
    }

    /**
     * Get OAuth2 access token from Firebase service account.
     */
    private static function getAccessToken(): ?string
    {
        $credentialsPath = config('services.fcm.credentials');

        if (!$credentialsPath || !file_exists($credentialsPath)) {
            Log::channel('fcm')->error('FCM: Service account credentials file not found at: ' . ($credentialsPath ?? 'null'));
            return null;
        }

        try {
            $credentials = json_decode(file_get_contents($credentialsPath), true);

            $now = time();
            $header = base64url_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $payload = base64url_encode(json_encode([
                'iss' => $credentials['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $now + 3600,
            ]));

            $signature = '';
            openssl_sign("{$header}.{$payload}", $signature, $credentials['private_key'], OPENSSL_ALGO_SHA256);
            $jwt = "{$header}.{$payload}." . base64url_encode($signature);

            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            $accessToken = $response->json('access_token');
            Log::channel('fcm')->info("FCM: Access token obtained: " . ($accessToken ? 'YES' : 'NO'));
            return $accessToken;
        } catch (\Exception $e) {
            Log::channel('fcm')->error("FCM: Failed to get access token: {$e->getMessage()}");
            return null;
        }
    }
}

/**
 * Base64 URL-safe encode.
 */
if (!function_exists('base64url_encode')) {
    function base64url_encode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
