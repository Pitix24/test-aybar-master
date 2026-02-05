<?php

use App\Livewire\Erp\Admin\AdminCrear;
use App\Livewire\Erp\Admin\AdminEditar;
use App\Livewire\Erp\Admin\AdminLista;
use App\Livewire\Erp\Cliente\ClienteCrear;
use App\Livewire\Erp\Cliente\ClienteEditar;
use App\Livewire\Erp\Cliente\ClienteLista;
use App\Livewire\Erp\Direccion\DireccionCrear;
use App\Livewire\Erp\Direccion\DireccionEditar;
use App\Livewire\Erp\Direccion\DireccionLista;
use App\Livewire\Erp\GrupoProyecto\GrupoProyectoCrear;
use App\Livewire\Erp\GrupoProyecto\GrupoProyectoEditar;
use App\Livewire\Erp\GrupoProyecto\GrupoProyectoLista;
use App\Livewire\Erp\Inicio\InicioLivewire;
use App\Livewire\Erp\Permiso\PermisoCrear;
use App\Livewire\Erp\Permiso\PermisoEditar;
use App\Livewire\Erp\Permiso\PermisoLista;
use App\Livewire\Erp\Proyecto\ProyectoCrear;
use App\Livewire\Erp\Proyecto\ProyectoEditar;
use App\Livewire\Erp\Proyecto\ProyectoLista;
use App\Livewire\Erp\Rol\RolCrear;
use App\Livewire\Erp\Rol\RolEditar;
use App\Livewire\Erp\Rol\RolLista;
use App\Livewire\Erp\UnidadNegocio\UnidadNegocioCrear;
use App\Livewire\Erp\UnidadNegocio\UnidadNegocioEditar;
use App\Livewire\Erp\UnidadNegocio\UnidadNegocioLista;
use Illuminate\Support\Facades\Route;

Route::get('/perfil', InicioLivewire::class)->name('home');

Route::prefix('rol')->name('rol.vista.')->group(function () {
    Route::get('/', RolLista::class)->name('todo');
    Route::get('/crear', RolCrear::class)->name('crear');
    Route::get('/editar/{id}', RolEditar::class)->name('editar');
});

Route::prefix('permiso')->name('permiso.vista.')->group(function () {
    Route::get('/', PermisoLista::class)->name('todo');
    Route::get('/crear', PermisoCrear::class)->name('crear');
    Route::get('/editar/{id}', PermisoEditar::class)->name('editar');
});

Route::prefix('admin')->name('admin.vista.')->group(function () {
    Route::get('/', AdminLista::class)->name('todo');
    Route::get('/crear', AdminCrear::class)->name('crear');
    Route::get('/editar/{id}', AdminEditar::class)->name('editar');
});

Route::prefix('cliente')->name('cliente.vista.')->group(function () {
    Route::get('/', ClienteLista::class)->name('todo');
    Route::get('/crear', ClienteCrear::class)->name('crear');
    Route::get('/editar/{id}', ClienteEditar::class)->name('editar');
});

Route::prefix('direccion')->name('direccion.vista.')->group(function () {
    Route::get('/', DireccionLista::class)->name('todo');
    Route::get('/crear', DireccionCrear::class)->name('crear');
    Route::get('/editar/{id}', DireccionEditar::class)->name('editar');
});

Route::prefix('unidad-negocio')->name('unidad-negocio.vista.')->group(function () {
    Route::get('/', UnidadNegocioLista::class)->name('todo');
    Route::get('/crear', UnidadNegocioCrear::class)->name('crear');
    Route::get('/editar/{id}', UnidadNegocioEditar::class)->name('editar');
});

Route::prefix('grupo-proyecto')->name('grupo-proyecto.vista.')->group(function () {
    Route::get('/', GrupoProyectoLista::class)->name('todo');
    Route::get('/crear', GrupoProyectoCrear::class)->name('crear');
    Route::get('/editar/{id}', GrupoProyectoEditar::class)->name('editar');
});

Route::prefix('proyecto')->name('proyecto.vista.')->group(function () {
    Route::get('/', ProyectoLista::class)->name('todo');
    Route::get('/crear', ProyectoCrear::class)->name('crear');
    Route::get('/editar/{id}', ProyectoEditar::class)->name('editar');
});
