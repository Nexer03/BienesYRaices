<?php

namespace Tests\Unit\Notifications;

use App\Models\Property;
use App\Models\PropertyReservation;
use App\Models\User;
use App\Notifications\ReservationPaidNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Tests\TestCase;

class ReservationPaidNotificationTest extends TestCase
{
    public function test_notification_sends_via_mail_and_database(): void
    {
        $notification = new ReservationPaidNotification(new PropertyReservation());
        $notifiable = new User(['name' => 'Admin']);

        $this->assertSame(['mail', 'database'], $notification->via($notifiable));
    }

    public function test_notification_contains_reservation_details(): void
    {
        $property = new Property(['title' => 'Casa Bonita']);

        $reservation = new PropertyReservation([
            'property_id' => 5,
            'start_date' => '2025-01-05',
            'end_date' => '2025-01-10',
            'total_price' => 12345.67,
            'payment_method' => 'paypal',
            'payment_status' => 'paid',
            'payment_id' => 'PAY-123',
            'payer_email' => 'cliente@example.com',
        ]);

        $reservation->id = 10;

        $reservation->setRelation('property', $property);

        $notification = new ReservationPaidNotification($reservation);
        $notifiable = new User(['name' => 'Admin']);

        $mail = $notification->toMail($notifiable);
        $this->assertInstanceOf(MailMessage::class, $mail);
        $body = implode(' ', array_merge($mail->introLines, $mail->outroLines));

        $this->assertStringContainsString('Casa Bonita', $body);
        $this->assertStringContainsString('05/01/2025', $body);
        $this->assertStringContainsString('10/01/2025', $body);
        $this->assertStringContainsString('Paypal', $body);
        $this->assertStringContainsString('cliente@example.com', $body);
        $this->assertStringContainsString('$12,345.67', $body);
        $this->assertStringContainsString('PAY-123', $body);

        $data = $notification->toArray($notifiable);

        $this->assertSame(10, $data['reservation_id']);
        $this->assertSame(5, $data['property_id']);
        $this->assertSame('Casa Bonita', $data['property_title']);
        $this->assertSame('05/01/2025', $data['start_date']);
        $this->assertSame('10/01/2025', $data['end_date']);
        $this->assertSame(12345.67, $data['total_price']);
        $this->assertSame('paypal', $data['payment_method']);
        $this->assertSame('paid', $data['payment_status']);
        $this->assertSame('PAY-123', $data['payment_id']);
        $this->assertSame('cliente@example.com', $data['payer_email']);
    }
}
