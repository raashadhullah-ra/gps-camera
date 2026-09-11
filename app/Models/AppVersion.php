<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppVersion extends Model
{
    use HasFactory;

    protected $table = 'app_versions';

    protected $fillable = [
        'app_name',
        'platform',
        'current_version',
        'minimum_version',
        'force_update',
        'update_title',
        'update_message',
        'play_store_url',
        'app_store_url',
        'is_under_maintenance',
        'maintenance_message',
    ];

    protected $casts = [
        'force_update' => 'boolean',
        'is_under_maintenance' => 'boolean',
    ];

    /**
     * Get or create the default app version settings record.
     *
     * @return static
     */
    public static function getSettings(): self
    {
        return static::firstOrCreate([], [
            'app_name' => 'GPS Camera',
            'platform' => 'all',
            'current_version' => '1.0.0',
            'minimum_version' => '1.0.0',
            'force_update' => false,
            'update_title' => 'New Update Available',
            'update_message' => 'A new version of GPS Camera is available with performance improvements and bug fixes. Please update to continue.',
            'play_store_url' => 'https://play.google.com/store/apps/details?id=com.geocam.app',
            'app_store_url' => 'https://apps.apple.com/app/gps-camera',
            'is_under_maintenance' => false,
            'maintenance_message' => 'GPS Camera is currently undergoing scheduled maintenance. Please check back shortly.',
        ]);
    }
}
