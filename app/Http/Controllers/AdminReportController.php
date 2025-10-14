<?php

namespace App\Http\Controllers;

// use App\Http\Controllers\UserController; <-- ELIMINAR ESTA LÍNEA
use App\Models\User;
use App\Models\Property;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    //
    // En un nuevo AdminReportController.php

public function salesReport()
{
    // Contar propiedades vendidas por cada agente
    $salesByAgent = User::where('role', 'agent')
        ->withCount(['properties' => function ($query) {
            $query->where('status', 'sold');
        }])
        ->get();

    // Calcular el valor total de las propiedades vendidas
    $totalValueSold = Property::where('status', 'sold')->sum('price');

    return view('admin.reports.sales', [
        'salesByAgent' => $salesByAgent,
        'totalValueSold' => $totalValueSold
    ]);
}
}
