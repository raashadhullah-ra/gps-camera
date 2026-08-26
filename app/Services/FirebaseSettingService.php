<?php

namespace App\Services;

use App\Models\FirebaseSetting;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use InvalidArgumentException;

class FirebaseSettingService
{
    /**
     * Retrieve all Firebase project settings ordered by active first, then newest.
     *
     * @return Collection<int, FirebaseSetting>
     */
    public function getAllSettings(): Collection
    {
        return FirebaseSetting::orderByDesc('is_active')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Retrieve the currently active Firebase setting.
     */
    public function getActiveSetting(): ?FirebaseSetting
    {
        return FirebaseSetting::getActive();
    }

    /**
     * Process, validate, and store a new Firebase project configuration.
     *
     * @param  array<string, mixed>  $data
     * @param  UploadedFile|null     $file
     * @param  string|null           $rawJson
     * @param  bool                  $makeActive
     * @return array{setting: FirebaseSetting, test_result: array}
     */
    public function storeSetting(array $data, ?UploadedFile $file = null, ?string $rawJson = null, bool $makeActive = false): array
    {
        $existing = FirebaseSetting::first();
        if ($existing) {
            return $this->updateSetting($existing, $data, $file, $rawJson, true);
        }

        $serviceAccountJson = null;

        // 1. Process uploaded Service Account JSON file
        if ($file instanceof UploadedFile) {
            $rawContent = file_get_contents($file->getRealPath());
            $decoded = json_decode($rawContent, true);

            if (!is_array($decoded)) {
                throw new InvalidArgumentException('The uploaded Service Account file is not a valid JSON document.');
            }

            $serviceAccountJson = $rawContent;

            // Auto-fill project_id if not explicitly typed
            if (empty($data['project_id']) && !empty($decoded['project_id'])) {
                $data['project_id'] = $decoded['project_id'];
            }
        } elseif (!empty($rawJson)) {
            $rawContent = trim($rawJson);
            $decoded = json_decode($rawContent, true);

            if (!is_array($decoded)) {
                throw new InvalidArgumentException('The pasted Service Account JSON is invalid.');
            }

            $serviceAccountJson = $rawContent;

            if (empty($data['project_id']) && !empty($decoded['project_id'])) {
                $data['project_id'] = $decoded['project_id'];
            }
        }

        $label = !empty($data['label']) 
            ? $data['label'] 
            : (!empty($data['project_id']) ? 'Project - ' . $data['project_id'] : 'Firebase Config #' . (FirebaseSetting::count() + 1));

        $shouldActivate = $makeActive || FirebaseSetting::count() === 0;

        $setting = FirebaseSetting::create([
            'label'                => $label,
            'project_id'           => $data['project_id'] ?? null,
            'api_key'              => $data['api_key'] ?? null,
            'auth_domain'          => $data['auth_domain'] ?? null,
            'storage_bucket'       => $data['storage_bucket'] ?? null,
            'messaging_sender_id'  => $data['messaging_sender_id'] ?? null,
            'app_id'               => $data['app_id'] ?? null,
            'measurement_id'       => $data['measurement_id'] ?? null,
            'vapid_key'            => $data['vapid_key'] ?? null,
            'service_account_json' => $serviceAccountJson,
            'is_active'            => false,
            'connection_status'    => 'untested',
        ]);

        if ($shouldActivate) {
            $setting->activate();
        }

        $testResult = $setting->testConnection();

        return [
            'setting'     => $setting,
            'test_result' => $testResult,
        ];
    }

    /**
     * Update an existing Firebase project configuration.
     *
     * @param  FirebaseSetting       $setting
     * @param  array<string, mixed>  $data
     * @param  UploadedFile|null     $file
     * @param  string|null           $rawJson
     * @param  bool                  $makeActive
     * @return array{setting: FirebaseSetting, test_result: array}
     */
    public function updateSetting(FirebaseSetting $setting, array $data, ?UploadedFile $file = null, ?string $rawJson = null, bool $makeActive = false): array
    {
        $updatePayload = [
            'label'               => $data['label'] ?? $setting->label,
            'project_id'          => $data['project_id'] ?? $setting->project_id,
            'api_key'             => $data['api_key'] ?? $setting->api_key,
            'auth_domain'         => $data['auth_domain'] ?? $setting->auth_domain,
            'storage_bucket'      => $data['storage_bucket'] ?? $setting->storage_bucket,
            'messaging_sender_id' => $data['messaging_sender_id'] ?? $setting->messaging_sender_id,
            'app_id'              => $data['app_id'] ?? $setting->app_id,
            'measurement_id'      => $data['measurement_id'] ?? $setting->measurement_id,
            'vapid_key'           => $data['vapid_key'] ?? $setting->vapid_key,
        ];

        // Replace service account JSON if uploaded
        if ($file instanceof UploadedFile) {
            $rawContent = file_get_contents($file->getRealPath());
            $decoded = json_decode($rawContent, true);
            if (!is_array($decoded)) {
                throw new InvalidArgumentException('The uploaded Service Account file is not a valid JSON document.');
            }
            $updatePayload['service_account_json'] = $rawContent;
        } elseif (!empty($rawJson)) {
            $rawContent = trim($rawJson);
            $decoded = json_decode($rawContent, true);
            if (!is_array($decoded)) {
                throw new InvalidArgumentException('The pasted Service Account JSON is invalid.');
            }
            $updatePayload['service_account_json'] = $rawContent;
        }

        $setting->update($updatePayload);

        if ($makeActive) {
            $setting->activate();
        }

        $testResult = $setting->testConnection();

        return [
            'setting'     => $setting,
            'test_result' => $testResult,
        ];
    }

    /**
     * Switch the active live Firebase project.
     */
    public function activateSetting(FirebaseSetting $setting): void
    {
        $setting->activate();
    }

    /**
     * Test connection for a given Firebase configuration.
     */
    public function testConnection(FirebaseSetting $setting): array
    {
        return $setting->testConnection();
    }

    /**
     * Delete a Firebase configuration and reassign active setting if needed.
     */
    public function deleteSetting(FirebaseSetting $setting): string
    {
        $wasActive = $setting->is_active;
        $label = $setting->label;

        $setting->delete();

        if ($wasActive) {
            $next = FirebaseSetting::latest()->first();
            if ($next) {
                $next->activate();
            }
        }

        return $label;
    }

    /**
     * Send an FCM v1 Push Notification using the active Firebase project credentials.
     *
     * @param  string  $deviceToken
     * @param  string  $title
     * @param  string  $body
     * @param  array<string, string>  $data
     * @return array<string, mixed>
     */
    public function sendPushNotification(string $deviceToken, string $title, string $body, array $data = []): array
    {
        $setting = $this->getActiveSetting();
        if (!$setting) {
            throw new \Exception("No active Firebase project configuration found.");
        }

        $accessToken = $setting->fetchGoogleAccessToken();
        $projectId = $setting->project_id;
        if (empty($projectId) && !empty($setting->service_account_json)) {
            $decoded = json_decode($setting->service_account_json, true);
            $projectId = $decoded['project_id'] ?? null;
        }

        if (empty($projectId)) {
            throw new \Exception("Firebase Project ID is required for sending push notifications.");
        }

        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $message = [
            'token' => $deviceToken,
            'notification' => [
                'title' => $title,
                'body'  => $body,
            ],
        ];

        if (!empty($data)) {
            $message['data'] = array_map('strval', $data);
        }

        $payload = [
            'message' => $message,
        ];

        $response = \Illuminate\Support\Facades\Http::withToken($accessToken)
            ->timeout(10)
            ->post($url, $payload);

        if (!$response->successful()) {
            $errorBody = $response->json();
            $msg = $errorBody['error']['message'] ?? $response->body();
            if ($msg === 'NotRegistered') {
                $msg = 'Token is NotRegistered / Expired on Firebase (The mobile app was uninstalled/reinstalled or generated a newer token).';
            } elseif (str_contains($msg, 'SenderId mismatch') || str_contains($msg, 'SENDER_ID_MISMATCH')) {
                $msg = 'Firebase SenderId mismatch (The Flutter app is using a google-services.json from a different Firebase project).';
            }
            throw new \Exception($msg);
        }

        return $response->json();
    }
}
