<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Erp\Soporte\SoporteLista;
use App\Livewire\Erp\Soporte\SoporteCrear;
use App\Livewire\Erp\Soporte\SoporteVer;
use App\Livewire\Erp\Soporte\SoporteEditar;
use App\Livewire\Erp\Soporte\TipoSoporte\TipoSoporteLista;
use App\Livewire\Erp\Soporte\TipoSoporte\TipoSoporteCrear;
use App\Livewire\Erp\Soporte\TipoSoporte\TipoSoporteVer;
use App\Livewire\Erp\Soporte\TipoSoporte\TipoSoporteEditar;
use App\Livewire\Erp\Soporte\PrioridadSoporte\PrioridadSoporteLista;
use App\Livewire\Erp\Soporte\PrioridadSoporte\PrioridadSoporteCrear;
use App\Livewire\Erp\Soporte\PrioridadSoporte\PrioridadSoporteVer;
use App\Livewire\Erp\Soporte\PrioridadSoporte\PrioridadSoporteEditar;
use App\Livewire\Erp\Soporte\EstadoSoporte\EstadoSoporteLista;
use App\Livewire\Erp\Soporte\EstadoSoporte\EstadoSoporteCrear;
use App\Livewire\Erp\Soporte\EstadoSoporte\EstadoSoporteVer;
use App\Livewire\Erp\Soporte\EstadoSoporte\EstadoSoporteEditar;

Route::group(['middleware' => ['permission:modulo-soporte.ver']], function () {
    Route::group(['middleware' => ['permission:soporte.navegacion']], function () {
        Route::prefix('soporte')
            ->name('soporte.vista.')
            ->group(function () {
                Route::get('/', SoporteLista::class)->middleware('permission:soporte.vista-lista')->name('todo');
                Route::get('/ver/{soporte}', SoporteVer::class)->middleware('permission:soporte.vista-ver')->name('ver');
                Route::get('/crear', SoporteCrear::class)->middleware('permission:soporte.vista-crear')->name('crear');
                Route::get('/editar/{soporte}', SoporteEditar::class)->middleware('permission:soporte.vista-editar')->name('editar');
            });
    });

    Route::group(['middleware' => ['permission:tipo-soporte.navegacion']], function () {
        Route::prefix('tipo-soporte')
            ->name('tipo-soporte.vista.')
            ->group(function () {
                Route::get('/', TipoSoporteLista::class)->middleware('permission:tipo-soporte.vista-lista')->name('lista');
                Route::get('/ver/{id}', TipoSoporteVer::class)->middleware('permission:tipo-soporte.vista-ver')->name('ver');
                Route::get('/crear', TipoSoporteCrear::class)->middleware('permission:tipo-soporte.vista-crear')->name('crear');
                Route::get('/editar/{id}', TipoSoporteEditar::class)->middleware('permission:tipo-soporte.vista-editar')->name('editar');
            });
    });

    Route::group(['middleware' => ['permission:prioridad-soporte.navegacion']], function () {
        Route::prefix('prioridad-soporte')
            ->name('prioridad-soporte.vista.')
            ->group(function () {
                Route::get('/', PrioridadSoporteLista::class)->middleware('permission:prioridad-soporte.vista-lista')->name('lista');
                Route::get('/ver/{id}', PrioridadSoporteVer::class)->middleware('permission:prioridad-soporte.vista-ver')->name('ver');
                Route::get('/crear', PrioridadSoporteCrear::class)->middleware('permission:prioridad-soporte.vista-crear')->name('crear');
                Route::get('/editar/{id}', PrioridadSoporteEditar::class)->middleware('permission:prioridad-soporte.vista-editar')->name('editar');
            });
    });

    Route::group(['middleware' => ['permission:estado-soporte.navegacion']], function () {
        Route::prefix('estado-soporte')
            ->name('estado-soporte.vista.')
            ->group(function () {
                Route::get('/', EstadoSoporteLista::class)->middleware('permission:estado-soporte.vista-lista')->name('lista');
                Route::get('/ver/{id}', EstadoSoporteVer::class)->middleware('permission:estado-soporte.vista-ver')->name('ver');
                Route::get('/crear', EstadoSoporteCrear::class)->middleware('permission:estado-soporte.vista-crear')->name('crear');
                Route::get('/editar/{id}', EstadoSoporteEditar::class)->middleware('permission:estado-soporte.vista-editar')->name('editar');
            });
    });
});
