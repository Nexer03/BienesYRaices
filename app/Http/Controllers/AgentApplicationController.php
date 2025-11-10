<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AgentApplication;
use Illuminate\Support\Facades\Auth;

class AgentApplicationController extends Controller
{
    
    /**
     * Guarda la solicitud para convertirse en agente.
     */
    public function store(Request $request)
    {
        // Validación de datos
        $request->validate([
            'rfc' => 'required|string|max:20',
            'curp' => 'required|string|max:20',
            'ine_front' => 'required|image|mimes:jpg,jpeg,png|max:4096',
            'ine_back'  => 'required|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        // Verificar si el usuario ya tiene una solicitud pendiente o aprobada
        $existing = AgentApplication::where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Ya tienes una solicitud en proceso o aprobada.');
        }

        // Guardar las imágenes en storage/app/public/ine_photos
        $frontPath = $request->file('ine_front')->store('ine_photos', 'public');
        $backPath  = $request->file('ine_back')->store('ine_photos', 'public');

        // Crear la solicitud del agente
        AgentApplication::create([
            'user_id'   => Auth::id(),
            'rfc'       => $request->rfc,
            'curp'      => $request->curp,
            'ine_front' => $frontPath,
            'ine_back'  => $backPath,
            'status'    => 'pending',
        ]);

        return redirect()->route('dashboard')->with(
            'success',
            'Tu solicitud ha sido enviada correctamente. En breve será revisada por un administrador.'
        );
    }
}
