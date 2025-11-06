<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Review;
use App\Models\PropertyReservation; // ajusta si tu modelo se llama distinto
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class ReviewController extends Controller
{
    public function store(Request $req, Property $property)
    {
        // 1) Validaciones base
        $data = $req->validate([
            'reservation_id' => 'required|exists:property_reservations,id',
            'cleanliness'    => 'required|integer|min:1|max:5',
            'accuracy'       => 'required|integer|min:1|max:5',
            'communication'  => 'required|integer|min:1|max:5',
            'location'       => 'required|integer|min:1|max:5',
            'value'          => 'required|integer|min:1|max:5',
            'checkin'        => 'required|integer|min:1|max:5',
            'comment'        => 'nullable|string|max:2000',
        ]);

        // 2) Reglas de negocio
        if ($property->listing_type !== 'rent') {
            return back()->with('error', 'Las reseñas solo aplican a propiedades en renta.');
        }

        $reservation = PropertyReservation::where('id', $data['reservation_id'])
            ->where('property_id', $property->id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$reservation) {
            return back()->with('error', 'No se encontró una reserva válida para reseñar.');
        }

        // Debe haber finalizado la estancia
        if (Carbon::parse($reservation->end_date)->isFuture()) {
            return back()->with('error', 'Solo puedes reseñar después de tu estancia.');
        }

        // Una reseña por reserva
        $already = Review::where('reservation_id', $reservation->id)->exists();
        if ($already) {
            return back()->with('error', 'Ya enviaste una reseña para esta reserva.');
        }

        // 3) Crear reseña (calcular promedio)
        $overall = round((
            $data['cleanliness'] + $data['accuracy'] + $data['communication'] +
            $data['location'] + $data['value'] + $data['checkin']
        ) / 6, 2);

        Review::create([
            'property_id'    => $property->id,
            'reservation_id' => $reservation->id,
            'author_id'      => Auth::id(),
            'agent_id'       => $property->user_id,
            'cleanliness'    => $data['cleanliness'],
            'accuracy'       => $data['accuracy'],
            'communication'  => $data['communication'],
            'location'       => $data['location'],
            'value'          => $data['value'],
            'checkin'        => $data['checkin'],
            'overall'        => $overall,
            'comment'        => $data['comment'] ?? null,
            'is_public'      => true,
            'published_at'   => now(),
        ]);

        return back()->with('success', '¡Gracias por tu reseña!');
    }
}
