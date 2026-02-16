<?php
use App\Livewire\Erp\Cita\Cita\CitaCalendario;
use App\Livewire\Erp\Cita\EstadoCita\EstadoCitaCrear;
use App\Livewire\Erp\Cita\EstadoCita\EstadoCitaEditar;
use App\Livewire\Erp\Cita\EstadoCita\EstadoCitaLista;
use App\Livewire\Erp\Cita\EstadoCita\EstadoCitaVer;
use App\Livewire\Erp\Cita\MotivoCita\MotivoCitaCrear;
use App\Livewire\Erp\Cita\MotivoCita\MotivoCitaEditar;
use App\Livewire\Erp\Cita\MotivoCita\MotivoCitaLista;
use App\Livewire\Erp\Cita\MotivoCita\MotivoCitaVer;
use App\Livewire\Erp\Cita\Cita\CitaCrear;
use App\Livewire\Erp\Cita\Cita\CitaEditar;
use App\Livewire\Erp\Cita\Cita\CitaLista;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['permission:modulo-cita.ver']], function () {
    Route::group(['middleware' => ['permission:estado-cita.navegacion']], function () {
        Route::prefix('estado-cita')
            ->name('estado-cita.vista.')
            ->group(function () {
                Route::get('/', EstadoCitaLista::class)->middleware('permission:estado-cita.lista')->name('todo');
                Route::get('/ver/{id}', EstadoCitaVer::class)->middleware('permission:estado-cita.ver')->name('ver');
                Route::get('/crear', EstadoCitaCrear::class)->middleware('permission:estado-cita.crear')->name('crear');
                Route::get('/editar/{id}', EstadoCitaEditar::class)->middleware('permission:estado-cita.editar')->name('editar');
            });
    });

    Route::group(['middleware' => ['permission:motivo-cita.navegacion']], function () {
        Route::prefix('motivo-cita')
            ->name('motivo-cita.vista.')
            ->group(function () {
                Route::get('/', MotivoCitaLista::class)->middleware('permission:motivo-cita.lista')->name('todo');
                Route::get('/ver/{id}', MotivoCitaVer::class)->middleware('permission:motivo-cita.ver')->name('ver');
                Route::get('/crear', MotivoCitaCrear::class)->middleware('permission:motivo-cita.crear')->name('crear');
                Route::get('/editar/{id}', MotivoCitaEditar::class)->middleware('permission:motivo-cita.editar')->name('editar');
            });
    });

    Route::group(['middleware' => ['permission:cita.navegacion']], function () {
        Route::prefix('cita')
            ->name('cita.vista.')
            ->group(function () {
                Route::get('/', CitaLista::class)->middleware('permission:cita.lista')->name('todo');
                Route::get('/crear/{citaPadre?}', CitaCrear::class)->middleware('permission:cita.crear')->name('crear');
                Route::get('/editar/{id}', CitaEditar::class)->middleware('permission:cita.editar')->name('editar');
                Route::get('/calendario', CitaCalendario::class)->middleware('permission:cita.ver')->name('calendario');
            });
    });
});

/*
--------------------------------------------------------------------------
PERMISOS CITA
--------------------------------------------------------------------------
Convención: recurso.accion

ESTADO CITA
1. estado-cita.navegacion
2. estado-cita.lista
3. estado-cita.ver
4. estado-cita.crear
5. estado-cita.editar
6. estado-cita.eliminar
7. estado-cita.exportar-filtro
8. estado-cita.exportar-todo

MOTIVO CITA
1. motivo-cita.navegacion
2. motivo-cita.lista
3. motivo-cita.ver
4. motivo-cita.crear
5. motivo-cita.editar
6. motivo-cita.eliminar
7. motivo-cita.exportar-filtro
8. motivo-cita.exportar-todo

CITA
1. cita.navegacion
2. cita.lista
3. cita.ver
4. cita.crear
5. cita.editar
6. cita.eliminar
7. cita.exportar-filtro
8. cita.exportar-todo
9. cita.enviar-correo
*/
