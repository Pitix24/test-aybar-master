<?php

use App\Http\Controllers\CavaliController;
use App\Http\Controllers\SlinController;
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

Route::get('/slin/comprobante/ver', [SlinController::class, 'verComprobante'])->name('slin.comprobante.ver');
Route::get('/cavali/constancia/ver/{numeroLetra}', [CavaliController::class, 'verLetra'])->name('cavali.constancia.ver');
