<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemCommission;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SystemCommissionController extends Controller
{
    public function index(): View
    {
        $commissions = SystemCommission::with('agent')
            ->orderByRaw('user_id IS NOT NULL DESC')
            ->orderBy('user_id')
            ->orderBy('listing_type')
            ->get();

        $agents = User::query()
            ->where('role', 'agent')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.commissions.index', compact('commissions', 'agents'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        SystemCommission::create($data);

        return redirect()
            ->route('admin.commissions.index')
            ->with('success', 'Comisión registrada correctamente.');
    }

    public function edit(SystemCommission $commission): View
    {
        $agents = User::query()
            ->where('role', 'agent')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.commissions.edit', compact('commission', 'agents'));
    }

    public function update(Request $request, SystemCommission $commission): RedirectResponse
    {
        $data = $this->validateData($request, $commission->id);

        $commission->update($data);

        return redirect()
            ->route('admin.commissions.index')
            ->with('success', 'Comisión actualizada correctamente.');
    }

    public function destroy(SystemCommission $commission): RedirectResponse
    {
        $commission->delete();

        return redirect()
            ->route('admin.commissions.index')
            ->with('success', 'Comisión eliminada.');
    }

    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        $listingTypes = ['sale', 'rent', 'both'];

        $rules = [
            'user_id' => ['nullable', 'exists:users,id'],
            'listing_type' => ['required', 'in:' . implode(',', $listingTypes)],
            'percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'customer_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'effective_from' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:255'],
        ];

        $data = $request->validate($rules);

        $query = SystemCommission::query()
            ->where('listing_type', $data['listing_type'])
            ->where(function ($q) use ($data) {
                if (!empty($data['user_id'])) {
                    $q->where('user_id', $data['user_id']);
                } else {
                    $q->whereNull('user_id');
                }
            });

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        if ($query->exists()) {
            return back()->withErrors([
                'listing_type' => 'Ya existe una comisión configurada para este agente y tipo de listado.',
            ])->withInput();
        }

        return $data;
    }
}
