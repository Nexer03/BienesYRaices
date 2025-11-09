<?php

namespace Tests\Unit\Listeners;

use App\Events\ReservationPaid;
use App\Listeners\NotifyAdminsOfReservationPaid;
use App\Models\Property;
use App\Models\PropertyReservation;
use App\Models\User;
use App\Notifications\ReservationPaidNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotifyAdminsOfReservationPaidTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_notifies_admin_users_when_reservation_is_paid(): void
    {
        Notification::fake();

        $admins = User::factory(2)->create(['role' => 'admin']);
        $nonAdmin = User::factory()->create(['role' => 'client']);
        $property = Property::factory()->create();

        $reservation = PropertyReservation::create([
            'property_id' => $property->id,
            'user_id' => $nonAdmin->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'total_price' => 5000,
            'payment_status' => 'paid',
            'payment_method' => 'paypal',
            'payment_id' => 'PAY-999',
            'payer_email' => 'client@example.com',
        ]);

        $event = new ReservationPaid($reservation);
        $listener = new NotifyAdminsOfReservationPaid();

        $listener->handle($event);

        Notification::assertSentToTimes($admins[0], ReservationPaidNotification::class, 1);
        Notification::assertSentToTimes($admins[1], ReservationPaidNotification::class, 1);
        Notification::assertNothingSentTo($nonAdmin);
    }

    public function test_it_does_not_fail_when_no_admins_exist(): void
    {
        Notification::fake();

        $property = Property::factory()->create();
        $user = User::factory()->create(['role' => 'client']);

        $reservation = PropertyReservation::create([
            'property_id' => $property->id,
            'user_id' => $user->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(1)->toDateString(),
            'total_price' => 1000,
        ]);

        $event = new ReservationPaid($reservation);
        $listener = new NotifyAdminsOfReservationPaid();

        $listener->handle($event);

        Notification::assertNothingSent();
        $this->assertTrue(true); // Se ejecutó sin errores
    }
}
