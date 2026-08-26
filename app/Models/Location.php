<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'latitude'                         => 'float',
        'longitude'                        => 'float',
        'notification_enabled_percentage'  => 'float',
        'location_sources'                 => 'array',
        'permission_breakdown'             => 'array',
        'platform_distribution'            => 'array',
        'top_app_versions'                 => 'array',
        'activity_areas'                   => 'array',
        'activity_by_hour_matrix'          => 'array',
        'activity_types'                   => 'array',
        'daily_pattern'                    => 'array',
        'last_activity_at'                 => 'datetime',
    ];

    /**
     * Country Flag emoji getter.
     */
    public function getCountryFlagEmojiAttribute(): string
    {
        $code = strtoupper($this->country_code ?? 'IN');
        return match ($code) {
            'IN' => '🇮🇳',
            'US' => '🇺🇸',
            'BR' => '🇧🇷',
            'ID' => '🇮🇩',
            'GB', 'UK' => '🇬🇧',
            'AE' => '🇦🇪',
            'DE' => '🇩🇪',
            'FR' => '🇫🇷',
            'JP' => '🇯🇵',
            'SG' => '🇸🇬',
            'AU' => '🇦🇺',
            'CA' => '🇨🇦',
            default => '🌐',
        };
    }

    /**
     * Status badge CSS styling class.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'High Activity' => 'status-high-activity',
            'Active'        => 'status-active',
            'Inactive'      => 'status-inactive',
            default         => 'status-active',
        };
    }

    /**
     * Human readable last activity.
     */
    public function getLastActivityHumanAttribute(): string
    {
        if (!$this->last_activity_at) {
            return '2 min ago';
        }
        return $this->last_activity_at->diffForHumans(['short' => false, 'parts' => 1]);
    }

    /**
     * Get all installed devices in this location.
     */
    public function devices()
    {
        return $this->hasMany(Device::class, 'city', 'city');
    }
}
