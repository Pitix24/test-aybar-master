<?php
use App\Livewire\Atc\Canal\CanalCrear;
use App\Livewire\Atc\Canal\CanalEditar;
use App\Livewire\Atc\Canal\CanalLista;
use App\Livewire\Atc\EstadoTicket\EstadoTicketCrear;
use App\Livewire\Atc\EstadoTicket\EstadoTicketEditar;
use App\Livewire\Atc\EstadoTicket\EstadoTicketLista;
use App\Livewire\Atc\PrioridadTicket\PrioridadTicketCrear;
use App\Livewire\Atc\PrioridadTicket\PrioridadTicketEditar;
use App\Livewire\Atc\PrioridadTicket\PrioridadTicketLista;
use App\Livewire\Atc\SubTipoSolicitud\SubTipoSolicitudCrear;
use App\Livewire\Atc\SubTipoSolicitud\SubTipoSolicitudEditar;
use App\Livewire\Atc\SubTipoSolicitud\SubTipoSolicitudLista;
use App\Livewire\Atc\Ticket\TicketCrear;
use App\Livewire\Atc\Ticket\TicketEditar;
use App\Livewire\Atc\Ticket\TicketLista;
use App\Livewire\Atc\TipoSolicitud\TipoSolicitudCrear;
use App\Livewire\Atc\TipoSolicitud\TipoSolicitudEditar;
use App\Livewire\Atc\TipoSolicitud\TipoSolicitudLista;
use Illuminate\Support\Facades\Route;

Route::prefix('tipo-solicitud')
    ->name('tipo-solicitud.vista.')
    ->group(function () {
        Route::get('/', TipoSolicitudLista::class)->name('todo');
        Route::get('/crear', TipoSolicitudCrear::class)->name('crear');
        Route::get('/editar/{id}', TipoSolicitudEditar::class)->name('editar');
    });

Route::prefix('sub-tipo-solicitud')
    ->name('sub-tipo-solicitud.vista.')
    ->group(function () {
        Route::get('/', SubTipoSolicitudLista::class)->name('todo');
        Route::get('/crear', SubTipoSolicitudCrear::class)->name('crear');
        Route::get('/editar/{id}', SubTipoSolicitudEditar::class)->name('editar');
    });

Route::prefix('estado-ticket')
    ->name('estado-ticket.vista.')
    ->group(function () {
        Route::get('/', EstadoTicketLista::class)->name('todo');
        Route::get('/crear', EstadoTicketCrear::class)->name('crear');
        Route::get('/editar/{id}', EstadoTicketEditar::class)->name('editar');
    });

Route::prefix('prioridad-ticket')
    ->name('prioridad-ticket.vista.')
    ->group(function () {
        Route::get('/', PrioridadTicketLista::class)->name('todo');
        Route::get('/crear', PrioridadTicketCrear::class)->name('crear');
        Route::get('/editar/{id}', PrioridadTicketEditar::class)->name('editar');
    });

Route::prefix('canal')
    ->name('canal.vista.')
    ->group(function () {
        Route::get('/', CanalLista::class)->name('todo');
        Route::get('/crear', CanalCrear::class)->name('crear');
        Route::get('/editar/{id}', CanalEditar::class)->name('editar');
    });

Route::prefix('ticket')
    ->name('ticket.vista.')
    ->group(function () {
        Route::get('/', TicketLista::class)->name('todo');
        Route::get('/crear/{ticketPadre?}', TicketCrear::class)->name('crear');
        Route::get('/editar/{id}', TicketEditar::class)->name('editar');
    });
