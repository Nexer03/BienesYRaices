<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyReservation;
use Illuminate\Http\Request;
use Carbon\Carbon;

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

        // Calcular precio
        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
        $nights = $start->diffInDays($end);
        $totalPrice = $property->price * $nights;

        $reservation = PropertyReservation::create([
        'property_id' => $request->property_id,
        'user_id' => auth()->id(),
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
        'total_price' => $totalPrice,
        'payment_id' => $request->payment_id, // Nuevo campo
        'payer_email' => $request->payer_email, // Nuevo campo
    ]);

        return response()->json([
            'success' => true, 
            'reservation_id' => $reservation->id,
            'total_price' => $totalPrice,
            'nights' => $nights,
            'message' => 'Reserva creada exitosamente'
        ]);
    }
}