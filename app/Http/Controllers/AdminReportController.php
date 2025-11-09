<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Contracts\View\View;

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

        return view('admin.reports.sales', [
            'salesByAgent' => $salesByAgent,
            'totalValueSold' => $totalValueSold,
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
}
