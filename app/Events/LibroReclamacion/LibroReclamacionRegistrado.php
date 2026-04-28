<?php

namespace App\Events\LibroReclamacion;

use App\Models\LibroReclamacion\LibroReclamacion as LibroReclamacionModel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LibroReclamacionRegistrado
{
    use Dispatchable, SerializesModels;

    public function __construct(public LibroReclamacionModel $reclamo)
    {
        // Evento de dominio para envio de comunicaciones post-registro.
    }
}