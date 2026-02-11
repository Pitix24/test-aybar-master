<?php


use App\Livewire\Letra\EnvioCavaliSolicitud\EnvioCavaliSolicitudEditar;
use App\Livewire\Letra\EnvioCavaliSolicitud\EnvioCavaliSolicitudLista;
use App\Livewire\Letra\EstadoSolicitudDigitalizarLetra\EstadoSolicitudDigitalizarLetraCrear;
use App\Livewire\Letra\EstadoSolicitudDigitalizarLetra\EstadoSolicitudDigitalizarLetraEditar;
use App\Livewire\Letra\EstadoSolicitudDigitalizarLetra\EstadoSolicitudDigitalizarLetraLista;
use App\Livewire\Letra\SolicitudDigitalizarLetra\SolicitudDigitalizarLetraLista;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['permission:estado-solicitud-digitalizar-letra.ver']], function () {
    Route::prefix('estado-solicitud-digitalizar-letra')->name('estado-solicitud-digitalizar-letra.vista.')->group(function () {
        Route::get('/', EstadoSolicitudDigitalizarLetraLista::class)->name('todo');
        Route::get('/crear', EstadoSolicitudDigitalizarLetraCrear::class)->middleware('permission:estado-solicitud-digitalizar-letra.crear')->name('crear');
        Route::get('/editar/{id}', EstadoSolicitudDigitalizarLetraEditar::class)->middleware('permission:estado-solicitud-digitalizar-letra.editar')->name('editar');
    });
});

Route::group(['middleware' => ['permission:solicitud-digitalizar-letra.ver']], function () {
    Route::prefix('solicitar-letra-digital')->name('solicitar-letra-digital.vista.')->group(function () {
        Route::get('/', SolicitudDigitalizarLetraLista::class)->name('todo');
    });
});

Route::group(['middleware' => ['permission:envio-cavali-solicitud.ver']], function () {
    Route::prefix('envio-cavali-solicitud')->name('envio-cavali-solicitud.vista.')->group(function () {
        Route::get('/', EnvioCavaliSolicitudLista::class)->name('todo');
        Route::get('/editar/{id}', EnvioCavaliSolicitudEditar::class)->middleware('permission:envio-cavali-solicitud.editar')->name('editar');
    });
});
