<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class NotificationCampaign extends Model
{
    use HasFactory;

    protected $table = 'notification_campaigns';

    protected $fillable = [
        'campaign_id',
        'name',
        'title',
        'message',
        'action',
        'action_url',
        'image_url',
        'custom_payload',
        'audience_type',
        'audience_label',
        'segment_id',
        'target_device_ids',
        'android_count',
        'ios_count',
        'total_audience',
        'delivered_count',
        'open_count',
        'open_rate',
        'status',
        'scheduled_at',
        'sent_at',
        'time_zone',
        'quiet_hours_enabled',
        'expiry_hours',
        'timeline_steps',
        'created_by',
    ];

    protected $casts = [
        'target_device_ids'   => 'array',
        'timeline_steps'      => 'array',
        'scheduled_at'        => 'datetime',
        'sent_at'             => 'datetime',
        'quiet_hours_enabled' => 'boolean',
        'open_rate'           => 'decimal:2',
    ];

    /**
     * Relationship to AudienceSegment
     */
    public function segment(): BelongsTo
    {
        return $this->belongsTo(AudienceSegment::class, 'segment_id');
    }

    /**
     * Relationship to User (Creator)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Badge CSS class depending on status
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match (strtolower($this->status ?? 'draft')) {
            'scheduled' => 'pill-badge pill-scheduled',
            'sent'      => 'pill-badge pill-sent',
            'failed'    => 'pill-badge pill-failed',
            'archived'  => 'pill-badge pill-archived',
            default     => 'pill-badge pill-draft',
        };
    }

    /**
     * Human formatted delivery string for tables
     */
    public function getDeliveryFormattedAttribute(): string
    {
        $tz = $this->time_zone ?: 'Asia/Kolkata';

        if ($this->status === 'sent' && $this->sent_at) {
            return 'Sent ' . $this->sent_at->timezone($tz)->format('d M Y, h:i A');
        }

        if ($this->status === 'scheduled' && $this->scheduled_at) {
            return $this->scheduled_at->timezone($tz)->format('d M Y, h:i A');
        }

        if ($this->status === 'failed' && $this->sent_at) {
            return 'Sent ' . $this->sent_at->timezone($tz)->format('d M Y, h:i A');
        }

        return 'Not scheduled';
    }

    /**
     * Formatted delivered vs total string
     */
    public function getDeliveredFormattedAttribute(): string
    {
        if (in_array($this->status, ['sent', 'failed']) && $this->total_audience > 0) {
            return number_format($this->delivered_count) . ' / ' . number_format($this->total_audience);
        }

        return '—';
    }

    /**
     * Formatted open rate
     */
    public function getOpenRateFormattedAttribute(): string
    {
        if ($this->status === 'sent' && $this->open_rate !== null) {
            return number_format($this->open_rate, 1) . '%';
        }

        if ($this->status === 'failed') {
            return '0%';
        }

        return '—';
    }
}
