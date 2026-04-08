<?php

namespace App\Policies;

use App\Models\Modalidad;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ModalidadPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('modalidades.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Modalidad $modalidad): bool
    {
        return $user->can('modalidades.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('modalidades.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Modalidad $modalidad): bool
    {
        return $user->can('modalidades.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Modalidad $modalidad): bool
    {
        return $user->can('modalidades.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Modalidad $modalidad): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Modalidad $modalidad): bool
    {
        return false;
    }
}
