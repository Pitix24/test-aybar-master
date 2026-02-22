<?php

use App\Livewire\Erp\Backoffice\EstadoSolicitudEvidenciaPago\EstadoSolicitudEvidenciaPagoCrear;
use App\Livewire\Erp\Backoffice\EstadoSolicitudEvidenciaPago\EstadoSolicitudEvidenciaPagoEditar;
use App\Livewire\Erp\Backoffice\EstadoSolicitudEvidenciaPago\EstadoSolicitudEvidenciaPagoLista;
use App\Livewire\Erp\Backoffice\EstadoSolicitudEvidenciaPago\EstadoSolicitudEvidenciaPagoVer;
use App\Livewire\Erp\Backoffice\EvidenciaPagoAntiguo\EvidenciaPagoAntiguoVer;
use App\Livewire\Erp\Backoffice\SolicitudEvidenciaPago\SolicitudEvidenciaPagoCrear;
use App\Livewire\Erp\Backoffice\SolicitudEvidenciaPago\SolicitudEvidenciaPagoEditar;
use App\Livewire\Erp\Backoffice\SolicitudEvidenciaPago\SolicitudEvidenciaPagoLista;
use App\Livewire\Erp\Backoffice\SolicitudEvidenciaPago\SolicitudEvidenciaPagoVer;
use App\Livewire\Erp\Backoffice\EvidenciaPagoAntiguo\EvidenciaPagoAntiguoEditar;
use App\Livewire\Erp\Backoffice\EvidenciaPagoAntiguo\EvidenciaPagoAntiguoLista;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['permission:modulo-backoffice.ver']], function () {
    Route::group(['middleware' => ['permission:estado-solicitud-evidencia-pago.navegacion']], function () {
        Route::prefix('estado-solicitud-evidencia-pago')->name('estado-solicitud-evidencia-pago.vista.')->group(function () {
            Route::get('/', EstadoSolicitudEvidenciaPagoLista::class)->middleware('permission:estado-solicitud-evidencia-pago.lista')->name('todo');
            Route::get('/ver/{id}', EstadoSolicitudEvidenciaPagoVer::class)->middleware('permission:estado-solicitud-evidencia-pago.ver')->name('ver');
            Route::get('/crear', EstadoSolicitudEvidenciaPagoCrear::class)->middleware('permission:estado-solicitud-evidencia-pago.crear')->name('crear');
            Route::get('/editar/{id}', EstadoSolicitudEvidenciaPagoEditar::class)->middleware('permission:estado-solicitud-evidencia-pago.editar')->name('editar');
        });
    });

    Route::group(['middleware' => ['permission:solicitud-evidencia-pago.navegacion']], function () {
        Route::prefix('solicitud-evidencia-pago')->name('solicitud-evidencia-pago.vista.')->group(function () {
            Route::get('/', SolicitudEvidenciaPagoLista::class)->middleware('permission:solicitud-evidencia-pago.ver')->name('todo');
            Route::get('/ver/{id}', SolicitudEvidenciaPagoVer::class)->middleware('permission:solicitud-evidencia-pago.ver')->name('ver');
            Route::get('/editar/{id}', SolicitudEvidenciaPagoEditar::class)->middleware('permission:solicitud-evidencia-pago.editar')->name('editar');
        });
    });

    Route::group(['middleware' => ['permission:evidencia-pago-antiguo.navegacion']], function () {
        Route::prefix('evidencia-pago-antiguo')->name('evidencia-pago-antiguo.vista.')->group(function () {
            Route::get('/', EvidenciaPagoAntiguoLista::class)->middleware('permission:evidencia-pago-antiguo.lista')->name('todo');
            Route::get('/ver/{id}', EvidenciaPagoAntiguoVer::class)->middleware('permission:evidencia-pago-antiguo.ver')->name('ver');
            Route::get('/editar/{id}', EvidenciaPagoAntiguoEditar::class)->middleware('permission:evidencia-pago-antiguo.editar')->name('editar');
        });
    });
});

/*
--------------------------------------------------------------------------
PERMISOS BACKOFFICE
--------------------------------------------------------------------------
Convención: recurso.accion
MODULO
1. modulo-backoffice.ver

ESTADO SOLICITUD EVIDENCIA PAGO
1. estado-solicitud-evidencia-pago.navegacion
2. estado-solicitud-evidencia-pago.lista
3. estado-solicitud-evidencia-pago.ver
4. estado-solicitud-evidencia-pago.crear
5. estado-solicitud-evidencia-pago.editar
6. estado-solicitud-evidencia-pago.eliminar
7. estado-solicitud-evidencia-pago.exportar-filtro
8. estado-solicitud-evidencia-pago.exportar-todo

SOLICITUD EVIDENCIA PAGO
1. solicitud-evidencia-pago.navegacion
2. solicitud-evidencia-pago.lista
3. solicitud-evidencia-pago.ver
4. solicitud-evidencia-pago.editar
5. solicitud-evidencia-pago.exportar-filtro
6. solicitud-evidencia-pago.exportar-todo
7. solicitud-evidencia-pago.validar
8. solicitud-evidencia-pago.enviar-correo
9. solicitud-evidencia-pago.chat

EVIDENCIA PAGO ANTIGUO
1. evidencia-pago-antiguo.navegacion
2. evidencia-pago-antiguo.lista
3. evidencia-pago-antiguo.ver
4. evidencia-pago-antiguo.editar
5. evidencia-pago-antiguo.exportar-filtro
6. evidencia-pago-antiguo.exportar-todo
7. evidencia-pago-antiguo.validar
*/