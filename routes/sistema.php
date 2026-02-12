<?php

use App\Livewire\Erp\Permiso\PermisoCrear;
use App\Livewire\Erp\Permiso\PermisoEditar;
use App\Livewire\Erp\Permiso\PermisoLista;
use App\Livewire\Erp\Rol\RolCrear;
use App\Livewire\Erp\Rol\RolEditar;
use App\Livewire\Erp\Rol\RolLista;
use App\Livewire\Erp\Menu\MenuCrear;
use App\Livewire\Erp\Menu\MenuEditar;
use App\Livewire\Erp\Menu\MenuLista;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['permission:modulo-sistema.ver']], function () {
    Route::group(['middleware' => ['permission:rol.navegacion']], function () {
        Route::prefix('rol')->name('rol.vista.')->group(function () {
            Route::get('/', RolLista::class)->middleware('permission:rol.ver')->name('todo');
            Route::get('/crear', RolCrear::class)->middleware('permission:rol.crear')->name('crear');
            Route::get('/editar/{id}', RolEditar::class)->middleware('permission:rol.editar')->name('editar');
        });
    });

    Route::group(['middleware' => ['permission:permiso.navegacion']], function () {
        Route::prefix('permiso')->name('permiso.vista.')->group(function () {
            Route::get('/', PermisoLista::class)->middleware('permission:permiso.ver')->name('todo');
            Route::get('/crear', PermisoCrear::class)->middleware('permission:permiso.crear')->name('crear');
            Route::get('/editar/{id}', PermisoEditar::class)->middleware('permission:permiso.editar')->name('editar');
        });
    });

    Route::group(['middleware' => ['permission:menu.navegacion']], function () {
        Route::prefix('menu')->name('menu.vista.')->group(function () {
            Route::get('/', MenuLista::class)->middleware('permission:menu.ver')->name('todo');
            Route::get('/crear', MenuCrear::class)->middleware('permission:menu.crear')->name('crear');
            Route::get('/editar/{id}', MenuEditar::class)->middleware('permission:menu.editar')->name('editar');
        });
    });
});

/*
--------------------------------------------------------------------------
PERMISOS DEL SISTEMA
--------------------------------------------------------------------------
Convención: recurso.accion
MODULO
1. modulo-sistema.ver

ROL
1. rol.navegacion
2. rol.ver
3. rol.crear
4. rol.editar
5. rol.eliminar
6. rol.exportar

PERMISO
1. permiso.navegacion
2. permiso.ver
3. permiso.crear
4. permiso.editar
5. permiso.eliminar
6. permiso.exportar

MENÚ
1. menu.navegacion
2. menu.ver
3. menu.crear
4. menu.editar
5. menu.eliminar
6. menu.exportar
*/
