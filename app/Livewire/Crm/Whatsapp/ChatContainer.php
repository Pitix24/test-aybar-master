<?php

namespace App\Livewire\Crm\Whatsapp;

use Livewire\Component;
use App\Models\WhatsappConversacion;
use Illuminate\Support\Facades\Auth;

/**
 * ChatContainer: orquesta los paneles izquierdo y derecho.
 * Mantiene el estado global: conversación activa y qué sidebars están abiertos.
 */
#[\Livewire\Attributes\Layout('layouts.layout-whatsapp')]
class ChatContainer extends Component
{
    // ─── Estado global ─────────────────────────────────────────────────────
    public ?int $conversacionActivaId = null;

    // Sidebars izquierdo
    public bool $sidebarPerfil = false;
    public bool $sidebarNuevoChat = false;
    public bool $sidebarFiltro = false;

    // Sidebars derecho
    public bool $sidebarBuscarMensajes = false;
    public bool $sidebarPlantillas = false;
    public bool $sidebarInfoContacto = false;

    // Modal multimedia
    public bool $modalMultimedia = false;
    public ?string $mediaUrl = null;
    public ?string $mediaTipo = null;

    // Modal pausar chat
    public bool $modalPausar = false;
    public bool $chatPausado = false;
    public ?string $motivoPausaSeleccionado = null;

    protected $listeners = [
        'conversacionSeleccionada' => 'seleccionarConversacion',
        'abrirSidebarPerfil' => 'abrirPerfil',
        'abrirSidebarNuevoChat' => 'abrirNuevoChat',
        'abrirSidebarFiltro' => 'abrirFiltro',
        'abrirSidebarBuscar' => 'abrirBuscar',
        'abrirSidebarPlantillas' => 'abrirPlantillas',
        'abrirSidebarInfoContacto' => 'abrirInfoContacto',
        'abrirModalMultimedia' => 'abrirMultimedia',
        'abrirModalPausar' => 'abrirPausar',
        'cerrarTodosLosModales' => 'cerrarTodo',
    ];

    // ─── Selección de conversación ──────────────────────────────────────────
    public function seleccionarConversacion(int $id): void
    {
        $this->conversacionActivaId = $id;
        // Cerrar sidebars derechos al cambiar de conv
        $this->sidebarBuscarMensajes = false;
        $this->sidebarInfoContacto = false;
    }

    // ─── Sidebars Izquierdo ─────────────────────────────────────────────────
    public function abrirPerfil(): void
    {
        $this->sidebarPerfil = true;
        $this->sidebarNuevoChat = $this->sidebarFiltro = false;
    }

    public function cerrarPerfil(): void
    {
        $this->sidebarPerfil = false;
    }

    public function abrirNuevoChat(): void
    {
        $this->sidebarNuevoChat = true;
        $this->sidebarPerfil = $this->sidebarFiltro = false;
    }

    public function cerrarNuevoChat(): void
    {
        $this->sidebarNuevoChat = false;
    }

    public function abrirFiltro(): void
    {
        $this->sidebarFiltro = true;
        $this->sidebarPerfil = $this->sidebarNuevoChat = false;
    }

    public function cerrarFiltro(): void
    {
        $this->sidebarFiltro = false;
    }

    // ─── Sidebars Derecho ───────────────────────────────────────────────────
    public function abrirBuscar(): void
    {
        $this->sidebarBuscarMensajes = !$this->sidebarBuscarMensajes;
        $this->sidebarPlantillas = $this->sidebarInfoContacto = false;
    }

    public function abrirPlantillas(): void
    {
        $this->sidebarPlantillas = !$this->sidebarPlantillas;
        $this->sidebarBuscarMensajes = $this->sidebarInfoContacto = false;
    }

    public function abrirInfoContacto(): void
    {
        $this->sidebarInfoContacto = !$this->sidebarInfoContacto;
        $this->sidebarBuscarMensajes = $this->sidebarPlantillas = false;
    }

    // ─── Modal Multimedia ───────────────────────────────────────────────────
    public function abrirMultimedia(string $url, string $tipo): void
    {
        $this->mediaUrl = $url;
        $this->mediaTipo = $tipo;
        $this->modalMultimedia = true;
    }

    public function cerrarMultimedia(): void
    {
        $this->modalMultimedia = false;
        $this->mediaUrl = $this->mediaTipo = null;
    }

    // ─── Modal Pausar Chat ──────────────────────────────────────────────────
    public function abrirPausar(): void
    {
        $this->modalPausar = true;
    }

    public function cerrarPausar(): void
    {
        $this->modalPausar = false;
        $this->motivoPausaSeleccionado = null;
    }

    public function confirmarPausa(): void
    {
        $this->chatPausado = true;
        $this->modalPausar = false;
    }

    public function reanudarChat(): void
    {
        $this->chatPausado = false;
        $this->motivoPausaSeleccionado = null;
    }

    // ─── Cerrar todo ────────────────────────────────────────────────────────
    public function cerrarTodo(): void
    {
        $this->sidebarPerfil = $this->sidebarNuevoChat = $this->sidebarFiltro = false;
        $this->sidebarBuscarMensajes = $this->sidebarPlantillas = $this->sidebarInfoContacto = false;
        $this->modalMultimedia = $this->modalPausar = false;
    }

    public function render()
    {
        return view('livewire.crm.whatsapp.chat-container');
    }
}
