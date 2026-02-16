<?php

use App\Livewire\Erp\Reporte\Sistema\ReporteCliente;
use App\Livewire\Erp\Reporte\Sistema\ReporteSolicitudEvidenciaPago;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['permission:modulo-reporte.ver']], function () {
    Route::group(['middleware' => ['permission:reporte-sistema.navegacion']], function () {
        Route::prefix('reporte')->name('reporte.vista.')->group(function () {
            Route::get('/cliente', ReporteCliente::class)->middleware('permission:reporte.cliente.ver')->name('cliente');
            Route::get('/solicitud-evidencia-pago', ReporteSolicitudEvidenciaPago::class)->middleware('permission:reporte.solicitud-evidencia-pago.ver')->name('solicitud-evidencia-pago');
        });
    });
});