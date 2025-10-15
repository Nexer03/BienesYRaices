<?php

namespace App\Http\Controllers;

use App\Models\Property; // <-- AÑADIR ESTA LÍNEA
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- AÑADIR ESTA LÍNEA

class AgentDashboardController extends Controller
{
    public function index()
    {
        $agentId = Auth::id();

        // Buscamos solo las propiedades cuyo user_id coincida con el del agente
        $myProperties = Property::where('user_id', $agentId)
                                ->with('images')
                                ->get();

        return view('agent.dashboard', ['properties' => $myProperties]);
    }
}
