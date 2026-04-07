<?php

use App\Livewire\Web\LibroReclamacion\LibroReclamacionLivewire;
use Illuminate\Support\Facades\Route;

Route::get('/libro-de-reclamaciones', LibroReclamacionLivewire::class)->name('libro-reclamaciones');