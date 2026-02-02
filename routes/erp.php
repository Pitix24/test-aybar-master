<?php

use App\Livewire\Erp\Inicio\InicioLivewire;
use Illuminate\Support\Facades\Route;

Route::get('/perfil', InicioLivewire::class)->name('home');
