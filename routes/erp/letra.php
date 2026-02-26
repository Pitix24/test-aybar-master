<?php


use App\Livewire\Erp\Letra\ConsultarLetra\ConsultarLetraVer;
use App\Livewire\Erp\Letra\EstadoSolicitudDigitalizarLetra\EstadoSolicitudDigitalizarLetraCrear;
use App\Livewire\Erp\Letra\EstadoSolicitudDigitalizarLetra\EstadoSolicitudDigitalizarLetraEditar;
use App\Livewire\Erp\Letra\EstadoSolicitudDigitalizarLetra\EstadoSolicitudDigitalizarLetraLista;
use App\Livewire\Erp\Letra\EstadoSolicitudDigitalizarLetra\EstadoSolicitudDigitalizarLetraVer;
use App\Livewire\Erp\Letra\SolicitudDigitalizarLetra\SolicitudDigitalizarLetraLista;
use App\Livewire\Erp\Letra\SolicitudDigitalizarLetra\SolicitudDigitalizarLetraVer;
use App\Livewire\Erp\Letra\EnvioCavali\EnvioCavaliDetalle;
use App\Livewire\Erp\Letra\EnvioCavali\EnvioCavaliLista;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['permission:modulo-letras.ver']], function () {
    Route::group(['middleware' => ['permission:estado-solicitud-digitalizar-letra.navegacion']], function () {
        Route::prefix('estado-solicitud-digitalizar-letra')->name('estado-solicitud-digitalizar-letra.vista.')->group(function () {
            Route::get('/', EstadoSolicitudDigitalizarLetraLista::class)->middleware('permission:estado-solicitud-digitalizar-letra.lista')->name('todo');
            Route::get('/ver/{id}', EstadoSolicitudDigitalizarLetraVer::class)->middleware('permission:estado-solicitud-digitalizar-letra.ver')->name('ver');
            Route::get('/crear', EstadoSolicitudDigitalizarLetraCrear::class)->middleware('permission:estado-solicitud-digitalizar-letra.crear')->name('crear');
            Route::get('/editar/{id}', EstadoSolicitudDigitalizarLetraEditar::class)->middleware('permission:estado-solicitud-digitalizar-letra.editar')->name('editar');
        });
    });

    Route::group(['middleware' => ['permission:solicitud-digitalizar-letra.navegacion']], function () {
        Route::prefix('solicitar-letra-digital')->name('solicitar-letra-digital.vista.')->group(function () {
            Route::get('/', SolicitudDigitalizarLetraLista::class)->middleware('permission:solicitud-digitalizar-letra.ver')->name('todo');
            Route::get('/ver/{id}', SolicitudDigitalizarLetraVer::class)->middleware('permission:solicitud-digitalizar-letra.ver')->name('ver');
        });
    });

    Route::group(['middleware' => ['permission:envio-cavali.navegacion']], function () {
        Route::prefix('envio-cavali')->name('envio-cavali.vista.')->group(function () {
            Route::get('/', EnvioCavaliLista::class)->middleware('permission:envio-cavali.lista')->name('todo');
            Route::get('/detalle/{id}', EnvioCavaliDetalle::class)->middleware('permission:envio-cavali.detalle')->name('detalle');
        });
    });

    Route::group(['middleware' => ['permission:consultar-letra.navegacion']], function () {
        Route::prefix('consultar-letra')->name('consultar-letra.vista.')->group(function () {
            Route::get('/', ConsultarLetraVer::class)->middleware('permission:consultar-letra.ver')->name('ver');
        });
    });
});

/*
--------------------------------------------------------------------------
PERMISOS LETRAS
--------------------------------------------------------------------------
Convención: recurso.accion
MODULO
1. modulo-letras.ver

ESTADO SOLICITUD DIGITALIZAR LETRA
1. estado-solicitud-digitalizar-letra.navegacion
2. estado-solicitud-digitalizar-letra.lista
3. estado-solicitud-digitalizar-letra.ver
4. estado-solicitud-digitalizar-letra.crear
5. estado-solicitud-digitalizar-letra.editar
6. estado-solicitud-digitalizar-letra.eliminar
7. estado-solicitud-digitalizar-letra.exportar-filtro
8. estado-solicitud-digitalizar-letra.exportar-todo

SOLICITUD DIGITALIZAR LETRA
1. solicitud-digitalizar-letra.navegacion
2. solicitud-digitalizar-letra.lista
3. solicitud-digitalizar-letra.ver
4. solicitud-digitalizar-letra.exportar-filtro
5. solicitud-digitalizar-letra.exportar-todo
6. solicitud-digitalizar-letra.ejecutar-cron-letra
7. solicitud-digitalizar-letra.validar-cron-letra

ENVIO CAVALI
1. envio-cavali.navegacion
2. envio-cavali.lista
3. envio-cavali.detalle
4. envio-cavali.exportar-filtro
5. envio-cavali.exportar-todo
6. envio-cavali.exportar-envios
*/
