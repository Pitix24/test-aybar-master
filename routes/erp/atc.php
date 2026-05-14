<?php

use App\Livewire\Erp\Atc\Canal\CanalCrear;
use App\Livewire\Erp\Atc\Canal\CanalEditar;
use App\Livewire\Erp\Atc\Canal\CanalLista;
use App\Livewire\Erp\Atc\Canal\CanalVer;
use App\Livewire\Erp\Atc\EstadoTicket\EstadoTicketCrear;
use App\Livewire\Erp\Atc\EstadoTicket\EstadoTicketEditar;
use App\Livewire\Erp\Atc\EstadoTicket\EstadoTicketLista;
use App\Livewire\Erp\Atc\EstadoTicket\EstadoTicketVer;
use App\Livewire\Erp\Atc\PrioridadTicket\PrioridadTicketCrear;
use App\Livewire\Erp\Atc\PrioridadTicket\PrioridadTicketEditar;
use App\Livewire\Erp\Atc\PrioridadTicket\PrioridadTicketLista;
use App\Livewire\Erp\Atc\PrioridadTicket\PrioridadTicketVer;
use App\Livewire\Erp\Atc\SubTipoSolicitud\SubTipoSolicitudCrear;
use App\Livewire\Erp\Atc\SubTipoSolicitud\SubTipoSolicitudEditar;
use App\Livewire\Erp\Atc\SubTipoSolicitud\SubTipoSolicitudLista;
use App\Livewire\Erp\Atc\SubTipoSolicitud\SubTipoSolicitudVer;
use App\Livewire\Erp\Atc\Ticket\TicketCrear;
use App\Livewire\Erp\Atc\Ticket\TicketDerivar;
use App\Livewire\Erp\Atc\Ticket\TicketEditar;
use App\Livewire\Erp\Atc\Ticket\TicketImportar;
use App\Livewire\Erp\Atc\Ticket\TicketLista;
use App\Livewire\Erp\Atc\Ticket\TicketVer;
use App\Livewire\Erp\Atc\TipoSolicitud\TipoSolicitudCrear;
use App\Livewire\Erp\Atc\TipoSolicitud\TipoSolicitudEditar;
use App\Livewire\Erp\Atc\TipoSolicitud\TipoSolicitudFlujo;
use App\Livewire\Erp\Atc\TipoSolicitud\TipoSolicitudLista;
use App\Livewire\Erp\Atc\TipoSolicitud\TipoSolicitudUser;
use App\Livewire\Erp\Atc\TipoSolicitud\TipoSolicitudVer;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['permission:modulo-atc.ver']], function () {
    Route::group(['middleware' => ['permission:tipo-solicitud.navegacion']], function () {
        Route::prefix('tipo-solicitud')
            ->name('tipo-solicitud.vista.')
            ->group(function () {
                Route::get('/', TipoSolicitudLista::class)->middleware('permission:tipo-solicitud.vista-lista')->name('todo');
                Route::get('/ver/{id}', TipoSolicitudVer::class)->middleware('permission:tipo-solicitud.vista-ver')->name('ver');
                Route::get('/crear', TipoSolicitudCrear::class)->middleware('permission:tipo-solicitud.vista-crear')->name('crear');
                Route::get('/editar/{id}', TipoSolicitudEditar::class)->middleware('permission:tipo-solicitud.vista-editar')->name('editar');
                Route::get('/usuarios/{id}', TipoSolicitudUser::class)->middleware('permission:tipo-solicitud.vista-agregar-usuario')->name('usuarios');
                Route::get('/flujo/{id}', TipoSolicitudFlujo::class)->middleware('permission:tipo-solicitud.vista-flujo')->name('flujo');
            });
    });

    Route::group(['middleware' => ['permission:sub-tipo-solicitud.navegacion']], function () {
        Route::prefix('sub-tipo-solicitud')
            ->name('sub-tipo-solicitud.vista.')
            ->group(function () {
                Route::get('/', SubTipoSolicitudLista::class)->middleware('permission:sub-tipo-solicitud.vista-lista')->name('todo');
                Route::get('/ver/{id}', SubTipoSolicitudVer::class)->middleware('permission:sub-tipo-solicitud.vista-ver')->name('ver');
                Route::get('/crear', SubTipoSolicitudCrear::class)->middleware('permission:sub-tipo-solicitud.vista-crear')->name('crear');
                Route::get('/editar/{id}', SubTipoSolicitudEditar::class)->middleware('permission:sub-tipo-solicitud.vista-editar')->name('editar');
            });
    });

    Route::group(['middleware' => ['permission:estado-ticket.navegacion']], function () {
        Route::prefix('estado-ticket')
            ->name('estado-ticket.vista.')
            ->group(function () {
                Route::get('/', EstadoTicketLista::class)->middleware('permission:estado-ticket.vista-lista')->name('todo');
                Route::get('/ver/{id}', EstadoTicketVer::class)->middleware('permission:estado-ticket.vista-ver')->name('ver');
                Route::get('/crear', EstadoTicketCrear::class)->middleware('permission:estado-ticket.vista-crear')->name('crear');
                Route::get('/editar/{id}', EstadoTicketEditar::class)->middleware('permission:estado-ticket.vista-editar')->name('editar');
            });
    });

    Route::group(['middleware' => ['permission:prioridad-ticket.navegacion']], function () {
        Route::prefix('prioridad-ticket')
            ->name('prioridad-ticket.vista.')
            ->group(function () {
                Route::get('/', PrioridadTicketLista::class)->middleware('permission:prioridad-ticket.vista-lista')->name('todo');
                Route::get('/ver/{id}', PrioridadTicketVer::class)->middleware('permission:prioridad-ticket.vista-ver')->name('ver');
                Route::get('/crear', PrioridadTicketCrear::class)->middleware('permission:prioridad-ticket.vista-crear')->name('crear');
                Route::get('/editar/{id}', PrioridadTicketEditar::class)->middleware('permission:prioridad-ticket.vista-editar')->name('editar');
            });
    });

    Route::group(['middleware' => ['permission:canal.navegacion']], function () {
        Route::prefix('canal')
            ->name('canal.vista.')
            ->group(function () {
                Route::get('/', CanalLista::class)->middleware('permission:canal.vista-lista')->name('todo');
                Route::get('/ver/{id}', CanalVer::class)->middleware('permission:canal.vista-ver')->name('ver');
                Route::get('/crear', CanalCrear::class)->middleware('permission:canal.vista-crear')->name('crear');
                Route::get('/editar/{id}', CanalEditar::class)->middleware('permission:canal.vista-editar')->name('editar');
            });
    });

    Route::group(['middleware' => ['permission:ticket.navegacion']], function () {
        Route::prefix('ticket')
            ->name('ticket.vista.')
            ->group(function () {
                Route::get('/', TicketLista::class)->middleware('permission:ticket.lista')->name('todo');
                Route::get('/ver/{id}', TicketVer::class)->middleware('permission:ticket.ver')->name('ver');
                Route::get('/crear/{ticketPadre?}', TicketCrear::class)->middleware('permission:ticket.crear')->name('crear');
                Route::get('/editar/{id}', TicketEditar::class)->middleware('permission:ticket.editar')->name('editar');
                Route::get('/derivado/{id}', TicketDerivar::class)->middleware('permission:ticket.derivar')->name('derivar');
                Route::get('/importar', TicketImportar::class)->middleware('permission:ticket.importar-tickets')->name('importar');
            });
    });
});

