<?php

use App\Livewire\Erp\Usuario\Cliente\ClienteConsultar;
use App\Livewire\Erp\Usuario\Cliente\ClienteCrear;
use App\Livewire\Erp\Usuario\Cliente\ClienteEditar;
use App\Livewire\Erp\Usuario\Cliente\ClienteLista;
use App\Livewire\Erp\Usuario\Cliente\ClienteVer;
use App\Livewire\Erp\Usuario\Admin\AdminCrear;
use App\Livewire\Erp\Usuario\Admin\AdminEditar;
use App\Livewire\Erp\Usuario\Admin\AdminLista;
use App\Livewire\Erp\Usuario\Admin\AdminVer;
use App\Livewire\Erp\Usuario\ClienteAntiguo\ClienteAntiguoEditar;
use App\Livewire\Erp\Usuario\ClienteAntiguo\ClienteAntiguoLista;
use App\Livewire\Erp\Usuario\ClienteAntiguo\ClienteAntiguoCrear;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['permission:modulo-usuarios.ver']], function () {
    Route::group(['middleware' => ['permission:admin.navegacion']], function () {
        Route::prefix('admin')->name('admin.vista.')->group(function () {
            Route::get('/', AdminLista::class)->middleware('permission:admin.lista')->name('todo');
            Route::get('/ver/{id}', AdminVer::class)->middleware('permission:admin.ver')->name('ver');
            Route::get('/crear', AdminCrear::class)->middleware('permission:admin.crear')->name('crear');
            Route::get('/editar/{id}', AdminEditar::class)->middleware('permission:admin.editar')->name('editar');
        });
    });

    Route::group(['middleware' => ['permission:cliente.navegacion']], function () {
        Route::prefix('cliente')->name('cliente.vista.')->group(function () {
            Route::get('/', ClienteLista::class)->middleware('permission:cliente.lista')->name('todo');
            Route::get('/ver/{id}', ClienteVer::class)->middleware('permission:cliente.ver')->name('ver');
            Route::get('/crear', ClienteCrear::class)->middleware('permission:cliente.crear')->name('crear');
            Route::get('/editar/{id}', ClienteEditar::class)->middleware('permission:cliente.editar')->name('editar');
            Route::get('/consultar/{dni?}', ClienteConsultar::class)->middleware('permission:cliente.consultar')->name('consultar');
        });
    });

    Route::group(['middleware' => ['permission:cliente-antiguo.navegacion']], function () {
        Route::prefix('cliente-antiguo')->name('cliente-antiguo.vista.')->group(function () { //ok
            Route::get('/', ClienteAntiguoLista::class)->middleware('permission:cliente-antiguo.lista')->name('todo');
            Route::get('/crear', ClienteAntiguoCrear::class)->middleware('permission:cliente-antiguo.crear')->name('crear');
            Route::get('/editar/{id}', ClienteAntiguoEditar::class)->middleware('permission:cliente-antiguo.editar')->name('editar');
        });
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
3. admin.ver
4. admin.crear
5. admin.editar
6. admin.eliminar
7. admin.cambiar-clave
8. admin.exportar-filtro
9. admin.exportar-todo

CLIENTE
1. cliente.navegacion
2. cliente.lista
3. cliente.ver
4. cliente.crear
5. cliente.editar
6. cliente.eliminar
7. cliente.exportar-filtro
8. cliente.exportar-todo
9. cliente.consultar
10. cliente.enviar-recuperar-clave
11. cliente.movimientos

*/
