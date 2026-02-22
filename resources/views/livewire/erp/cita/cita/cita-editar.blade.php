@section('tituloPagina', 'Atender Cita')

<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="update" message="Actualizando cita..." />

    <div class="g_panel cabecera_titulo_pagina">
        <div>
            <h2>Cita #{{ $cita->id }}</h2>
            <p style="margin: 0; color: #64748b;">
                Programada por: <span class="g_negrita">{{ $cita->creador?->name ?? 'Sistema' }}</span>
            </p>
        </div>

        <div class="cabecera_titulo_botones">
            @can('cita.eliminar')
                <button type="button" class="g_boton danger" onclick="confirmarEliminarCita()">
                    Eliminar <i class="fa-solid fa-trash"></i>
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

                        <button type="button" @click="activeTab = 'asunto'"
                            :class="activeTab === 'asunto' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                            <i class="fa-solid fa-message"></i> Asunto final
                        </button>
                    </div>
                </div>

                <div x-show="activeTab === 'general'" x-transition class="g_tab_content">
                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Sede</label>
                            <input type="text" value="{{ $cita->sede->nombre ?? 'N/A' }}" readonly disabled>
                        </div>

                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Motivo</label>
                            <input type="text" value="{{ $cita->motivo->nombre ?? 'N/A' }}" readonly disabled>
                        </div>

                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Área</label>
                            <input type="text" value="{{ $cita->area->nombre ?? 'N/A' }}" readonly disabled>
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Gestor (Asignado)</label>
                            <input type="text" value="{{ $cita->gestor->name ?? 'Sin asignar' }}" readonly disabled>
                        </div>

                        <div class="g_margin_bottom_10 g_columna_6">
                            <label for="estado_cita_id">Estado<span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <select id="estado_cita_id" wire:model.live="estado_cita_id" required>
                                <option value="" selected disabled>Seleccionar un estado</option>
                                @foreach ($estados as $estado)
                                    <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                                @endforeach
                            </select>
                            @error('estado_cita_id')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Fecha</label>
                            <input type="date" value="{{ $cita->fecha_inicio?->format('Y-m-d') }}" readonly disabled>
                        </div>

                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Hora Inicio</label>
                            <input type="time" value="{{ $cita->fecha_inicio?->format('H:i') }}" readonly disabled>
                        </div>

                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Hora fin</label>
                            <input type="time" value="{{ $cita->fecha_fin?->format('H:i') }}" readonly disabled>
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_12">
                            <label>Asunto (Solicitud)</label>
                            <textarea rows="4" readonly disabled>{{ $cita->asunto_solicitud }}</textarea>
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_12">
                            <label>Descripción (Solicitud)</label>
                            <textarea rows="6" readonly disabled>{{ $cita->descripcion_solicitud }}</textarea>
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'cliente'" x-transition class="g_tab_content">
                    <div class="g_margin_bottom_10">
                        @can('cliente.consultar')
                            <a href="{{ route('erp.cliente.vista.consultar', $cita->ticket->dni) }}"
                                class="g_boton primary">
                                <i class="fa-solid fa-border-all"></i> Portal cliente
                            </a>
                        @endcan

                        @can('cliente.ver')
                            @if(isset($cita->ticket->userCliente))
                                <a href="{{ route('erp.cliente.vista.ver', $cita->ticket->userCliente->id) }}"
                                    class="g_boton info">
                                    <i class="fa-solid fa-circle-user"></i> Perfil
                                </a>
                            @endif
                        @endcan
                    </div>
                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Cliente</label>
                            <input type="text" disabled value="{{ $cita->ticket->nombres ?? 'Sin asignar' }}">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>DNI</label>
                            <input type="text" disabled value="{{ $cita->ticket->dni ?? 'Sin asignar' }}">
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Correo</label>
                            <input type="text" disabled value="{{ $cita->ticket->email ?? 'Sin asignar' }}">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Celular</label>
                            <input type="text" disabled value="{{ $cita->ticket->celular ?? 'Sin asignar' }}">
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'asunto'" x-transition class="g_tab_content">
                    <div class="g_fila">
                        <div class="g_columna_12 g_margin_bottom_10">
                            <label for="asunto_respuesta">Asunto (Respuesta)</label>
                            <input type="text" id="asunto_respuesta" wire:model.live="asunto_respuesta">
                            @error('asunto_respuesta')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_12">
                            <label for="descripcion_respuesta">Descripción (Respuesta)</label>
                            <textarea id="descripcion_respuesta" wire:model.live="descripcion_respuesta"
                                rows="10"></textarea>
                            @error('descripcion_respuesta')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="formulario_botones">
                    @can('cita.editar')
                        <button type="submit" class="g_boton guardar" wire:loading.attr="disabled" wire:target="update">
                            <span wire:loading.remove wire:target="update">
                                <i class="fa-solid fa-save"></i> Actualizar
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

            @livewire('erp.cita.cita.cita-email', ['cita' => $cita])
        </div>

        <div class="g_columna_4 formulario">
            <div class="g_panel">
                <h4 class="g_panel_titulo">Ticket asociado</h4>

                @if($ticket)
                    <div class="g_margin_bottom_10">
                        @can('ticket.ver')
                            <a href="{{ route('erp.ticket.vista.ver', $ticket->id) }}" class="g_boton warning">
                                <i class="fa-solid fa-eye"></i> Ver ticket
                            </a>
                        @endcan

                        @can('ticket.editar')
                            <a href="{{ route('erp.ticket.vista.editar', $ticket->id) }}" class="g_boton info">
                                <i class="fa-solid fa-pencil"></i> Editar ticket
                            </a>
                        @endcan
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Empresa</label>
                            <input type="text" disabled value="{{ $ticket->unidadNegocio->nombre ?? 'Sin asignar' }}">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Proyecto</label>
                            <input type="text" disabled value="{{ $ticket->proyecto->nombre ?? 'Sin asignar' }}">
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Área origen</label>
                            <input type="text" disabled value="{{ $ticket->area->nombre ?? 'Sin asignar' }}">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Tipo solicitud</label>
                            <input type="text" disabled value="{{ $ticket->tipoSolicitud->nombre ?? 'Sin asignar' }}">
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Sub tipo solicitud</label>
                            <input type="text" disabled value="{{ $ticket->subTipoSolicitud->nombre ?? 'Sin asignar' }}">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Canal</label>
                            <input type="text" disabled value="{{ $ticket->canal->nombre ?? 'Sin asignar' }}">
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Cliente</label>
                            <input type="text" disabled value="{{ $ticket->nombres ?? 'Sin asignar' }}">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Asignado </label>
                            <input type="text" disabled value="{{ $ticket->gestor->name ?? 'Sin asignar' }}">
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_12 g_margin_bottom_10">
                            <label>Asunto </label>
                            <textarea disabled>{{ $ticket->asunto_inicial ?? 'Sin asunto' }}</textarea>
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_12 g_margin_bottom_10">
                            <label>Descripción </label>
                            <textarea disabled>{{ $ticket->descripcion_inicial ?? 'Sin descripción' }}</textarea>
                        </div>
                    </div>

                    @if (!empty($ticket->lotes))
                        <div class="g_fila">
                            <div class="g_columna_12">
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
                                                <tr wire:key="lote-parent-{{ $index }}">
                                                    <td> {{ $l['razon_social'] }} </td>
                                                    <td> {{ $l['proyecto'] }} </td>
                                                    <td> {{ $l['numero_lote'] }} </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="g_vacio">
                        <i class="fa-solid fa-ticket-simple"></i>
                        <p>Esta cita no está vinculada a un ticket.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @script
    <script>
        window.confirmarEliminarCita = function () {
            Swal.fire({
                title: '¿Eliminar esta cita?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: '¡Sí, eliminar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.eliminarCitaOn();
                }
            })
        }
    </script>
    @endscript
</div>