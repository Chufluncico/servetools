<?php

namespace App\Actions\User;

use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class UpdateUserProfile
{
    use ProfileValidationRules;

    public function updateOld(User $user, array $input): void
    {
        Validator::make($input, [
            ...$this->profileRules($user->id),
            'roles' => ['array'],
            'roles.*' => ['string', 'exists:roles,name'],
        ])->validate();

        // Protección: no quitar el rol al último superadmin
        if ($user->hasRole('superadmin') && !in_array('superadmin', $input['roles'] ?? [])) {

            // Protección: no quitarse su propio rol
            if ($user->id === auth()->id()) {
                throw ValidationException::withMessages([
                    'roles' => 'No puedes quitarte tu propio rol superadmin.',
                ]);
            }

            $superadmins = User::role('superadmin')
                ->withoutTrashed()
                ->count();

            if ($superadmins <= 1) {
                throw ValidationException::withMessages([
                    'roles' => 'No se puede quitar el rol al último superadmin del sistema.',
                ]);
            }
        }

        $user->update([
            'name' => $input['name'],
            'email' => $input['email'],
        ]);

        if (isset($input['roles'])) {
            $user->syncRoles($input['roles']);
        }
    }


    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            ...$this->profileRules($user->id),
            'roles' => ['array'],
            'roles.*' => ['string', 'exists:roles,name'],
        ])->validate();

        $roles = $input['roles'] ?? [];

        // Protección: no quitar el rol al último superadmin
        if ($user->hasRole('superadmin') && !in_array('superadmin', $roles)) {

            // Protección: no quitarse su propio rol
            if ($user->id === auth()->id()) {
                throw ValidationException::withMessages([
                    'roles' => 'No puedes quitarte tu propio rol superadmin.',
                ]);
            }

            $superadmins = User::role('superadmin')
                ->withoutTrashed()
                ->count();

            if ($superadmins <= 1) {
                throw ValidationException::withMessages([
                    'roles' => 'No se puede quitar el rol al último superadmin del sistema.',
                ]);
            }
        }

        $user->update([
            'name' => $input['name'],
            'email' => $input['email'],
        ]);

        $user->syncRoles($roles);
    }


}