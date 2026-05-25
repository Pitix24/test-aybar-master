<div class="g_gap_pagina">

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Detalle del ticket #{{ $ticket->id }}</h2>

        <div class="cabecera_titulo_botones">
            @can('ticket.vista-lista')
                <a href="{{ route('erp.ticket.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i>
                </a>
            @endcan

            @can('ticket.vista-editar')
                <a href="{{ route('erp.ticket.vista.editar', $ticket->id) }}" class="g_boton primary">
                    Editar <i class="fa-solid fa-pencil"></i>
                </a>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_8 g_gap_pagina">
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
                            <label>Estado</label>
                            <input type="text" disabled value="{{ $ticket->estado->nombre ?? 'Sin asignar' }}">
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
                            <input type="text" disabled value="{{ $ticket->email }}">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Celular</label>
                            <input type="text" disabled value="{{ $ticket->celular }}">
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
            </div>

            <div>
                @livewire('erp.atc.ticket.ticket-email', ['ticket' => $ticket, 'soloLectura' => true])
            </div>
        </div>

        <div class="g_columna_4 g_gap_pagina">
            @livewire('erp.atc.ticket.ticket-archivo', ['ticket' => $ticket, 'soloLectura' => true])

            @if ($ticket->libroReclamacion)
                @php
                    $libro = $ticket->libroReclamacion;
                    $razonSocial = $libro->proyecto?->unidadNegocio?->razon_social ?: ($libro->proyecto?->unidadNegocio?->nombre ?: 'Sin proveedor definido');
                    $proyectoLibro = $libro->proyecto?->nombre ?: 'Sin proyecto definido';
                    $lotesLibro = ($libro->manzana || $libro->lote) ? trim(($libro->manzana ?: '-') . '/' . ($libro->lote ?: '-')) : 'Sin lote';

                    $clienteCompleto = trim((string) ($libro->cliente_nombre ?: trim(($libro->nombre ?: '') . ' ' . ($libro->apellido_paterno ?: '') . ' ' . ($libro->apellido_materno ?: ''))));
                    $clienteCompleto = $clienteCompleto !== '' ? $clienteCompleto : 'Sin nombre registrado';
                    $documentoCliente = $libro->cliente_documento ?: $libro->numero_documento ?: 'N/D';
                    $tipoDocumentoCliente = $libro->cliente_tipo_documento ?: $libro->tipo_documento ?: 'N/D';
                    $correoCliente = $libro->cliente_email ?: $libro->email ?: 'N/D';
                    $celularCliente = $libro->cliente_celular ?: $libro->telefono ?: 'N/D';
                    $direccionCliente = $libro->cliente_direccion ?: $libro->domicilio ?: 'N/D';

                    $detalleLibre = trim(implode("\n", array_filter([
                        'Detalle de la reclamación: ' . ($libro->detalle ?: 'N/D'),
                        'Pedido del consumidor: ' . ($libro->pedido ?: 'N/D'),
                    ])));
                @endphp
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Libro de Reclamaciones Vinculado</h4>

                    <div class="formulario">
                        <div class="g_margin_bottom_10">
                            <label>Código del Libro</label>
                            <input type="text" disabled value="{{ $libro->codigo_ticket ?: 'N/D' }}">
                        </div>

                        <div class="g_panel" style="padding: 12px; margin-bottom: 12px;">
                            <h5 class="g_panel_titulo" style="margin-bottom: 12px;">Identificación del Proveedor</h5>

                            <div class="g_fila">
                                <div class="g_columna_6 g_margin_bottom_10">
                                    <label>Razón social</label>
                                    <input type="text" disabled value="{{ $razonSocial }}">
                                </div>

                                <div class="g_columna_6 g_margin_bottom_10">
                                    <label>Proyecto</label>
                                    <input type="text" disabled value="{{ $proyectoLibro }}">
                                </div>
                            </div>

                            <div class="g_fila">
                                <div class="g_columna_12 g_margin_bottom_10">
                                    <label>Lotes</label>
                                    <input type="text" disabled value="{{ $lotesLibro }}">
                                </div>
                            </div>
                        </div>

                        <div class="g_panel" style="padding: 12px; margin-bottom: 12px;">
                            <h5 class="g_panel_titulo" style="margin-bottom: 12px;">Datos del Consumidor Reclamante</h5>

                            <div class="g_fila">
                                <div class="g_columna_12 g_margin_bottom_10">
                                    <label>Nombre completo</label>
                                    <input type="text" disabled value="{{ $clienteCompleto }}">
                                </div>
                            </div>

                            <div class="g_fila">
                                <div class="g_columna_6 g_margin_bottom_10">
                                    <label>Tipo de documento</label>
                                    <input type="text" disabled value="{{ $tipoDocumentoCliente }}">
                                </div>

                                <div class="g_columna_6 g_margin_bottom_10">
                                    <label>N° de documento</label>
                                    <input type="text" disabled value="{{ $documentoCliente }}">
                                </div>
                            </div>

                            <div class="g_fila">
                                <div class="g_columna_6 g_margin_bottom_10">
                                    <label>Correo</label>
                                    <input type="text" disabled value="{{ $correoCliente }}">
                                </div>

                                <div class="g_columna_6 g_margin_bottom_10">
                                    <label>Celular</label>
                                    <input type="text" disabled value="{{ $celularCliente }}">
                                </div>
                            </div>

                            <div class="g_fila">
                                <div class="g_columna_12 g_margin_bottom_10">
                                    <label>Dirección</label>
                                    <input type="text" disabled value="{{ $direccionCliente }}">
                                </div>
                            </div>

                        </div>

                        <div class="g_panel" style="padding: 12px; margin-bottom: 12px;">
                            <h5 class="g_panel_titulo" style="margin-bottom: 12px;">Tipo de Pedido y Bien Contratado</h5>

                            <div class="g_fila">
                                <div class="g_columna_6 g_margin_bottom_10">
                                    <label>Tipo de pedido</label>
                                    <input type="text" disabled
                                        value="{{ $libro->tipo_pedido ? str_replace('_', ' ', $libro->tipo_pedido) : 'N/D' }}">
                                </div>

                                <div class="g_columna_6 g_margin_bottom_10">
                                    <label>Bien contratado</label>
                                    <input type="text" disabled
                                        value="{{ $libro->tipo_bien_contratado ? str_replace('_', ' ', $libro->tipo_bien_contratado) : 'N/D' }}">
                                </div>
                            </div>

                            <div class="g_fila">
                                <div class="g_columna_6 g_margin_bottom_10">
                                    <label>Monto reclamado</label>
                                    <input type="text" disabled
                                        value="{{ $libro->monto_reclamado !== null ? $libro->monto_reclamado : 'N/D' }}">
                                </div>

                                <div class="g_columna_6 g_margin_bottom_10">
                                    <label>Clasificación</label>
                                    <input type="text" disabled
                                        value="{{ $libro->clasificacion ? str_replace('_', ' ', $libro->clasificacion) : 'N/D' }}">
                                </div>
                            </div>
                        </div>

                        <div class=" g_margin_bottom_10">
                            <label>Descripción del bien</label>
                            <textarea rows="3" disabled>{{ $libro->descripcion ?: 'N/D' }}</textarea>
                        </div>

                        <div class="g_margin_bottom_10">
                            <label>Detalle de la Reclamación</label>
                            <textarea rows="7" disabled>{{ $detalleLibre }}</textarea>
                        </div>

                        @can('ticket-libro-reclamacion.ver')
                            <div class="formulario_botones">
                                <a href="{{ route('erp.libro-reclamacion.vista.ver', $libro->ticket) }}"
                                    class="g_boton warning">
                                    Ver Libro <i class="fa-solid fa-book"></i>
                                </a>
                            </div>
                        @endcan
                    </div>
                </div>
            @endif

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
                                        @can('ticket.vista-ver')
                                            <a href="{{ route('erp.ticket.vista.ver', $ticket->padre->id) }}"
                                                class="g_accion ver" title="Ver Ticket Padre">
                                                <i class="fa-solid fa-eye"></i>
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
                                            @can('ticket.vista-ver')
                                                <a href="{{ route('erp.ticket.vista.ver', $hijo->id) }}" class="g_accion ver"
                                                    title="Ver Ticket Hijo">
                                                    <i class="fa-solid fa-eye"></i>
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

    @livewire('erp.atc.ticket.ticket-chat', ['ticket' => $ticket, 'soloLectura' => true])
</div>
