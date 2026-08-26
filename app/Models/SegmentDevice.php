<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SegmentDevice extends Model
{
    use HasFactory;

    protected $table = 'segment_devices';

    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = [
        'is_deliverable' => 'boolean',
        'added_at'       => 'datetime',
    ];

    public function segment(): BelongsTo
    {
        return $this->belongsTo(AudienceSegment::class, 'segment_id');
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_id');
    }
}
