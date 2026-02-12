<?php


use App\Livewire\Letra\EstadoSolicitudDigitalizarLetra\EstadoSolicitudDigitalizarLetraCrear;
use App\Livewire\Letra\EstadoSolicitudDigitalizarLetra\EstadoSolicitudDigitalizarLetraEditar;
use App\Livewire\Letra\EstadoSolicitudDigitalizarLetra\EstadoSolicitudDigitalizarLetraLista;
use App\Livewire\Letra\SolicitudDigitalizarLetra\SolicitudDigitalizarLetraEditar;
use App\Livewire\Letra\SolicitudDigitalizarLetra\SolicitudDigitalizarLetraLista;
use App\Livewire\Letras\EnvioCavali\EnvioCavaliDetalle;
use App\Livewire\Letras\EnvioCavali\EnvioCavaliLista;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['permission:modulo-letras.ver']], function () {
    Route::group(['middleware' => ['permission:estado-solicitud-digitalizar-letra.navegacion']], function () {
        Route::prefix('estado-solicitud-digitalizar-letra')->name('estado-solicitud-digitalizar-letra.vista.')->group(function () {
            Route::get('/', EstadoSolicitudDigitalizarLetraLista::class)->middleware('permission:estado-solicitud-digitalizar-letra.ver')->name('todo');
            Route::get('/crear', EstadoSolicitudDigitalizarLetraCrear::class)->middleware('permission:estado-solicitud-digitalizar-letra.crear')->name('crear');
            Route::get('/editar/{id}', EstadoSolicitudDigitalizarLetraEditar::class)->middleware('permission:estado-solicitud-digitalizar-letra.editar')->name('editar');
        });
    });

    Route::group(['middleware' => ['permission:solicitud-digitalizar-letra.navegacion']], function () {
        Route::prefix('solicitar-letra-digital')->name('solicitar-letra-digital.vista.')->group(function () {
            Route::get('/', SolicitudDigitalizarLetraLista::class)->middleware('permission:solicitud-digitalizar-letra.ver')->name('todo');
            Route::get('/editar/{id}', SolicitudDigitalizarLetraEditar::class)->middleware('permission:solicitud-digitalizar-letra.editar')->name('editar');
        });
    });

    Route::group(['middleware' => ['permission:envio-cavali.navegacion']], function () {
        Route::prefix('envio-cavali')->name('envio-cavali.vista.')->group(function () {
            Route::get('/', EnvioCavaliLista::class)->middleware('permission:envio-cavali.ver')->name('todo');
            Route::get('/detalle/{id}', EnvioCavaliDetalle::class)->middleware('permission:envio-cavali.detalle')->name('detalle');
        });
    });
});

/*
--------------------------------------------------------------------------
PERMISOS LETRAS
--------------------------------------------------------------------------
Convención: recurso.accion

ESTADO SOLICITUD DIGITALIZAR LETRA
1. estado-solicitud-digitalizar-letra.navegacion
2. estado-solicitud-digitalizar-letra.ver
3. estado-solicitud-digitalizar-letra.crear
4. estado-solicitud-digitalizar-letra.editar
5. estado-solicitud-digitalizar-letra.eliminar
6. estado-solicitud-digitalizar-letra.exportar

SOLICITUD DIGITALIZAR LETRA
1. solicitud-digitalizar-letra.navegacion
2. solicitud-digitalizar-letra.ver
3. solicitud-digitalizar-letra.crear
4. solicitud-digitalizar-letra.editar
5. solicitud-digitalizar-letra.eliminar
6. solicitud-digitalizar-letra.exportar

ENVIO CAVALI SOLICITUD
1. envio-cavali-solicitud.navegacion
2. envio-cavali-solicitud.ver
3. envio-cavali-solicitud.crear
4. envio-cavali-solicitud.editar
5. envio-cavali-solicitud.eliminar
6. envio-cavali-solicitud.exportar
*/
