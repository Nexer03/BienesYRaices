<?php

namespace App\Events;

use App\Models\Property;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PropertyPublishedOrUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Property $property, public string $trigger = 'event')
    {
    }
}