/*
--------------------------------------------------------------------------
PERMISOS ATC
--------------------------------------------------------------------------
Convención: recurso.accion
MODULO
1. modulo-atc.ver

TIPO SOLICITUD
1. tipo-solicitud.navegacion
2. tipo-solicitud.lista
3. tipo-solicitud.ver
4. tipo-solicitud.crear
5. tipo-solicitud.editar
6. tipo-solicitud.eliminar
7. tipo-solicitud.exportar-filtro
8. tipo-solicitud.exportar-todo

SUB TIPO SOLICITUD
1. sub-tipo-solicitud.navegacion
2. sub-tipo-solicitud.lista
3. sub-tipo-solicitud.ver
4. sub-tipo-solicitud.crear
5. sub-tipo-solicitud.editar
6. sub-tipo-solicitud.eliminar
7. sub-tipo-solicitud.exportar-filtro
8. sub-tipo-solicitud.exportar-todo

PRIORIDAD TICKET
1. prioridad-ticket.navegacion
2. prioridad-ticket.lista
3. prioridad-ticket.ver
4. prioridad-ticket.crear
5. prioridad-ticket.editar
6. prioridad-ticket.eliminar
7. prioridad-ticket.exportar-filtro
8. prioridad-ticket.exportar-todo

ESTADO TICKET
1. estado-ticket.navegacion
2. estado-ticket.lista
3. estado-ticket.ver
4. estado-ticket.crear
5. estado-ticket.editar
6. estado-ticket.eliminar
7. estado-ticket.exportar-filtro
8. estado-ticket.exportar-todo

CANAL
1. canal.navegacion
2. canal.lista
3. canal.ver
4. canal.crear
5. canal.editar
6. canal.eliminar
7. canal.exportar-filtro
8. canal.exportar-todo

TICKET
1. ticket.navegacion
2. ticket.lista
3. ticket.ver
4. ticket.crear
5. ticket.editar
6. ticket.eliminar
7. ticket.exportar-filtro
8. ticket.exportar-todo
9. ticket.derivar
10. ticket.agregar-archivo
11. ticket.eliminar-archivo
11. ticket.ver-archivo
12. ticket.enviar-correo
13. ticket.chat
*/
