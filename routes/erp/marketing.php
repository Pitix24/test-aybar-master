<?php

use App\Livewire\Erp\Marketing\Tutorial\TutorialCrear;
use App\Livewire\Erp\Marketing\Tutorial\TutorialEditar;
use App\Livewire\Erp\Marketing\Tutorial\TutorialLista;
use App\Livewire\Erp\Marketing\Tutorial\TutorialVer;
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
