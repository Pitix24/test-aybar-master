<?php

use App\Livewire\Backoffice\EstadoSolicitudEvidenciaPago\EstadoSolicitudEvidenciaPagoCrear;
use App\Livewire\Backoffice\EstadoSolicitudEvidenciaPago\EstadoSolicitudEvidenciaPagoEditar;
use App\Livewire\Backoffice\EstadoSolicitudEvidenciaPago\EstadoSolicitudEvidenciaPagoLista;
use App\Livewire\Backoffice\SolicitudEvidenciaPago\SolicitudEvidenciaPagoEditar;
use App\Livewire\Backoffice\SolicitudEvidenciaPago\SolicitudEvidenciaPagoLista;
use App\Livewire\Backoffice\EvidenciaPagoAntiguo\EvidenciaPagoAntiguoEditar;
use App\Livewire\Backoffice\EvidenciaPagoAntiguo\EvidenciaPagoAntiguoLista;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['permission:modulo-backoffice.ver']], function () {
    Route::group(['middleware' => ['permission:estado-solicitud-evidencia-pago.navegacion']], function () {
        Route::prefix('estado-solicitud-evidencia-pago')->name('estado-solicitud-evidencia-pago.vista.')->group(function () {
            Route::get('/', EstadoSolicitudEvidenciaPagoLista::class)->middleware('permission:estado-solicitud-evidencia-pago.ver')->name('todo');
            Route::get('/crear', EstadoSolicitudEvidenciaPagoCrear::class)->middleware('permission:estado-solicitud-evidencia-pago.crear')->name('crear');
            Route::get('/editar/{id}', EstadoSolicitudEvidenciaPagoEditar::class)->middleware('permission:estado-solicitud-evidencia-pago.editar')->name('editar');
        });
    });

    Route::group(['middleware' => ['permission:solicitud-evidencia-pago.navegacion']], function () {
        Route::prefix('solicitud-evidencia-pago')->name('solicitud-evidencia-pago.vista.')->group(function () {
            Route::get('/', SolicitudEvidenciaPagoLista::class)->middleware('permission:solicitud-evidencia-pago.ver')->name('todo');
            Route::get('/editar/{id}', SolicitudEvidenciaPagoEditar::class)->middleware('permission:solicitud-evidencia-pago.editar')->name('editar');
        });
    });

    Route::group(['middleware' => ['permission:evidencia-pago-antiguo.navegacion']], function () {
        Route::prefix('evidencia-pago-antiguo')->name('evidencia-pago-antiguo.vista.')->group(function () {
            Route::get('/', EvidenciaPagoAntiguoLista::class)->middleware('permission:evidencia-pago-antiguo.ver')->name('todo');
            Route::get('/editar/{id}', EvidenciaPagoAntiguoEditar::class)->middleware('permission:evidencia-pago-antiguo.editar')->name('editar');
        });
    });
});

/*
--------------------------------------------------------------------------
PERMISOS BACKOFFICE
--------------------------------------------------------------------------
Convención: recurso.accion

ESTADO SOLICITUD EVIDENCIA PAGO
1. estado-solicitud-evidencia-pago.navegacion
2. estado-solicitud-evidencia-pago.ver
3. estado-solicitud-evidencia-pago.crear
4. estado-solicitud-evidencia-pago.editar
5. estado-solicitud-evidencia-pago.eliminar
6. estado-solicitud-evidencia-pago.exportar

SOLICITUD EVIDENCIA PAGO
1. solicitud-evidencia-pago.navegacion
2. solicitud-evidencia-pago.ver
3. solicitud-evidencia-pago.crear
4. solicitud-evidencia-pago.editar
5. solicitud-evidencia-pago.eliminar
6. solicitud-evidencia-pago.exportar

EVIDENCIA PAGO ANTIGUO
1. evidencia-pago-antiguo.navegacion
2. evidencia-pago-antiguo.ver
3. evidencia-pago-antiguo.editar
4. evidencia-pago-antiguo.eliminar
5. evidencia-pago-antiguo.exportar
*/