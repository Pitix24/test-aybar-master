<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Detalle Ticket Libro Reclamacion {{ $ticket->codigo }}</h2>

        <div class="cabecera_titulo_botones">
            @can('ticket-libro-reclamacion.lista')
                <a href="{{ route('erp.libro-reclamacion.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i>
                </a>
            @endcan

            @can('ticket-libro-reclamacion.editar')
                <a href="{{ route('erp.libro-reclamacion.vista.editar', $ticket->id) }}" class="g_boton primary">
                    Editar <i class="fa-solid fa-pencil"></i>
                </a>
            @endcan
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_8 g_gap_pagina">
            <div class="g_panel">
                <h4 class="g_panel_titulo">Informacion general</h4>
                <div class="g_fila formulario">
                    <div class="g_columna_6">
                        <label>Codigo</label>
                        <input type="text" value="{{ $ticket->codigo }}" disabled>
                    </div>
                    <div class="g_columna_6">
                        <label>Estado legal</label>
                        <input type="text" value="{{ str_replace('_', ' ', $ticket->estado_legal) }}" disabled>
                    </div>
                    <div class="g_columna_6">
                        <label>Clasificacion</label>
                        <input type="text" value="{{ str_replace('_', ' ', $ticket->clasificacion) }}" disabled>
                    </div>
                    <div class="g_columna_6">
                        <label>Libro ticket origen</label>
                        <input type="text" value="{{ $ticket->libro_reclamacion_ticket ?: 'N/D' }}" disabled>
                    </div>
                </div>
            </div>

            <div class="g_panel">
                <h4 class="g_panel_titulo">Contexto</h4>
                <div class="g_fila formulario">
                    <div class="g_columna_6">
                        <label>Unidad de negocio</label>
                        <input type="text" value="{{ $ticket->unidadNegocio?->nombre ?: 'N/D' }}" disabled>
                    </div>
                    <div class="g_columna_6">
                        <label>Proyecto</label>
                        <input type="text" value="{{ $ticket->proyecto?->nombre ?: 'N/D' }}" disabled>
                    </div>
                    <div class="g_columna_6">
                        <label>Cliente</label>
                        <input type="text" value="{{ $ticket->cliente?->name ?: 'N/D' }}" disabled>
                    </div>
                    <div class="g_columna_6">
                        <label>Gestor</label>
                        <input type="text" value="{{ $ticket->gestor?->name ?: 'N/D' }}" disabled>
                    </div>
                </div>
            </div>

            <div class="g_panel formulario">
                <h4 class="g_panel_titulo">Nota fuente</h4>
                <textarea rows="8" disabled>{{ $ticket->nota_fuente ?: 'Sin nota fuente.' }}</textarea>

                <h4 class="g_panel_titulo g_margin_top_20">Observaciones internas</h4>
                <textarea rows="5" disabled>{{ $ticket->observaciones_internas ?: 'Sin observaciones.' }}</textarea>
            </div>
        </div>

        <div class="g_columna_4 formulario">
            <div class="g_panel">
                <h4 class="g_panel_titulo">Auditoria</h4>
                <div class="g_margin_bottom_10">
                    <label>Creado</label>
                    <input type="text" value="{{ optional($ticket->created_at)->format('d/m/Y H:i') ?: 'N/D' }}" disabled>
                </div>
                <div class="g_margin_bottom_10">
                    <label>Actualizado</label>
                    <input type="text" value="{{ optional($ticket->updated_at)->format('d/m/Y H:i') ?: 'N/D' }}" disabled>
                </div>
                <div class="g_margin_bottom_10">
                    <label>Asignado</label>
                    <input type="text" value="{{ optional($ticket->assigned_at)->format('d/m/Y H:i') ?: 'N/D' }}" disabled>
                </div>
            </div>
        </div>
    </div>
</div>
