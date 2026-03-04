# Una vez creado el proyecto con Laravel:

## Instalar Spatie
- composer require spatie/laravel-permission
- php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
- añadir al modelo User: 
	+ use Spatie\Permission\Traits\HasRoles;
	+ use HasRoles;
- php artisan make:seeder PermissionSeeder
- modificar el Seeder para crear unos cuantos roles y permisos
- desactivo el registro de usuarios desde config/fortify.php
- añadir los alias de spatie en bootstrap/app.php