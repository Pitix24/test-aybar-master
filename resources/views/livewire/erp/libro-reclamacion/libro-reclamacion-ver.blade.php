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
                    <input type="text" value="{{ $ticket->created_by ? 'ERP - Registro Interno' : 'Formulario web' }}"
                        disabled>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_12">
                    <label>Subtipo</label>
                    <input type="text"
                        value="{{ $ticket->tipo_pedido ? ucwords(strtolower(str_replace('_', ' ', $ticket->tipo_pedido))) : 'N/D' }}"
                        disabled>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_12">
                    <label>Ticket vinculado</label>
                    <input type="text"
                        value="{{ $ticket->ticketRelacionado ? ('#' . $ticket->ticketRelacionado->id) : 'Sin vincular' }}"
                        disabled>
                </div>
            </div>
        </div>

        <div x-show="activeTab === 'cliente'" x-transition class="g_tab_content">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Documento</label>
                    <input type="text" value="{{ $ticket->cliente_documento ?: 'N/D' }}" disabled>
                </div>

                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Tipo documento</label>
                    <input type="text" value="{{ $ticket->cliente_tipo_documento ?: 'N/D' }}" disabled>
                </div>

                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Gestor</label>
                    <input type="text" value="{{ $ticket->gestor?->name ?: 'N/D' }}" disabled>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_6">
                    <label>Nombre cliente</label>
                    <input type="text" value="{{ $ticket->cliente_nombre ?: 'N/D' }}" disabled>
                </div>

                <div class="g_margin_bottom_10 g_columna_6">
                    <label>Email</label>
                    <input type="text" value="{{ $ticket->cliente_email ?: 'N/D' }}" disabled>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_6">
                    <label>Celular</label>
                    <input type="text" value="{{ $ticket->cliente_celular ?: 'N/D' }}" disabled>
                </div>

                <div class="g_margin_bottom_10 g_columna_6">
                    <label>Dirección</label>
                    <input type="text" value="{{ $ticket->cliente_direccion ?: 'N/D' }}" disabled>
                </div>
            </div>

            <!-- Bloque de Menor de Edad y Representante Legal (Solo lectura) -->
            @if ($ticket->es_cliente_menor)
            <div class="g_margin_top_20 g_alerta info">
                <i class="fa-solid fa-exclamation-triangle"></i>
                <strong>Cliente Menor de Edad - Representante Legal Registrado</strong>
            </div>

            <div class="g_fila">
                <div class="g_columna_6 g_margin_bottom_10">
                    <label>Nombre del representante legal</label>
                    <input type="text" value="{{ $ticket->representante_legal_nombre ?: 'N/D' }}" disabled>
                </div>

                <div class="g_columna_6 g_margin_bottom_10">
                    <label>Apellido paterno del representante legal</label>
                    <input type="text" value="{{ $ticket->representante_legal_apellido_paterno ?: 'N/D' }}" disabled>
                </div>

                <div class="g_columna_6 g_margin_bottom_10">
                    <label>Apellido materno del representante legal</label>
                    <input type="text" value="{{ $ticket->representante_legal_apellido_materno ?: 'N/D' }}" disabled>
                </div>
            </div>
            @endif
        </div>

        <div x-show="activeTab === 'asunto'" x-transition class="g_tab_content">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_6">
                    <label>Tipo de bien contratado</label>
                    <select disabled>
                        <option value="">Seleccionar...</option>
                        <option value="PRODUCTO" @selected(strtoupper((string) $ticket->tipo_bien_contratado) ===
                            'PRODUCTO')>PRODUCTO</option>
                        <option value="SERVICIO" @selected(strtoupper((string) $ticket->tipo_bien_contratado) ===
                            'SERVICIO')>SERVICIO</option>
                        <option value="NO_DEFINIDO" @selected(strtoupper((string) $ticket->tipo_bien_contratado) ===
                            'NO_DEFINIDO')>NO DEFINIDO</option>
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_6">
                    <label>Monto reclamado</label>
                    <input type="text" value="{{ $ticket->monto_reclamado ?? 'N/D' }}" disabled>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_6">
                    <label>Descripcion del bien</label>
                    <input type="text" value="{{ $ticket->descripcion ?: 'N/D' }}" disabled>
                </div>

                <div class="g_margin_bottom_10 g_columna_6">
                    <label>Detalle de la reclamacion</label>
                    <input type="text" value="{{ $ticket->detalle ?: 'N/D' }}" disabled>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_6">
                    <label>Pedido del consumidor</label>
                    <input type="text" value="{{ $ticket->pedido ?: 'N/D' }}" disabled>
                </div>

                <div class="g_margin_bottom_10 g_columna_6">
                    <label>Conformidad</label>
                    <input type="text" value="{{ $ticket->conformidad ? 'Si' : 'No' }}" disabled>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_6">
                    <label>Manzana</label>
                    <input type="text" value="{{ $ticket->manzana ?: 'N/D' }}" disabled>
                </div>

                <div class="g_margin_bottom_10 g_columna_6">
                    <label>Lote</label>
                    <input type="text" value="{{ $ticket->lote ?: 'N/D' }}" disabled>
                </div>
            </div>
        </div>

        <div x-show="activeTab === 'nota'" x-transition class="g_tab_content">
            <div class="g_margin_top_20">
                <label>Observaciones internas</label>
                <textarea rows="5" disabled>{{ $ticket->observaciones_internas ?: 'Sin observaciones.' }}</textarea>
            </div>
        </div>

        <div x-show="activeTab === 'auditoria'" x-transition class="g_tab_content">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Creado</label>
                    <input type="text" value="{{ optional($ticket->created_at)->format('d/m/Y H:i') ?: 'N/D' }}"
                        disabled>
                </div>

                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Actualizado</label>
                    <input type="text" value="{{ optional($ticket->updated_at)->format('d/m/Y H:i') ?: 'N/D' }}"
                        disabled>
                </div>

                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Asignado</label>
                    <input type="text" value="{{ optional($ticket->assigned_at)->format('d/m/Y H:i') ?: 'N/D' }}"
                        disabled>
                </div>
            </div>
        </div>
    </div>
</div>
