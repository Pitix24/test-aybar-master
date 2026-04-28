<?php

use App\Livewire\Erp\LibroReclamacion\LibroReclamacionCrear;
use App\Livewire\Erp\LibroReclamacion\LibroReclamacionEditar;
use App\Livewire\Erp\LibroReclamacion\LibroReclamacionLista;
use App\Livewire\Erp\LibroReclamacion\LibroReclamacionVer;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['permission:modulo-libro-reclamacion.ver']], function () {
    Route::group(['middleware' => ['permission:ticket-libro-reclamacion.navegacion']], function () {
        Route::prefix('libro-reclamacion')
            ->name('libro-reclamacion.vista.')
            ->group(function () {
                Route::get('/', LibroReclamacionLista::class)->middleware('permission:ticket-libro-reclamacion.lista')->name('todo');
                Route::get('/ver/{id}', LibroReclamacionVer::class)->middleware('permission:ticket-libro-reclamacion.ver')->name('ver');

                if (config('libro_reclamacion.crear_erp_habilitado')) {
                    Route::get('/crear', LibroReclamacionCrear::class)->middleware('permission:ticket-libro-reclamacion.crear')->name('crear');
                }

                Route::get('/editar/{id}', LibroReclamacionEditar::class)->middleware('permission:ticket-libro-reclamacion.editar')->name('editar');
            });
    });
});
