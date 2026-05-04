<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Detalle Ticket Reclamacion #{{ $ticket->ticket }}</h2>

        <div class="cabecera_titulo_botones">
            @can('ticket-libro-reclamacion.lista')
            <a href="{{ route('erp.libro-reclamacion.vista.todo') }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>
            @endcan

            @can('ticket-libro-reclamacion.editar')
            <a href="{{ route('erp.libro-reclamacion.vista.editar', $ticket->ticket) }}" class="g_boton primary">
                Editar <i class="fa-solid fa-pencil"></i>
            </a>
            @endcan

            @can('ticket.ver')
            @if ($ticket->ticketRelacionado)
            <a href="{{ route('erp.ticket.vista.ver', $ticket->ticketRelacionado->id) }}" class="g_boton warning">
                Ver Ticket <i class="fa-solid fa-ticket"></i>
            </a>
            @endif
            @endcan
        </div>
    </div>

    <div class="formulario g_panel" x-data="{ activeTab: 'general' }">
        <div class="g_tab_navegacion">
            <div class="g_tab_botones">
                <button type="button" @click="activeTab = 'general'"
                    :class="activeTab === 'general' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                    <i class="fa-solid fa-file-invoice"></i> Información General
                </button>

                <button type="button" @click="activeTab = 'cliente'"
                    :class="activeTab === 'cliente' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                    <i class="fa-solid fa-user"></i> Cliente
                </button>

                <button type="button" @click="activeTab = 'asunto'"
                    :class="activeTab === 'asunto' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                    <i class="fa-solid fa-comment-dots"></i> Notas
                </button>

                <button type="button" @click="activeTab = 'nota'"
                    :class="activeTab === 'nota' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                    <i class="fa-solid fa-note-sticky"></i> Observaciones
                </button>

                <button type="button" @click="activeTab = 'auditoria'"
                    :class="activeTab === 'auditoria' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                    <i class="fa-solid fa-shield-halved"></i> Auditoría
                </button>
            </div>
        </div>

        <div x-show="activeTab === 'general'" x-transition class="g_tab_content">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Codigo</label>
                    <input type="text" value="{{ $ticket->codigo }}" disabled>
                </div>

                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Estado (Libro/Ticket)</label>
                    <input type="text" value="{{ $ticket->estadoActualNombre() }}" disabled>
                </div>

                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Clasificacion</label>
                    <input type="text"
                        value="{{ $ticket->clasificacion === 'PENDIENTE_REVISION' ? 'PENDIENTE VERIFICACION' : str_replace('_', ' ', $ticket->clasificacion) }}"
                        disabled>
                </div>
            </div>

            <div class="formulario g_panel">
                @include('livewire.erp.libro-reclamacion.libro-reclamacion-form', ['isView' => true])
            </div>
        </div>
    </div>
</div>