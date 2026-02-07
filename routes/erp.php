<?php

use App\Livewire\Erp\Admin\AdminCrear;
use App\Livewire\Erp\Admin\AdminEditar;
use App\Livewire\Erp\Admin\AdminLista;
use App\Livewire\Erp\Area\AreaCrear;
use App\Livewire\Erp\Area\AreaEditar;
use App\Livewire\Erp\Area\AreaLista;
use App\Livewire\Erp\Area\AreaSolicitud;
use App\Livewire\Erp\Area\AreaUser;
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
use App\Livewire\Erp\Sede\SedeCrear;
use App\Livewire\Erp\Sede\SedeEditar;
use App\Livewire\Erp\Sede\SedeLista;
use App\Livewire\Erp\UnidadNegocio\UnidadNegocioCrear;
use App\Livewire\Erp\UnidadNegocio\UnidadNegocioEditar;
use App\Livewire\Erp\UnidadNegocio\UnidadNegocioLista;
use Illuminate\Support\Facades\Route;

//Route::get('/', InicioLivewire::class)->name('home');

Route::get('/perfil', InicioLivewire::class)->name('home');

Route::group(['middleware' => ['permission:rol-ver']], function () {
    Route::prefix('rol')->name('rol.vista.')->group(function () {
        Route::get('/', RolLista::class)->name('todo');
        Route::get('/crear', RolCrear::class)->middleware('permission:rol-crear')->name('crear');
        Route::get('/editar/{id}', RolEditar::class)->middleware('permission:rol-editar')->name('editar');
    });
});

Route::group(['middleware' => ['permission:permiso-ver']], function () {
    Route::prefix('permiso')->name('permiso.vista.')->group(function () {
        Route::get('/', PermisoLista::class)->name('todo');
        Route::get('/crear', PermisoCrear::class)->middleware('permission:permiso-crear')->name('crear');
        Route::get('/editar/{id}', PermisoEditar::class)->middleware('permission:permiso-editar')->name('editar');
    });
});

Route::group(['middleware' => ['permission:admin-ver']], function () {
    Route::prefix('admin')->name('admin.vista.')->group(function () {
        Route::get('/', AdminLista::class)->name('todo');
        Route::get('/crear', AdminCrear::class)->middleware('permission:admin-crear')->name('crear');
        Route::get('/editar/{id}', AdminEditar::class)->middleware('permission:admin-editar')->name('editar');
    });
});

Route::group(['middleware' => ['permission:cliente-ver']], function () {
    Route::prefix('cliente')->name('cliente.vista.')->group(function () {
        Route::get('/', ClienteLista::class)->name('todo');
        Route::get('/crear', ClienteCrear::class)->middleware('permission:cliente-crear')->name('crear');
        Route::get('/editar/{id}', ClienteEditar::class)->middleware('permission:cliente-editar')->name('editar');
    });
});

Route::prefix('direccion')->name('direccion.vista.')->group(function () {
    Route::get('/', DireccionLista::class)->name('todo');
    Route::get('/crear', DireccionCrear::class)->name('crear');
    Route::get('/editar/{id}', DireccionEditar::class)->name('editar');
});

Route::group(['middleware' => ['permission:unidad-negocio-ver']], function () {
    Route::prefix('unidad-negocio')->name('unidad-negocio.vista.')->group(function () {
        Route::get('/', UnidadNegocioLista::class)->name('todo');
        Route::get('/crear', UnidadNegocioCrear::class)->middleware('permission:unidad-negocio-crear')->name('crear');
        Route::get('/editar/{id}', UnidadNegocioEditar::class)->middleware('permission:unidad-negocio-editar')->name('editar');
    });
});

Route::group(['middleware' => ['permission:grupo-proyecto-ver']], function () {
    Route::prefix('grupo-proyecto')->name('grupo-proyecto.vista.')->group(function () {
        Route::get('/', GrupoProyectoLista::class)->name('todo');
        Route::get('/crear', GrupoProyectoCrear::class)->middleware('permission:grupo-proyecto-crear')->name('crear');
        Route::get('/editar/{id}', GrupoProyectoEditar::class)->middleware('permission:grupo-proyecto-editar')->name('editar');
    });
});

Route::group(['middleware' => ['permission:proyecto-ver']], function () {
    Route::prefix('proyecto')->name('proyecto.vista.')->group(function () {
        Route::get('/', ProyectoLista::class)->name('todo');
        Route::get('/crear', ProyectoCrear::class)->middleware('permission:proyecto-crear')->name('crear');
        Route::get('/editar/{id}', ProyectoEditar::class)->middleware('permission:proyecto-editar')->name('editar');
    });
});

Route::group(['middleware' => ['permission:sede-ver']], function () {
    Route::prefix('sede')->name('sede.vista.')->group(function () { //ok
        Route::get('/', SedeLista::class)->name('todo');
        Route::get('/crear', SedeCrear::class)->name('crear');
        Route::get('/editar/{id}', SedeEditar::class)->name('editar');
    });
});

Route::group(['middleware' => ['permission:area-ver']], function () {
    Route::prefix('area')->name('area.vista.')->group(function () { //ok
        Route::get('/', AreaLista::class)->name('todo');
        Route::get('/crear', AreaCrear::class)->name('crear');
        Route::get('/editar/{id}', AreaEditar::class)->name('editar');
        Route::get('/user/{id}', AreaUser::class)->name('user');
        Route::get('/solicitud/{id}', AreaSolicitud::class)->name('solicitud');
    });
});

require __DIR__ . '/atc.php';
