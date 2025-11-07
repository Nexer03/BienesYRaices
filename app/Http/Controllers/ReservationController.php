<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\PropertyReservation;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReservationController extends Controller
{
    /**
     * POST /reservations/preview
     * Calcula importes, crea/actualiza una reserva pendiente y muestra el checkout.
     */
    public function preview(Request $request)
    {
        $data = $request->validate([
            'property_id' => ['required', 'exists:properties,id'],
            'start_date'  => ['required', 'date'],
            'end_date'    => ['required', 'date', 'after:start_date'],
        ]);


        $property = Property::with('images')->findOrFail($data['property_id']);

        $start  = Carbon::parse($data['start_date'])->startOfDay();
        $end    = Carbon::parse($data['end_date'])->startOfDay();
        $nights = max(1, $start->diffInDays($end)); // nunca 0

        $price      = (float) $property->price;              // precio por noche
        $subtotal   = round($price * $nights, 2);
        $serviceFee = round($subtotal * 0.05, 2);            // 5% ejemplo
        $taxes      = round(($subtotal + $serviceFee) * 0.16, 2); // 16% ejemplo
        $total      = round($subtotal + $serviceFee + $taxes, 2);

        // Crea/actualiza una reserva "pendiente" para este usuario y propiedad
        $reservation = PropertyReservation::updateOrCreate(
            [
                'property_id' => $property->id,
                'user_id'     => $request->user()->id,
                'status'      => 'pending',
            ],
            [
                'start_date'     => $start->toDateString(),
                'end_date'       => $end->toDateString(),
                'total_price'    => $total,          // 👈 OBLIGATORIO
                'payment_status' => 'unpaid',        // si tienes esta columna
            ]
        );

        session(['current_reservation_id' => $reservation->id]);


        // Atributos auxiliares solo para la vista
        $reservation->setAttribute('nights', $nights);
        $reservation->setAttribute('guests', $reservation->guests ?? 1);

        return view('reservations.checkout', [
            'property'    => $property,
            'reservation' => $reservation,
            'price'       => $price,
            'subtotal'    => $subtotal,
            'serviceFee'  => $serviceFee,
            'taxes'       => $taxes,
            'total'       => $total,
        ]);
    }

    /**
     * GET /reservations/{reservation}/checkout
     * Muestra la página tipo Airbnb para una reserva existente.
     */
    public function checkout(PropertyReservation $reservation, Request $request)
    {
        abort_unless($reservation->user_id === $request->user()->id, 403);

        $reservation->load('property.images');

        // No dependemos de una columna nights
        $nights     = $reservation->nights; // accessor en el modelo
        $price      = (float) $reservation->property->price;
        $subtotal   = round($price * $nights, 2);
        $serviceFee = round($subtotal * 0.05, 2);
        $taxes      = round(($subtotal + $serviceFee) * 0.16, 2);
        $total      = round($subtotal + $serviceFee + $taxes, 2);

        return view('reservations.checkout', [
            'reservation' => $reservation->setAttribute('nights', $nights),
            'property'    => $reservation->property,
            'price'       => $price,
            'subtotal'    => $subtotal,
            'serviceFee'  => $serviceFee,
            'taxes'       => $taxes,
            'total'       => $total,
        ]);
    }
}
