<?php

use App\Livewire\Erp\Reporte\Sistema\ReporteCliente;
use App\Livewire\Erp\Reporte\Sistema\ReporteSolicitudEvidenciaPago;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['permission:modulo-reporte.ver']], function () {
    Route::group(['middleware' => ['permission:reporte-usuario.navegacion']], function () {
        Route::prefix('reporte')->name('reporte.vista.')->group(function () {
            Route::get('/cliente', ReporteCliente::class)->middleware('permission:reporte-usuario.cliente.ver')->name('cliente');
        });
    });

    Route::group(['middleware' => ['permission:reporte-backoffice.navegacion']], function () {
        Route::prefix('reporte')->name('reporte.vista.')->group(function () {
            Route::get('/solicitud-evidencia-pago', ReporteSolicitudEvidenciaPago::class)->middleware('permission:reporte-backoffice.solicitud-evidencia-pago.ver')->name('solicitud-evidencia-pago');
        });
    });
});