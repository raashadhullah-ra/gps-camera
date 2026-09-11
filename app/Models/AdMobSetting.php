<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdMobSetting extends Model
{
    use HasFactory;

    protected $table = 'admob_settings';

    protected $fillable = [
        'publisher_id',
        'contact_email',
        'reporting_currency',
        'android_enabled',
        'android_package_name',
        'android_app_id',
        'ios_enabled',
        'ios_bundle_id',
        'ios_app_id',
        'enable_reporting',
        'sync_frequency',
        'default_report_range',
        'connection_status',
        'last_synced_at',
        'last_sync_message',
        'enable_test_ads',
        'test_device_ids',
        'google_client_id',
        'google_client_secret',
        'is_connected',
        'connected_email',
        'access_token',
        'refresh_token',
        'token_expires_at',
    ];

    protected $casts = [
        'android_enabled' => 'boolean',
        'ios_enabled' => 'boolean',
        'enable_reporting' => 'boolean',
        'enable_test_ads' => 'boolean',
        'is_connected' => 'boolean',
        'last_synced_at' => 'datetime',
        'token_expires_at' => 'datetime',
    ];

    /**
     * Get the singleton configuration record or create one if it doesn't exist.
     */
    public static function getSingleton()
    {
        $setting = self::first();
        if (!$setting) {
            $setting = self::create([]);
        }
        return $setting;
    }
}
