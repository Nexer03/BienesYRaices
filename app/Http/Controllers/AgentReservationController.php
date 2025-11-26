<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PropertyReservation;
use Illuminate\Support\Facades\Auth;

class AgentReservationController extends Controller
{
    // Muestra la vista con el calendario
    public function index()
    {
        return view('agent.reservations.index');
    }

    // Devuelve eventos JSON para FullCalendar
    public function feed()
    {
        $agentId = Auth::id();

        $reservations = PropertyReservation::with('property', 'property.user')
            ->whereHas('property', function ($q) use ($agentId) {
                $q->where('user_id', $agentId);
            })
            ->get();

        $events = $reservations->map(function ($r) {
            return [
                'id' => $r->id,
                'title' => $r->property->title ?? 'Propiedad sin título',
                'start' => $r->start_date->toDateString(),
                'end'   => $r->end_date->addDay()->toDateString(), // para mostrar correctamente el rango
                'status' => $r->status,
                'client' => $r->user->name ?? 'Cliente no disponible',
                'color' => match($r->status) {
                    'pending'   => '#3b82f6',
                    'confirmed' => '#3b82f6',
                    'completed' => '#3b82f6',
                    'cancelled' => '#3b82f6',
                    default     => '#9ca3af',
                },
            ];
        });

        return response()->json($events);
    }
}
