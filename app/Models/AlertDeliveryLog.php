<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlertDeliveryLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'alert_criteria_id',
        'channel',
        'status',
        'sent_at',
        'properties_count',
        'frequency',
        'dedup_hash',
        'error_message',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function criteria(): BelongsTo
    {
        return $this->belongsTo(AlertCriteria::class, 'alert_criteria_id');
    }
}
