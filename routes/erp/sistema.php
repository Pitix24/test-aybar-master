<?php

use App\Livewire\Erp\Sistema\Permiso\PermisoCrear;
use App\Livewire\Erp\Sistema\Permiso\PermisoEditar;
use App\Livewire\Erp\Sistema\Permiso\PermisoLista;
use App\Livewire\Erp\Sistema\Permiso\PermisoVer;
use App\Livewire\Erp\Sistema\Rol\RolCrear;
use App\Livewire\Erp\Sistema\Rol\RolEditar;
use App\Livewire\Erp\Sistema\Rol\RolLista;
use App\Livewire\Erp\Sistema\Rol\RolVer;
use App\Livewire\Erp\Sistema\Menu\MenuCrear;
use App\Livewire\Erp\Sistema\Menu\MenuEditar;
use App\Livewire\Erp\Sistema\Menu\MenuLista;
use App\Livewire\Erp\Sistema\Menu\MenuVer;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['permission:modulo-sistema.ver']], function () {
    Route::group(['middleware' => ['permission:rol.navegacion']], function () {
        Route::prefix('rol')->name('rol.vista.')->group(function () {
            Route::get('/', RolLista::class)->middleware('permission:rol.lista')->name('todo');
            Route::get('/ver/{id}', RolVer::class)->middleware('permission:rol.ver')->name('ver');
            Route::get('/crear', RolCrear::class)->middleware('permission:rol.crear')->name('crear');
            Route::get('/editar/{id}', RolEditar::class)->middleware('permission:rol.editar')->name('editar');
        });
    });

    Route::group(['middleware' => ['permission:permiso.navegacion']], function () {
        Route::prefix('permiso')->name('permiso.vista.')->group(function () {
            Route::get('/', PermisoLista::class)->middleware('permission:permiso.lista')->name('todo');
            Route::get('/ver/{id}', PermisoVer::class)->middleware('permission:permiso.ver')->name('ver');
            Route::get('/crear', PermisoCrear::class)->middleware('permission:permiso.crear')->name('crear');
            Route::get('/editar/{id}', PermisoEditar::class)->middleware('permission:permiso.editar')->name('editar');
        });
    });

    Route::group(['middleware' => ['permission:menu.navegacion']], function () {
        Route::prefix('menu')->name('menu.vista.')->group(function () {
            Route::get('/', MenuLista::class)->middleware('permission:menu.lista')->name('todo');
            Route::get('/ver/{id}', MenuVer::class)->middleware('permission:menu.ver')->name('ver');
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
2. rol.lista
3. rol.ver
4. rol.crear
5. rol.editar
6. rol.eliminar
7. rol.exportar-filtro
8. rol.exportar-todo

PERMISO
1. permiso.navegacion
2. permiso.lista
3. permiso.ver
4. permiso.crear
5. permiso.editar
6. permiso.eliminar
7. permiso.exportar-filtro
8. permiso.exportar-todo

MENÚ
1. menu.navegacion
2. menu.lista
3. menu.ver
4. menu.crear
5. menu.editar
6. menu.eliminar
7. menu.exportar-filtro
8. menu.exportar-todo
*/
