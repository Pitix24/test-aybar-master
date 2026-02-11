<?php

use App\Livewire\Erp\Cliente\ClienteCrear;
use App\Livewire\Erp\Cliente\ClienteEditar;
use App\Livewire\Erp\Cliente\ClienteLista;

use App\Livewire\Erp\Admin\AdminCrear;
use App\Livewire\Erp\Admin\AdminEditar;
use App\Livewire\Erp\Admin\AdminLista;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['permission:admin.navegacion']], function () {
    Route::prefix('admin')->name('admin.vista.')->group(function () {
        Route::get('/', AdminLista::class)->middleware('permission:admin.lista')->name('todo');
        Route::get('/crear', AdminCrear::class)->middleware('permission:admin.crear')->name('crear');
        Route::get('/editar/{id}', AdminEditar::class)->middleware('permission:admin.editar')->name('editar');
    });
});

Route::group(['middleware' => ['permission:cliente.navegacion']], function () {
    Route::prefix('cliente')->name('cliente.vista.')->group(function () {
        Route::get('/', ClienteLista::class)->middleware('permission:cliente.lista')->name('todo');
        Route::get('/crear', ClienteCrear::class)->middleware('permission:cliente.crear')->name('crear');
        Route::get('/editar/{id}', ClienteEditar::class)->middleware('permission:cliente.editar')->name('editar');
    });
});

/*
--------------------------------------------------------------------------
PERMISOS DEL USUARIO
--------------------------------------------------------------------------
Convención: recurso.accion
MODULO
1. modulo-usuarios.ver

ADMIN
1. admin.navegacion
2. admin.lista
3. admin.crear
4. admin.ver
5. admin.editar
6. admin.eliminar
7. admin.exportar
8. admin.cambiar-clave

CLIENTE
1. cliente.navegacion
2. cliente.lista
3. cliente.crear
4. cliente.ver
5. cliente.editar
6. cliente.exportar

*/
