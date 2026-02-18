<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="store, adjuntar, eliminarTicketOn, eliminarArchivo"
        message="Guardando cambios..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Editar ticket</h2>

        <div class="cabecera_titulo_botones">
            @can('ticket.lista')
                <a href="{{ route('erp.ticket.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i>
                </a>
            @endcan

            @can('ticket.crear')
                <a href="{{ route('erp.ticket.vista.crear', $ticket->id) }}" class="g_boton primary">
                    Ticket asociado <i class="fa-solid fa-square-plus"></i></a>
            @endcan

            @can('ticket.derivar')
                <a href="{{ route('erp.ticket.vista.derivar', $ticket->id) }}" class="g_boton secondary">
                    Derivar <i class="fa-solid fa-route"></i>
                </a>
            @endcan

            @can('ticket.eliminar')
                <button type="button" class="g_boton danger" onclick="alertaEliminarTicket()">
                    Eliminar <i class="fa-solid fa-trash-can"></i>
                </button>
            @endcan

            @can('ticket.chat')
                <button type="button" class="g_boton info" wire:click="$dispatch('toggleChat')">
                    Chat <i class="fa-solid fa-comments"></i>
                </button>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_8 g_gap_pagina">
            <form wire:submit="update" class="formulario g_panel" x-data="{ activeTab: 'general' }">
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

                        <button type="button" @click="activeTab = 'participantes'"
                            :class="activeTab === 'participantes' ? 'g_tab_active' : 'g_tab_inactive'"
                            class="g_tab_boton">
                            <i class="fa-solid fa-users"></i> Participantes
                        </button>

                        <button type="button" @click="activeTab = 'derivaciones'"
                            :class="activeTab === 'derivaciones' ? 'g_tab_active' : 'g_tab_inactive'"
                            class="g_tab_boton">
                            <i class="fa-solid fa-route"></i> Derivaciones
                        </button>

                        <button type="button" @click="activeTab = 'historial'"
                            :class="activeTab === 'historial' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                            <i class="fa-solid fa-clock-rotate-left"></i> Historial
                        </button>
                    </div>
                </div>

                <div x-show="activeTab === 'general'" x-transition class="g_tab_content">
                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Empresa</label>
                            <input type="text" disabled value="{{ $ticket->unidadNegocio->nombre ?? 'Sin asignar' }}">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Proyecto</label>
                            <input type="text" disabled value="{{ $ticket->proyecto->nombre ?? 'Sin asignar' }}">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Área origen</label>
                            <input type="text" disabled value="{{ $ticket->area->nombre ?? 'Sin asignar' }}">
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Tipo solicitud</label>
                            <input type="text" disabled value="{{ $ticket->tipoSolicitud->nombre ?? 'Sin asignar' }}">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Sub tipo solicitud</label>
                            <input type="text" disabled
                                value="{{ $ticket->subTipoSolicitud->nombre ?? 'Sin asignar' }}">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Canal</label>
                            <input type="text" disabled value="{{ $ticket->canal->nombre ?? 'Sin asignar' }}">
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Prioridad</label>
                            <input type="text" disabled value="{{ $ticket->prioridad->nombre ?? 'Sin asignar' }}">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Gestor Asignado</label>
                            <input type="text" disabled value="{{ $ticket->gestor->name ?? 'Sin asignar' }}">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Estado <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                            <select wire:model.live="estado_ticket_id"
                                class="@error('estado_ticket_id') input-error @enderror">
                                <option value="">Seleccionar...</option>
                                @foreach($estados as $es)
                                    <option value="{{ $es->id }}">{{ $es->nombre }}</option>
                                @endforeach
                            </select>
                            @error('estado_ticket_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Asunto inicial </label>
                        <textarea disabled>{{ $ticket->asunto_inicial ?? 'Sin asunto' }}</textarea>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Descripción inicial </label>
                        <textarea disabled rows="6">{{ $ticket->descripcion_inicial ?? 'Sin descripción' }}</textarea>
                    </div>

                    @if (!empty($ticket->lotes))
                        <div class="g_margin_bottom_10">
                            <h4 class="g_panel_titulo"><i class="fa-solid fa-layer-group"></i> Lotes vinculados</h4>

                            <div class="g_contenedor_tabla">
                                <table class="g_tabla">
                                    <thead>
                                        <tr>
                                            <th>Razón Social</th>
                                            <th>Proyecto</th>
                                            <th>Mz./Lt.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($ticket->lotes as $index => $l)
                                            <tr class="sorteable_item" wire:key="lote-{{ $index }}">
                                                <td> {{ $l['razon_social'] }} </td>
                                                <td> {{ $l['proyecto'] }} </td>
                                                <td> {{ $l['numero_lote'] }} </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

                <div x-show="activeTab === 'cliente'" x-transition class="g_tab_content">
                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Cliente</label>
                            <input type="text" disabled value="{{ $ticket->nombres ?? 'Sin asignar' }}">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>DNI</label>
                            <input type="text" disabled value="{{ $ticket->dni ?? 'Sin asignar' }}">
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Correo</label>
                            <input type="text" wire:model.live="email">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Celular</label>
                            <input type="text" wire:model.live="celular">
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'participantes'" x-transition class="g_tab_content">
                    @livewire('erp.atc.ticket.ticket-participante', ['ticket' => $ticket])
                </div>

                <div x-show="activeTab === 'derivaciones'" x-transition class="g_tab_content g_margin_bottom_10">
                    @livewire('erp.atc.ticket.ticket-derivados', ['ticket' => $ticket])
                </div>

                <div x-show="activeTab === 'historial'" x-transition class="g_tab_content g_margin_bottom_10">
                    @livewire('erp.atc.ticket.ticket-historial', ['ticket' => $ticket])
                </div>

                <div class="formulario_botones">
                    @can('ticket.editar')
                        <button type="submit" class="g_boton guardar" wire:loading.attr="disabled" wire:target="update">
                            <span wire:loading.remove wire:target="update">
                                <i class="fa-solid fa-pencil"></i> Actualizar
                            </span>
                            <span wire:loading wire:target="update">
                                <i class="fa-solid fa-spinner fa-spin"></i> Actualizando...
                            </span>
                        </button>
                    @endcan

                    <button type="button" class="g_boton cancelar" onclick="history.back()">
                        <i class="fa-solid fa-times"></i> Cancelar
                    </button>
                </div>
            </form>

            <div>
                @livewire('erp.atc.ticket.ticket-email', ['ticket' => $ticket])
            </div>
        </div>

        <div class="g_columna_4 g_gap_pagina">
            @livewire('erp.atc.ticket.ticket-archivo', ['ticket' => $ticket])

            @if ($ticket->padre)
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Ticket Principal (Padre)</h4>
                    <div class="g_contenedor_tabla">
                        <table class="g_tabla g_tabla_pequena">
                            <thead>
                                <tr>
                                    <th>Ticket</th>
                                    <th>Gestor</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="g_negrita">#{{ $ticket->padre->id }}</td>
                                    <td>{{ $ticket->padre->gestor->name ?? 'N/A' }}</td>
                                    <td class="g_celda_centro">
                                        @can('ticket.ver')
                                            <a href="{{ route('erp.ticket.vista.ver', $ticket->padre->id) }}"
                                                class="g_accion ver" title="Ver detalle">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                        @endcan

                                        @can('ticket.editar')
                                            <a href="{{ route('erp.ticket.vista.editar', $ticket->padre->id) }}"
                                                class="g_accion editar" title="Editar">
                                                <i class="fa-solid fa-pencil"></i>
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if (!$ticket->hijos->isEmpty())
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Tickets Asociados (Hijos)</h4>
                    <div class="g_contenedor_tabla">
                        <table class="g_tabla g_tabla_pequena">
                            <thead>
                                <tr>
                                    <th>Ticket</th>
                                    <th>Gestor</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ticket->hijos as $hijo)
                                    <tr>
                                        <td class="g_negrita">#{{ $hijo->id }}</td>
                                        <td>{{ $hijo->gestor->name ?? 'N/A' }}</td>
                                        <td class="g_celda_centro">
                                            @can('ticket.ver')
                                                <a href="{{ route('erp.ticket.vista.ver', $hijo->id) }}" class="g_accion ver"
                                                    title="Ver Ticket Hijo">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>
                                            @endcan

                                            @can('ticket.editar')
                                                <a href="{{ route('erp.ticket.vista.editar', $hijo->id) }}" class="g_accion editar"
                                                    title="Editar Ticket Hijo">
                                                    <i class="fa-solid fa-pencil"></i>
                                                </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @script
    <script>
        window.alertaEliminarTicket = function () {
            Swal.fire({
                title: '¿Eliminar Ticket?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '¡Sí, eliminar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.eliminarTicketOn();
                }
            });
        }
    </script>
    @endscript

    @livewire('erp.atc.ticket.ticket-chat', ['ticket' => $ticket])
</div>