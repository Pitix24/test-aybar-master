<?php

use App\Livewire\Web\EntregaFest\PreInvitacionPropietario;
use App\Livewire\Web\EntregaFest\PreInvitacionCopropietario;
use App\Livewire\Web\EntregaFest\AsistenciaInvitacionPropietario;
use App\Livewire\Web\EntregaFest\AsistenciaInvitacionCopropietario;
use App\Livewire\Web\EntregaFest\CitaAgendarPropietario;
use Illuminate\Support\Facades\Route;
use App\Livewire\Web\EntregaFest\EventoConcluido;
use App\Livewire\Web\EntregaFest\EventoLleno;

Route::get('/pre-invitacion-propietario/{slug}/{propietarioId}', PreInvitacionPropietario::class)
    ->name('entrega-fest.pre-invitacion.propietario');

Route::get('/pre-invitacion-copropietario/{slug}/{copropietarioId}', PreInvitacionCopropietario::class)
    ->name('entrega-fest.pre-invitacion.copropietario');

Route::get('/asistencia-invitacion-propietario/{slug}/{propietarioId}', AsistenciaInvitacionPropietario::class)
    ->name('entrega-fest.asistencia-invitacion.propietario');

Route::get('/asistencia-invitacion-copropietario/{slug}/{copropietarioId}', AsistenciaInvitacionCopropietario::class)
    ->name('entrega-fest.asistencia-invitacion.copropietario');

Route::get('/cita-agendar-propietario/{slug}/{propietarioId}', CitaAgendarPropietario::class)
    ->name('entrega-fest.cita-agendar.propietario');

Route::get('/entrega-fest/{slug}/concluido', EventoConcluido::class)
    ->name('entrega-fest.concluido');

Route::get('/entrega-fest/{slug}/aforo-lleno', EventoLleno::class)
    ->name('entrega-fest.aforo-lleno');
