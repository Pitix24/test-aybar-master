@section('tituloPagina', 'Atender Cita')

<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="update" message="Actualizando cita..." />

    <div class="g_panel cabecera_titulo_pagina">
        <div>
            <h2>Cita #{{ $cita->id }}</h2>
            <p style="margin: 0; color: #64748b;">
                Programada por: <span class="g_negrita">{{ $cita->creadoPor?->name ?? 'Sistema' }}</span>
            </p>
        </div>

        <div class="cabecera_titulo_botones">
            @can('cita.eliminar')
                <button type="button" class="g_boton danger" onclick="confirmarEliminarCita()">
                    Eliminar <i class="fa-solid fa-trash"></i>
                </button>
            @endcan

            <a href="{{ route('erp.cita.vista.todo') }}" class="g_boton g_boton_dark">
                <i class="fa-solid fa-arrow-left"></i> Regresar</a>
        </div>
    </div>

    <form wire:submit="update" class="formulario">
        <div class="g_fila">
            <div class="g_columna_8 g_gap_pagina">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">General / Solicitud</h4>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_4">
                            <label for="sede_id">
                                Sede <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                            </label>
                            <select id="sede_id" wire:model.live="sede_id" required>
                                <option value="" selected disabled>Seleccionar una sede</option>
                                @foreach ($sedes as $sede)
                                    <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                                @endforeach
                            </select>
                            @error('sede_id')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="g_margin_bottom_10 g_columna_4">
                            <label for="motivo_cita_id">
                                Motivo <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                            </label>
                            <select id="motivo_cita_id" wire:model.live="motivo_cita_id" required>
                                <option value="" selected disabled>Seleccionar un motivo</option>
                                @foreach ($motivos as $motivo)
                                    <option value="{{ $motivo->id }}">{{ $motivo->nombre }}</option>
                                @endforeach
                            </select>
                            @error('motivo_cita_id')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="g_margin_bottom_10 g_columna_4">
                            <label for="area_id">
                                Area <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                            </label>
                            <select id="area_id" wire:model.live="area_id" required>
                                <option value="" selected disabled>Seleccionar un area</option>
                                @foreach ($areas as $area)
                                    <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                                @endforeach
                            </select>
                            @error('area_id')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_6">
                            <label for="gestor_id">
                                Gestor <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                            </label>
                            <select id="gestor_id" wire:model.live="gestor_id" required>
                                <option value="" selected disabled>Seleccionar un asignado</option>
                                @foreach ($gestores as $usuario)
                                    <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                                @endforeach
                            </select>
                            @error('gestor_id')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
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
                            <label for="fecha">Fecha<span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <input type="date" id="fecha" wire:model.live="fecha" required>
                            @error('fecha') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        <div class="g_margin_bottom_10 g_columna_4">
                            <label for="hora_inicio">Hora inicio <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <input type="time" id="hora_inicio" wire:model.live="hora_inicio" required>
                            @error('hora_inicio') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        <div class="g_margin_bottom_10 g_columna_4">
                            <label for="hora_fin">Hora fin <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <input type="time" id="hora_fin" wire:model.live="hora_fin" required>
                            @error('hora_fin') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_12">
                            <label for="asunto_solicitud">Asunto (Solicitud) <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <textarea id="asunto_solicitud" wire:model.live="asunto_solicitud" rows="4"></textarea>
                            @error('asunto_solicitud')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_12">
                            <label for="descripcion_solicitud">Descripción (Solicitud) <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <textarea id="descripcion_solicitud" wire:model.live="descripcion_solicitud"
                                rows="6"></textarea>
                            @error('descripcion_solicitud')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="g_panel">
                    <h4 class="g_panel_titulo" style="color: var(--primary);">Atención y Respuesta</h4>

                    <div class="g_fila">
                        <div class="g_columna_12">
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

                    <div class="g_margin_top_20">
                        <div class="formulario_botones">
                            <button type="submit" class="g_boton guardar"
                                wire:loading.attr="disabled">Actualizar</button>

                            <a href="{{ route('erp.cita.vista.todo') }}" class="g_boton cancelar">Cancelar</a>
                        </div>
                    </div>
                </div>
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
                                <input type="text" disabled
                                    value="{{ $ticket->subTipoSolicitud->nombre ?? 'Sin asignar' }}">
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

                <div class="g_panel g_margin_top_20">
                    <h4 class="g_panel_titulo">Datos del Cliente (Referencia)</h4>
                    <div class="g_margin_bottom_10">
                        <label>DNI / RUC</label>
                        <input type="text" disabled value="{{ $dni }}">
                    </div>
                    <div class="g_margin_bottom_10">
                        <label>Nombres / Razón Social</label>
                        <input type="text" disabled value="{{ $nombres }}">
                    </div>
                </div>
            </div>
        </div>
    </form>

    @livewire('erp.cita.cita.cita-calendario')

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