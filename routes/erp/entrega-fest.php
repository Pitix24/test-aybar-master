<?php

use App\Livewire\Erp\EntregaFest\AsistenciaEntregaFest\AsistenciaEntregaFestLista;
use App\Livewire\Erp\EntregaFest\EntregaFest\EntregaFestAsistencia;
use App\Livewire\Erp\EntregaFest\EntregaFest\EntregaFestCrear;
use App\Livewire\Erp\EntregaFest\EntregaFest\EntregaFestEditar;
use App\Livewire\Erp\EntregaFest\EntregaFest\EntregaFestInvitado;
use App\Livewire\Erp\EntregaFest\EntregaFest\EntregaFestInvitadoCrear;
use App\Livewire\Erp\EntregaFest\EntregaFest\EntregaFestInvitadoEditar;
use App\Livewire\Erp\EntregaFest\EntregaFest\EntregaFestLista;
use App\Livewire\Erp\EntregaFest\EntregaFest\EntregaFestProspecto;
use App\Livewire\Erp\EntregaFest\EntregaFest\EntregaFestProspectoCrear;
use App\Livewire\Erp\EntregaFest\EntregaFest\EntregaFestProspectoEditar;
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
                Route::get('/prospectos/{id}/crear', EntregaFestProspectoCrear::class)->middleware('permission:entrega-fest.prospectos')->name('prospectos.crear');
                Route::get('/prospectos/{id}/editar/{prospectoId}', EntregaFestProspectoEditar::class)->middleware('permission:entrega-fest.prospectos')->name('prospectos.editar');
                Route::get('/invitados/{id}', EntregaFestInvitado::class)->middleware('permission:entrega-fest.invitados')->name('invitados');
                Route::get('/invitados/{id}/crear', EntregaFestInvitadoCrear::class)->middleware('permission:entrega-fest.invitados')->name('invitados.crear');
                Route::get('/invitados/{id}/editar/{invitadoId}', EntregaFestInvitadoEditar::class)->middleware('permission:entrega-fest.invitados')->name('invitados.editar');
                Route::get('/asistencia/{id}', EntregaFestAsistencia::class)->middleware('permission:entrega-fest.asistencia')->name('asistencia');
            });
    });

});
