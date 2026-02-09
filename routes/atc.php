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

Route::group(['middleware' => ['permission:tipo-solicitud.ver']], function () {
    Route::prefix('tipo-solicitud')
        ->name('tipo-solicitud.vista.')
        ->group(function () {
            Route::get('/', TipoSolicitudLista::class)->name('todo');
            Route::get('/crear', TipoSolicitudCrear::class)->middleware('permission:tipo-solicitud.crear')->name('crear');
            Route::get('/editar/{id}', TipoSolicitudEditar::class)->middleware('permission:tipo-solicitud.editar')->name('editar');
        });
});

Route::group(['middleware' => ['permission:sub-tipo-solicitud.ver']], function () {
    Route::prefix('sub-tipo-solicitud')
        ->name('sub-tipo-solicitud.vista.')
        ->group(function () {
            Route::get('/', SubTipoSolicitudLista::class)->name('todo');
            Route::get('/crear', SubTipoSolicitudCrear::class)->middleware('permission:sub-tipo-solicitud.crear')->name('crear');
            Route::get('/editar/{id}', SubTipoSolicitudEditar::class)->middleware('permission:sub-tipo-solicitud.editar')->name('editar');
        });
});

Route::group(['middleware' => ['permission:estado-ticket.ver']], function () {
    Route::prefix('estado-ticket')
        ->name('estado-ticket.vista.')
        ->group(function () {
            Route::get('/', EstadoTicketLista::class)->name('todo');
            Route::get('/crear', EstadoTicketCrear::class)->middleware('permission:estado-ticket.crear')->name('crear');
            Route::get('/editar/{id}', EstadoTicketEditar::class)->middleware('permission:estado-ticket.editar')->name('editar');
        });
});

Route::group(['middleware' => ['permission:prioridad-ticket.ver']], function () {
    Route::prefix('prioridad-ticket')
        ->name('prioridad-ticket.vista.')
        ->group(function () {
            Route::get('/', PrioridadTicketLista::class)->name('todo');
            Route::get('/crear', PrioridadTicketCrear::class)->middleware('permission:prioridad-ticket.crear')->name('crear');
            Route::get('/editar/{id}', PrioridadTicketEditar::class)->middleware('permission:prioridad-ticket.editar')->name('editar');
        });
});

Route::group(['middleware' => ['permission:canal.ver']], function () {
    Route::prefix('canal')
        ->name('canal.vista.')
        ->group(function () {
            Route::get('/', CanalLista::class)->name('todo');
            Route::get('/crear', CanalCrear::class)->middleware('permission:canal.crear')->name('crear');
            Route::get('/editar/{id}', CanalEditar::class)->middleware('permission:canal.editar')->name('editar');
        });
});

Route::group(['middleware' => ['permission:ticket.ver']], function () {
    Route::prefix('ticket')
        ->name('ticket.vista.')
        ->group(function () {
            Route::get('/', TicketLista::class)->name('todo');
            Route::get('/crear/{ticketPadre?}', TicketCrear::class)->middleware('permission:ticket.crear')->name('crear');
            Route::get('/editar/{id}', TicketEditar::class)->middleware('permission:ticket.editar')->name('editar');
        });
});
