<?php

namespace App\Actions\User;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;


class UpdateUserProfile
{
    use PasswordValidationRules, ProfileValidationRules;


    public function update()
    {
        $user = User::findOrFail($this->userId);

        $validated = $this->validate([
            ...$this->profileRules($user->id),
            'password' => $this->password
                ? $this->passwordRules()
                : ['nullable'],
            'roles' => ['array'],
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = $validated['password'];
        }

        $user->update($data);

        $user->syncRoles($this->roles);

        $this->dispatch('alert', type: 'success', message: 'Usuario actualizado.');
    }




    
}