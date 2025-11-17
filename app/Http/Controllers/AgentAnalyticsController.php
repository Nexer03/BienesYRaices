<?php

namespace App\Http\Controllers;

use App\Models\Property;
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

        // 3) Contadores por estado (usamos un rango más amplio fijo de 90 días para tener contexto)
        $since90 = Carbon::now()->subDays(90);
        $visitsBase = Visit::where('agent_id', $agentId)->where('visit_date', '>=', $since90);
        $visits = [
            'pending'   => (clone $visitsBase)->where('status', 'pending')->count(),
            'confirmed' => (clone $visitsBase)->where('status', 'confirmed')->count(),
            'completed' => (clone $visitsBase)->where('status', 'completed')->count(),
            'cancelled' => (clone $visitsBase)->where('status', 'cancelled')->count(),
            'total'     => (clone $visitsBase)->count(),
        ];

        // 3b) Ventas registradas desde visitas
        $salesQuery = Visit::where('agent_id', $agentId)
            ->whereNotNull('sale_recorded_at');

        $sales = [
            'count'           => (clone $salesQuery)->count(),
            'amount'          => (float) (clone $salesQuery)->sum('sale_price'),
            'commission'      => (float) (clone $salesQuery)->sum('commission_amount'),
            'commission_paid' => (float) (clone $salesQuery)->whereNotNull('commission_paid_at')->sum('commission_amount'),
        ];

        // 4) Serie diaria dentro del rango seleccionado
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

        // 5) Top 5 propiedades por visitas en el rango seleccionado
        $topProps = Visit::selectRaw('property_id, COUNT(*) as total')
            ->where('agent_id', $agentId)
            ->whereBetween('visit_date', [$from, $to])
            ->groupBy('property_id')
            ->orderByDesc('total')
            ->with('property:id,title') // asume relación Visit->property
            ->limit(5)
            ->get();

        return view('agent.analytics.index', compact(
            'totalProps','saleProps','rentProps','visits','labels','values','from','to','topProps','sales'
        ));
    }
}
