<?php

use App\Livewire\Backoffice\EstadoSolicitudEvidenciaPago\EstadoSolicitudEvidenciaPagoCrear;
use App\Livewire\Backoffice\EstadoSolicitudEvidenciaPago\EstadoSolicitudEvidenciaPagoEditar;
use App\Livewire\Backoffice\EstadoSolicitudEvidenciaPago\EstadoSolicitudEvidenciaPagoLista;
use App\Livewire\Backoffice\SolicitudEvidenciaPago\SolicitudEvidenciaPagoCrear;
use App\Livewire\Backoffice\SolicitudEvidenciaPago\SolicitudEvidenciaPagoEditar;
use App\Livewire\Backoffice\SolicitudEvidenciaPago\SolicitudEvidenciaPagoLista;
use App\Livewire\Backoffice\EvidenciaPagoAntiguo\EvidenciaPagoAntiguoEditar;
use App\Livewire\Backoffice\EvidenciaPagoAntiguo\EvidenciaPagoAntiguoLista;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['permission:estado-solicitud-evidencia-pago.ver']], function () {
    Route::prefix('estado-solicitud-evidencia-pago')->name('estado-solicitud-evidencia-pago.vista.')->group(function () {
        Route::get('/', EstadoSolicitudEvidenciaPagoLista::class)->name('todo');
        Route::get('/crear', EstadoSolicitudEvidenciaPagoCrear::class)->middleware('permission:estado-solicitud-evidencia-pago.crear')->name('crear');
        Route::get('/editar/{id}', EstadoSolicitudEvidenciaPagoEditar::class)->middleware('permission:estado-solicitud-evidencia-pago.editar')->name('editar');
    });
});

Route::group(['middleware' => ['permission:solicitud-evidencia-pago.ver']], function () {
    Route::prefix('solicitud-evidencia-pago')->name('solicitud-evidencia-pago.vista.')->group(function () {
        Route::get('/', SolicitudEvidenciaPagoLista::class)->name('todo');
        Route::get('/crear', SolicitudEvidenciaPagoCrear::class)->middleware('permission:solicitud-evidencia-pago.crear')->name('crear');
        Route::get('/editar/{id}', SolicitudEvidenciaPagoEditar::class)->middleware('permission:solicitud-evidencia-pago.editar')->name('editar');
    });
});

Route::group(['middleware' => ['permission:evidencia-pago-antiguo.ver']], function () {
    Route::prefix('evidencia-pago-antiguo')->name('evidencia-pago-antiguo.vista.')->group(function () {
        Route::get('/', EvidenciaPagoAntiguoLista::class)->name('todo');
        Route::get('/editar/{id}', EvidenciaPagoAntiguoEditar::class)->middleware('permission:evidencia-pago-antiguo.editar')->name('editar');
    });
});