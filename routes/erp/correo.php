<?php

use App\Livewire\Crm\Correo\CorreoContactoLista;
use App\Livewire\Crm\Correo\CorreoDashboard;
use App\Livewire\Crm\Correo\CorreoListaLista;
use App\Livewire\Crm\Correo\CorreoPlantillaLista;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth']], function () {
    Route::prefix('correo')
        ->name('correo.vista.')
        ->group(function () {
            Route::get('/', CorreoDashboard::class)->name('todo');
            Route::get('/plantillas', CorreoPlantillaLista::class)->name('plantillas');
            Route::get('/listas', CorreoListaLista::class)->name('listas');
            Route::get('/contactos', CorreoContactoLista::class)->name('contactos');
        });
});

