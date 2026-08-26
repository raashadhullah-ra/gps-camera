<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FirebaseSetting extends Model
{
    protected $fillable = [
        'label',
        'project_id',
        'api_key',
        'auth_domain',
        'storage_bucket',
        'messaging_sender_id',
        'app_id',
        'measurement_id',
        'vapid_key',
        'service_account_json',
        'is_active',
        'connection_status',
        'last_error_message',
        'last_tested_at',
    ];

    protected $casts = [
        'is_active'            => 'boolean',
        'service_account_json' => 'encrypted',
        'last_tested_at'       => 'datetime',
    ];

    /**
     * Get the active Firebase configuration
     */
    public static function getActive(): ?self
    {
        return static::where('is_active', true)->first();
    }

    /**
     * Mark this setting as active and deactivate all others safely within a DB transaction.
     */
    public function activate(): void
    {
        \Illuminate\Support\Facades\DB::transaction(function () {
            static::where('id', '!=', $this->id)->update(['is_active' => false]);
            $this->update(['is_active' => true]);
        });
    }

    /**
     * Fetch a genuine Google OAuth2 Access Token using the Service Account Private Key.
     */
    public function fetchGoogleAccessToken(): string
    {
        if (empty($this->service_account_json)) {
            throw new \Exception("Service Account credentials are required for Google Cloud authentication.");
        }

        $serviceAccount = json_decode($this->service_account_json, true);
        if (!$serviceAccount || !is_array($serviceAccount)) {
            throw new \Exception("Invalid JSON format in Service Account credentials.");
        }

        if (empty($serviceAccount['client_email']) || empty($serviceAccount['private_key'])) {
            throw new \Exception("Service account JSON is missing required fields (client_email, private_key).");
        }

        $header = rtrim(strtr(base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT'])), '+/', '-_'), '=');
        $now = time();
        $claims = [
            'iss'   => $serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging https://www.googleapis.com/auth/cloud-platform',
            'aud'   => 'https://oauth2.googleapis.com/token',
            'exp'   => $now + 3600,
            'iat'   => $now,
        ];
        $payload = rtrim(strtr(base64_encode(json_encode($claims)), '+/', '-_'), '=');

        $binarySignature = '';
        $signSuccess = openssl_sign(
            $header . '.' . $payload,
            $binarySignature,
            $serviceAccount['private_key'],
            OPENSSL_ALGO_SHA256
        );

        if (!$signSuccess) {
            throw new \Exception("Failed to sign Google OAuth2 JWT assertion. Please verify the private key formatting.");
        }

        $jwt = $header . '.' . $payload . '.' . rtrim(strtr(base64_encode($binarySignature), '+/', '-_'), '=');

        $response = \Illuminate\Support\Facades\Http::asForm()
            ->timeout(10)
            ->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]);

        if (!$response->successful()) {
            $errorBody = $response->json();
            $errorMsg = $errorBody['error_description'] ?? $errorBody['error'] ?? $response->body();
            throw new \Exception("Google OAuth2 authentication rejected: " . $errorMsg);
        }

        $tokenData = $response->json();
        return $tokenData['access_token'] ?? throw new \Exception("Google OAuth2 response did not return an access token.");
    }

    /**
     * Test credentials against live Google Cloud & Firebase APIs.
     */
    public function testConnection(): array
    {
        try {
            if (!empty($this->service_account_json)) {
                $token = $this->fetchGoogleAccessToken();
                $json = json_decode($this->service_account_json, true);
                $projectId = $json['project_id'] ?? $this->project_id ?? 'Google Cloud Project';

                $this->update([
                    'connection_status'  => 'connected',
                    'last_error_message' => null,
                    'last_tested_at'     => now(),
                ]);

                return [
                    'success' => true,
                    'message' => "Successfully authenticated with Google Cloud OAuth2 for Firebase Project '{$projectId}'.",
                ];
            } elseif (!empty($this->project_id) && !empty($this->api_key)) {
                // Verify Web API key against Google Identity Toolkit API probe
                $response = \Illuminate\Support\Facades\Http::timeout(10)
                    ->post("https://identitytoolkit.googleapis.com/v1/accounts:lookup?key={$this->api_key}", [
                        'idToken' => 'health_probe_token',
                    ]);

                $resJson = $response->json();
                if (isset($resJson['error']['message']) && str_contains($resJson['error']['message'], 'API key not valid')) {
                    throw new \Exception("Google rejected Web API Key: " . $resJson['error']['message']);
                }

                $this->update([
                    'connection_status'  => 'connected',
                    'last_error_message' => null,
                    'last_tested_at'     => now(),
                ]);

                return [
                    'success' => true,
                    'message' => "Successfully validated Firebase Web API Key for project '{$this->project_id}'.",
                ];
            } else {
                throw new \Exception("Please provide either a Service Account JSON or Project ID with Web API Key.");
            }
        } catch (\Throwable $e) {
            $this->update([
                'connection_status'  => 'failed',
                'last_error_message' => $e->getMessage(),
                'last_tested_at'     => now(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
