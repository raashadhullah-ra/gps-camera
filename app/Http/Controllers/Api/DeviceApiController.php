<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeviceApiController extends Controller
{
    /**
     * Register or sync device information when Flutter app opens.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'installation_id'          => 'required|string|max:64',
            'firebase_installation_id' => 'nullable|string|max:64',
            'hardware_id'              => 'nullable|string|max:128',
            'fcm_token'                => 'nullable|string',
            'device_manufacturer'      => 'nullable|string|max:50',
            'device_brand'             => 'nullable|string|max:50',
            'device_model'             => 'nullable|string|max:100',
            'device_code'              => 'nullable|string|max:50',
            'cpu_architecture'         => 'nullable|string|max:30',
            'platform'                 => 'nullable|string|in:Android,iOS,android,ios',
            'os_version'               => 'nullable|string|max:30',
            'sdk_version'              => 'nullable|integer',
            'app_version'              => 'nullable|string|max:20',
            'app_build_number'         => 'nullable|integer',
            'screen_resolution'        => 'nullable|string|max:30',
            'language'                 => 'nullable|string|max:30',
            'timezone'                 => 'nullable|string|max:50',
            'permissions'              => 'nullable|array',
            'permissions_status'       => 'nullable|string|max:100',
            'notification_status'      => 'nullable|string|max:30',
            'latitude'                 => 'nullable|numeric|between:-90,90',
            'longitude'                => 'nullable|numeric|between:-180,180',
            'city'                     => 'nullable|string|max:100',
            'state'                    => 'nullable|string|max:100',
            'country'                  => 'nullable|string|max:100',
            'country_code'             => 'nullable|string|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Normalize platform string (e.g. 'android' -> 'Android')
        if (!empty($data['platform'])) {
            $data['platform'] = ucfirst(strtolower($data['platform']));
        }

        // Auto-generate readable permission summary if not passed explicitly
        if (empty($data['permissions_status']) && !empty($data['permissions'])) {
            $perms = $data['permissions'];
            $granted = [];
            if (($perms['camera'] ?? '') === 'granted') $granted[] = 'Camera';
            if (in_array($perms['location'] ?? '', ['granted', 'precise', 'approximate', 'approx'])) {
                $granted[] = ($perms['location'] ?? '') === 'precise' ? 'Precise Location' : 'Location';
            }
            if (($perms['notifications'] ?? '') === 'granted') $granted[] = 'Notifications';
            if (($perms['photos_media'] ?? '') === 'granted') $granted[] = 'Photos';

            $data['permissions_status'] = count($granted) > 0 ? implode(' + ', $granted) . ' granted' : 'All permissions denied';
        }

        // Detect client IP
        $data['ip_address'] = $request->ip();

        // Geolocation resolution
        $lat = $data['latitude'] ?? null;
        $lng = $data['longitude'] ?? null;

        // If GPS coordinates are provided but address fields are missing, reverse geocode
        if ($lat !== null && $lng !== null && empty($data['city'])) {
            $geo = $this->resolveReverseGeocode((float) $lat, (float) $lng);
            if (!empty($geo['city'])) $data['city'] = $geo['city'];
            if (!empty($geo['state']) && empty($data['state'])) $data['state'] = $geo['state'];
            if (!empty($geo['country']) && empty($data['country'])) $data['country'] = $geo['country'];
            if (!empty($geo['country_code']) && empty($data['country_code'])) $data['country_code'] = $geo['country_code'];
        } elseif (($lat === null || $lng === null) && empty($data['city']) && !empty($data['ip_address'])) {
            // Fallback: try resolving public IP location
            $ipGeo = $this->resolveIpLocation($data['ip_address']);
            if (!empty($ipGeo)) {
                if (empty($data['city']) && !empty($ipGeo['city'])) $data['city'] = $ipGeo['city'];
                if (empty($data['state']) && !empty($ipGeo['state'])) $data['state'] = $ipGeo['state'];
                if (empty($data['country']) && !empty($ipGeo['country'])) $data['country'] = $ipGeo['country'];
                if (empty($data['country_code']) && !empty($ipGeo['country_code'])) $data['country_code'] = $ipGeo['country_code'];
                if (empty($data['latitude']) && !empty($ipGeo['latitude'])) $data['latitude'] = $ipGeo['latitude'];
                if (empty($data['longitude']) && !empty($ipGeo['longitude'])) $data['longitude'] = $ipGeo['longitude'];
                if (empty($data['timezone']) && !empty($ipGeo['timezone'])) $data['timezone'] = $ipGeo['timezone'];
            }
        }

        // Find or instantiate device by unique installation_id
        $device = Device::where('installation_id', $data['installation_id'])->first();

        $isNew = false;
        if (!$device) {
            $isNew = true;
            $device = new Device();
            $device->installation_id = $data['installation_id'];
            $device->first_installed_at = now();
            $device->total_sessions_count = 1;
            $device->is_active = true;
        } else {
            $device->total_sessions_count += 1;
        }

        // Track FCM token update timestamp
        if (!empty($data['fcm_token']) && $device->fcm_token !== $data['fcm_token']) {
            $device->fcm_token_updated_at = now();
        }

        $device->last_active_at = now();

        // Fill all provided properties
        $device->fill($data);
        $device->save();

        // Automatically sync Location metrics for this device's city
        if (!empty($device->city)) {
            try {
                app(\App\Services\LocationService::class)->syncLocationForDevice($device);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Location sync error on device register: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => $isNew ? 'Device registered successfully.' : 'Device session synced successfully.',
            'data'    => [
                'id'                       => $device->id,
                'installation_id'          => $device->installation_id,
                'is_active'                => $device->is_active,
                'platform'                 => $device->platform,
                'app_version'              => $device->app_version,
                'city'                     => $device->city,
                'state'                    => $device->state,
                'country'                  => $device->country,
                'latitude'                 => $device->latitude,
                'longitude'                => $device->longitude,
                'total_sessions_count'     => $device->total_sessions_count,
                'last_active_at'           => $device->last_active_at?->toISOString(),
            ],
        ], $isNew ? 201 : 200);
    }

    /**
     * Resolve reverse geocoding via OpenStreetMap Nominatim with fast timeout.
     */
    protected function resolveReverseGeocode(float $lat, float $lng): array
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(3)
                ->withHeaders(['User-Agent' => 'GeoCam-Admin-App/1.0'])
                ->get('https://nominatim.openstreetmap.org/reverse', [
                    'format' => 'json',
                    'lat'    => $lat,
                    'lon'    => $lng,
                    'zoom'   => 10,
                    'addressdetails' => 1,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $address = $data['address'] ?? [];

                $city = $address['city'] ?? $address['town'] ?? $address['village'] ?? $address['municipality'] ?? $address['county'] ?? $address['state_district'] ?? null;
                $state = $address['state'] ?? $address['region'] ?? null;
                $country = $address['country'] ?? null;
                $countryCode = isset($address['country_code']) ? strtoupper($address['country_code']) : null;

                return array_filter([
                    'city'         => $city,
                    'state'        => $state,
                    'country'      => $country,
                    'country_code' => $countryCode,
                ]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::debug('Reverse geocode error: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Resolve IP Geolocation fallback for public IPs.
     */
    protected function resolveIpLocation(string $ip): array
    {
        if (in_array($ip, ['127.0.0.1', '::1']) || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return [];
        }

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(3)
                ->get("http://ip-api.com/json/{$ip}?fields=status,country,countryCode,regionName,city,lat,lon,timezone");

            if ($response->successful() && ($response->json('status') === 'success')) {
                $res = $response->json();
                return [
                    'city'         => $res['city'] ?? null,
                    'state'        => $res['regionName'] ?? null,
                    'country'      => $res['country'] ?? null,
                    'country_code' => $res['countryCode'] ?? null,
                    'latitude'     => $res['lat'] ?? null,
                    'longitude'    => $res['lon'] ?? null,
                    'timezone'     => $res['timezone'] ?? null,
                ];
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::debug('IP Geo error: ' . $e->getMessage());
        }

        return [];
    }
}
