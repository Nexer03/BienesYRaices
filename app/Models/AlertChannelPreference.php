<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlertChannelPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'alert_criteria_id',
        'channel',
        'frequency',
        'enabled',
        'consented_at',
        'unsubscribed_at',
        'last_sent_at',
        'cooldown_minutes',
        'antispam_window_minutes',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'consented_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'last_sent_at' => 'datetime',
    ];

    public function criteria(): BelongsTo
    {
        return $this->belongsTo(AlertCriteria::class, 'alert_criteria_id');
    }

    public function isSubscribed(): bool
    {
        return $this->enabled && $this->unsubscribed_at === null;
    }
}
