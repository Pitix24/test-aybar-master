@section('tituloPagina', 'Nuevo Ticket')

<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Crear Nuevo Ticket</h2>
        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.ticket.vista.todo') }}" class="g_boton g_boton_light">
                Lista <i class="fa-solid fa-list"></i></a>
            <button type="button" class="g_boton g_boton_dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <form wire:submit="store" class="formulario">
        <div class="g_fila">
            <!-- COLUMNA IZQUIERDA: DATOS GENERALES -->
            <div class="g_columna_8">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Detalle del Ticket</h4>

                    <div class="g_margin_bottom_15">
                        <label for="asunto_inicial">Asunto <span class="obligatorio"><i
                                    class="fa-solid fa-asterisk"></i></span></label>
                        <input type="text" id="asunto_inicial" wire:model.blur="asunto_inicial"
                            class="@error('asunto_inicial') input-error @enderror"
                            placeholder="Resumen corto del problema">
                        @error('asunto_inicial') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>

                    <div class="g_margin_bottom_15">
                        <label for="descripcion_inicial">Descripción Detallada <span class="obligatorio"><i
                                    class="fa-solid fa-asterisk"></i></span></label>
                        <textarea id="descripcion_inicial" wire:model.blur="descripcion_inicial" rows="6"
                            class="@error('descripcion_inicial') input-error @enderror"
                            placeholder="Explique detalladamente el requerimiento..."></textarea>
                        @error('descripcion_inicial') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_15">
                            <label>Unidad de Negocio <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <select wire:model.live="unidad_negocio_id">
                                <option value="">Seleccione...</option>
                                @foreach($unidades as $u) <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                                @endforeach
                            </select>
                            @error('unidad_negocio_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        <div class="g_columna_6 g_margin_bottom_15">
                            <label>Proyecto <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <select wire:model.live="proyecto_id">
                                <option value="">Seleccione...</option>
                                @foreach($proyectos as $p) <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                                @endforeach
                            </select>
                            @error('proyecto_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_4 g_margin_bottom_15">
                            <label>Tipo Solicitud <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <select wire:model.live="tipo_solicitud_id">
                                <option value="">Seleccione...</option>
                                @foreach($tipos as $t) <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                                @endforeach
                            </select>
                            @error('tipo_solicitud_id') <p class="mensaje_error">{{ $message }}</p> @enderror
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
                            <label>Canal <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                            <select wire:model="canal_id">
                                <option value="">Seleccione...</option>
                                @foreach($canales as $c) <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                                @endforeach
                            </select>
                            @error('canal_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="formulario_botones">
                        <button type="submit" class="g_boton g_boton_guardar" wire:loading.attr="disabled">
                            <i class="fa-solid fa-save"></i> Generar Ticket
                        </button>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA: ASIGNACIONES Y PARTICIPANTES -->
            <div class="g_columna_4">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Clasificación</h4>

                    <div class="g_margin_bottom_15">
                        <label>Cliente / Solicitante <span class="obligatorio"><i
                                    class="fa-solid fa-asterisk"></i></span></label>
                        <select wire:model="cliente_id">
                            <option value="">Seleccione...</option>
                            @foreach($clientes as $cl) <option value="{{ $cl->id }}">{{ $cl->name }}</option>
                            @endforeach
                        </select>
                        @error('cliente_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>

                    <div class="g_margin_bottom_15">
                        <label>Área Destino <span class="obligatorio"><i
                                    class="fa-solid fa-asterisk"></i></span></label>
                        <select wire:model="area_id">
                            <option value="">Seleccione...</option>
                            @foreach($areas as $ar) <option value="{{ $ar->id }}">{{ $ar->nombre }}</option> @endforeach
                        </select>
                        @error('area_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>

                    <div class="g_margin_bottom_15">
                        <label>Estado Inicial</label>
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
                </div>

                <!-- PARTICIPANTES -->
                <div class="g_panel g_margin_top_20">
                    <h4 class="g_panel_titulo">Participantes (CC)</h4>
                    <div class="g_margin_bottom_10">
                        <input type="text" wire:model.live.debounce.300ms="searchUser" placeholder="Buscar usuario...">

                        @if(!empty($participantesDisponibles))
                            <div class="g_sugerencias"
                                style="position: absolute; width: 100%; z-index: 10; background: white; border: 1px solid #ddd; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                @foreach($participantesDisponibles as $du)
                                    <div wire:click="addParticipant({{ $du->id }})"
                                        style="padding: 10px; cursor: pointer; border-bottom: 1px solid #eee; display: flex; align-items: center; gap: 10px;">
                                        <div
                                            style="width: 30px; height: 30px; background: #3b82f6; border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center; font-size: 0.7rem;">
                                            {{ $du->initials() }}
                                        </div>
                                        <span>{{ $du->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        @foreach($participantesSeleccionados as $su)
                            <div
                                style="padding: 8px 12px; background: #f1f5f9; border-radius: 6px; display: flex; align-items: center; justify-content: space-between;">
                                <span style="font-size: 0.85rem;">{{ $su->name }}</span>
                                <button type="button" wire:click="removeParticipant({{ $su->id }})"
                                    style="color: #ef4444; background: none; border: none; cursor: pointer;">
                                    <i class="fa-solid fa-times"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>