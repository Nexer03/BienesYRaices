<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgentApplication;
use App\Notifications\AgentApplicationApproved;
use App\Notifications\AgentApplicationRejected;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgentApplicationController extends Controller
{
    public function index(Request $request)
    {
        $applications = AgentApplication::with('user')
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where('status', $request->string('status'))
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        if ($request->wantsJson()) {
            return response()->json($applications);
        }

        return view('admin.agent-applications.index', [
            'applications' => $applications,
            'status' => $request->query('status', ''),
        ]);
    }

    public function show(AgentApplication $agentApplication)
    {
        $agentApplication->load('user');

        return view('admin.agent-applications.show', [
            'application' => $agentApplication,
        ]);
    }

    public function approve(AgentApplication $agentApplication): RedirectResponse
    {
        if ($agentApplication->status === AgentApplication::STATUS_APPROVED) {
            return back()->with('info', 'La solicitud ya fue aprobada previamente.');
        }

        DB::transaction(function () use ($agentApplication) {
            $agentApplication->update([
                'status' => AgentApplication::STATUS_APPROVED,
                'rejection_reason' => null,
            ]);

            $agentApplication->user->update(['role' => 'agent']);
            $agentApplication->user->notify(new AgentApplicationApproved($agentApplication));
        });

       return redirect()
            ->route('admin.agent-applications.index')
            ->with('success', 'Solicitud aprobada correctamente.');
    
    }

    public function reject(Request $request, AgentApplication $agentApplication): RedirectResponse
    {
        if ($agentApplication->status === AgentApplication::STATUS_APPROVED) {
            return back()->with('error', 'No puedes rechazar una solicitud que ya fue aprobada.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

            DB::transaction(function () use ($agentApplication, $validated) {
            $agentApplication->update([
                'status' => AgentApplication::STATUS_REJECTED,
                'rejection_reason' => $validated['rejection_reason'],
            ]);

            $agentApplication->user->notify(new AgentApplicationRejected($agentApplication));
        });

          return redirect()
            ->route('admin.agent-applications.index')
            ->with('success', 'Solicitud rechazada correctamente.');
    }
}
