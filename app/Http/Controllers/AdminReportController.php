<?php

namespace App\Http\Controllers;

use App\Exports\PropertyZoneComparisonExport;
use App\Models\Property;
use App\Models\PropertyReservation;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminReportController extends Controller
{
    public function salesReport(): View
    {
        $salesByAgent = User::where('role', 'agent')
            ->withCount(['properties' => function ($query) {
                $query->where('status', 'sold');
            }])
            ->get();

        $totalValueSold = Property::where('status', 'sold')->sum('price');

        $rentalBaseQuery = PropertyReservation::query()
            ->whereIn('property_reservations.status', ['confirmed', 'paid', 'completed'])
            ->where('property_reservations.payment_status', 'paid');

        $totalRentalRevenue = (clone $rentalBaseQuery)->sum('property_reservations.total_price');
        $totalRentalReservations = (clone $rentalBaseQuery)->count();

        $rentalsByAgent = (clone $rentalBaseQuery)
            ->join('properties', 'property_reservations.property_id', '=', 'properties.id')
            ->selectRaw('properties.user_id as agent_id, COUNT(*) as total_reservations, SUM(property_reservations.total_price) as total_revenue')
            ->groupBy('properties.user_id')
            ->get();

        $agents = User::whereIn('id', $rentalsByAgent->pluck('agent_id')->filter()->unique())
            ->get()
            ->keyBy('id');

        $rentalsByAgent = $rentalsByAgent->map(function ($row) use ($agents) {
            $row->agent = $agents->get($row->agent_id);
            return $row;
        })->filter(fn ($row) => $row->agent !== null);

        $zoneComparison = Property::query()
            ->select('city')
            ->whereNotNull('city')
            ->selectRaw('COUNT(*) as total_properties')
            ->selectRaw("SUM(CASE WHEN status = 'sold' THEN 1 ELSE 0 END) as sold_count")
            ->selectRaw("SUM(CASE WHEN status = 'rented' THEN 1 ELSE 0 END) as rented_count")
            ->selectRaw("SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available_count")
            ->selectRaw('AVG(price) as average_price')
            ->groupBy('city')
            ->orderByDesc('total_properties')
            ->get();

        return view('admin.reports.sales', [
            'salesByAgent' => $salesByAgent,
            'totalValueSold' => $totalValueSold,
            'rentalsByAgent' => $rentalsByAgent,
            'totalRentalRevenue' => $totalRentalRevenue,
            'totalRentalReservations' => $totalRentalReservations,
            'zoneComparison' => $zoneComparison,
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
            'totalVisits' => $totalVisits,
            'visitsByStatus' => $visitsByStatus,
            'visitsByAgent' => $visitsByAgent,
            'visitsByMonth' => $visitsByMonth,
            'upcomingVisits' => $upcomingVisits,
            'upcomingVisitsCount' => $upcomingVisitsQuery->count(),
        ]);
    }

    public function exportPropertyReport(): BinaryFileResponse
    {
        $fileName = 'reporte_propiedades_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new PropertyZoneComparisonExport(), $fileName);
    }
}
