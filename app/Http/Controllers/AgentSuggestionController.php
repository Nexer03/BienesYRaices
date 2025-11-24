<?php

namespace App\Http\Controllers;

use App\Models\AgentSuggestion;
use App\Models\PropertyReservation;
use App\Models\Visit;
use App\Notifications\AgentSuggestionNotification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AgentSuggestionController extends Controller
{
    public function index(Request $request)
    {
        $agentId = $request->user()->id;

        $type = $request->query('type');

        $suggestions = AgentSuggestion::with(['user', 'property', 'reservation', 'visit'])
            ->where('agent_id', $agentId)
            ->when($type, fn($q) => $q->where('type', $type))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('agent.suggestions.index', compact('suggestions', 'type'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(['reservation', 'visit'])],
            'reservation_id' => ['nullable', 'integer', 'exists:property_reservations,id'],
            'visit_id' => ['nullable', 'integer', 'exists:visits,id'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $user = $request->user();

        if ($data['type'] === 'reservation') {
            abort_unless($data['reservation_id'], 422, 'Falta la reservación.');
            $reservation = PropertyReservation::with('property.user')->findOrFail($data['reservation_id']);
            abort_unless($reservation->user_id === $user->id, 403, 'Solo puedes opinar sobre tus propias reservas.');

            $agent = $reservation->property?->user;
            abort_unless($agent, 422, 'No se encontró el agente de la propiedad.');

            $suggestion = AgentSuggestion::create([
                'agent_id' => $agent->id,
                'user_id' => $user->id,
                'property_id' => $reservation->property_id,
                'reservation_id' => $reservation->id,
                'type' => 'reservation',
                'message' => $data['message'],
            ]);
        } else {
            abort_unless($data['visit_id'], 422, 'Falta la visita seleccionada.');
            $visit = Visit::with(['agent', 'property'])->findOrFail($data['visit_id']);
            abort_unless($visit->client_id === $user->id, 403, 'Solo puedes opinar sobre tus propias visitas.');

            $agent = $visit->agent;
            abort_unless($agent, 422, 'No se encontró el agente asignado a la visita.');

            $suggestion = AgentSuggestion::create([
                'agent_id' => $agent->id,
                'user_id' => $user->id,
                'property_id' => $visit->property_id,
                'visit_id' => $visit->id,
                'type' => 'visit',
                'message' => $data['message'],
            ]);
        }

        $agent->notify(new AgentSuggestionNotification($suggestion));

        return back()->with('status', 'Gracias por compartir tu sugerencia.');
    }
}
