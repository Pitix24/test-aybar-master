<?php

use App\Livewire\Erp\Cliente\ClienteCrear;
use App\Livewire\Erp\Cliente\ClienteEditar;
use App\Livewire\Erp\Cliente\ClienteLista;

use App\Livewire\Erp\Admin\AdminCrear;
use App\Livewire\Erp\Admin\AdminEditar;
use App\Livewire\Erp\Admin\AdminLista;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['permission:admin.ver']], function () {
    Route::prefix('admin')->name('admin.vista.')->group(function () {
        Route::get('/', AdminLista::class)->name('todo');
        Route::get('/crear', AdminCrear::class)->middleware('permission:admin.crear')->name('crear');
        Route::get('/editar/{id}', AdminEditar::class)->middleware('permission:admin.editar')->name('editar');
    });
});

Route::group(['middleware' => ['permission:cliente.ver']], function () {
    Route::prefix('cliente')->name('cliente.vista.')->group(function () {
        Route::get('/', ClienteLista::class)->name('todo');
        Route::get('/crear', ClienteCrear::class)->middleware('permission:cliente.crear')->name('crear');
        Route::get('/editar/{id}', ClienteEditar::class)->middleware('permission:cliente.editar')->name('editar');
    });
});