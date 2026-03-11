<?php

use App\Livewire\Erp\EntregaFest\EntregaFest\EntregaFestCrear;
use App\Livewire\Erp\EntregaFest\EntregaFest\EntregaFestLista;
use App\Livewire\Erp\EntregaFest\EntregaFest\EntregaFestEditar;
use App\Livewire\Erp\EntregaFest\EntregaFest\EntregaFestPanel;
use App\Livewire\Erp\EntregaFest\EntregaFest\EntregaFestVer;
use App\Livewire\Erp\EntregaFest\EntregaFest\StaffDashboard;
use App\Livewire\Erp\EntregaFest\Asistencia\EntregaFestAsistencia;
use App\Livewire\Erp\EntregaFest\Invitado\EntregaFestInvitado;
use App\Livewire\Erp\EntregaFest\Invitado\EntregaFestInvitadoCrear;
use App\Livewire\Erp\EntregaFest\Invitado\EntregaFestInvitadoEditar;
use App\Livewire\Erp\EntregaFest\Prospecto\EntregaFestProspecto;
use App\Livewire\Erp\EntregaFest\Prospecto\EntregaFestProspectoCrear;
use App\Livewire\Erp\EntregaFest\Prospecto\EntregaFestProspectoEditar;
use App\Livewire\Erp\EntregaFest\Incidencia\StaffIncidencias;
use App\Livewire\Erp\EntregaFest\Incidencia\StaffIncidenciasCrear;
use App\Livewire\Erp\EntregaFest\Incidencia\StaffIncidenciasEditar;
use App\Livewire\Erp\EntregaFest\Contingencia\StaffContingencias;
use App\Livewire\Erp\EntregaFest\Contingencia\StaffContingenciasCrear;
use App\Livewire\Erp\EntregaFest\Contingencia\StaffContingenciasEditar;
use App\Livewire\Erp\EntregaFest\Protocolo\StaffProtocolos;
use App\Livewire\Erp\EntregaFest\Protocolo\StaffProtocolosCrear;
use App\Livewire\Erp\EntregaFest\Protocolo\StaffProtocolosEditar;
use App\Livewire\Erp\EntregaFest\Itinerario\StaffItinerario;
use App\Livewire\Erp\EntregaFest\Itinerario\StaffItinerarioCrear;
use App\Livewire\Erp\EntregaFest\Itinerario\StaffItinerarioEditar;
use App\Livewire\Erp\EntregaFest\Mop\MopPlantillaCrear;
use App\Livewire\Erp\EntregaFest\Mop\MopPlantillaEditar;
use App\Livewire\Erp\EntregaFest\Mop\MopPlantillaLista;
use App\Livewire\Erp\EntregaFest\Mop\MopTareaCrear;
use App\Livewire\Erp\EntregaFest\Mop\MopTareaEditar;
use App\Livewire\Erp\EntregaFest\Mop\MopTareaLista;
use App\Livewire\Erp\EntregaFest\Mop\StaffMop;
use App\Livewire\Erp\EntregaFest\Proveedor\StaffProveedores;
use App\Livewire\Erp\EntregaFest\Proveedor\StaffProveedoresCrear;
use App\Livewire\Erp\EntregaFest\Proveedor\StaffProveedoresEditar;
use App\Livewire\Erp\EntregaFest\Recurso\StaffRecursos;
use App\Livewire\Erp\EntregaFest\Recurso\StaffRecursosCrear;
use App\Livewire\Erp\EntregaFest\Recurso\StaffRecursosEditar;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['permission:modulo-entrega-fest.ver']], function () {

    Route::group(['middleware' => ['permission:entrega-fest.navegacion']], function () {
        Route::prefix('entrega-fest')
            ->name('entrega-fest.vista.')
            ->group(function () {
                Route::get('/', EntregaFestLista::class)->middleware('permission:entrega-fest.lista')->name('todo');
                Route::get('/crear', EntregaFestCrear::class)->middleware('permission:entrega-fest.crear')->name('crear');
                Route::get('/ver/{id}', EntregaFestVer::class)->middleware('permission:entrega-fest.ver')->name('ver');
                Route::get('/editar/{id}', EntregaFestEditar::class)->middleware('permission:entrega-fest.editar')->name('editar');
                Route::get('/panel/{id}', EntregaFestPanel::class)->middleware('permission:entrega-fest.ver-panel')->name('panel');
                Route::get('/staff/{id}', StaffDashboard::class)->middleware('permission:entrega-fest.ver-staff')->name('staff');
            });
    });

    Route::group(['middleware' => ['permission:prospecto.navegacion']], function () {
        Route::prefix('entrega-fest/prospecto')
            ->name('entrega-fest.prospecto.')
            ->group(function () {
                Route::get('/{id}', EntregaFestProspecto::class)->middleware('permission:prospecto.lista')->name('todo');
                Route::get('/crear/{id}', EntregaFestProspectoCrear::class)->middleware('permission:prospecto.crear')->name('crear');
                Route::get('/editar/{id}/{prospectoId}', EntregaFestProspectoEditar::class)->middleware('permission:prospecto.editar')->name('editar');
            });
    });

    Route::group(['middleware' => ['permission:invitado.navegacion']], function () {
        Route::prefix('entrega-fest/invitado')
            ->name('entrega-fest.invitado.')
            ->group(function () {
                Route::get('/{id}', EntregaFestInvitado::class)->middleware('permission:invitado.lista')->name('todo');
                Route::get('/crear/{id}', EntregaFestInvitadoCrear::class)->middleware('permission:invitado.crear')->name('crear');
                Route::get('/editar/{id}/{invitadoId}', EntregaFestInvitadoEditar::class)->middleware('permission:invitado.editar')->name('editar');
            });
    });

    Route::group(['middleware' => ['permission:asistencia.navegacion']], function () {
        Route::prefix('entrega-fest/asistencia')
            ->name('entrega-fest.asistencia.')
            ->group(function () {
                Route::get('/{id}', EntregaFestAsistencia::class)->middleware('permission:asistencia.lista')->name('todo');
            });
    });

    Route::group(['middleware' => ['permission:itinerario.navegacion']], function () {
        Route::prefix('entrega-fest/itinerario')
            ->name('entrega-fest.itinerario.')
            ->group(function () {
                Route::get('/{id}', StaffItinerario::class)->middleware('permission:itinerario.lista')->name('todo');
                Route::get('/crear/{id}', StaffItinerarioCrear::class)->middleware('permission:itinerario.crear')->name('crear');
                Route::get('/editar/{id}/{bloqueId}', StaffItinerarioEditar::class)->middleware('permission:itinerario.editar')->name('editar');
            });
    });

    Route::group(['middleware' => ['permission:mop.navegacion']], function () {
        Route::prefix('entrega-fest/mop')
            ->name('entrega-fest.mop.')
            ->group(function () {
                Route::get('/{id}', StaffMop::class)->middleware('permission:mop.lista')->name('todo');
                Route::get('/tareas/{id}', MopTareaLista::class)->middleware('permission:mop.tareas')->name('tareas');
                Route::get('/tareas/crear/{id}', MopTareaCrear::class)->middleware('permission:mop.tareas.crear')->name('tareas.crear');
                Route::get('/tareas/{id}/editar/{tareaId}', MopTareaEditar::class)->middleware('permission:mop.tareas.editar')->name('tareas.editar');
            });
    });

    Route::group(['middleware' => ['permission:mop-plantilla.navegacion']], function () {
        Route::prefix('entrega-fest/mop-plantilla')
            ->name('entrega-fest.mop-plantilla.')
            ->group(function () {
                Route::get('/', MopPlantillaLista::class)->middleware('permission:mop-plantilla.lista')->name('todo');
                Route::get('/crear', MopPlantillaCrear::class)->middleware('permission:mop-plantilla.crear')->name('crear');
                Route::get('/editar/{id}', MopPlantillaEditar::class)->middleware('permission:mop-plantilla.editar')->name('editar');
            });
    });

    Route::group(['middleware' => ['permission:proveedor.navegacion']], function () {
        Route::prefix('entrega-fest/proveedor')
            ->name('entrega-fest.proveedor.')
            ->group(function () {
                Route::get('/{id}', StaffProveedores::class)->middleware('permission:proveedor.lista')->name('todo');
                Route::get('/crear/{id}', StaffProveedoresCrear::class)->middleware('permission:proveedor.crear')->name('crear');
                Route::get('/editar/{id}/{proveedorId}', StaffProveedoresEditar::class)->middleware('permission:proveedor.editar')->name('editar');
            });
    });

    Route::group(['middleware' => ['permission:incidencia.navegacion']], function () {
        Route::prefix('entrega-fest/incidencia')
            ->name('entrega-fest.incidencia.')
            ->group(function () {
                Route::get('/{id}', StaffIncidencias::class)->middleware('permission:incidencia.lista')->name('todo');
                Route::get('/crear/{id}', StaffIncidenciasCrear::class)->middleware('permission:incidencia.crear')->name('crear');
                Route::get('/editar/{id}/{incidenciasId}', StaffIncidenciasEditar::class)->middleware('permission:incidencia.editar')->name('editar');
            });
    });

    Route::group(['middleware' => ['permission:protocolo.navegacion']], function () {
        Route::prefix('entrega-fest/protocolo')
            ->name('entrega-fest.protocolo.')
            ->group(function () {
                Route::get('/{id}', StaffProtocolos::class)->middleware('permission:protocolo.lista')->name('todo');
                Route::get('/crear/{id}', StaffProtocolosCrear::class)->middleware('permission:protocolo.crear')->name('crear');
                Route::get('/editar/{id}/{protocoloId}', StaffProtocolosEditar::class)->middleware('permission:protocolo.editar')->name('editar');
            });
    });

    Route::group(['middleware' => ['permission:contingencia.navegacion']], function () {
        Route::prefix('entrega-fest/contingencia')
            ->name('entrega-fest.contingencia.')
            ->group(function () {
                Route::get('/{id}', StaffContingencias::class)->middleware('permission:contingencia.lista')->name('todo');
                Route::get('/crear/{id}', StaffContingenciasCrear::class)->middleware('permission:contingencia.crear')->name('crear');
                Route::get('/editar/{id}/{contingenciaId}', StaffContingenciasEditar::class)->middleware('permission:contingencia.editar')->name('editar');
            });
    });

    Route::group(['middleware' => ['permission:recurso.navegacion']], function () {
        Route::prefix('entrega-fest/recurso')
            ->name('entrega-fest.recurso.')
            ->group(function () {
                Route::get('/{id}', StaffRecursos::class)->middleware('permission:recurso.lista')->name('todo');
                Route::get('/crear/{id}', StaffRecursosCrear::class)->middleware('permission:recurso.crear')->name('crear');
                Route::get('/editar/{id}/{recursoId}', StaffRecursosEditar::class)->middleware('permission:recurso.editar')->name('editar');
            });
    });
});


