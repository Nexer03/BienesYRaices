<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule; // Import Rule for unique validation

class AdminUserController extends Controller
{
    /**
     * Display a listing of the users with filtering.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filters
        if ($request->filled('name')) { $query->where('name', 'like', '%' . $request->input('name') . '%'); }
        if ($request->filled('email')) { $query->where('email', 'like', '%' . $request->input('email') . '%'); }
        if ($request->filled('role') && in_array($request->input('role'), ['admin', 'agent', 'client'])) { $query->where('role', $request->input('role')); }

        $users = $query->latest()->paginate(15)->withQueryString();

        // AJAX Check for live filtering
        if ($request->ajax()) {
            return view('admin.users._table_body', compact('users'))->render();
        }

        // Normal page load
        return view('admin.users.index', [
            'users' => $users,
            'filters' => $request->only(['name', 'email', 'role'])
        ]);
    }

    /**
     * Show the form for creating a new user (Optional).
     * Admins might create users directly.
     */
    public function create()
    {
        // return view('admin.users.create'); // Uncomment if you need an admin create form
        abort(404); // Or disable if admins don't create users this way
    }

    /**
     * Store a newly created user in storage (Optional).
     */
    public function store(Request $request)
    {
        // Add validation similar to update if you implement create()
        // User::create(...);
        // return redirect()->route('admin.users.index')->with('success', 'Usuario creado.');
        abort(404);
    }

    /**
     * Display the specified user (Optional).
     * Often not needed if main info is in the index table.
     */
    public function show(User $user) // Use Route Model Binding
    {
        // return view('admin.users.show', compact('user'));
        abort(404);
    }

    /**
     * Show the form for editing the specified user (Optional).
     * We are using a modal, so this specific route might not be directly used,
     * but it's good practice for RESTful structure.
     */
    public function edit(User $user) // Use Route Model Binding
    {
         // This might just redirect back to index if using only modals
         // or could show a dedicated edit page if preferred.
         // return view('admin.users.edit', compact('user'));
         return redirect()->route('admin.users.index'); // Redirect if using modal only
    }

    /**
     * Update the specified user in storage. Handles AJAX modal form.
     */
    public function update(Request $request, User $user) // Use Route Model Binding
    {
        // Prevent non-admins from making others admin or making themselves admin
        if (Auth::user()->role !== 'admin' && $request->role === 'admin') {
             return response()->json(['success' => false, 'message' => 'No tienes permiso para asignar el rol de administrador.'], 403);
        }
         // Prevent users from making themselves admin unless they already are (redundant check, good safeguard)
         if (Auth::id() === $user->id && $request->role === 'admin' && Auth::user()->role !== 'admin') {
              return response()->json(['success' => false, 'message' => 'No puedes asignarte el rol de administrador.'], 403);
         }


        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id), // Correct unique validation on update
            ],
            'role' => 'required|in:admin,agent,client',
        ]);

        // Prevent deleting the last admin (important safeguard!)
        if ($user->role === 'admin' && User::where('role', 'admin')->count() === 1 && $request->role !== 'admin') {
             return response()->json(['success' => false, 'message' => 'No puedes eliminar el rol del último administrador.'], 422);
        }


        $user->update($validated);

        // Respond based on request type
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Usuario actualizado con éxito.']);
        }

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado con éxito.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user) // Use Route Model Binding
    {
         // Basic authorization - Ensure logged-in user is an admin
         if (Auth::user()->role !== 'admin') {
             abort(403, 'Acción no autorizada.');
         }

        // Prevent deleting oneself
        if (Auth::id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'No puedes eliminar tu propia cuenta de administrador.');
        }

        // Prevent deleting the last admin
         if ($user->role === 'admin' && User::where('role', 'admin')->count() === 1) {
             return redirect()->route('admin.users.index')->with('error', 'No puedes eliminar al último administrador.');
         }

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($user) {
                // 1. Eliminar preferencias
                $user->preferences()->delete();

                // 2. Desvincular favoritos
                $user->favoriteProperties()->detach();

                // 3. Eliminar reseñas hechas por el usuario
                $user->reviews()->delete();

                // 4. Eliminar criterios de alerta
                $user->alertCriteria()->delete();

                // 5. Eliminar propiedades (si es agente)
                // Iteramos para que se disparen eventos de modelo si los hubiera (ej. borrar imágenes)
                foreach($user->properties as $property) {
                    $property->delete();
                }

                // 6. Eliminar visitas (como cliente y como agente)
                $user->visitsAsClient()->delete();
                $user->visitsAsAgent()->delete();

                // 7. Finalmente eliminar el usuario
                $user->delete();
            });

            return redirect()->route('admin.users.index')->with('success', 'Usuario y todos sus datos asociados eliminados exitosamente.');

        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')->with('error', 'Ocurrió un error al eliminar el usuario: ' . $e->getMessage());
        }
    }
}
