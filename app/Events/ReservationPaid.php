<?php

namespace App\Events;

use App\Models\PropertyReservation;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReservationPaid
{
    use Dispatchable, SerializesModels;

    public PropertyReservation $reservation;

    public function __construct(PropertyReservation $reservation)
    {
        $this->reservation = $reservation;
    }
}
