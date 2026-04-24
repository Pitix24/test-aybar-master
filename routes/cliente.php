<?php

use App\Http\Controllers\Cliente\InicioController;
use App\Http\Controllers\Cliente\LoteController;
use App\Http\Controllers\Cliente\TutorialController;
use App\Http\Controllers\Cliente\AvanceProyectoController;
use Illuminate\Support\Facades\Route;

Route::get('/home', [InicioController::class, 'index'])->name('home');
Route::get('/lote', [LoteController::class, 'index'])->name('lote');
Route::get('/tutorial', [TutorialController::class, 'index'])->name('tutorial');
Route::get('/avance-proyecto', [AvanceProyectoController::class, 'index'])->name('avance-proyecto');
