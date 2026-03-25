<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Volkswagen;
use Illuminate\Auth\Access\Response;

class VolkswagenPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Volkswagen $volkswagen): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Volkswagen $volkswagen): bool
    {
        return $user->id === $volkswagen->user_id || $user->is_admin;
    }

    public function delete(User $user, Volkswagen $volkswagen): bool
    {
        return $user->id === $volkswagen->user_id || $user->is_admin;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Volkswagen $volkswagen): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Volkswagen $volkswagen): bool
    {
        return false;
    }
}
