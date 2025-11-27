<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Visit;

class VisitPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_agent ?? true; // ajusta a tu flag/rol real
    }

    public function view(User $user, Visit $visit): bool
    {
        return $visit->agent_id === $user->id || $user->is_admin ?? false;
    }

    public function create(User $user): bool
    {
        return $user->is_agent ?? true;
    }

    public function update(User $user, Visit $visit): bool
    {
        return $visit->agent_id === $user->id || $user->is_admin ?? false;
    }

    public function delete(User $user, Visit $visit): bool
    {
        return $visit->agent_id === $user->id || $user->is_admin ?? false;
    }
}
