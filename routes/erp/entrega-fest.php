<?php

use App\Livewire\Erp\EntregaFest\AsistenciaEntregaFest\AsistenciaEntregaFestLista;
use App\Livewire\Erp\EntregaFest\EntregaFest\EntregaFestCrear;
use App\Livewire\Erp\EntregaFest\EntregaFest\EntregaFestEditar;
use App\Livewire\Erp\EntregaFest\EntregaFest\EntregaFestInvitado;
use App\Livewire\Erp\EntregaFest\EntregaFest\EntregaFestLista;
use App\Livewire\Erp\EntregaFest\EntregaFest\EntregaFestProspecto;
use App\Livewire\Erp\EntregaFest\EntregaFest\EntregaFestVer;
use App\Livewire\Erp\EntregaFest\InvitadoEntregaFest\InvitadoEntregaFestLista;
use App\Livewire\Erp\EntregaFest\ProspectoEntregaFest\ProspectoEntregaFestCrear;
use App\Livewire\Erp\EntregaFest\ProspectoEntregaFest\ProspectoEntregaFestEditar;
use App\Livewire\Erp\EntregaFest\ProspectoEntregaFest\ProspectoEntregaFestLista;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['permission:modulo-entrega-fest.ver']], function () {

    // Gestión de Eventos (Entrega Fest)
    Route::group(['middleware' => ['permission:entrega-fest.navegacion']], function () {
        Route::prefix('entrega-fest')
            ->name('entrega-fest.vista.')
            ->group(function () {
                Route::get('/', EntregaFestLista::class)->middleware('permission:entrega-fest.lista')->name('todo');
                Route::get('/ver/{id}', EntregaFestVer::class)->middleware('permission:entrega-fest.ver')->name('ver');
                Route::get('/crear', EntregaFestCrear::class)->middleware('permission:entrega-fest.crear')->name('crear');
                Route::get('/editar/{id}', EntregaFestEditar::class)->middleware('permission:entrega-fest.editar')->name('editar');
                Route::get('/prospectos/{id}', EntregaFestProspecto::class)->middleware('permission:entrega-fest.prospectos')->name('prospectos');
                Route::get('/invitados/{id}', EntregaFestInvitado::class)->middleware('permission:entrega-fest.invitados')->name('invitados');
            });
    });

    // Gestión de Prospectos
    Route::group(['middleware' => ['permission:prospecto-entrega-fest.navegacion']], function () {
        Route::prefix('prospecto')
            ->name('prospecto-entrega-fest.vista.')
            ->group(function () {
                Route::get('/', ProspectoEntregaFestLista::class)->middleware('permission:prospecto-entrega-fest.lista')->name('todo');
                Route::get('/crear/{eventoId?}', ProspectoEntregaFestCrear::class)->middleware('permission:prospecto-entrega-fest.crear')->name('crear');
                Route::get('/editar/{id}', ProspectoEntregaFestEditar::class)->middleware('permission:prospecto-entrega-fest.editar')->name('editar');
            });
    });

    // Gestión de Invitados
    Route::group(['middleware' => ['permission:invitado-entrega-fest.navegacion']], function () {
        Route::prefix('invitado')
            ->name('invitado-entrega-fest.vista.')
            ->group(function () {
                Route::get('/', InvitadoEntregaFestLista::class)->middleware('permission:invitado-entrega-fest.lista')->name('todo');
            });
    });

    // Control de Asistencia
    Route::group(['middleware' => ['permission:asistencia-entrega-fest.navegacion']], function () {
        Route::prefix('asistencia')
            ->name('asistencia-entrega-fest.vista.')
            ->group(function () {
                Route::get('/', AsistenciaEntregaFestLista::class)->middleware('permission:asistencia-entrega-fest.lista')->name('todo');
            });
    });

});
