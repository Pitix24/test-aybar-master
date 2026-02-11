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

Route::group(['middleware' => ['permission:estado-cita.ver']], function () {
    Route::prefix('estado-cita')
        ->name('estado-cita.vista.')
        ->group(function () {
            Route::get('/', EstadoCitaLista::class)->name('todo');
            Route::get('/crear', EstadoCitaCrear::class)->middleware('permission:estado-cita.crear')->name('crear');
            Route::get('/editar/{id}', EstadoCitaEditar::class)->middleware('permission:estado-cita.editar')->name('editar');
        });
});

Route::group(['middleware' => ['permission:motivo-cita.ver']], function () {
    Route::prefix('motivo-cita')
        ->name('motivo-cita.vista.')
        ->group(function () {
            Route::get('/', MotivoCitaLista::class)->name('todo');
            Route::get('/crear', MotivoCitaCrear::class)->middleware('permission:motivo-cita.crear')->name('crear');
            Route::get('/editar/{id}', MotivoCitaEditar::class)->middleware('permission:motivo-cita.editar')->name('editar');
        });
});

Route::group(['middleware' => ['permission:cita.ver']], function () {
    Route::prefix('cita')
        ->name('cita.vista.')
        ->group(function () {
            Route::get('/', CitaLista::class)->name('todo');
            Route::get('/crear/{citaPadre?}', CitaCrear::class)->middleware('permission:cita.crear')->name('crear');
            Route::get('/editar/{id}', CitaEditar::class)->middleware('permission:cita.editar')->name('editar');
            Route::get('/calendario', CitaCalendario::class)->middleware('permission:cita.ver')->name('calendario');
        });
});
