<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyReservation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PropertyReservationController extends Controller
{

public function store(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión para hacer una reserva'
            ], 401);
        }

        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $property = Property::findOrFail($request->property_id);

        if ($property->listing_type !== 'rent') {
            return response()->json([
                'success' => false,
                'message' => 'Esta propiedad no está disponible para renta'
            ], 422);
        }

        if ($property->user_id === $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes reservar tu propia propiedad.'
            ], 403);
        }

        abort_unless($property->status === 'available', 422, 'La propiedad no está disponible.');
        abort_if(
            PropertyReservation::overlaps($property->id, $request->start_date, $request->end_date),
            422,
            'Las fechas seleccionadas se traslapan con otra reserva.'
        );

        // Calcular precio
        $start  = Carbon::parse($request->start_date)->startOfDay();
        $end    = Carbon::parse($request->end_date)->startOfDay();
        $nights = max(1, $start->diffInDays($end));
        $nightlyRate = (float) $property->price;
        $totalPrice = $nightlyRate * $nights;

        $reservation = PropertyReservation::create([
            'property_id' => $property->id,
            'user_id' => auth()->id(),
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'total_price' => $totalPrice,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_id' => $request->input('payment_id'),
            'payer_email' => $request->input('payer_email'),
        ]);

        return response()->json([
            'success' => true,
            'reservation_id' => $reservation->id,
            'total_price' => $totalPrice,
            'nights' => $nights,
            'message' => 'Reserva creada exitosamente'
        ]);
    }

    public function confirmPayment(Request $request, PropertyReservation $reservation)
    {
        $user = $request->user();

        abort_unless($user, 401);

        $reservation->loadMissing('property');

        abort_unless(
            $user->role === 'admin'
                || ($user->role === 'agent' && optional($reservation->property)->user_id === $user->id),
            403,
            'No tienes permisos para confirmar este pago.'
        );

        $data = $request->validate([
            'payment_id' => ['nullable', 'string', 'max:100'],
            'payment_method' => ['nullable', 'string', 'max:30'],
            'payer_email' => ['nullable', 'email', 'max:255'],
        ]);

        $reservation->fill([
            'payment_id' => $data['payment_id'] ?? $reservation->payment_id,
            'payment_method' => $data['payment_method'] ?? $reservation->payment_method ?? 'manual',
            'payer_email' => $data['payer_email'] ?? $reservation->payer_email,
            'payment_status' => 'paid',
            'status' => 'confirmed',
        ]);

        $reservation->save();

        event(new \App\Events\ReservationPaid($reservation));

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'reservation' => $reservation->fresh(),
            ]);
        }

        return redirect()->back()->with('status', 'Pago confirmado correctamente.');
    }
}
