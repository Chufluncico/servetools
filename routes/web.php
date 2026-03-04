<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Auth\Middleware\Authorize;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';


Route::middleware(['auth', 'verified', 'role:superadmin|admin|informatica'])->group(function () {

    Route::view('adminPanel', 'vistas.adminPanel.index')->name('adminPanel.index');

});