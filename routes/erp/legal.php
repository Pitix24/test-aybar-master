<?php

use App\Livewire\Erp\Legal\LibroReclamacion\LibroReclamacionCrear;
use App\Livewire\Erp\Legal\LibroReclamacion\LibroReclamacionEditar;
use App\Livewire\Erp\Legal\LibroReclamacion\LibroReclamacionLista;
use App\Livewire\Erp\Legal\LibroReclamacion\LibroReclamacionVer;
use App\Livewire\Erp\Legal\TicketNotarial\TicketNotarialLista;
use App\Livewire\Erp\Atc\Ticket\TicketCrear;
use App\Livewire\Erp\Atc\Ticket\TicketEditar;
use App\Livewire\Erp\Atc\Ticket\TicketVer;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['permission:modulo-legal.ver']], function () {
    Route::group(['middleware' => ['permission:libro-reclamacion.navegacion']], function () {
        Route::prefix('libro-reclamacion')
            ->name('libro-reclamacion.vista.')
            ->group(function () {
                Route::get('/', LibroReclamacionLista::class)->middleware('permission:libro-reclamacion.lista')->name('todo');
                Route::get('/ver/{id}', LibroReclamacionVer::class)->middleware('permission:libro-reclamacion.ver')->name('ver');

                if (config('libro_reclamacion.crear_erp_habilitado')) {
                    Route::get('/crear', LibroReclamacionCrear::class)->middleware('permission:libro-reclamacion.crear')->name('crear');
                }

                Route::get('/editar/{id}', LibroReclamacionEditar::class)->middleware('permission:libro-reclamacion.editar')->name('editar');
            });
    });

    Route::group(['middleware' => ['permission:ticket-notarial.navegacion']], function () {
        Route::prefix('ticket-notarial')
            ->name('ticket-notarial.vista.')
            ->group(function () {
                Route::get('/', TicketNotarialLista::class)->middleware('permission:ticket-notarial.lista')->name('todo');
                Route::get('/crear', TicketCrear::class)->middleware('permission:ticket-notarial.crear')->name('crear');
                Route::get('/ver/{id}', TicketVer::class)->middleware('permission:ticket-notarial.ver')->name('ver');
                Route::get('/editar/{id}', TicketEditar::class)->middleware('permission:ticket-notarial.editar')->name('editar');
            });
    });
});
