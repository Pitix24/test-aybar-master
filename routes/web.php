<?php

use App\Http\Controllers\CavaliController;
use App\Http\Controllers\SlinController;
use App\Livewire\Public\EntregaFest\PreInvitacionPropietario;
use App\Livewire\Public\EntregaFest\PreInvitacionCopropietario;
use App\Livewire\Public\EntregaFest\AsistenciaPublica;
use App\Livewire\Public\EntregaFest\AsistenciaPublicaCopropietario;
use App\Livewire\Public\EntregaFest\FirmaPublica;
use App\Livewire\Web\Sesion\ClienteRegistrarLivewire;
use App\Livewire\Web\LibroReclamacionLivewire;
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
Route::get('/libro-de-reclamaciones', LibroReclamacionLivewire::class)->name('libro-reclamaciones');

Route::post('/email/verification-notification', [VerificationController::class, 'send'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

Route::get('/slin/comprobante/ver', [SlinController::class, 'verComprobante'])->name('slin.comprobante.ver');
Route::get('/cavali/constancia/ver/{numeroLetra}', [CavaliController::class, 'verLetra'])->name('cavali.constancia.ver');
Route::get('/cavali/constancia/validar/{numeroLetra}', [CavaliController::class, 'validarLetra'])->name('cavali.constancia.validar');
Route::post('/consulta-codigo-cliente', [ConsultaCodigoClienteController::class, 'consultarClienteDbApi'])->name('consulta-codigo-cliente');

// Entrega Fest - Pre-Invitación (Titular)
Route::get('/pre-invitacion/{slug}/{id}', PreInvitacionPropietario::class)
    ->name('public.entrega-fest.pre-invitacion');

// Entrega Fest - Pre-Invitación (Copropietario)
Route::get('/pre-invitacion-copropietario/{slug}/{copropietarioId}', PreInvitacionCopropietario::class)
    ->name('public.entrega-fest.pre-invitacion.copropietario');

// Entrega Fest - Asistencia Pública (Titular)
Route::get('/asistencia/{slug}/{id}', AsistenciaPublica::class)
    ->name('public.entrega-fest.asistencia');

// Entrega Fest - Asistencia Pública (Copropietario)
Route::get('/asistencia-copropietario/{slug}/{copropietarioId}', AsistenciaPublicaCopropietario::class)
    ->name('public.entrega-fest.asistencia.copropietario');

// Entrega Fest - Agendamiento Firma de Contrato (solo Titular)
Route::get('/evento/{slug}/firma/{id}', FirmaPublica::class)
    ->name('public.entrega-fest.firma');

Route::get('/impersonate/leave', function () {
    if (!session()->has('impersonator_id')) {
        return redirect()->route('home');
    }

    $admin = \App\Models\User::find(session('impersonator_id'));
    session()->forget('impersonator_id');
    auth()->login($admin);

    return redirect()->route('erp.cliente.vista.todo');
})->name('impersonate.leave')->middleware('auth');
