<?php

use App\Livewire\Erp\Reporte\Sistema\ReporteRol;
use App\Livewire\Erp\Reporte\Sistema\PermisoRol;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['permission:modulo-reporte.ver']], function () {
    Route::group(['middleware' => ['permission:reporte-sistema.navegacion']], function () {
        Route::prefix('reporte')->name('reporte.vista.')->group(function () {
            Route::get('/rol', ReporteRol::class)->middleware('permission:reporte.rol.ver')->name('rol');
            Route::get('/permiso', PermisoRol::class)->middleware('permission:reporte.permiso.ver')->name('permiso');
        });
    });
});