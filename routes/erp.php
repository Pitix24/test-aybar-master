<?php

use App\Livewire\Erp\Inicio\InicioLivewire;
use App\Livewire\Erp\UnidadNegocio\UnidadNegocioCrear;
use App\Livewire\Erp\UnidadNegocio\UnidadNegocioEditar;
use App\Livewire\Erp\UnidadNegocio\UnidadNegocioLista;
use Illuminate\Support\Facades\Route;

Route::get('/perfil', InicioLivewire::class)->name('home');

Route::prefix('unidad-negocio')->name('unidad-negocio.vista.')->group(function () {
    Route::get('/', UnidadNegocioLista::class)->name('todo');
    Route::get('/crear', UnidadNegocioCrear::class)->name('crear');
    Route::get('/editar/{id}', UnidadNegocioEditar::class)->name('editar');
});