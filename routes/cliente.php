<?php

use App\Http\Controllers\Cliente\InicioController;
use App\Http\Controllers\Cliente\LoteController;
use App\Http\Controllers\Cliente\TutorialController;
use App\Http\Controllers\Cliente\AvanceProyectoController;
use App\Http\Controllers\Cliente\ReglamentoController;
use App\Http\Controllers\Cliente\ClienteDocumentoController;
use App\Livewire\Cliente\Documento\DocumentoProyecto;
use Illuminate\Support\Facades\Route;

Route::get('/home', [InicioController::class, 'index'])->name('home');
Route::get('/lote', [LoteController::class, 'index'])->name('lote');
Route::get('/tutorial', [TutorialController::class, 'index'])->name('tutorial');
Route::get('/avance-proyecto', [AvanceProyectoController::class, 'index'])->name('avance-proyecto');
Route::get('/reglamento', [ReglamentoController::class, 'index'])->name('reglamento');
Route::get('/reglamento/stream/{id}', [ReglamentoController::class, 'stream'])->name('reglamento.stream');

// Documentos
Route::get('/documentos/stream/{id}', [ClienteDocumentoController::class, 'stream'])->name('documentos.stream');
Route::get('/documentos/{proyecto_id}', DocumentoProyecto::class)->name('documentos.proyecto');
