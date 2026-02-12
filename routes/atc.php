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
use App\Livewire\Atc\Ticket\TicketDerivar;
use App\Livewire\Atc\Ticket\TicketEditar;
use App\Livewire\Atc\Ticket\TicketLista;
use App\Livewire\Atc\TipoSolicitud\TipoSolicitudCrear;
use App\Livewire\Atc\TipoSolicitud\TipoSolicitudEditar;
use App\Livewire\Atc\TipoSolicitud\TipoSolicitudLista;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['permission:modulo-atc.ver']], function () {
    Route::group(['middleware' => ['permission:tipo-solicitud.navegacion']], function () {
        Route::prefix('tipo-solicitud')
            ->name('tipo-solicitud.vista.')
            ->group(function () {
                Route::get('/', TipoSolicitudLista::class)->middleware('permission:tipo-solicitud.ver')->name('todo');
                Route::get('/crear', TipoSolicitudCrear::class)->middleware('permission:tipo-solicitud.crear')->name('crear');
                Route::get('/editar/{id}', TipoSolicitudEditar::class)->middleware('permission:tipo-solicitud.editar')->name('editar');
            });
    });

    Route::group(['middleware' => ['permission:sub-tipo-solicitud.navegacion']], function () {
        Route::prefix('sub-tipo-solicitud')
            ->name('sub-tipo-solicitud.vista.')
            ->group(function () {
                Route::get('/', SubTipoSolicitudLista::class)->middleware('permission:sub-tipo-solicitud.ver')->name('todo');
                Route::get('/crear', SubTipoSolicitudCrear::class)->middleware('permission:sub-tipo-solicitud.crear')->name('crear');
                Route::get('/editar/{id}', SubTipoSolicitudEditar::class)->middleware('permission:sub-tipo-solicitud.editar')->name('editar');
            });
    });

    Route::group(['middleware' => ['permission:estado-ticket.navegacion']], function () {
        Route::prefix('estado-ticket')
            ->name('estado-ticket.vista.')
            ->group(function () {
                Route::get('/', EstadoTicketLista::class)->middleware('permission:estado-ticket.ver')->name('todo');
                Route::get('/crear', EstadoTicketCrear::class)->middleware('permission:estado-ticket.crear')->name('crear');
                Route::get('/editar/{id}', EstadoTicketEditar::class)->middleware('permission:estado-ticket.editar')->name('editar');
            });
    });

    Route::group(['middleware' => ['permission:prioridad-ticket.navegacion']], function () {
        Route::prefix('prioridad-ticket')
            ->name('prioridad-ticket.vista.')
            ->group(function () {
                Route::get('/', PrioridadTicketLista::class)->middleware('permission:prioridad-ticket.ver')->name('todo');
                Route::get('/crear', PrioridadTicketCrear::class)->middleware('permission:prioridad-ticket.crear')->name('crear');
                Route::get('/editar/{id}', PrioridadTicketEditar::class)->middleware('permission:prioridad-ticket.editar')->name('editar');
            });
    });

    Route::group(['middleware' => ['permission:canal.navegacion']], function () {
        Route::prefix('canal')
            ->name('canal.vista.')
            ->group(function () {
                Route::get('/', CanalLista::class)->middleware('permission:canal.ver')->name('todo');
                Route::get('/crear', CanalCrear::class)->middleware('permission:canal.crear')->name('crear');
                Route::get('/editar/{id}', CanalEditar::class)->middleware('permission:canal.editar')->name('editar');
            });
    });

    Route::group(['middleware' => ['permission:ticket.navegacion']], function () {
        Route::prefix('ticket')
            ->name('ticket.vista.')
            ->group(function () {
                Route::get('/', TicketLista::class)->middleware('permission:ticket.ver')->name('todo');
                Route::get('/crear/{ticketPadre?}', TicketCrear::class)->middleware('permission:ticket.crear')->name('crear');
                Route::get('/editar/{id}', TicketEditar::class)->middleware('permission:ticket.editar')->name('editar');
                Route::get('/derivado/{id}', TicketDerivar::class)->middleware('permission:ticket.derivar')->name('derivar');
            });
    });
});

/*
--------------------------------------------------------------------------
PERMISOS ATC
--------------------------------------------------------------------------
Convención: recurso.accion

TIPO SOLICITUD
1. tipo-solicitud.navegacion
2. tipo-solicitud.ver
3. tipo-solicitud.crear
4. tipo-solicitud.editar
5. tipo-solicitud.eliminar
6. tipo-solicitud.exportar

SUB TIPO SOLICITUD
1. sub-tipo-solicitud.navegacion
2. sub-tipo-solicitud.ver
3. sub-tipo-solicitud.crear
4. sub-tipo-solicitud.editar
5. sub-tipo-solicitud.eliminar
6. sub-tipo-solicitud.exportar

PRIORIDAD TICKET
1. prioridad-ticket.navegacion
2. prioridad-ticket.ver
3. prioridad-ticket.crear
4. prioridad-ticket.editar
5. prioridad-ticket.eliminar
6. prioridad-ticket.exportar

ESTADO TICKET
1. estado-ticket.navegacion
2. estado-ticket.ver
3. estado-ticket.crear
4. estado-ticket.editar
5. estado-ticket.eliminar
6. estado-ticket.exportar

CANAL
1. canal.navegacion
2. canal.ver
3. canal.crear
4. canal.editar
5. canal.eliminar
6. canal.exportar

TICKET
1. ticket.navegacion
2. ticket.ver
3. ticket.crear
4. ticket.editar
5. ticket.eliminar
6. ticket.exportar
7. ticket.derivar
8. ticket.validar
9. ticket.reportar
*/
