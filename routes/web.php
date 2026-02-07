<?php

use App\Livewire\Web\Sesion\ClienteRegistrarLivewire;
use Illuminate\Support\Facades\Route;

/**
 * Rutas Públicas
 */
Route::get('/', function () {
    return redirect()->route('login');
})->middleware(['web', 'redirect.by.role'])->name('home');

// Registro de clientes (Livewire con búsqueda DNI)
Route::get('/registrar', ClienteRegistrarLivewire::class)->name('registrar.cliente');