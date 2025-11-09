<?php

namespace App\Listeners;

use App\Events\ReservationPaid;
use App\Models\User;
use App\Notifications\ReservationPaidNotification;
use Illuminate\Support\Facades\Notification;

class NotifyAdminsOfReservationPaid
{
    public function handle(ReservationPaid $event): void
    {
        $admins = User::query()->where('role', 'admin')->get();

        if ($admins->isEmpty()) {
            return;
        }

        $reservation = $event->reservation->loadMissing('property');

        Notification::send($admins, new ReservationPaidNotification($reservation));
    }
}
