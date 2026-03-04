<?php

namespace App\Livewire\Crm\Whatsapp;

use Livewire\Component;
use App\Models\WhatsappConversacion;

class ChatLista extends Component
{
    public string $search = '';
    public ?string $filtro = null;
    public ?int $conversacionActivaId = null;

    // Filtros de sidebar (pasados desde container)
    public ?string $filtroDeptSidebar = null;
    public ?string $filtroLeido = null;
    public ?string $filtroFechaInicio = null;
    public ?string $filtroFechaFin = null;

    protected $listeners = [
        'mensajeRecibido' => '$refresh',
        'conversacionSeleccionada' => 'marcarActivo',
        'aplicarFiltroAvanzado' => 'aplicarFiltro',
        'limpiarFiltroAvanzado' => 'limpiarFiltro',
    ];

    public function marcarActivo(int $id): void
    {
        $this->conversacionActivaId = $id;
    }

    public function seleccionarConversacion(int $id): void
    {
        $this->conversacionActivaId = $id;
        $this->dispatch('conversacionSeleccionada', $id);
    }

    public function setFiltro(?string $filtro): void
    {
        $this->filtro = ($this->filtro === $filtro) ? null : $filtro;
    }

    public function aplicarFiltro(array $datos): void
    {
        $this->filtroDeptSidebar = $datos['dept'] ?? null;
        $this->filtroLeido = $datos['leido'] ?? null;
        $this->filtroFechaInicio = $datos['fecha_inicio'] ?? null;
        $this->filtroFechaFin = $datos['fecha_fin'] ?? null;
    }

    public function limpiarFiltro(): void
    {
        $this->filtroDeptSidebar = null;
        $this->filtroLeido = null;
        $this->filtroFechaInicio = null;
        $this->filtroFechaFin = null;
        $this->search = '';
        $this->filtro = null;
    }

    public function render()
    {
        $query = WhatsappConversacion::with([
            'contacto.cliente',
            'mensajes' => fn($q) => $q->latest()->limit(1)
        ])->orderBy('last_message_at', 'desc');

        // Búsqueda por nombre / wa_id
        if ($this->search) {
            $query->whereHas('contacto', function ($q) {
                $q->where('nombre_wa', 'like', '%' . $this->search . '%')
                    ->orWhere('wa_id', 'like', '%' . $this->search . '%')
                    ->orWhereHas(
                        'cliente',
                        fn($sq) =>
                        $sq->where('nombre', 'like', '%' . $this->search . '%')
                    );
            });
        }

        // Chip: no leídos
        if ($this->filtro === 'no_leidos') {
            $query->where('mensajes_sin_leer', '>', 0);
        }

        // Filtros del sidebar avanzado
        if ($this->filtroDeptSidebar) {
            $query->where('departamento_destino', $this->filtroDeptSidebar);
        }

        if ($this->filtroLeido === '1') {
            $query->where('mensajes_sin_leer', '>', 0);
        }

        if ($this->filtroFechaInicio) {
            $query->whereDate('last_message_at', '>=', $this->filtroFechaInicio);
        }

        if ($this->filtroFechaFin) {
            $query->whereDate('last_message_at', '<=', $this->filtroFechaFin);
        }

        return view('livewire.crm.whatsapp.chat-lista', [
            'conversaciones' => $query->get(),
        ]);
    }
}
