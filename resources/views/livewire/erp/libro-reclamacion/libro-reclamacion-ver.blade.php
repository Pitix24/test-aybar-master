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
                @if ($ticket->ticket_id)
                    <a href="{{ route('erp.ticket.vista.ver', $ticket->ticket_id) }}" class="g_boton warning">
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
                    <i class="fa-solid fa-comment-dots"></i> Asunto y Lotes
                </button>

                <button type="button" @click="activeTab = 'nota'"
                    :class="activeTab === 'nota' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                    <i class="fa-solid fa-note-sticky"></i> Nota y Observaciones
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
                    <label>Estado legal</label>
                    <input type="text" value="{{ str_replace('_', ' ', $ticket->estadoLibroReclamacion?->nombre ?? 'N/D') }}" disabled>
                </div>

                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Clasificacion</label>
                    <input type="text" value="{{ str_replace('_', ' ', $ticket->clasificacion) }}" disabled>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Ticket</label>
                    <input type="text" value="{{ $ticket->ticket ?: 'N/D' }}" disabled>
                </div>

                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Unidad de negocio</label>
                    <input type="text" value="{{ $ticket->unidadNegocio?->nombre ?: 'N/D' }}" disabled>
                </div>

                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Proyecto</label>
                    <input type="text" value="{{ $ticket->proyecto?->nombre ?: 'N/D' }}" disabled>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_12">
                    <label>Origen</label>
                    <input type="text" value="{{ $ticket->esOrigenErp() ? 'ERP - Registro Interno' : 'Formulario web' }}" disabled>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_12">
                    <label>Subtipo</label>
                    <input type="text" value="{{ $ticket->tipo_pedido ? ucwords(strtolower(str_replace('_', ' ', $ticket->tipo_pedido))) : 'N/D' }}" disabled>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_12">
                    <label>Ticket ATC vinculado</label>
                    <input type="text" value="{{ $ticket->ticket_id ? ('#' . $ticket->ticket_id) : 'Sin vincular' }}" disabled>
                </div>
            </div>
        </div>

        <div x-show="activeTab === 'cliente'" x-transition class="g_tab_content">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Documento</label>
                    <input type="text" value="{{ $ticket->cliente_documento ?: ($ticket->numero_documento ?: 'N/D') }}" disabled>
                </div>

                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Tipo documento</label>
                    <input type="text" value="{{ $ticket->cliente_tipo_documento ?: ($ticket->tipo_documento ?: 'N/D') }}" disabled>
                </div>

                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Gestor</label>
                    <input type="text" value="{{ $ticket->gestor?->name ?: 'N/D' }}" disabled>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_6">
                    <label>Nombre cliente</label>
                    <input type="text" value="{{ $ticket->cliente_nombre ?: ($ticket->cliente?->name ?: $ticket->nombre ?: 'N/D') }}" disabled>
                </div>

                <div class="g_margin_bottom_10 g_columna_6">
                    <label>Email</label>
                    <input type="text" value="{{ $ticket->cliente_email ?: ($ticket->email ?: 'N/D') }}" disabled>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_6">
                    <label>Celular</label>
                    <input type="text" value="{{ $ticket->cliente_celular ?: ($ticket->telefono ?: 'N/D') }}" disabled>
                </div>

                <div class="g_margin_bottom_10 g_columna_6">
                    <label>Dirección</label>
                    <input type="text" value="{{ $ticket->cliente_direccion ?: ($ticket->domicilio ?: 'N/D') }}" disabled>
                </div>
            </div>
        </div>

        <div x-show="activeTab === 'asunto'" x-transition class="g_tab_content">
            <div class="g_margin_bottom_10">
                <label>Asunto</label>
                <textarea rows="5" disabled>{{ $ticket->asunto ?: 'Sin asunto.' }}</textarea>
            </div>

            @if (! empty($ticket->lotes))
                <div class="g_margin_bottom_10">
                    <h4 class="g_panel_titulo"><i class="fa-solid fa-layer-group"></i> Lotes</h4>

                    <div class="g_contenedor_tabla">
                        <table class="g_tabla">
                            <thead>
                                <tr>
                                    <th>Razón Social</th>
                                    <th>Proyecto</th>
                                    <th>Mz./Lt.</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ticket->lotes as $lote)
                                    <tr>
                                        <td>{{ $lote['razon_social'] ?? 'N/D' }}</td>
                                        <td>{{ $lote['proyecto'] ?? 'N/D' }}</td>
                                        <td>{{ $lote['numero_lote'] ?? 'N/D' }}</td>
                                        <td>{{ $lote['estado_lote'] ?? 'N/D' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <div x-show="activeTab === 'nota'" x-transition class="g_tab_content">
            <div class="g_margin_top_20">
                <label>Observaciones internas</label>
                <textarea rows="5" disabled>{{ $ticket->observaciones_internas ?: 'Sin observaciones.' }}</textarea>
            </div>

            <div class="g_fila g_margin_top_10">
                <div class="g_columna_6">
                    <label>Titulo de nota</label>
                    <input type="text" value="{{ $ticket->tituloNotaFuenteResuelto() }}" disabled>
                </div>

                <div class="g_columna_6">
                    <label>Fecha de nota</label>
                    <input type="text" value="{{ optional($ticket->nota_fuente_fecha)->format('d/m/Y H:i') ?: 'N/D' }}" disabled>
                </div>
            </div>

            <div class="g_margin_bottom_10">
                <label>Nota fuente</label>
                <textarea rows="8" disabled>{{ $ticket->esOrigenErp() ? '' : ($ticket->contenidoNotaFuenteResuelto() ?: 'Sin nota fuente.') }}</textarea>
            </div>
        </div>

        <div x-show="activeTab === 'auditoria'" x-transition class="g_tab_content">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Creado</label>
                    <input type="text" value="{{ optional($ticket->created_at)->format('d/m/Y H:i') ?: 'N/D' }}" disabled>
                </div>

                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Actualizado</label>
                    <input type="text" value="{{ optional($ticket->updated_at)->format('d/m/Y H:i') ?: 'N/D' }}" disabled>
                </div>

                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Asignado</label>
                    <input type="text" value="{{ optional($ticket->assigned_at)->format('d/m/Y H:i') ?: 'N/D' }}" disabled>
                </div>
            </div>
        </div>
    </div>
</div>
