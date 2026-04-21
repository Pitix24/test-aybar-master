<?php

use App\Http\Controllers\Api\PowerBiController;
use App\Livewire\Erp\Reporte\Atc\ReporteTicket;
use App\Livewire\Erp\Reporte\Backoffice\ReporteEvidenciaPago;
use App\Livewire\Erp\Reporte\Backoffice\ReporteEvidenciaPagoAntiguo;
use App\Livewire\Erp\Reporte\Cita\ReporteCita;
use App\Livewire\Erp\Reporte\Letra\ReporteLetra;
use App\Livewire\Erp\Reporte\Usuario\ReporteAdmin;
use App\Livewire\Erp\Reporte\Usuario\ReporteCliente;
use App\Livewire\Erp\Reporte\Backoffice\ReporteSolicitudEvidenciaPago;
use App\Livewire\Erp\Reporte\Usuario\ReporteDireccion;

// Power BI Livewire Components
use App\Livewire\Erp\Reporte\PowerBI\ReporteClientePowerBI;
use App\Livewire\Erp\Reporte\PowerBI\ReporteAdminPowerBI;
use App\Livewire\Erp\Reporte\PowerBI\ReporteDireccionPowerBI;
use App\Livewire\Erp\Reporte\PowerBI\ReporteSolicitudEvidenciaPagoPowerBI;
use App\Livewire\Erp\Reporte\PowerBI\ReporteEvidenciaPagoPowerBI;
use App\Livewire\Erp\Reporte\PowerBI\ReporteEvidenciaPagoAntiguoPowerBI;
use App\Livewire\Erp\Reporte\PowerBI\ReporteTicketPowerBI;
use App\Livewire\Erp\Reporte\PowerBI\ReporteCitaPowerBI;
use App\Livewire\Erp\Reporte\PowerBI\ReporteLetraPowerBI;

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['permission:modulo-reporte.ver']], function () {
    Route::group(['middleware' => ['permission:reporte-usuario.navegacion']], function () {
        Route::prefix('reporte')->name('reporte.vista.')->group(function () {
            Route::get('/cliente', ReporteCliente::class)->middleware('permission:reporte-usuario.cliente.ver')->name('cliente');
            Route::get('/admin', ReporteAdmin::class)->middleware('permission:reporte-usuario.admin.ver')->name('admin');
            Route::get('/direccion', ReporteDireccion::class)->middleware('permission:reporte-usuario.direccion.ver')->name('direccion');

            // Power BI
            Route::get('/cliente-powerbi', ReporteClientePowerBI::class)->middleware('permission:reporte-usuario.cliente.ver')->name('cliente-powerbi');
            Route::get('/admin-powerbi', ReporteAdminPowerBI::class)->middleware('permission:reporte-usuario.admin.ver')->name('admin-powerbi');
            Route::get('/direccion-powerbi', ReporteDireccionPowerBI::class)->middleware('permission:reporte-usuario.direccion.ver')->name('direccion-powerbi');
        });
    });

    Route::group(['middleware' => ['permission:reporte-backoffice.navegacion']], function () {
        Route::prefix('reporte')->name('reporte.vista.')->group(function () {
            Route::get('/solicitud-evidencia-pago', ReporteSolicitudEvidenciaPago::class)->middleware('permission:reporte-backoffice.solicitud-evidencia-pago.ver')->name('solicitud-evidencia-pago');
            Route::get('/evidencia-pago', ReporteEvidenciaPago::class)->middleware('permission:reporte-backoffice.evidencia-pago.ver')->name('evidencia-pago');
            Route::get('/evidencia-pago-antiguo', ReporteEvidenciaPagoAntiguo::class)->middleware('permission:reporte-backoffice.evidencia-pago-antiguo.ver')->name('evidencia-pago-antiguo');

            // Power BI
            Route::get('/solicitud-evidencia-pago-powerbi', ReporteSolicitudEvidenciaPagoPowerBI::class)->middleware('permission:reporte-backoffice.solicitud-evidencia-pago.ver')->name('solicitud-evidencia-pago-powerbi');
            Route::get('/evidencia-pago-powerbi', ReporteEvidenciaPagoPowerBI::class)->middleware('permission:reporte-backoffice.evidencia-pago.ver')->name('evidencia-pago-powerbi');
            Route::get('/evidencia-pago-antiguo-powerbi', ReporteEvidenciaPagoAntiguoPowerBI::class)->middleware('permission:reporte-backoffice.evidencia-pago-antiguo.ver')->name('evidencia-pago-antiguo-powerbi');
        });
    });

    Route::group(['middleware' => ['permission:reporte-atc.navegacion']], function () {
        Route::prefix('reporte')->name('reporte.vista.')->group(function () {
            Route::get('/ticket', ReporteTicket::class)->middleware('permission:reporte-atc.ticket.ver')->name('ticket');

            // Power BI
            Route::get('/ticket-powerbi', ReporteTicketPowerBI::class)->middleware('permission:reporte-atc.ticket.ver')->name('ticket-powerbi');
        });
    });

    Route::group(['middleware' => ['permission:reporte-cita.navegacion']], function () {
        Route::prefix('reporte')->name('reporte.vista.')->group(function () {
            Route::get('/cita', ReporteCita::class)->middleware('permission:reporte-cita.cita.ver')->name('cita');

            // Power BI
            Route::get('/cita-powerbi', ReporteCitaPowerBI::class)->middleware('permission:reporte-cita.cita.ver')->name('cita-powerbi');
        });
    });

    Route::group(['middleware' => ['permission:reporte-letra.navegacion']], function () {
        Route::prefix('reporte')->name('reporte.vista.')->group(function () {
            Route::get('/letra', ReporteLetra::class)->middleware('permission:reporte-letra.letra.ver')->name('letra');

            // Power BI
            Route::get('/letra-powerbi', ReporteLetraPowerBI::class)->middleware('permission:reporte-letra.letra.ver')->name('letra-powerbi');
        });
    });

    // Power BI — Endpoint para refrescar embed token desde el frontend
    Route::get('/api/powerbi/token/{reportKey}', [PowerBiController::class, 'getToken'])->name('powerbi.token');
});