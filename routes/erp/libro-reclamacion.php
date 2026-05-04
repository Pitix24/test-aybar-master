<?php

use App\Livewire\Erp\LibroReclamacion\LibroReclamacionCrear;
use App\Livewire\Erp\LibroReclamacion\LibroReclamacionEditar;
use App\Livewire\Erp\LibroReclamacion\LibroReclamacionLista;
use App\Livewire\Erp\LibroReclamacion\LibroReclamacionVer;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['permission:reclamacion.gestor']], function () {
    Route::group(['middleware' => ['permission:reclamacion.gestor']], function () {
        Route::prefix('libro-reclamacion')
            ->name('libro-reclamacion.vista.')
            ->group(function () {
                Route::get('/', LibroReclamacionLista::class)->middleware('permission:reclamacion.gestor')->name('todo');
                Route::get('/ver/{id}', LibroReclamacionVer::class)->middleware('permission:reclamacion.gestor')->name('ver');

                if (config('libro_reclamacion.crear_erp_habilitado')) {
                    Route::get('/crear', LibroReclamacionCrear::class)->middleware('permission:reclamacion.gestor')->name('crear');
                }

                Route::get('/editar/{id}', LibroReclamacionEditar::class)->middleware('permission:reclamacion.gestor')->name('editar');
            });
    });
});
