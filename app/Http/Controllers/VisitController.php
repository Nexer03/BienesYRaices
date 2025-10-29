<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visit;
use App\Models\Property;
use App\Models\PropertyReservation;


class VisitController extends Controller
{
    // Cliente agenda visita
    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'agent_id' => 'required|exists:users,id',
            'visit_date' => 'required|date|after:now'
        ]);

        Visit::create([
            'client_id' => auth()->id(),
            'agent_id' => $request->agent_id,
            'property_id' => $request->property_id,
            'visit_date' => $request->visit_date,
            'status' => 'pending'
        ]);

        return response()->json(['success' => true, 'message' => 'Visita agendada']);
    }

    // En VisitController.php

    public function agentVisits()
    {
        // Visitas pendientes para la primera sección
        $pendingVisits = Visit::with(['client', 'property'])
            ->where('agent_id', auth()->id())
            ->where('status', 'pending')
            ->orderBy('visit_date', 'asc')
            ->get();

        // Todas las visitas para la agenda (pendientes y confirmadas)
        $allVisits = Visit::with(['client', 'property'])
            ->where('agent_id', auth()->id())
            ->whereIn('status', ['confirmed', 'pending'])
            ->orderBy('visit_date', 'asc')
            ->get();

        return view('agent.visits', compact('pendingVisits', 'allVisits'));
    }


    public function confirm(Visit $visit)
    {
        // Verificar que el agente sea el dueño de esta visita
        if ($visit->agent_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para confirmar esta visita'
            ], 403);
        }

        $visit->update(['status' => 'confirmed']);

        return response()->json([
            'success' => true,
            'message' => 'Visita confirmada correctamente'
        ]);
    }

    public function cancel(Visit $visit)
    {
        // Verificar que el agente sea el dueño de esta visita
        if ($visit->agent_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para cancelar esta visita'
            ], 403);
        }

        $visit->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Visita cancelada correctamente'
        ]);
    }

     public function myVisits(Request $request)
        {
            $query = Visit::with(['property', 'agent'])
                ->where('client_id', auth()->id());

            if ($request->status) {
                $query->where('status', $request->status);
            }

            $visits = $query->orderBy('visit_date', 'desc')->get();

            $reservations = \App\Models\PropertyReservation::with('property')
                ->where('user_id', auth()->id())
                ->orderBy('start_date', 'desc')
                ->get()
                ->map(function($res) {
                    $res->nights = \Carbon\Carbon::parse($res->start_date)
                        ->diffInDays(\Carbon\Carbon::parse($res->end_date));
                    return $res;
                });

            return view('visits.myVisits', compact('visits', 'reservations'))
                ->with('activeTab', 'visits');
        }


}