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

Route::group(['middleware' => ['permission:modulo-negocio.ver']], function () {
    Route::group(['middleware' => ['permission:unidad-negocio.navegacion']], function () {
        Route::prefix('unidad-negocio')->name('unidad-negocio.vista.')->group(function () {
            Route::get('/', UnidadNegocioLista::class)->middleware('permission:unidad-negocio.ver')->name('todo');
            Route::get('/crear', UnidadNegocioCrear::class)->middleware('permission:unidad-negocio.crear')->name('crear');
            Route::get('/editar/{id}', UnidadNegocioEditar::class)->middleware('permission:unidad-negocio.editar')->name('editar');
        });
    });

    Route::group(['middleware' => ['permission:grupo-proyecto.navegacion']], function () {
        Route::prefix('grupo-proyecto')->name('grupo-proyecto.vista.')->group(function () {
            Route::get('/', GrupoProyectoLista::class)->middleware('permission:grupo-proyecto.ver')->name('todo');
            Route::get('/crear', GrupoProyectoCrear::class)->middleware('permission:grupo-proyecto.crear')->name('crear');
            Route::get('/editar/{id}', GrupoProyectoEditar::class)->middleware('permission:grupo-proyecto.editar')->name('editar');
        });
    });

    Route::group(['middleware' => ['permission:proyecto.navegacion']], function () {
        Route::prefix('proyecto')->name('proyecto.vista.')->group(function () {
            Route::get('/', ProyectoLista::class)->middleware('permission:proyecto.ver')->name('todo');
            Route::get('/crear', ProyectoCrear::class)->middleware('permission:proyecto.crear')->name('crear');
            Route::get('/editar/{id}', ProyectoEditar::class)->middleware('permission:proyecto.editar')->name('editar');
        });
    });

    Route::group(['middleware' => ['permission:sede.navegacion']], function () {
        Route::prefix('sede')->name('sede.vista.')->group(function () {
            Route::get('/', SedeLista::class)->middleware('permission:sede.ver')->name('todo');
            Route::get('/crear', SedeCrear::class)->middleware('permission:sede.crear')->name('crear');
            Route::get('/editar/{id}', SedeEditar::class)->middleware('permission:sede.editar')->name('editar');
        });
    });

    Route::group(['middleware' => ['permission:area.navegacion']], function () {
        Route::prefix('area')->name('area.vista.')->group(function () {
            Route::get('/', AreaLista::class)->middleware('permission:area.ver')->name('todo');
            Route::get('/crear', AreaCrear::class)->middleware('permission:area.crear')->name('crear');
            Route::get('/editar/{id}', AreaEditar::class)->middleware('permission:area.editar')->name('editar');
            Route::get('/user/{id}', AreaUser::class)->middleware('permission:area.ver-usuarios')->name('user');
            Route::get('/solicitud/{id}', AreaSolicitud::class)->middleware('permission:area.ver-solicitudes')->name('solicitud');
        });
    });
});

require __DIR__ . '/atc.php';
require __DIR__ . '/cita.php';
require __DIR__ . '/sistema.php';
require __DIR__ . '/usuario.php';
require __DIR__ . '/letra.php';
require __DIR__ . '/backoffice.php';

/*
--------------------------------------------------------------------------
PERMISOS DEL ERP
--------------------------------------------------------------------------
Convención: recurso.accion
MODULO
1. modulo-negocio.ver

UNIDAD NEGOCIO
1. unidad-negocio.navegacion
2. unidad-negocio.ver
3. unidad-negocio.crear
4. unidad-negocio.editar
5. unidad-negocio.eliminar
6. unidad-negocio.exportar

GRUPO PROYECTO
1. grupo-proyecto.navegacion
2. grupo-proyecto.ver
3. grupo-proyecto.crear
4. grupo-proyecto.editar
5. grupo-proyecto.eliminar
6. grupo-proyecto.exportar

PROYECTO
1. proyecto.navegacion
2. proyecto.ver
3. proyecto.crear
4. proyecto.editar
5. proyecto.eliminar
6. proyecto.exportar

SEDE
1. sede.navegacion
2. sede.ver
3. sede.crear
4. sede.editar
5. sede.eliminar
6. sede.exportar

AREA
1. area.navegacion
2. area.ver
3. area.crear
4. area.editar
5. area.eliminar
6. area.exportar
7. area.ver-usuarios
8. area.ver-solicitudes
9. area.agregar-usuarios
10. area.agregar-solicitudes
11. area.eliminar-usuarios
12. area.eliminar-solicitudes
13. area.exportar-usuarios
14. area.exportar-solicitudes
*/
