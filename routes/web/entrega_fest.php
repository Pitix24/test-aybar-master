<?php

use App\Livewire\Web\EntregaFest\PreInvitacionPropietario;
use App\Livewire\Web\EntregaFest\PreInvitacionCopropietario;
use App\Livewire\Web\EntregaFest\AsistenciaInvitacionPropietario;
use App\Livewire\Web\EntregaFest\AsistenciaInvitacionCopropietario;
use App\Livewire\Web\EntregaFest\CitaAgendar;
use Illuminate\Support\Facades\Route;

Route::get('/pre-invitacion-propietario/{slug}/{propietarioId}', PreInvitacionPropietario::class)
    ->name('entrega-fest.pre-invitacion.propietario');

Route::get('/pre-invitacion-copropietario/{slug}/{copropietarioId}', PreInvitacionCopropietario::class)
    ->name('entrega-fest.pre-invitacion.copropietario');

Route::get('/asistencia-invitacion-propietario/{slug}/{propietarioId}', AsistenciaInvitacionPropietario::class)
    ->name('entrega-fest.asistencia-invitacion.propietario');

Route::get('/asistencia-invitacion-copropietario/{slug}/{copropietarioId}', AsistenciaInvitacionCopropietario::class)
    ->name('entrega-fest.asistencia-invitacion.copropietario');

Route::get('/cita-agendar-propietario/{slug}/{propietarioId}', CitaAgendar::class)
    ->name('entrega-fest.cita-agendar.propietario');
