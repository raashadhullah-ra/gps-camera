<?php

namespace App\Services;

use App\Models\AppVersion;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AppVersionService
{
    /**
     * Get current active app version settings.
     *
     * @return AppVersion
     */
    public function getSettings(): AppVersion
    {
        return AppVersion::getSettings();
    }

    /**
     * Update app version settings.
     *
     * @param array $data
     * @return AppVersion
     * @throws ValidationException
     */
    public function updateSettings(array $data): AppVersion
    {
        $validator = Validator::make($data, [
            'app_name'             => 'required|string|max:100',
            'platform'             => 'required|string|in:all,android,ios',
            'current_version'      => 'required|string|max:20|regex:/^\d+(\.\d+)*(\+[a-zA-Z0-9]+)?$/',
            'minimum_version'      => 'required|string|max:20|regex:/^\d+(\.\d+)*(\+[a-zA-Z0-9]+)?$/',
            'force_update'         => 'nullable|boolean',
            'update_title'         => 'nullable|string|max:150',
            'update_message'       => 'nullable|string|max:1000',
            'play_store_url'       => 'nullable|url|max:255',
            'app_store_url'        => 'nullable|url|max:255',
            'is_under_maintenance' => 'nullable|boolean',
            'maintenance_message'  => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $settings = $this->getSettings();
        $validated = $validator->validated();
        $validated['force_update'] = (bool) ($data['force_update'] ?? false);
        if (array_key_exists('is_under_maintenance', $data)) {
            $validated['is_under_maintenance'] = (bool) $data['is_under_maintenance'];
        }

        $settings->update($validated);

        return $settings;
    }

    /**
     * Evaluate app version for mobile client (Flutter).
     *
     * @param string|null $clientVersion e.g. "1.2.0"
     * @param string $platform e.g. "android" or "ios"
     * @return array
     */
    public function evaluateAppVersion(?string $clientVersion = null, string $platform = 'android'): array
    {
        $settings = $this->getSettings();
        $platform = strtolower($platform);

        $storeUrl = ($platform === 'ios') 
            ? ($settings->app_store_url ?? $settings->play_store_url)
            : ($settings->play_store_url ?? $settings->app_store_url);

        // Basic payload if no client version provided
        if (empty($clientVersion)) {
            return [
                'app_name'             => $settings->app_name,
                'platform'             => $platform,
                'current_version'      => $settings->current_version,
                'minimum_version'      => $settings->minimum_version,
                'force_update_enabled' => (bool) $settings->force_update,
                'is_under_maintenance' => (bool) $settings->is_under_maintenance,
                'maintenance_message'  => $settings->maintenance_message,
                'update_title'         => $settings->update_title,
                'update_message'       => $settings->update_message,
                'store_url'            => $storeUrl,
                'play_store_url'       => $settings->play_store_url,
                'app_store_url'        => $settings->app_store_url,
            ];
        }

        // Clean client version (e.g., strip 'v' prefix)
        $cleanClientVersion = ltrim(trim($clientVersion), 'vV');

        // Semantic Version Comparison
        $isBelowMinimum = version_compare($cleanClientVersion, $settings->minimum_version, '<');
        $isBelowCurrent = version_compare($cleanClientVersion, $settings->current_version, '<');

        // Force update is required if either:
        // 1. Client is strictly below minimum required version, OR
        // 2. Client is below current version AND force_update toggle is ON.
        $forceUpdateRequired = $isBelowMinimum || ($settings->force_update && $isBelowCurrent);
        $updateAvailable = $isBelowCurrent;

        return [
            'app_name'             => $settings->app_name,
            'client_version'       => $cleanClientVersion,
            'current_version'      => $settings->current_version,
            'minimum_version'      => $settings->minimum_version,
            'is_update_available'  => $updateAvailable,
            'force_update'         => $forceUpdateRequired,
            'is_under_maintenance' => (bool) $settings->is_under_maintenance,
            'maintenance_message'  => $settings->maintenance_message,
            'update_title'         => $settings->update_title,
            'update_message'       => $settings->update_message,
            'store_url'            => $storeUrl,
            'play_store_url'       => $settings->play_store_url,
            'app_store_url'        => $settings->app_store_url,
        ];
    }
}
