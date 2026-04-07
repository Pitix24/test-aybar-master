<?php
use App\Livewire\Erp\LibroReclamacion\LibroReclamacionCrear;
use App\Livewire\Erp\LibroReclamacion\LibroReclamacionEditar;
use App\Livewire\Erp\LibroReclamacion\LibroReclamacionLista;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['']], function () {
    Route::group(['middleware' => ['']], function () {
        Route::prefix('libro-reclamacion')
            ->name('libro-reclamacion.vista.')
            ->group(function () {
                Route::get('/', LibroReclamacionLista::class)->middleware('')->name('todo');
                Route::get('/crear', LibroReclamacionCrear::class)->middleware('')->name('crear');
                Route::get('/editar/{id}', LibroReclamacionEditar::class)->middleware('')->name('editar');
            });
    });
});
