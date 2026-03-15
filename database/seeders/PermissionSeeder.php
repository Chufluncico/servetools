<?php

namespace Database\Seeders;

//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [

            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',

        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        $superadmin = Role::firstOrCreate(['name' => 'superadmin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $tecnico = Role::firstOrCreate(['name' => 'tecnico']);
        $informatica = Role::firstOrCreate(['name' => 'informatica']);

        $superadmin->givePermissionTo(Permission::all());


        // ===== USUARIO SUPERADMIN =====
        $user = User::firstOrCreate(
            ['email' => 'chuflun@chufluncico.com'],
            [
                'name' => 'Chuflun',
                'password' => Hash::make('Jarritas1981'),
            ]
        );
        $user->assignRole($superadmin);


    }





}
