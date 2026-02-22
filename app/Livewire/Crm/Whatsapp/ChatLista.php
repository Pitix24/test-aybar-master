<?php

namespace App\Livewire\Crm\Whatsapp;

use Livewire\Component;
use App\Models\WhatsappConversacion;

class ChatLista extends Component
{
    public $search = '';
    public $filtroDepartamento = null;

    protected $listeners = ['mensajeRecibido' => '$refresh'];

    public function seleccionarConversacion($id)
    {
        $this->dispatch('conversacionSeleccionada', $id);
    }

    public function render()
    {
        $query = WhatsappConversacion::with([
            'contacto.cliente',
            'mensajes' => function ($q) {
                $q->latest()->limit(1);
            }
        ])->orderBy('last_message_at', 'desc');

        if ($this->search) {
            $query->whereHas('contacto', function ($q) {
                $q->where('nombre_wa', 'like', '%' . $this->search . '%')
                    ->orWhere('wa_id', 'like', '%' . $this->search . '%')
                    ->orWhereHas('cliente', function ($sq) {
                        $sq->where('nombre', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->filtroDepartamento) {
            $query->where('departamento_destino', $this->filtroDepartamento);
        }

        return view('livewire.crm.whatsapp.chat-lista', [
            'conversaciones' => $query->get()
        ]);
    }
}
