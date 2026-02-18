<?php

use App\Livewire\Erp\Reporte\Atc\ReporteTicket;
use App\Livewire\Erp\Reporte\Backoffice\ReporteEvidenciaPago;
use App\Livewire\Erp\Reporte\Backoffice\ReporteEvidenciaPagoAntiguo;
use App\Livewire\Erp\Reporte\Cita\ReporteCita;
use App\Livewire\Erp\Reporte\Letra\ReporteLetra;
use App\Livewire\Erp\Reporte\Usuario\ReporteAdmin;
use App\Livewire\Erp\Reporte\Usuario\ReporteCliente;
use App\Livewire\Erp\Reporte\Backoffice\ReporteSolicitudEvidenciaPago;
use App\Livewire\Erp\Reporte\Usuario\ReporteDireccion;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['permission:modulo-reporte.ver']], function () {
    Route::group(['middleware' => ['permission:reporte-usuario.navegacion']], function () {
        Route::prefix('reporte')->name('reporte.vista.')->group(function () {
            Route::get('/cliente', ReporteCliente::class)->middleware('permission:reporte-usuario.cliente.ver')->name('cliente');
            Route::get('/admin', ReporteAdmin::class)->middleware('permission:reporte-usuario.admin.ver')->name('admin');
            Route::get('/direccion', ReporteDireccion::class)->middleware('permission:reporte-usuario.direccion.ver')->name('direccion');
        });
    });

    Route::group(['middleware' => ['permission:reporte-backoffice.navegacion']], function () {
        Route::prefix('reporte')->name('reporte.vista.')->group(function () {
            Route::get('/solicitud-evidencia-pago', ReporteSolicitudEvidenciaPago::class)->middleware('permission:reporte-backoffice.solicitud-evidencia-pago.ver')->name('solicitud-evidencia-pago');
            Route::get('/evidencia-pago', ReporteEvidenciaPago::class)->middleware('permission:reporte-backoffice.evidencia-pago.ver')->name('evidencia-pago');
            Route::get('/evidencia-pago-antiguo', ReporteEvidenciaPagoAntiguo::class)->middleware('permission:reporte-backoffice.evidencia-pago-antiguo.ver')->name('evidencia-pago-antiguo');
        });
    });

    Route::group(['middleware' => ['permission:reporte-atc.navegacion']], function () {
        Route::prefix('reporte')->name('reporte.vista.')->group(function () {
            Route::get('/ticket', ReporteTicket::class)->middleware('permission:reporte-atc.ticket.ver')->name('ticket');
        });
    });

    Route::group(['middleware' => ['permission:reporte-cita.navegacion']], function () {
        Route::prefix('reporte')->name('reporte.vista.')->group(function () {
            Route::get('/cita', ReporteCita::class)->middleware('permission:reporte-cita.cita.ver')->name('cita');
        });
    });

    Route::group(['middleware' => ['permission:reporte-letra.navegacion']], function () {
        Route::prefix('reporte')->name('reporte.vista.')->group(function () {
            Route::get('/letra', ReporteLetra::class)->middleware('permission:reporte-letra.letra.ver')->name('letra');
        });
    });
});