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

        // Crear la solicitud
        AgentApplication::create([
            'user_id' => auth()->id(),
            'rfc' => $validated['rfc'],
            'curp' => $validated['curp']
        ]);
            // Actualizar el rol del usuario a AGENTE XD
         auth()->user()->update([
            'role' => 'agent'
        ]);

         return redirect()->route('agent.home')->with('success', 'Felicitaciones! Ahora eres un agente registrado.');
    }
}
