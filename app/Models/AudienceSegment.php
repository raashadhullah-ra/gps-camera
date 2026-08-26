<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AudienceSegment extends Model
{
    use HasFactory;

    protected $table = 'audience_segments';

    protected $guarded = ['id'];

    protected $casts = [
        'rule_groups'           => 'array',
        'platform_filters'      => 'array',
        'exclusions'            => 'array',
        'platform_distribution' => 'array',
        'paused_until'          => 'date',
        'last_synced_at'        => 'datetime',
        'auto_refresh_enabled'  => 'boolean',
        'audience_size'         => 'integer',
        'deliverable_count'     => 'integer',
        'excluded_count'        => 'integer',
    ];

    /**
     * Devices belonging to this segment.
     */
    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(Device::class, 'segment_devices', 'segment_id', 'device_id')
            ->withPivot('is_deliverable', 'added_at');
    }

    /**
     * Activity timeline history.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(SegmentActivityLog::class, 'segment_id')->orderBy('created_at', 'desc');
    }

    /**
     * Segment creator.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Status badge CSS class.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'active'   => 'status-active',
            'draft'    => 'status-draft',
            'paused'   => 'status-paused',
            'archived' => 'status-archived',
            default    => 'status-active',
        };
    }

    /**
     * Type badge CSS class.
     */
    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->type) {
            'dynamic' => 'type-dynamic',
            'static'  => 'type-static',
            default   => 'type-dynamic',
        };
    }

    /**
     * Human readable last synced time.
     */
    public function getLastSyncedHumanAttribute(): string
    {
        if (!$this->last_synced_at) {
            return 'Not synced';
        }
        return $this->last_synced_at->diffForHumans(['short' => false, 'parts' => 1]);
    }

    /**
     * Deliverable percentage (e.g. 97.0%).
     */
    public function getDeliverablePercentageAttribute(): float
    {
        $base = $this->audience_size > 0 ? $this->audience_size : ($this->deliverable_count + $this->excluded_count);
        if ($base <= 0) {
            return 0.0;
        }
        return min(100.0, round(($this->deliverable_count / $base) * 100, 1));
    }

    /**
     * Excluded percentage (e.g. 3.0%).
     */
    public function getExcludedPercentageAttribute(): float
    {
        $base = $this->audience_size > 0 ? $this->audience_size : ($this->deliverable_count + $this->excluded_count);
        if ($base <= 0) {
            return 0.0;
        }
        if ($this->excluded_count > $base) {
            return max(0.0, round(100.0 - $this->deliverable_percentage, 1));
        }
        return round(($this->excluded_count / $base) * 100, 1);
    }
}
