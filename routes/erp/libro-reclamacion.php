<?php

use App\Livewire\Erp\LibroReclamacion\LibroReclamacionCrear;
use App\Livewire\Erp\LibroReclamacion\LibroReclamacionEditar;
use App\Livewire\Erp\LibroReclamacion\LibroReclamacionLista;
use App\Livewire\Erp\LibroReclamacion\LibroReclamacionVer;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['permission:modulo-legal.ver']], function () {
    Route::group(['middleware' => ['permission:libro-reclamacion.navegacion']], function () {
        Route::prefix('libro-reclamacion')
            ->name('libro-reclamacion.vista.')
            ->group(function () {
                Route::get('/', LibroReclamacionLista::class)->middleware('permission:libro-reclamacion.gestor')->name('todo');
                Route::get('/ver/{id}', LibroReclamacionVer::class)->middleware('permission:libro-reclamacion.gestor')->name('ver');

                if (config('libro_reclamacion.crear_erp_habilitado')) {
                    Route::get('/crear', LibroReclamacionCrear::class)->middleware('permission:libro-reclamacion.gestor')->name('crear');
                }

                Route::get('/editar/{id}', LibroReclamacionEditar::class)->middleware('permission:libro-reclamacion.gestor')->name('editar');
            });
    });
});
