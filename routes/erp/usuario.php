<?php

use App\Livewire\Erp\Usuario\Cliente\ClienteConsultar;
use App\Livewire\Erp\Usuario\Cliente\ClienteCrear;
use App\Livewire\Erp\Usuario\Cliente\ClienteEditar;
use App\Livewire\Erp\Usuario\Cliente\ClienteLista;
use App\Livewire\Erp\Usuario\Admin\AdminCrear;
use App\Livewire\Erp\Usuario\Admin\AdminEditar;
use App\Livewire\Erp\Usuario\Admin\AdminLista;
use App\Livewire\Erp\Usuario\ClienteAntiguo\ClienteAntiguoLista;
use App\Livewire\Erp\Usuario\ClienteAntiguo\ClienteAntiguoCrear;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['permission:modulo-usuarios.ver']], function () {
    Route::group(['middleware' => ['permission:admin.navegacion']], function () {
        Route::prefix('admin')->name('admin.vista.')->group(function () {
            Route::get('/', AdminLista::class)->middleware('permission:admin.ver')->name('todo');
            Route::get('/crear', AdminCrear::class)->middleware('permission:admin.crear')->name('crear');
            Route::get('/editar/{id}', AdminEditar::class)->middleware('permission:admin.editar')->name('editar');
        });
    });

    Route::group(['middleware' => ['permission:cliente.navegacion']], function () {
        Route::prefix('cliente')->name('cliente.vista.')->group(function () {
            Route::get('/', ClienteLista::class)->middleware('permission:cliente.ver')->name('todo');
            Route::get('/crear', ClienteCrear::class)->middleware('permission:cliente.crear')->name('crear');
            Route::get('/editar/{id}', ClienteEditar::class)->middleware('permission:cliente.editar')->name('editar');
            Route::get('/consultar/{dni?}', ClienteConsultar::class)->middleware('permission:cliente.consultar')->name('consultar');
        });
    });
});

Route::prefix('cliente-antiguo')->name('cliente-antiguo.vista.')->group(function () { //ok
    Route::get('/', ClienteAntiguoLista::class)->name('todo');
    Route::get('/crear', ClienteAntiguoCrear::class)->name('crear');
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
2. admin.ver
3. admin.crear
4. admin.editar
5. admin.eliminar
6. admin.exportar
7. admin.cambiar-clave

CLIENTE
1. cliente.navegacion
2. cliente.ver
3. cliente.crear
4. cliente.editar
5. cliente.exportar
6. cliente.consultar

*/