/*
--------------------------------------------------------------------------
ROLES ENTREGA FEST
--------------------------------------------------------------------------
supervisor-entrega-fest
asesor-entrega-fest
staff-asistencia
staff-itinerario
staff-mop
staff-proveedores
staff-incidencias
staff-recursos
staff-protocolo
staff-contingencia

--------------------------------------------------------------------------
PERMISOS ENTREGA FEST
--------------------------------------------------------------------------
Convención: recurso.accion
MODULO
ROLES: supervisor-entrega-fest, asesor-entrega-fest
1. modulo-entrega-fest.ver

ENTREGA FEST
ROLES: supervisor-entrega-fest, asesor-entrega-fest
1. entrega-fest.navegacion
2. entrega-fest.lista
3. entrega-fest.ver
4. entrega-fest.crear
5. entrega-fest.editar
6. entrega-fest.eliminar
7. entrega-fest.exportar-filtro
8. entrega-fest.exportar-todo
9. entrega-fest.ver-panel
10. entrega-fest.ver-staff

PROSPECTO
ROLES: supervisor-entrega-fest, asesor-entrega-fest, supervisor-backoffice, asesor-backoffice, supervisor-legal, asesor-legal
1. prospecto.navegacion
2. prospecto.lista
3. prospecto.ver
4. prospecto.crear
5. prospecto.editar
6. prospecto.eliminar
7. prospecto.exportar-filtro
8. prospecto.exportar-todo

INVITADO
ROLES: supervisor-entrega-fest, asesor-entrega-fest
1. invitado.navegacion
2. invitado.lista
3. invitado.ver
4. invitado.exportar-filtro
5. invitado.exportar-todo

ASISTENCIA
ROLES: supervisor-entrega-fest, asesor-entrega-fest, staff-asistencia
1. asistencia.navegacion
2. asistencia.lista
3. asistencia.ver
4. asistencia.marcar
5. asistencia.exportar-filtro
6. asistencia.exportar-todo

ITINERARIO
ROLES: supervisor-entrega-fest, asesor-entrega-fest, staff-itinerario
1. itinerario.navegacion
2. itinerario.lista
3. itinerario.ver
4. itinerario.crear
5. itinerario.editar
6. itinerario.eliminar
7. itinerario.exportar-filtro
8. itinerario.exportar-todo
9. itinerario.crear-tarea
10. itinerario.editar-tarea
11. itinerario.eliminar-tarea
12. itinerario.marcar-tarea

MOP
ROLES: supervisor-entrega-fest, asesor-entrega-fest, staff-mop
1. mop.navegacion
2. mop.lista
3. mop.ver
4. mop.crear
5. mop.editar
6. mop.eliminar
7. mop.exportar-filtro
8. mop.exportar-todo
9. mop.crear-tarea
10. mop.editar-tarea
11. mop.eliminar-tarea
12. mop.marcar-tarea

MOP PLANTILLA
ROLES: supervisor-entrega-fest, asesor-entrega-fest, staff-mop
1. mop-plantilla.navegacion
2. mop-plantilla.lista
3. mop-plantilla.ver
4. mop-plantilla.crear
5. mop-plantilla.editar
6. mop-plantilla.eliminar
7. mop-plantilla.exportar-filtro
8. mop-plantilla.exportar-todo

PROVEEDORES
ROLES: supervisor-entrega-fest, asesor-entrega-fest, staff-proveedores
1. proveedor.navegacion
2. proveedor.lista
3. proveedor.ver
4. proveedor.crear
5. proveedor.editar
6. proveedor.eliminar
7. proveedor.exportar-filtro
8. proveedor.exportar-todo
9. proveedor.crear-requerimiento
10. proveedor.editar-requerimiento
11. proveedor.eliminar-requerimiento
12. proveedor.marcar-requerimiento

INCIDENCIAS
ROLES: supervisor-entrega-fest, asesor-entrega-fest, staff-incidencias
1. incidencia.navegacion
2. incidencia.lista
3. incidencia.ver
4. incidencia.crear
5. incidencia.editar
6. incidencia.eliminar
7. incidencia.exportar-filtro
8. incidencia.exportar-todo

RECURSOS
ROLES: supervisor-entrega-fest, asesor-entrega-fest, staff-recursos
1. recurso.navegacion
2. recurso.lista
3. recurso.ver
4. recurso.crear
5. recurso.editar
6. recurso.eliminar
7. recurso.exportar-filtro
8. recurso.exportar-todo

CONTINGENCIA
ROLES: supervisor-entrega-fest, asesor-entrega-fest, staff-contingencia
1. contingencia.navegacion
2. contingencia.lista
3. contingencia.ver
4. contingencia.crear
5. contingencia.editar
6. contingencia.eliminar
7. contingencia.exportar-filtro
8. contingencia.exportar-todo

PROTOCOLO
ROLES: supervisor-entrega-fest, asesor-entrega-fest, staff-protocolo
1. protocolo.navegacion
2. protocolo.lista
3. protocolo.ver
4. protocolo.crear
5. protocolo.editar
6. protocolo.eliminar
7. protocolo.exportar-filtro
8. protocolo.exportar-todo

*/
