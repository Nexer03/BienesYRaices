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
        $reservation = $event->reservation->loadMissing('property.user');

        $admins = User::query()->where('role', 'admin')->get();

        if ($admins->isNotEmpty()) {
            Notification::send($admins, new ReservationPaidNotification($reservation));
        }

        $agent = $reservation->property?->user;

        if ($agent && !$admins->contains('id', $agent->id)) {
            $agent->notify(new ReservationPaidNotification($reservation));
        }
    }
}
