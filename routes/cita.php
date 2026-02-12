<?php
use App\Livewire\Cita\Cita\CitaCalendario;
use App\Livewire\Cita\EstadoCita\EstadoCitaCrear;
use App\Livewire\Cita\EstadoCita\EstadoCitaEditar;
use App\Livewire\Cita\EstadoCita\EstadoCitaLista;
use App\Livewire\Cita\MotivoCita\MotivoCitaCrear;
use App\Livewire\Cita\MotivoCita\MotivoCitaEditar;
use App\Livewire\Cita\MotivoCita\MotivoCitaLista;
use App\Livewire\Cita\Cita\CitaCrear;
use App\Livewire\Cita\Cita\CitaEditar;
use App\Livewire\Cita\Cita\CitaLista;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['permission:modulo-cita.ver']], function () {
    Route::group(['middleware' => ['permission:estado-cita.navegacion']], function () {
        Route::prefix('estado-cita')
            ->name('estado-cita.vista.')
            ->group(function () {
                Route::get('/', EstadoCitaLista::class)->middleware('permission:estado-cita.ver')->name('todo');
                Route::get('/crear', EstadoCitaCrear::class)->middleware('permission:estado-cita.crear')->name('crear');
                Route::get('/editar/{id}', EstadoCitaEditar::class)->middleware('permission:estado-cita.editar')->name('editar');
            });
    });

    Route::group(['middleware' => ['permission:motivo-cita.navegacion']], function () {
        Route::prefix('motivo-cita')
            ->name('motivo-cita.vista.')
            ->group(function () {
                Route::get('/', MotivoCitaLista::class)->middleware('permission:motivo-cita.ver')->name('todo');
                Route::get('/crear', MotivoCitaCrear::class)->middleware('permission:motivo-cita.crear')->name('crear');
                Route::get('/editar/{id}', MotivoCitaEditar::class)->middleware('permission:motivo-cita.editar')->name('editar');
            });
    });

    Route::group(['middleware' => ['permission:cita.navegacion']], function () {
        Route::prefix('cita')
            ->name('cita.vista.')
            ->group(function () {
                Route::get('/', CitaLista::class)->middleware('permission:cita.ver')->name('todo');
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
2. estado-cita.ver
3. estado-cita.crear
4. estado-cita.editar
5. estado-cita.eliminar
6. estado-cita.exportar

MOTIVO CITA
1. motivo-cita.navegacion
2. motivo-cita.ver
3. motivo-cita.crear
4. motivo-cita.editar
5. motivo-cita.eliminar
6. motivo-cita.exportar

CITA
1. cita.navegacion
2. cita.ver
3. cita.crear
4. cita.editar
5. cita.eliminar
6. cita.exportar
*/
