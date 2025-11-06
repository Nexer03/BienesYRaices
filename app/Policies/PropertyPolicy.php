<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;

class PropertyPolicy
{
    // Puede ver listados propios (opcional)
    public function viewAny(User $user): bool
    {
        return $user->role === 'agent' || $user->role === 'admin';
    }

    // Puede ver una propiedad (pública; ajusta si usas privadas)
    public function view(?User $user, Property $property): bool
    {
        return true;
    }

    // Crear (agentes/admin)
    public function create(User $user): bool
    {
        return $user->role === 'agent' || $user->role === 'admin';
    }

    // Actualizar (dueño o admin)
    public function update(User $user, Property $property): bool
    {
        return $user->id === $property->user_id || $user->role === 'admin';
    }

    // Eliminar (dueño o admin)
    public function delete(User $user, Property $property): bool
    {
        return $user->id === $property->user_id || $user->role === 'admin';
    }
}
