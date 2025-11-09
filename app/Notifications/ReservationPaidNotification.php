<?php

namespace App\Notifications;

use App\Models\PropertyReservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class ReservationPaidNotification extends Notification
{
    use Queueable;

    public function __construct(public PropertyReservation $reservation)
    {
        $this->reservation->loadMissing('property');
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $reservation = $this->reservation;
        $property = $reservation->property;
        $propertyTitle = $property?->title ?? 'Propiedad sin título';
        $startDate = $this->formatDate($reservation->start_date);
        $endDate = $this->formatDate($reservation->end_date);
        $total = $this->formatCurrency($reservation->total_price);

        $message = (new MailMessage)
            ->subject('Nueva reserva pagada')
            ->greeting('Hola ' . ($notifiable->name ?? 'Administrador') . '!')
            ->line('Se ha completado el pago de una reserva.')
            ->line('Propiedad: ' . $propertyTitle);

        if ($startDate && $endDate) {
            $message->line("Fechas: {$startDate} al {$endDate}");
        } elseif ($startDate) {
            $message->line('Fecha de inicio: ' . $startDate);
        }

        if ($reservation->payment_method) {
            $message->line('Método de pago: ' . ucfirst($reservation->payment_method));
        }

        if ($reservation->payer_email) {
            $message->line('Correo del pagador: ' . $reservation->payer_email);
        }

        if ($total) {
            $message->line('Total pagado: ' . $total);
        }

        return $message
            ->line('ID de pago: ' . ($reservation->payment_id ?? 'N/D'))
            ->line('Puedes revisar los detalles completos en el panel de administración.');
    }

    public function toArray(object $notifiable): array
    {
        $reservation = $this->reservation;
        $property = $reservation->property;

        return [
            'reservation_id' => $reservation->id,
            'property_id' => $reservation->property_id,
            'property_title' => $property?->title,
            'start_date' => $this->formatDate($reservation->start_date),
            'end_date' => $this->formatDate($reservation->end_date),
            'total_price' => $reservation->total_price,
            'payment_method' => $reservation->payment_method,
            'payment_status' => $reservation->payment_status,
            'payment_id' => $reservation->payment_id,
            'payer_email' => $reservation->payer_email,
        ];
    }

    private function formatDate(mixed $date): ?string
    {
        if (blank($date)) {
            return null;
        }

        return Carbon::parse($date)->format('d/m/Y');
    }

    private function formatCurrency(?float $amount): ?string
    {
        if (blank($amount)) {
            return null;
        }

        return '$' . number_format($amount, 2, '.', ',');
    }
}
