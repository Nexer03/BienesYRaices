<?php

namespace App\Http\Controllers;

use App\Events\VisitUpdated;                 // 👈 NUEVO
use App\Models\Conversation;
use App\Models\Visit;
use App\Services\ConversationMessenger;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class ChatVisitController extends Controller
{
    public function store(Request $request, Conversation $conversation, ConversationMessenger $messenger)
    {
        $this->authorizeConversation($request, $conversation);

        $conversation->loadMissing('property');
        $property = $conversation->property;

        abort_unless($property && $property->listing_type === 'sale', 422, 'Esta propiedad no permite agendar visitas.');

        $data = $request->validate([
            'visit_date' => ['required', 'date', 'after:now'],
            'notes'      => ['nullable', 'string', 'max:2000'],
        ]);

        $agentId   = $conversation->agent_id;
        $clientId  = $conversation->client_id;
        $visitDate = Carbon::parse($data['visit_date']);

        $existing = Visit::where('property_id', $property->id)
            ->where('agent_id', $agentId)
            ->where('client_id', $clientId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderByDesc('visit_date')
            ->first();

        // Evitar solapamiento en la agenda del agente
        if (Visit::overlapsForAgent($agentId, $visitDate, $existing?->id)) {
            return $this->respond(
                $request,
                [
                    'status'  => 'error',
                    'message' => 'Ya existe otra visita programada en ese horario.',
                ],
                back()
                    ->withInput()
                    ->withErrors(['visit_date' => 'Ya existe otra visita en ese horario.'])
            );
        }

        if ($existing) {
            $existing->update([
                'visit_date' => $visitDate,
                'notes'      => $data['notes'],
                'status'     => 'pending',
            ]);
            $visit  = $existing;
            $action = 'updated';
        } else {
            $visit = Visit::create([
                'property_id' => $property->id,
                'agent_id'    => $agentId,
                'client_id'   => $clientId,
                'visit_date'  => $visitDate,
                'status'      => 'pending',
                'notes'       => $data['notes'],
            ]);
            $action = 'created';
        }

        $formattedDate = $visitDate->timezone(config('app.timezone'))
            ->translatedFormat('d \d\e F, H:i');

        $messageBody = match ($action) {
            'updated' => 'actualizó la cita para el ' . $formattedDate,
            default   => 'agendó una visita para el ' . $formattedDate,
        };

        // Mensaje dentro del chat (ConversationMessenger ya se encarga de broadcast)
        $messenger->send(
            $conversation,
            $request->user()->id,
            '🗓️ ' . Str::title($request->user()->name) . ' ' . $messageBody . '.'
        );

        // 🔔 Broadcast para recargar el panel lateral en tiempo real
        event(new VisitUpdated($conversation, $visit, $action));

        return $this->respond(
            $request,
            [
                'status'  => 'ok',
                'message' => 'Visita guardada correctamente.',
                'visit'   => $visit->only(['id', 'visit_date', 'status', 'notes']),
            ],
            back()->with('success', 'La visita se guardó correctamente.')
        );
    }

    public function confirm(Request $request, Conversation $conversation, Visit $visit, ConversationMessenger $messenger)
    {
        $this->authorizeConversation($request, $conversation);
        $this->ensureVisitBelongsToConversation($visit, $conversation);

        abort_unless($conversation->client_id === $request->user()->id, 403);

        if ($visit->status !== 'pending') {
            return $this->respond(
                $request,
                [
                    'status'  => 'error',
                    'message' => 'La visita ya no está pendiente.',
                ],
                back()->with('error', 'La visita ya no está pendiente.')
            );
        }

        $visit->update(['status' => 'confirmed']);

        $formattedDate = $visit->visit_date->timezone(config('app.timezone'))
            ->translatedFormat('d \d\e F, H:i');

        $messenger->send(
            $conversation,
            $request->user()->id,
            '✅ Confirmé la visita del ' . $formattedDate . '.'
        );

        // 🔔 Broadcast para refrescar panel lateral
        event(new VisitUpdated($conversation, $visit, 'confirmed'));

        return $this->respond(
            $request,
            [
                'status'  => 'ok',
                'message' => 'Visita confirmada.',
            ],
            back()->with('success', 'Confirmaste la visita.')
        );
    }

    public function cancel(Request $request, Conversation $conversation, Visit $visit, ConversationMessenger $messenger)
    {
        $this->authorizeConversation($request, $conversation);
        $this->ensureVisitBelongsToConversation($visit, $conversation);

        $visit->update(['status' => 'cancelled']);

        $formattedDate = $visit->visit_date->timezone(config('app.timezone'))
            ->translatedFormat('d \d\e F, H:i');

        $messenger->send(
            $conversation,
            $request->user()->id,
            '❌ Cancelé la visita del ' . $formattedDate . '.'
        );

        // 🔔 Broadcast para refrescar panel lateral
        event(new VisitUpdated($conversation, $visit, 'cancelled'));

        return $this->respond(
            $request,
            [
                'status'  => 'ok',
                'message' => 'Visita cancelada.',
            ],
            back()->with('success', 'La visita fue cancelada.')
        );
    }

    public function panel(Request $request, Conversation $conversation)
    {
        $this->authorizeConversation($request, $conversation);

        // Cargar lo necesario igual que en el show del chat
        $conversation->loadMissing('property');
        $property  = $conversation->property;
        $authUser  = $request->user();
        $isClient  = $authUser && $conversation->client_id === $authUser->id;
        $isAgent   = $authUser && $conversation->agent_id === $authUser->id;

        $nextVisit         = null;
        $activeReservation = null;

        if ($property) {
            if ($property->listing_type === 'sale') {
                // Próxima visita pendiente/confirmada
                $nextVisit = Visit::where('property_id', $property->id)
                    ->where('agent_id', $conversation->agent_id)
                    ->where('client_id', $conversation->client_id)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->where('visit_date', '>=', now())
                    ->orderBy('visit_date', 'asc')
                    ->first();
            } elseif ($property->listing_type === 'rent') {
                // Si ya usas algo como esto en el ChatController, respeta la misma lógica
                $activeReservation = $conversation->reservations()
                    ->latest('id')
                    ->first();
            }
        }

        // Renderizamos SOLO el panel lateral
        $html = view('chat.partials.side-panel', [
            'conversation'       => $conversation,
            'property'           => $property,
            'authUser'           => $authUser,
            'isClient'           => $isClient,
            'isAgent'            => $isAgent,
            'nextVisit'          => $nextVisit,
            'activeReservation'  => $activeReservation,
        ])->render();

        // Si en algún futuro llamas esto vía fetch con Accept: application/json
        if ($request->wantsJson()) {
            return response()->json([
                'html'  => $html,
                'visit' => $nextVisit
                    ? $nextVisit->only(['id', 'visit_date', 'status', 'notes'])
                    : null,
            ]);
        }

        // Para llamadas normales (no AJAX) también devuelve el HTML tal cual
        return $html;
    }



    protected function authorizeConversation(Request $request, Conversation $conversation): void
    {
        abort_unless(
            in_array($request->user()->id, [$conversation->agent_id, $conversation->client_id]),
            403
        );
    }

    protected function ensureVisitBelongsToConversation(Visit $visit, Conversation $conversation): void
    {
        $matchesConversation =
            $visit->agent_id === $conversation->agent_id &&
            $visit->client_id === $conversation->client_id &&
            $visit->property_id === $conversation->property_id;

        abort_unless($matchesConversation, 404);
    }

    protected function respond(Request $request, array $payload, Response|RedirectResponse $fallback)
    {
        if ($request->wantsJson()) {
            return response()->json($payload);
        }

        return $fallback;
    }
}
