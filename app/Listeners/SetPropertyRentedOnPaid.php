<?php

namespace App\Listeners;

use App\Events\ReservationPaid;
use App\Jobs\RestorePropertyAvailability;
use Carbon\Carbon;

class SetPropertyRentedOnPaid
{
    public function handle(ReservationPaid $event): void
    {
        $reservation = $event->reservation;

        // marca la propiedad como 'rented'
        $property = $reservation->property()->first();
        if ($property) {
            $property->status = 'rented';
            $property->save();

            // agenda el revert al finalizar el último día de la reserva
            $runAt = Carbon::parse($reservation->end_date)->endOfDay();
            RestorePropertyAvailability::dispatch($property->id)->delay($runAt);
        }
    }
}
