<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    /**
     * Display the sales report view with aggregate information about sold properties.
     */
    public function salesReport(Request $request)
    {
        $agentId = $request->integer('agent_id');
        $dateFrom = $request->date('from');
        $dateTo = $request->date('to');

        $propertiesQuery = Property::query()
            ->with('user')
            ->where('status', 'sold');

        if ($agentId) {
            $propertiesQuery->where('user_id', $agentId);
        }

        if ($dateFrom) {
            $propertiesQuery->whereDate('updated_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $propertiesQuery->whereDate('updated_at', '<=', $dateTo);
        }

        $soldProperties = $propertiesQuery
            ->orderByDesc('updated_at')
            ->get();

        $totalValueSold = $soldProperties->sum('price');
        $totalSales = $soldProperties->count();
        $averageSalePrice = $totalSales > 0 ? $totalValueSold / $totalSales : 0;

        $salesByAgent = $soldProperties
            ->groupBy('user_id')
            ->map(function ($properties, $userId) {
                $agent = $properties->first()->user;

                return [
                    'agent_id' => $userId,
                    'agent_name' => $agent?->name ?? 'Sin asignar',
                    'properties_sold' => $properties->count(),
                    'total_value' => $properties->sum('price'),
                ];
            })
            ->sortByDesc('properties_sold')
            ->values();

        $agents = User::query()
            ->where('role', 'agent')
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('admin.reports.sales', [
            'soldProperties' => $soldProperties,
            'salesByAgent' => $salesByAgent,
            'totalValueSold' => $totalValueSold,
            'totalSales' => $totalSales,
            'averageSalePrice' => $averageSalePrice,
            'agents' => $agents,
            'filters' => [
                'agent_id' => $agentId,
                'from' => $dateFrom?->toDateString(),
                'to' => $dateTo?->toDateString(),
            ],
        ]);
    }
}
