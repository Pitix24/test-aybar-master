<?php

use App\Livewire\Erp\Marketing\Tutorial\TutorialCrear;
use App\Livewire\Erp\Marketing\Tutorial\TutorialEditar;
use App\Livewire\Erp\Marketing\Tutorial\TutorialLista;
use App\Livewire\Erp\Marketing\Tutorial\TutorialVer;
use App\Livewire\Erp\Marketing\AvanceProyecto\AvanceProyectoCrear;
use App\Livewire\Erp\Marketing\AvanceProyecto\AvanceProyectoEditar;
use App\Livewire\Erp\Marketing\AvanceProyecto\AvanceProyectoLista;
use App\Livewire\Erp\Marketing\AvanceProyecto\AvanceProyectoVer;
use App\Livewire\Erp\Marketing\Reglamento\ReglamentoCrear;
use App\Livewire\Erp\Marketing\Reglamento\ReglamentoEditar;
use App\Livewire\Erp\Marketing\Reglamento\ReglamentoLista;
use App\Livewire\Erp\Marketing\Reglamento\ReglamentoVer;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['permission:modulo-marketing.ver']], function () {
    Route::group(['middleware' => ['permission:tutorial.navegacion']], function () {
        Route::prefix('tutorial')->name('tutorial.vista.')->group(function () {
            Route::get('/', TutorialLista::class)->middleware('permission:tutorial.lista')->name('todo');
            Route::get('/ver/{id}', TutorialVer::class)->middleware('permission:tutorial.ver')->name('ver');
            Route::get('/crear', TutorialCrear::class)->middleware('permission:tutorial.crear')->name('crear');
            Route::get('/editar/{id}', TutorialEditar::class)->middleware('permission:tutorial.editar')->name('editar');
        });
    });

    Route::group(['middleware' => []], function () {
        Route::prefix('avance-proyecto')->name('avance-proyecto.vista.')->group(function () {
            Route::get('/', AvanceProyectoLista::class)->name('todo');
            Route::get('/ver/{id}', AvanceProyectoVer::class)->name('ver');
            Route::get('/crear', AvanceProyectoCrear::class)->name('crear');
            Route::get('/editar/{id}', AvanceProyectoEditar::class)->name('editar');
        });
    });

    Route::group(['middleware' => []], function () {
        Route::prefix('reglamento')->name('reglamento.vista.')->group(function () {
            Route::get('/', ReglamentoLista::class)->middleware('permission:reglamento.lista')->name('todo');
            Route::get('/ver/{id}', ReglamentoVer::class)->middleware('permission:reglamento.ver')->name('ver');
            Route::get('/crear', ReglamentoCrear::class)->middleware('permission:reglamento.crear')->name('crear');
            Route::get('/editar/{id}', ReglamentoEditar::class)->middleware('permission:reglamento.editar')->name('editar');
        });
    });
});

/*
--------------------------------------------------------------------------
PERMISOS DE MARKETING
--------------------------------------------------------------------------
Convención: recurso.accion
MODULO
1. modulo-marketing.ver

TUTORIAL
1. tutorial.navegacion (Permite ver el ítem en el menú)
2. tutorial.lista
3. tutorial.ver
4. tutorial.crear
5. tutorial.editar
6. tutorial.eliminar
7. tutorial.exportar-filtro
8. tutorial.exportar-todo
*/
