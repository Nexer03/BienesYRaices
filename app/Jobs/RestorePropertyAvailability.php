<?php

namespace App\Jobs;

use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RestorePropertyAvailability implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $propertyId;

    public function __construct(int $propertyId)
    {
        $this->propertyId = $propertyId;
    }

    public function handle(): void
    {
        $property = Property::find($this->propertyId);
        if (!$property) return;

        // Solo vuelve a available si aún está rented
        if ($property->status === 'rented') {
            $property->status = 'available';
            $property->save();
        }
    }
}
