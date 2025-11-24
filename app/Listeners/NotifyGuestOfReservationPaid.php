<?php

namespace App\Listeners;

use App\Events\ReservationPaid;
use App\Notifications\ReservationConfirmationNotification;

class NotifyGuestOfReservationPaid
{
    public function handle(ReservationPaid $event): void
    {
        $reservation = $event->reservation->loadMissing('user');
        $guest = $reservation->user;

        if ($guest) {
            $guest->notify(new ReservationConfirmationNotification($reservation));
        }
    }
}
