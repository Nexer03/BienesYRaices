<?php

namespace App\Notifications;

use App\Models\PropertyReservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationConfirmationNotification extends Notification
{
    use Queueable;

    public function __construct(public PropertyReservation $reservation)
    {
        $this->reservation->loadMissing('property.user');
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $reservation = $this->reservation;
        $property = $reservation->property;
        $host = $property?->user;

        $mail = (new MailMessage)
            ->subject('Tu reservación está confirmada')
            ->greeting('Hola ' . ($notifiable->name ?? ''))
            ->line('Tu reservación fue confirmada correctamente.')
            ->line('Propiedad: ' . ($property?->title ?? ''))
            ->line('Fechas: ' . optional($reservation->start_date)->format('d/m/Y') . ' al ' . optional($reservation->end_date)->format('d/m/Y'))
            ->line('Noches: ' . ($reservation->nights ?? 1))
            ->line('Total pagado: $' . number_format((float) $reservation->total_price, 2) . ' MXN')
            ->line('Método de pago: ' . ucfirst($reservation->payment_method ?? 'N/D'))
            ->line('Reserva #: ' . $reservation->id)
            ->action('Ver detalles', route('visits.my', ['tab' => 'reservations']))
            ->line('Anfitrión: ' . ($host?->name ?? ''));

        if ($host?->email) {
            $mail->line('Contacto del anfitrión: ' . $host->email);
        }

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        $reservation = $this->reservation;
        $property = $reservation->property;

        return [
            'reservation_id' => $reservation->id,
            'property_title' => $property?->title,
            'start_date' => optional($reservation->start_date)->toDateString(),
            'end_date' => optional($reservation->end_date)->toDateString(),
            'total_price' => $reservation->total_price,
        ];
    }
}
