<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'campaign_id',
        'objective',
        'source',
        'format',
        'is_active',
        'conversion_goal',
        'destination_type',
        'destination',
        'tracking_event',
        'image_path',
        'headline',
        'description',
        'call_to_action',
        'destination_url',
        'alt_text',
        'placements',
        'audience_segment_id',
        'countries',
        'platforms',
        'min_app_version',
        'device_languages',
        'exclude_subscribed',
        'consent_eligibility',
        'frequency_cap',
        'frequency_per_user',
        'max_impressions',
        'stop_at_limit',
        'daily_budget',
        'pacing',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'timezone',
        'delivery_type',
        'status',
        'publish_option',
        'notify_admins',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'placements' => 'array',
        'countries' => 'array',
        'platforms' => 'array',
        'device_languages' => 'array',
        'exclude_subscribed' => 'boolean',
        'stop_at_limit' => 'boolean',
        'notify_admins' => 'boolean',
        'daily_budget' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];
}
