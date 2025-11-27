<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyReservation;
use App\Models\Visit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class AgentAnalyticsController extends Controller
{
    public function index()
    {
        $agentId = Auth::id();

        // 1) Rango de fechas (por defecto últimos 30 días para gráfica y 90 para contadores)
        $fromReq = request('from');
        $toReq   = request('to');

        // Normaliza fechas
        $from = $fromReq ? Carbon::parse($fromReq)->startOfDay() : Carbon::now()->subDays(29)->startOfDay();
        $to   = $toReq   ? Carbon::parse($toReq)->endOfDay()     : Carbon::now()->endOfDay();

        // 2) Propiedades del agente
        $totalProps = Property::where('user_id', $agentId)->count();
        $saleProps  = Property::where('user_id', $agentId)->where('listing_type', 'sale')->count();
        $rentProps  = Property::where('user_id', $agentId)->where('listing_type', 'rent')->count();

        // 3) Contadores por estado de visitas (usamos un rango más amplio fijo de 90 días para tener contexto)
        $since90 = Carbon::now()->subDays(90);
        $visitsBase = Visit::where('agent_id', $agentId)->where('visit_date', '>=', $since90);
        $visits = [
            'pending'   => (clone $visitsBase)->where('status', 'pending')->count(),
            'confirmed' => (clone $visitsBase)->where('status', 'confirmed')->count(),
            'completed' => (clone $visitsBase)->where('status', 'completed')->count(),
            'cancelled' => (clone $visitsBase)->where('status', 'cancelled')->count(),
            'total'     => (clone $visitsBase)->count(),
        ];

        // 3.b) Contadores de reservaciones (mismo rango de 90 días)
        $reservationsBase = PropertyReservation::whereHas('property', function ($q) use ($agentId) {
                $q->where('user_id', $agentId);
            })
            ->where('start_date', '>=', $since90);

        $reservations = [
            'pending'   => (clone $reservationsBase)->where('status', 'pending')->count(),
            'confirmed' => (clone $reservationsBase)->where('status', 'confirmed')->count(),
            'completed' => (clone $reservationsBase)->where('status', 'completed')->count(),
            'cancelled' => (clone $reservationsBase)->where('status', 'cancelled')->count(),
            'total'     => (clone $reservationsBase)->count(),
        ];

        // 4) Serie diaria de visitas dentro del rango seleccionado
        $daily = Visit::selectRaw('DATE(visit_date) as d, COUNT(*) as c')
            ->where('agent_id', $agentId)
            ->whereBetween('visit_date', [$from, $to])
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('c', 'd');

        $labels = [];
        $values = [];
        $cursor = $from->copy();
        while ($cursor <= $to) {
            $day = $cursor->toDateString();
            $labels[] = $day;
            $values[] = (int) ($daily[$day] ?? 0);
            $cursor->addDay();
        }

        // 4.b) Serie diaria de reservaciones dentro del rango seleccionado
        $dailyReservations = PropertyReservation::selectRaw('DATE(start_date) as d, COUNT(*) as c')
            ->whereHas('property', function ($q) use ($agentId) {
                $q->where('user_id', $agentId);
            })
            ->whereBetween('start_date', [$from, $to])
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('c', 'd');

        $reservationLabels = [];
        $reservationValues = [];
        $cursor = $from->copy();
        while ($cursor <= $to) {
            $day = $cursor->toDateString();
            $reservationLabels[] = $day;
            $reservationValues[] = (int) ($dailyReservations[$day] ?? 0);
            $cursor->addDay();
        }

        // 5) Top 5 propiedades por visitas en el rango seleccionado
        $topProps = Visit::selectRaw('property_id, COUNT(*) as total')
            ->where('agent_id', $agentId)
            ->whereBetween('visit_date', [$from, $to])
            ->groupBy('property_id')
            ->orderByDesc('total')
            ->with('property:id,title') // asume relación Visit->property
            ->limit(5)
            ->get();

        // 5.b) Top 5 propiedades por reservaciones en el rango seleccionado
        $topReservationProps = PropertyReservation::selectRaw('property_id, COUNT(*) as total')
            ->whereHas('property', function ($q) use ($agentId) {
                $q->where('user_id', $agentId);
            })
            ->whereBetween('start_date', [$from, $to])
            ->groupBy('property_id')
            ->orderByDesc('total')
            ->with('property:id,title')
            ->limit(5)
            ->get();

        return view('agent.analytics.index', compact(
            'totalProps','saleProps','rentProps','visits','reservations','labels','values','from','to','topProps',
            'reservationLabels','reservationValues','topReservationProps'
        ));
    }
}
