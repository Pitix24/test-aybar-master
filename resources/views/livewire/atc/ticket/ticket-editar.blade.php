@section('tituloPagina', 'Editar Ticket')

<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <div>
            <h2>Ticket #{{ $ticket->id }}</h2>
            <p style="margin: 0; color: #64748b;">Creado por: {{ $ticket->creadoPor?->name ?? 'Sistema' }} el
                {{ $ticket->created_at->format('d/m/Y H:i') }}
            </p>
        </div>
        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.ticket.vista.todo') }}" class="g_boton g_boton_light">
                Lista <i class="fa-solid fa-list"></i></a>

            <button type="button" class="g_boton g_boton_danger" onclick="alertaEliminarTicket()">
                Eliminar <i class="fa-solid fa-trash-can"></i>
            </button>

            <button type="button" class="g_boton g_boton_dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <form wire:submit="update" class="formulario">
        <div class="g_fila">
            <!-- COLUMNA IZQUIERDA -->
            <div class="g_columna_8">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Actualizar Información</h4>

                    <div class="g_margin_bottom_15">
                        <label for="asunto_inicial">Asunto <span class="obligatorio"><i
                                    class="fa-solid fa-asterisk"></i></span></label>
                        <input type="text" id="asunto_inicial" wire:model.blur="asunto_inicial"
                            class="@error('asunto_inicial') input-error @enderror">
                        @error('asunto_inicial') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>

                    <div class="g_margin_bottom_15">
                        <label for="descripcion_inicial">Descripción <span class="obligatorio"><i
                                    class="fa-solid fa-asterisk"></i></span></label>
                        <textarea id="descripcion_inicial" wire:model.blur="descripcion_inicial" rows="8"
                            class="@error('descripcion_inicial') input-error @enderror"></textarea>
                        @error('descripcion_inicial') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_15">
                            <label>Unidad de Negocio</label>
                            <select wire:model.live="unidad_negocio_id">
                                @foreach($unidades as $u) <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="g_columna_6 g_margin_bottom_15">
                            <label>Proyecto</label>
                            <select wire:model.live="proyecto_id">
                                @foreach($proyectos as $p) <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_4 g_margin_bottom_15">
                            <label>Tipo Solicitud</label>
                            <select wire:model.live="tipo_solicitud_id">
                                @foreach($tipos as $t) <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="g_columna_4 g_margin_bottom_15">
                            <label>Subtipo</label>
                            <select wire:model="sub_tipo_solicitud_id">
                                <option value="">General</option>
                                @foreach($subtipos as $st) <option value="{{ $st->id }}">{{ $st->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="g_columna_4 g_margin_bottom_15">
                            <label>Canal</label>
                            <select wire:model="canal_id">
                                @foreach($canales as $c) <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="formulario_botones">
                        <button type="submit" class="g_boton g_boton_guardar" wire:loading.attr="disabled">
                            <i class="fa-solid fa-pencil"></i> Guardar Cambios
                        </button>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA -->
            <div class="g_columna_4">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Estado y Asignación</h4>

                    <div class="g_margin_bottom_15">
                        <label>Estado Actual</label>
                        <select wire:model="estado_ticket_id">
                            @foreach($estados as $es) <option value="{{ $es->id }}">{{ $es->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="g_margin_bottom_15">
                        <label>Prioridad</label>
                        <select wire:model="prioridad_ticket_id">
                            @foreach($prioridades as $pr) <option value="{{ $pr->id }}">{{ $pr->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="g_margin_bottom_15">
                        <label>Gestor Asignado</label>
                        <select wire:model="gestor_id">
                            <option value="">Sin asignar</option>
                            @foreach($gestores as $ge) <option value="{{ $ge->id }}">{{ $ge->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <hr style="margin: 20px 0; border: none; border-top: 1px solid #e2e8f0;">

                    <div class="g_margin_bottom_15">
                        <label>Cliente / Solicitante</label>
                        <select wire:model="cliente_id">
                            @foreach($clientes as $cl) <option value="{{ $cl->id }}">{{ $cl->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="g_margin_bottom_15">
                        <label>Área Responsable</label>
                        <select wire:model="area_id">
                            @foreach($areas as $ar) <option value="{{ $ar->id }}">{{ $ar->nombre }}</option> @endforeach
                        </select>
                    </div>
                </div>

                <!-- PARTICIPANTES -->
                <div class="g_panel g_margin_top_20">
                    <h4 class="g_panel_titulo">Participantes (CC)</h4>

                    <div class="g_select_search">
                        <input type="text" wire:model.live.debounce.300ms="searchUser" class="g_select_search_input"
                            placeholder="Buscar para agregar...">

                        @if(!empty($participantesDisponibles))
                            <div class="g_select_search_results">
                                @foreach($participantesDisponibles as $du)
                                    <div class="g_select_search_item" wire:click="addParticipant({{ $du->id }})">
                                        <div class="g_select_search_avatar">
                                            {{ $du->initials() }}
                                        </div>
                                        <div class="g_select_search_info">
                                            <span class="g_select_search_name">{{ $du->name }}</span>
                                            <span class="g_select_search_sub">{{ $du->email }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="g_select_search_selected_list">
                        @foreach($participantesSeleccionados as $su)
                            <div class="g_select_search_selected_item">
                                <div class="g_select_search_selected_info">
                                    <div class="g_select_search_selected_avatar">
                                        {{ $su->initials() }}
                                    </div>
                                    <span class="g_select_search_selected_name">{{ $su->name }}</span>
                                </div>
                                <button type="button" class="g_select_search_remove"
                                    wire:click="removeParticipant({{ $su->id }})" title="Quitar">
                                    <i class="fa-solid fa-times"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </form>

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
</div>