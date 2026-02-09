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

Route::group(['middleware' => ['permission:rol.ver']], function () {
    Route::prefix('rol')->name('rol.vista.')->group(function () {
        Route::get('/', RolLista::class)->name('todo');
        Route::get('/crear', RolCrear::class)->middleware('permission:rol.crear')->name('crear');
        Route::get('/editar/{id}', RolEditar::class)->middleware('permission:rol.editar')->name('editar');
    });
});

Route::group(['middleware' => ['permission:permiso.ver']], function () {
    Route::prefix('permiso')->name('permiso.vista.')->group(function () {
        Route::get('/', PermisoLista::class)->name('todo');
        Route::get('/crear', PermisoCrear::class)->middleware('permission:permiso.crear')->name('crear');
        Route::get('/editar/{id}', PermisoEditar::class)->middleware('permission:permiso.editar')->name('editar');
    });
});

Route::group(['middleware' => ['permission:menu.ver']], function () {
    Route::prefix('menu')->name('menu.vista.')->group(function () {
        Route::get('/', MenuLista::class)->name('todo');
        Route::get('/crear', MenuCrear::class)->middleware('permission:menu.crear')->name('crear');
        Route::get('/editar/{id}', MenuEditar::class)->middleware('permission:menu.editar')->name('editar');
    });
});