<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\PropertyReservation;



class VisitController extends Controller
{
        public function myVisits(Request $request)
    {
        $clientId = $request->user()->id;

        $query = Visit::with(['property', 'agent'])
            ->where('client_id', $clientId);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $visits = $query->orderBy('visit_date', 'desc')->paginate(10)->withQueryString();

        $reservations = PropertyReservation::with('property')
            ->where('user_id', $clientId)
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($res) {
                $res->nights = Carbon::parse($res->start_date)
                    ->diffInDays(Carbon::parse($res->end_date));
                return $res;
            });

        return view('visits.myVisits', compact('visits', 'reservations'))
            ->with('activeTab', 'visits');
    }

    /**
     * GET /agent/visits
     * Listado de visitas del agente autenticado.
     */
    public function index(Request $request)
    {
        $agentId = $request->user()->id;

        $visits = Visit::with(['property', 'client'])
            ->where('agent_id', $agentId)
            ->orderBy('visit_date', 'asc')
            ->paginate(10);

        // Propiedades del agente para filtros/acciones rápidas
        $properties = Property::where('user_id', $agentId)
            ->where('listing_type', 'sale')   // 👈 solo venta
            ->pluck('title', 'id');


        // Clientes (ajusta el criterio si tu modelo de roles es distinto)
        $clients = User::when(
                schemaHasColumn('users', 'role'),
                fn($q) => $q->where('role', 'client'),
                fn($q) => $q->where('id', '!=', $agentId)
            )->pluck('name', 'id');

        return view('agent.visits.index', compact('visits', 'properties', 'clients'));
    }

    public function feed(Request $request)
{
    $agentId = $request->user()->id;

    $visits = Visit::with(['property', 'client'])
        ->where('agent_id', $agentId)
        ->orderBy('visit_date', 'asc')
        ->get();

    $events = $visits->map(function ($v) {
        $start = \Carbon\Carbon::parse($v->visit_date);
        $end   = (clone $start)->addMinutes(60);

        $colors = [
            'pending'   => ['#ffc107', '#fff3cd'], // amarillos
            'confirmed' => ['#0d6efd', '#cfe2ff'], // azules
            'completed' => ['#198754', '#d1e7dd'], // verdes
            'cancelled' => ['#dc3545', '#f8d7da'], // rojos
        ];
        [$border, $bg] = $colors[$v->status] ?? ['#6c757d', '#e2e3e5'];

        return [
            'id'    => $v->id,
            'title' => $v->property?->title ?? 'Visita',
            'start' => $start->toIso8601String(),
            'end'   => $end->toIso8601String(),
            'url'   => route('agent.visits.edit', $v),
            'backgroundColor' => $bg,
            'borderColor'     => $border,
            'textColor'       => '#000',
            'extendedProps'   => [
                'status'  => $v->status,
                'client'  => $v->client?->name,
            ],
        ];
    });

    return response()->json($events);
}



    /**
     * GET /agent/visits/create
     * Formulario de creación.
     */
    public function create(Request $request)
    {
        $agentId = $request->user()->id;

        $properties = Property::where('user_id', $agentId)
            ->where('listing_type', 'sale')   // 👈 solo venta
            ->pluck('title', 'id');


        $clients = User::when(
                schemaHasColumn('users', 'role'),
                fn($q) => $q->where('role', 'client'),
                fn($q) => $q->where('id', '!=', $agentId)
            )->pluck('name', 'id');

        return view('agent.visits.create', compact('properties', 'clients'));
    }

    /**
     * POST /agent/visits
     * Guarda una nueva visita del agente autenticado.
     */
    public function store(Request $request)
    {
        $agentId = $request->user()->id;

        $data = $request->validate([
            'property_id' => ['required', 'exists:properties,id'],
            'client_id'   => ['required', 'exists:users,id', Rule::notIn([$agentId])],
            'visit_date'  => ['required', 'date', 'after:now'],
            'notes'       => ['nullable', 'string', 'max:2000'],
        ], [
            'visit_date.after' => 'La fecha/hora debe ser futura.',
        ]);

        // Seguridad: la propiedad debe pertenecer al agente y ser de VENTA
        $owned = \App\Models\Property::where('id', $data['property_id'])
            ->where('user_id', $agentId)
            ->where('listing_type', 'sale')   // 👈 SOLO VENTA
            ->exists();

    abort_unless($owned, 403, 'Solo puedes agendar visitas para propiedades en venta que te pertenecen.');


        // Anti-solape (60 min por defecto)
        if ($this->hasOverlap($agentId, $data['visit_date'])) {
            return back()->withInput()->withErrors([
                'visit_date' => 'Ya existe otra visita del agente que se cruza con este horario.',
            ]);
        }

        $data['agent_id'] = $agentId;
        $data['status']   = 'pending';

        Visit::create($data);

        return redirect()->route('agent.visits.index')->with('success', 'Visita creada correctamente.');
    }


    /**
     * GET /agent/visits/{visit}/edit
     * Editar visita (solo del agente).
     */
    public function edit(Request $request, Visit $visit)
    {
        $this->authorizeAgent($request->user()->id, $visit);

        $agentId = $request->user()->id;

        $properties = Property::where('user_id', $agentId)
            ->where('listing_type', 'sale')   // 👈 solo venta
            ->pluck('title', 'id');


        $clients = User::when(
                schemaHasColumn('users', 'role'),
                fn($q) => $q->where('role', 'client'),
                fn($q) => $q->where('id', '!=', $agentId)
            )->pluck('name', 'id');

        return view('agent.visits.edit', compact('visit', 'properties', 'clients'));
    }

    /**
     * PUT /agent/visits/{visit}
     * Actualiza visita existente.
     */
    public function update(Request $request, Visit $visit)
    {
        $this->authorizeAgent($request->user()->id, $visit);

        $agentId = $request->user()->id;

        $data = $request->validate([
            'property_id' => ['required', 'exists:properties,id'],
            'client_id'   => ['required', 'exists:users,id', Rule::notIn([$agentId])],
            'visit_date'  => ['required', 'date', 'after:now'],
            'status'      => ['required', Rule::in(['pending','confirmed','completed','cancelled'])],
            'notes'       => ['nullable', 'string', 'max:2000'],
        ], [
            'visit_date.after' => 'La fecha/hora debe ser futura.',
        ]);

        // Propiedad debe seguir perteneciendo al agente y ser de VENTA
        $owned = \App\Models\Property::where('id', $data['property_id'])
            ->where('user_id', $agentId)
            ->where('listing_type', 'sale')   // 👈 SOLO VENTA
            ->exists();

        abort_unless($owned, 403, 'No puedes reasignar a una propiedad que no sea de venta y tuya.');


        if ($this->hasOverlap($agentId, $data['visit_date'], $visit->id)) {
            return back()->withInput()->withErrors([
                'visit_date' => 'Ya existe otra visita del agente que se cruza con este horario.',
            ]);
        }

        $visit->update($data);

        return redirect()->route('agent.visits.index')->with('success', 'Visita actualizada.');
    }

    /**
     * DELETE /agent/visits/{visit}
     * Elimina visita del agente.
     */
    public function destroy(Request $request, Visit $visit)
    {
        $this->authorizeAgent($request->user()->id, $visit);

        $visit->delete();

        return back()->with('success', 'Visita eliminada.');
    }

    /**
     * PATCH /agent/visits/{visit}/status
     * Actualiza solo el estado.
     */
    public function updateStatus(Request $request, Visit $visit)
    {
        $this->authorizeAgent($request->user()->id, $visit);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending','confirmed','completed','cancelled'])],
        ]);

        $visit->update(['status' => $validated['status']]);

        return back()->with('success', 'Estado actualizado.');
    }

    /**
     * Verifica que la visita pertenezca al agente autenticado.
     */
    protected function authorizeAgent(int $agentId, Visit $visit): void
    {
        abort_unless($visit->agent_id === $agentId, 403, 'No autorizado para esta visita.');
    }

    /**
     * Detección de solapes considerando una duración fija (60 min).
     * Se considera choque si (A.start < B.end) y (B.start < A.end).
     */
    protected function hasOverlap(int $agentId, string $visitDate, ?int $ignoreVisitId = null, int $durationMinutes = 60): bool
    {
        $start = Carbon::parse($visitDate);
        $end   = (clone $start)->addMinutes($durationMinutes);

        return Visit::where('agent_id', $agentId)
            ->when($ignoreVisitId, fn($q) => $q->where('id', '!=', $ignoreVisitId))
            ->where(function ($q) use ($start, $end, $durationMinutes) {
                // otra visita con inicio < fin actual
                $q->where('visit_date', '<', $end)
                  // y su inicio + duración > inicio actual (aprox.)
                  ->where('visit_date', '>=', $start->copy()->subMinutes($durationMinutes));
            })
            ->exists();
    }
}

/**
 * Helper: verifica si una columna existe (evita petar si no tienes 'role' en users).
 */
if (! function_exists('schemaHasColumn')) {
    function schemaHasColumn(string $table, string $column): bool
    {
        try {
            return \Illuminate\Support\Facades\Schema::hasColumn($table, $column);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
