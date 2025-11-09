<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AgentApplication;

class AgentApplicationController extends Controller
{
    public function create()
    {
        return view('agent.apply');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rfc' => 'required|regex:/^[A-Z&Ñ]{3,4}[0-9]{6}[A-Z0-9]{3}$/',
            'curp' => 'required|regex:/^[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9A-Z]{2}$/',
        ]);


        $validated['rfc'] = strtoupper($validated['rfc']);
        $validated['curp'] = strtoupper($validated['curp']);

        // Crear o actualizar la solicitud del usuario
        AgentApplication::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'rfc' => $validated['rfc'],
                'curp' => $validated['curp'],
                'status' => AgentApplication::STATUS_PENDING,
                'rejection_reason' => null,
            ]
        );

        return redirect()
            ->route('agent.view')
            ->with('success', 'Tu solicitud para convertirte en agente ha sido enviada y está pendiente de revisión.');
    }
}
