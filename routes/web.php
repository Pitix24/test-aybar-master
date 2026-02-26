<?php

use App\Http\Controllers\CavaliController;
use App\Http\Controllers\SlinController;
use App\Livewire\Public\EntregaFest\AsistenciaPublica;
use App\Livewire\Web\Sesion\ClienteRegistrarLivewire;
use App\Http\Controllers\Web\VerificationController;
use App\Http\Controllers\Web\ConsultaCodigoClienteController;
use Illuminate\Support\Facades\Route;

/**
 * Rutas Públicas
 */
Route::get('/', function () {
    return redirect()->route('login');
})->middleware(['web', 'redirect.by.role'])->name('home');

Route::get('/registrar', ClienteRegistrarLivewire::class)->name('registrar.cliente');

Route::post('/email/verification-notification', [VerificationController::class, 'send'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

Route::get('/slin/comprobante/ver', [SlinController::class, 'verComprobante'])->name('slin.comprobante.ver');
Route::get('/cavali/constancia/ver/{numeroLetra}', [CavaliController::class, 'verLetra'])->name('cavali.constancia.ver');
Route::get('/cavali/constancia/validar/{numeroLetra}', [CavaliController::class, 'validarLetra'])->name('cavali.constancia.validar');
Route::post('/consulta-codigo-cliente', [ConsultaCodigoClienteController::class, 'consultarClienteDbApi'])->name('consulta-codigo-cliente');

// Entrega Fest - Asistencia Pública
Route::get('/evento/{slug}/{id}', AsistenciaPublica::class)
    ->name('public.entrega-fest.asistencia');
