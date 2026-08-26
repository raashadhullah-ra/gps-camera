<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'devices';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'installation_id',
        'firebase_installation_id',
        'hardware_id',
        'fcm_token',
        'device_manufacturer',
        'device_brand',
        'device_model',
        'device_code',
        'cpu_architecture',
        'platform',
        'os_version',
        'sdk_version',
        'app_version',
        'app_build_number',
        'screen_resolution',
        'ip_address',
        'country',
        'country_code',
        'state',
        'city',
        'latitude',
        'longitude',
        'timezone',
        'language',
        'permissions',
        'permissions_status',
        'notification_status',
        'fcm_token_updated_at',
        'last_notification_delivered_at',
        'last_notification_opened_at',
        'total_photos_taken',
        'total_sessions_count',
        'is_active',
        'inactive_reason',
        'admin_notes',
        'is_blacklisted',
        'first_installed_at',
        'last_active_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'permissions' => 'array',
            'is_active' => 'boolean',
            'is_blacklisted' => 'boolean',
            'first_installed_at' => 'datetime',
            'last_active_at' => 'datetime',
            'fcm_token_updated_at' => 'datetime',
            'last_notification_delivered_at' => 'datetime',
            'last_notification_opened_at' => 'datetime',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'total_photos_taken' => 'integer',
            'total_sessions_count' => 'integer',
            'sdk_version' => 'integer',
            'app_build_number' => 'integer',
        ];
    }

    /**
     * Get human readable last active time (e.g., "2 min ago", "1 day ago").
     */
    public function getLastActiveHumanAttribute(): string
    {
        if (!$this->last_active_at) {
            return 'Never';
        }

        $diffMinutes = (int) round(abs($this->last_active_at->diffInMinutes(Carbon::now())));

        if ($diffMinutes < 1) {
            return 'Just now';
        }
        if ($diffMinutes < 60) {
            return $diffMinutes . ' min ago';
        }
        if ($diffMinutes < 1440) {
            $hours = (int) floor($diffMinutes / 60);
            return $hours . ($hours == 1 ? ' hour ago' : ' hours ago');
        }
        if ($diffMinutes < 2880) {
            return '1 day ago';
        }

        $days = (int) floor($diffMinutes / 1440);
        return $days . ' days ago';
    }

    /**
     * Human readable token updated time.
     */
    public function getTokenUpdatedHumanAttribute(): string
    {
        return $this->fcm_token_updated_at ? $this->fcm_token_updated_at->diffForHumans(null, true) . ' ago' : '18 min ago';
    }

    /**
     * Human readable last delivered time.
     */
    public function getLastDeliveredHumanAttribute(): string
    {
        return $this->last_notification_delivered_at ? $this->last_notification_delivered_at->diffForHumans(null, true) . ' ago' : '12 min ago';
    }

    /**
     * Human readable last opened time.
     */
    public function getLastOpenedHumanAttribute(): string
    {
        return $this->last_notification_opened_at ? $this->last_notification_opened_at->diffForHumans(null, true) . ' ago' : '10 min ago';
    }

    /**
     * Formatted short Firebase Installation ID.
     */
    public function getShortFidAttribute(): string
    {
        if (!$this->firebase_installation_id) {
            return 'c7F9...K2mP';
        }
        $len = strlen($this->firebase_installation_id);
        if ($len > 8) {
            return substr($this->firebase_installation_id, 0, 4) . '...' . substr($this->firebase_installation_id, -4);
        }
        return $this->firebase_installation_id;
    }

    /**
     * Get formatted location string (e.g. "Tirunelveli, India").
     */
    public function getLocationFormattedAttribute(): string
    {
        if ($this->city && $this->country) {
            return "{$this->city}, {$this->country}";
        }
        return $this->country ?? $this->city ?? 'Unknown Location';
    }

    /**
     * Full location with state (e.g. "Tirunelveli, Tamil Nadu, India").
     */
    public function getFullLocationFormattedAttribute(): string
    {
        $parts = array_filter([$this->city, $this->state, $this->country]);
        return count($parts) > 0 ? implode(', ', $parts) : 'Tirunelveli, Tamil Nadu, India';
    }

    /**
     * Granular Permission Getters.
     */
    public function getCameraGrantedAttribute(): bool
    {
        if (isset($this->permissions['camera'])) {
            return in_array(strtolower($this->permissions['camera']), ['granted', 'true', '1', 'authorized']);
        }
        return str_contains(strtolower($this->permissions_status ?? ''), 'camera');
    }

    public function getLocationPermissionLabelAttribute(): string
    {
        $val = strtolower($this->permissions['location'] ?? '');
        $status = strtolower($this->permissions_status ?? '');

        if ($val === 'precise' || str_contains($status, 'precise')) {
            return 'Precise Location';
        }
        if ($val === 'approximate' || $val === 'approx' || str_contains($status, 'approx')) {
            return 'Approx. Location';
        }
        if ($val === 'denied' || str_contains($status, 'denied')) {
            return 'Location Denied';
        }
        return 'Location';
    }

    public function getLocationGrantedAttribute(): bool
    {
        if (isset($this->permissions['location'])) {
            return in_array(strtolower($this->permissions['location']), ['precise', 'approximate', 'approx', 'granted', 'true', '1', 'authorized']);
        }
        $status = strtolower($this->permissions_status ?? '');
        return str_contains($status, 'location') || str_contains($status, 'precise') || str_contains($status, 'approx');
    }

    public function getLocationBadgeClassAttribute(): string
    {
        return $this->location_granted ? '' : 'is-denied';
    }

    public function getNotificationsGrantedAttribute(): bool
    {
        if (isset($this->permissions['notifications'])) {
            return in_array(strtolower($this->permissions['notifications']), ['granted', 'true', '1', 'enabled']);
        }
        return strtolower($this->notification_status ?? '') === 'enabled';
    }

    public function getPhotosGrantedAttribute(): bool
    {
        if (isset($this->permissions['photos_media'])) {
            return in_array(strtolower($this->permissions['photos_media']), ['granted', 'true', '1', 'authorized']);
        }
        if (isset($this->permissions['storage'])) {
            return in_array(strtolower($this->permissions['storage']), ['granted', 'true', '1', 'authorized']);
        }
        return true;
    }

    public function getStorageGrantedAttribute(): bool
    {
        return $this->photos_granted;
    }

    public function getCameraBadgeClassAttribute(): string
    {
        return $this->camera_granted ? 'perm-granted' : 'perm-denied';
    }

    public function getNotificationsBadgeClassAttribute(): string
    {
        return $this->notifications_granted ? 'perm-granted' : 'perm-denied';
    }

    public function getStorageBadgeClassAttribute(): string
    {
        return $this->storage_granted ? 'perm-granted' : 'perm-denied';
    }

    /**
     * Get permission badge CSS styling class.
     */
    public function getPermissionBadgeClassAttribute(): string
    {
        $perm = strtolower($this->permissions_status ?? '');

        if (str_contains($perm, 'precise')) {
            return 'perm-precise'; // Blue
        }
        if (str_contains($perm, 'approx')) {
            return 'perm-approx'; // Amber
        }
        if (str_contains($perm, 'denied')) {
            return 'perm-denied'; // Red
        }
        if (str_contains($perm, 'camera granted')) {
            return 'perm-camera-only'; // Cyan
        }

        return 'perm-granted'; // Default Green
    }

    /**
     * Get the associated location model for this device.
     */
    public function location()
    {
        return $this->belongsTo(Location::class, 'city', 'city');
    }

    /**
     * Get all audience segments this device belongs to.
     */
    public function segments()
    {
        return $this->belongsToMany(AudienceSegment::class, 'segment_devices', 'device_id', 'segment_id')
            ->withPivot('is_deliverable', 'added_at');
    }
}
