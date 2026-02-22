<?php

use App\Livewire\Crm\Whatsapp\ChatContainer;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth']], function () {
    Route::prefix('whatsapp')
        ->name('whatsapp.vista.')
        ->group(function () {
            Route::get('/', ChatContainer::class)->name('todo');
        });
});
