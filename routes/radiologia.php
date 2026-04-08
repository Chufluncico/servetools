<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Modalidad;
use Spatie\Permission\Models\Role;


Route::middleware(['auth'])->group(function () {

    Route::redirect('radiology', 'radiology/modalities');

    Route::livewire('radiology/modalities', 'pages::radiology.index')
        ->can('viewAny', Modalidad::class)
        ->name('radiology.modalities');

});

