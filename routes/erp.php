<?php

use App\Livewire\Erp\Area\AreaCrear;
use App\Livewire\Erp\Area\AreaEditar;
use App\Livewire\Erp\Area\AreaLista;
use App\Livewire\Erp\Area\AreaSolicitud;
use App\Livewire\Erp\Area\AreaUser;
use App\Livewire\Erp\GrupoProyecto\GrupoProyectoCrear;
use App\Livewire\Erp\GrupoProyecto\GrupoProyectoEditar;
use App\Livewire\Erp\GrupoProyecto\GrupoProyectoLista;
use App\Livewire\Erp\Inicio\InicioLivewire;
use App\Livewire\Erp\Proyecto\ProyectoCrear;
use App\Livewire\Erp\Proyecto\ProyectoEditar;
use App\Livewire\Erp\Proyecto\ProyectoLista;
use App\Livewire\Erp\Sede\SedeCrear;
use App\Livewire\Erp\Sede\SedeEditar;
use App\Livewire\Erp\Sede\SedeLista;
use App\Livewire\Erp\UnidadNegocio\UnidadNegocioCrear;
use App\Livewire\Erp\UnidadNegocio\UnidadNegocioEditar;
use App\Livewire\Erp\UnidadNegocio\UnidadNegocioLista;
use Illuminate\Support\Facades\Route;

//Route::get('/', InicioLivewire::class)->name('home');

Route::get('/perfil', InicioLivewire::class)->name('home');

Route::group(['middleware' => ['permission:unidad-negocio.ver']], function () {
    Route::prefix('unidad-negocio')->name('unidad-negocio.vista.')->group(function () {
        Route::get('/', UnidadNegocioLista::class)->name('todo');
        Route::get('/crear', UnidadNegocioCrear::class)->middleware('permission:unidad-negocio.crear')->name('crear');
        Route::get('/editar/{id}', UnidadNegocioEditar::class)->middleware('permission:unidad-negocio.editar')->name('editar');
    });
});

Route::group(['middleware' => ['permission:grupo-proyecto.ver']], function () {
    Route::prefix('grupo-proyecto')->name('grupo-proyecto.vista.')->group(function () {
        Route::get('/', GrupoProyectoLista::class)->name('todo');
        Route::get('/crear', GrupoProyectoCrear::class)->middleware('permission:grupo-proyecto.crear')->name('crear');
        Route::get('/editar/{id}', GrupoProyectoEditar::class)->middleware('permission:grupo-proyecto.editar')->name('editar');
    });
});

Route::group(['middleware' => ['permission:proyecto.ver']], function () {
    Route::prefix('proyecto')->name('proyecto.vista.')->group(function () {
        Route::get('/', ProyectoLista::class)->name('todo');
        Route::get('/crear', ProyectoCrear::class)->middleware('permission:proyecto.crear')->name('crear');
        Route::get('/editar/{id}', ProyectoEditar::class)->middleware('permission:proyecto.editar')->name('editar');
    });
});

Route::group(['middleware' => ['permission:sede.ver']], function () {
    Route::prefix('sede')->name('sede.vista.')->group(function () {
        Route::get('/', SedeLista::class)->name('todo');
        Route::get('/crear', SedeCrear::class)->middleware('permission:sede.crear')->name('crear');
        Route::get('/editar/{id}', SedeEditar::class)->middleware('permission:sede.editar')->name('editar');
    });
});

Route::group(['middleware' => ['permission:area.ver']], function () {
    Route::prefix('area')->name('area.vista.')->group(function () {
        Route::get('/', AreaLista::class)->name('todo');
        Route::get('/crear', AreaCrear::class)->middleware('permission:area.crear')->name('crear');
        Route::get('/editar/{id}', AreaEditar::class)->middleware('permission:area.editar')->name('editar');
        Route::get('/user/{id}', AreaUser::class)->name('user');
        Route::get('/solicitud/{id}', AreaSolicitud::class)->name('solicitud');
    });
});

require __DIR__ . '/atc.php';
require __DIR__ . '/cita.php';
require __DIR__ . '/sistema.php';
require __DIR__ . '/usuario.php';
require __DIR__ . '/letra.php';
require __DIR__ . '/backoffice.php';
