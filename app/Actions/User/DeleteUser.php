<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;


class DeleteUser
{
    public function delete(User $user): void
    {
        // Protección: no eliminarse a sí mismo
        if ($user->id === auth()->id()) {
            throw ValidationException::withMessages([
                'user' => 'No puedes eliminar tu propio usuario.',
            ]);
        }

        // Protección: no eliminar el último superadmin
        if ($user->hasRole('superadmin')) {

            $superadmins = User::role('superadmin')
                ->withoutTrashed()
                ->count();

            if ($superadmins <= 1) {
                throw ValidationException::withMessages([
                    'user' => 'No se puede eliminar el último superadmin del sistema.',
                ]);
            }
        }

        $user->delete();
    }

    
}