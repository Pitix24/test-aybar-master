<?php

use App\Http\Controllers\Web\LoginController;
use App\Livewire\Web\Sesion\ClienteRegistrarLivewire;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'indexIngresarCliente'])->name('home');
Route::get('/ingresar', [LoginController::class, 'indexIngresarCliente'])->name('ingresar.cliente');

Route::get('/registrar', ClienteRegistrarLivewire::class)->name('registrar.cliente');

/*Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__ . '/settings.php';*/
