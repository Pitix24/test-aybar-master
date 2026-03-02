<?php

use App\Livewire\Erp\EntregaFest\EntregaFest\EntregaFestCrear;
use App\Livewire\Erp\EntregaFest\EntregaFest\EntregaFestLista;
use App\Livewire\Erp\EntregaFest\EntregaFest\EntregaFestEditar;
use App\Livewire\Erp\EntregaFest\EntregaFest\EntregaFestPanel;
use App\Livewire\Erp\EntregaFest\EntregaFest\EntregaFestVer;
use App\Livewire\Erp\EntregaFest\Invitado\EntregaFestAsistencia;
use App\Livewire\Erp\EntregaFest\Invitado\EntregaFestInvitado;
use App\Livewire\Erp\EntregaFest\Invitado\EntregaFestInvitadoCrear;
use App\Livewire\Erp\EntregaFest\Invitado\EntregaFestInvitadoEditar;
use App\Livewire\Erp\EntregaFest\Invitado\EntregaFestProspecto;
use App\Livewire\Erp\EntregaFest\Invitado\EntregaFestProspectoCrear;
use App\Livewire\Erp\EntregaFest\Invitado\EntregaFestProspectoEditar;
use App\Livewire\Erp\EntregaFest\Staff\StaffDashboard;
use App\Livewire\Erp\EntregaFest\Staff\StaffIncidencias;
use App\Livewire\Erp\EntregaFest\Staff\StaffItinerario;
use App\Livewire\Erp\EntregaFest\Staff\StaffItinerarioCrear;
use App\Livewire\Erp\EntregaFest\Staff\StaffItinerarioEditar;
use App\Livewire\Erp\EntregaFest\Mop\MopPlantillaCrear;
use App\Livewire\Erp\EntregaFest\Mop\MopPlantillaEditar;
use App\Livewire\Erp\EntregaFest\Mop\MopPlantillaLista;
use App\Livewire\Erp\EntregaFest\Mop\MopTareaCrear;
use App\Livewire\Erp\EntregaFest\Mop\MopTareaEditar;
use App\Livewire\Erp\EntregaFest\Mop\MopTareaLista;
use App\Livewire\Erp\EntregaFest\Staff\StaffMop;
use App\Livewire\Erp\EntregaFest\Staff\StaffProveedores;
use App\Livewire\Erp\EntregaFest\Staff\StaffRecursos;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => []], function () {
    Route::group(['middleware' => []], function () {
        Route::prefix('entrega-fest')
            ->name('entrega-fest.vista.')
            ->group(function () {
                Route::get('/', EntregaFestLista::class)->name('todo');
                Route::get('/crear', EntregaFestCrear::class)->name('crear');
                Route::get('/ver/{id}', EntregaFestVer::class)->name('ver');
                Route::get('/editar/{id}', EntregaFestEditar::class)->name('editar');
                Route::get('/panel/{id}', EntregaFestPanel::class)->name('panel');

                Route::get('/prospecto/{id}', EntregaFestProspecto::class)->name('prospectos');
                Route::get('/prospecto/crear/{id}', EntregaFestProspectoCrear::class)->name('prospectos.crear');
                Route::get('/prospecto/{id}/editar/{prospectoId}', EntregaFestProspectoEditar::class)->name('prospectos.editar');

                Route::get('/invitado/{id}', EntregaFestInvitado::class)->name('invitados');
                Route::get('/invitado/crear/{id}', EntregaFestInvitadoCrear::class)->name('invitados.crear');
                Route::get('/invitado/{id}/editar/{invitadoId}', EntregaFestInvitadoEditar::class)->name('invitados.editar');

                Route::get('/asistencia/{id}', EntregaFestAsistencia::class)->name('asistencia');
                Route::get('/staff/{id}', StaffDashboard::class)->name('dashboard');

            });
    });
});


/*
Route::prefix('entrega-fest')->group(function () {

    // 1. Rutas Globales (Sin ID de evento específico)
    Route::group(['as' => 'entrega-fest.vista.'], function () {
        Route::get('/', EntregaFestLista::class)->name('todo');
        Route::get('/crear', EntregaFestCrear::class)->name('crear');
    });

    // MOP: Plantillas globales (sin evento específico)
    Route::prefix('mop/plantillas')->name('entrega-fest.mop.plantillas')->group(function () {
        Route::get('/', MopPlantillaLista::class)->name('');
        Route::get('/crear', MopPlantillaCrear::class)->name('.crear');
        Route::get('/editar/{id}', MopPlantillaEditar::class)->name('.editar');
    });

    // 2. Rutas Contextuales (Dependientes de un EntregaFest específico)
    Route::prefix('{id}')->group(function () {

        // Sub-módulo: Administración y Gestión
        Route::group(['as' => 'entrega-fest.vista.'], function () {
            Route::get('/panel', EntregaFestPanel::class)->name('panel');
            Route::get('/ver', EntregaFestVer::class)->name('ver');
            Route::get('/editar', EntregaFestEditar::class)->name('editar');
            Route::get('/asistencia', EntregaFestAsistencia::class)->name('asistencia');

            // Prospectos
            Route::prefix('prospectos')->name('prospectos')->group(function () {
                Route::get('/', EntregaFestProspecto::class)->name('');
                Route::get('/crear', EntregaFestProspectoCrear::class)->name('.crear');
                Route::get('/editar/{prospectoId}', EntregaFestProspectoEditar::class)->name('.editar');
            });

            // Invitados
            Route::prefix('invitados')->name('invitados')->group(function () {
                Route::get('/', EntregaFestInvitado::class)->name('');
                Route::get('/crear', EntregaFestInvitadoCrear::class)->name('.crear');
                Route::get('/editar/{invitadoId}', EntregaFestInvitadoEditar::class)->name('.editar');
            });
        });

        // Sub-módulo: Operaciones Staff (Campo)
        Route::group(['prefix' => 'staff', 'as' => 'entrega-fest.staff.'], function () {
            Route::get('/', StaffDashboard::class)->name('dashboard');

            // Itinerario (con CRUD)
            Route::prefix('itinerario')->name('itinerario')->group(function () {
                Route::get('/', StaffItinerario::class)->name('');
                Route::get('/crear', StaffItinerarioCrear::class)->name('.crear');
                Route::get('/editar/{bloqueId}', StaffItinerarioEditar::class)->name('.editar');
            });

            Route::get('/mop', StaffMop::class)->name('mop');
            Route::get('/proveedores', StaffProveedores::class)->name('proveedores');
            Route::get('/incidencias', StaffIncidencias::class)->name('incidencias');
            Route::get('/recursos', StaffRecursos::class)->name('recursos');
        });

        // MOP: Tareas por evento (administración coordinador)
        Route::prefix('mop/tareas')->name('entrega-fest.mop.tareas')->group(function () {
            Route::get('/', MopTareaLista::class)->name('');
            Route::get('/crear', MopTareaCrear::class)->name('.crear');
            Route::get('/editar/{tareaId}', MopTareaEditar::class)->name('.editar');
        });
    });
});*/
