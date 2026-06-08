<?php

namespace App\Support;

use App\Models\EntregaFest;

trait RedirigeSiEventoConcluido
{
    /**
     * Si el evento ya se realizó, redirige al view único de "Evento Concluido".
     * Debe llamarse al inicio de mount() después de cargar $this->evento.
     *
     * Uso típico:
     *   if ($redir = $this->redirigirSiConcluido($this->evento)) return $redir;
     *
     * @param  \App\Models\EntregaFest|null  $evento
     * @return \Livewire\Features\SupportRedirects\Redirector|null
     */
    protected function redirigirSiConcluido(?EntregaFest $evento)
    {
        if ($evento && $evento->realizado()) {
            return $this->redirect(
                route('entrega-fest.concluido', ['slug' => $evento->slug]),
                navigate: false
            );
        }

        return null;
    }
}
