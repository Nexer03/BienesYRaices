<?php

namespace App\Http\Controllers;

use App\Exports\SalesAndRentReportExport;
use App\Models\Property;
use App\Models\PropertyReservation;
use App\Models\User;
use App\Models\Visit;
use App\Services\CommissionService;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    public function salesReport(Request $request): View
    {
        $filters = $this->buildFilters($request);

        // Catálogos para selects
        $agentsOptions = User::where('role', 'agent')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $citiesOptions = Property::query()
            ->whereNotNull('city')
            ->select('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        $data = $this->buildSalesAndRentData($filters);

        return view('admin.reports.sales', [
            ...$data,
            'filters'       => $filters,
            'agentsOptions' => $agentsOptions,
            'citiesOptions' => $citiesOptions,
        ]);
    }

    public function visitsReport(): View
    {
        $totalVisits = Visit::count();

        $visitsByStatus = Visit::select('status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();

        $visitsByAgent = Visit::select('agent_id')
            ->selectRaw('COUNT(*) as total')
            ->with('agent')
            ->whereNotNull('agent_id')
            ->groupBy('agent_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        $visitsByMonth = Visit::orderBy('visit_date')
            ->get()
            ->groupBy(fn (Visit $visit) => optional($visit->visit_date)->format('Y-m'))
            ->map(fn ($group, $month) => (object) [
                'month' => $month ?? now()->format('Y-m'),
                'total' => $group->count(),
            ])
            ->values();

        $upcomingVisitsQuery = Visit::whereDate('visit_date', '>=', now()->startOfDay());

        $upcomingVisits = (clone $upcomingVisitsQuery)
            ->with(['property', 'agent', 'client'])
            ->orderBy('visit_date')
            ->take(5)
            ->get();

        return view('admin.reports.visits', [
            'totalVisits'         => $totalVisits,
            'visitsByStatus'      => $visitsByStatus,
            'visitsByAgent'       => $visitsByAgent,
            'visitsByMonth'       => $visitsByMonth,
            'upcomingVisits'      => $upcomingVisits,
            'upcomingVisitsCount' => $upcomingVisitsQuery->count(),
        ]);
    }

    public function exportPropertyReport(Request $request): BinaryFileResponse
    {
        $filters = $this->buildFilters($request);
        $data    = $this->buildSalesAndRentData($filters);

        $fileName = 'reporte_ventas_rentas_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new SalesAndRentReportExport($filters, $data), $fileName);
    }

    private function buildFilters(Request $request): array
    {
        return [
            'from'  => $request->date('from'),
            'to'    => $request->date('to'),
            'agent' => $request->integer('agent') ?: null,
            'city'  => $request->string('city')->trim()->toString() ?: null,
        ];
    }

    private function buildSalesAndRentData(array $filters): array
    {
        $commissionService = app(CommissionService::class);

        // ====== Ventas (inmuebles vendidos) ======
        $soldQuery = Property::with('user')
            ->whereNotNull('sold_at');

        if ($filters['agent']) {
            $soldQuery->where('user_id', $filters['agent']);
        }
        if ($filters['city']) {
            $soldQuery->where('city', $filters['city']);
        }
        if ($filters['from']) {
            $soldQuery->whereDate('sold_at', '>=', $filters['from']);
        }
        if ($filters['to']) {
            $soldQuery->whereDate('sold_at', '<=', $filters['to']);
        }

        $soldProperties = $soldQuery->get();
        $totalValueSold = $soldProperties->sum('price');

        $salesByAgent = User::where('role', 'agent')
            ->when($filters['agent'], fn($q) => $q->where('id', $filters['agent']))
            ->withCount(['properties' => function ($q) use ($filters) {
                $q->whereNotNull('sold_at');
                if ($filters['city']) {
                    $q->where('city', $filters['city']);
                }
                if ($filters['from']) {
                    $q->whereDate('sold_at', '>=', $filters['from']);
                }
                if ($filters['to']) {
                    $q->whereDate('sold_at', '<=', $filters['to']);
                }
            }])
            ->get();

        $salesCommissionByAgent = $soldProperties
            ->groupBy('user_id')
            ->map(function ($properties) use ($commissionService) {
                $agent   = $properties->first()->user;
                $agentId = $agent?->id ?? 0;
                $totalSold = (float) $properties->sum('price');

                return (object) [
                    'agent'      => $agent,
                    'total_sold' => $totalSold,
                    'rate'       => $commissionService->rateFor($agentId, 'sale'),
                    'commission' => (float) $commissionService->calculate($agentId, 'sale', $totalSold),
                ];
            })->values();

        $salesByAgent = $salesByAgent->map(function ($agent) use ($salesCommissionByAgent) {
            $cd = $salesCommissionByAgent->first(fn($row) => optional($row->agent)->id === $agent->id);
            $agent->total_sales_amount = (float) ($cd->total_sold ?? 0);
            $agent->commission_rate    = $cd->rate ?? null;
            $agent->commission_total   = (float) ($cd->commission ?? 0);
            return $agent;
        });

        $filterAgentName = $filters['agent']
            ? optional($salesByAgent->first(fn ($agent) => $agent->id === $filters['agent']))->name
            : null;

        // ====== Rentas (reservas pagadas/confirmadas) ======
        $rentalBaseQuery = PropertyReservation::query()
            ->whereIn('property_reservations.status', ['confirmed', 'paid', 'completed'])
            ->where('property_reservations.payment_status', 'paid');

        $rentalFiltered = (clone $rentalBaseQuery)
            ->join('properties', 'property_reservations.property_id', '=', 'properties.id');

        if ($filters['agent']) {
            $rentalFiltered->where('properties.user_id', $filters['agent']);
        }
        if ($filters['city']) {
            $rentalFiltered->where('properties.city', $filters['city']);
        }
        if ($filters['from']) {
            $rentalFiltered->whereDate('property_reservations.created_at', '>=', $filters['from']);
        }
        if ($filters['to']) {
            $rentalFiltered->whereDate('property_reservations.created_at', '<=', $filters['to']);
        }

        $totalRentalRevenue      = (clone $rentalFiltered)->sum('property_reservations.total_price');
        $totalRentalReservations = (clone $rentalFiltered)->count();

        $rentalsByAgent = (clone $rentalFiltered)
            ->selectRaw('properties.user_id as agent_id, COUNT(*) as total_reservations, SUM(property_reservations.total_price) as total_revenue')
            ->groupBy('properties.user_id')
            ->get();

        $agents = User::whereIn('id', $rentalsByAgent->pluck('agent_id')->filter()->unique())
            ->get()
            ->keyBy('id');

        $rentalsByAgent = $rentalsByAgent->map(function ($row) use ($agents, $commissionService) {
            $agent   = $agents->get($row->agent_id);
            $agentId = $agent?->id ?? 0;
            $rate    = $commissionService->rateFor($agentId, 'rent');

            $row->agent            = $agent;
            $row->commission_rate  = $rate ?? null;
            $row->commission_total = (float) $commissionService->calculate($agentId, 'rent', (float) $row->total_revenue);

            return $row;
        })->filter(fn ($row) => $row->agent !== null);

        $rentalCommissionTotal = $rentalsByAgent->sum('commission_total');
        $salesCommissionTotal  = $salesCommissionByAgent->sum('commission');

        // ====== Comparativa por zona ======
        [$salesDateFilter, $salesDateBindings] = $this->dateFilterSql('sold_at', $filters);
        [$rentalDateFilter, $rentalDateBindings] = $this->dateFilterSql('created_at', $filters);

        $zoneQuery = Property::query()
            ->select('city')
            ->whereNotNull('city')
            ->selectRaw('COUNT(*) as total_properties')
            ->selectRaw(
                "SUM(CASE WHEN listing_type = 'sale' AND sold_at IS NOT NULL{$salesDateFilter} THEN 1 ELSE 0 END) as sold_count",
                $salesDateBindings
            )
            ->selectRaw(
                "SUM(CASE WHEN listing_type = 'rent' AND status = 'rented'{$rentalDateFilter} THEN 1 ELSE 0 END) as rented_count",
                $rentalDateBindings
            )
            ->selectRaw("SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available_count")
            ->selectRaw(
                "AVG(CASE WHEN listing_type = 'sale' AND sold_at IS NOT NULL{$salesDateFilter} THEN price END) as average_sale_price",
                $salesDateBindings
            )
            ->selectRaw(
                "AVG(CASE WHEN listing_type = 'rent'{$rentalDateFilter} THEN price END) as average_rent_price",
                $rentalDateBindings
            );

        if ($filters['agent']) {
            $zoneQuery->where('user_id', $filters['agent']);
        }
        if ($filters['city']) {
            $zoneQuery->where('city', $filters['city']);
        }

        $zoneComparison = $zoneQuery
            ->groupBy('city')
            ->orderByDesc('total_properties')
            ->get();

        return [
            'salesByAgent'            => $salesByAgent,
            'filterAgentName'         => $filterAgentName,
            'totalSoldProperties'     => $salesByAgent->sum('properties_count'),
            'totalValueSold'          => $totalValueSold,
            'rentalsByAgent'          => $rentalsByAgent,
            'totalRentalRevenue'      => $totalRentalRevenue,
            'totalRentalReservations' => $totalRentalReservations,
            'zoneComparison'          => $zoneComparison,
            'salesCommissionByAgent'  => $salesCommissionByAgent,
            'salesCommissionTotal'    => $salesCommissionTotal,
            'rentalCommissionTotal'   => $rentalCommissionTotal,
        ];
    }

    private function dateFilterSql(string $column, array $filters): array
    {
        $sql      = '';
        $bindings = [];

        if ($filters['from']) {
            $sql        .= " AND DATE({$column}) >= ?";
            $bindings[] = $filters['from'];
        }

        if ($filters['to']) {
            $sql        .= " AND DATE({$column}) <= ?";
            $bindings[] = $filters['to'];
        }

        return [$sql, $bindings];
    }
}
