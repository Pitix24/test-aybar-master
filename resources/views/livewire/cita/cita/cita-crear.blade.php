@section('tituloPagina', 'Programar Cita')

<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="store" message="Guardando cita..." />

    <div class="g_panel cabecera_titulo_pagina">
        <div>
            <h2>Programar Nueva Cita</h2>
            @if($ticket)
                <p style="margin: 0; color: #64748b;">Asociada al Ticket #{{ $ticket->id }}</p>
            @endif
        </div>
        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.cita.vista.todo') }}" class="g_boton g_boton_dark">
                <i class="fa-solid fa-arrow-left"></i> Regresar</a>
        </div>
    </div>

    <form wire:submit="store" class="g_fila">
        <div class="g_columna_8">
            <div class="g_panel">
                <h4 class="g_panel_titulo">Detalles del Requerimiento</h4>

                <div class="g_margin_bottom_20">
                    <label for="asunto_solicitud">Asunto de la Cita <span class="g_requerido">*</span></label>
                    <input type="text" id="asunto_solicitud" wire:model="asunto_solicitud"
                        class="@error('asunto_solicitud') input-error @enderror"
                        placeholder="Resumen del motivo de la cita">
                    @error('asunto_solicitud') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_margin_bottom_20">
                    <label for="descripcion_solicitud">Descripción / Notas Iniciales <span
                            class="g_requerido">*</span></label>
                    <textarea id="descripcion_solicitud" wire:model="descripcion_solicitud" rows="5"
                        class="@error('descripcion_solicitud') input-error @enderror"
                        placeholder="Detalles importantes para la cita..."></textarea>
                    @error('descripcion_solicitud') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_fila">
                    <div class="g_columna_6 g_margin_bottom_20">
                        <label>Motivo <span class="g_requerido">*</span></label>
                        <select wire:model="motivo_cita_id" class="@error('motivo_cita_id') input-error @enderror">
                            <option value="">Seleccione motivo</option>
                            @foreach($motivos as $m) <option value="{{ $m->id }}">{{ $m->nombre }}</option> @endforeach
                        </select>
                        @error('motivo_cita_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                    <div class="g_columna_6 g_margin_bottom_20">
                        <label>Sede <span class="g_requerido">*</span></label>
                        <select wire:model="sede_id" class="@error('sede_id') input-error @enderror">
                            <option value="">Seleccione sede</option>
                            @foreach($sedes as $s) <option value="{{ $s->id }}">{{ $s->nombre }}</option> @endforeach
                        </select>
                        @error('sede_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- DATOS CLIENTE (Solo lectura si viene de ticket) -->
            <div class="g_panel g_margin_top_20">
                <h4 class="g_panel_titulo">Datos del Cliente</h4>
                <div class="g_fila">
                    <div class="g_columna_4 g_margin_bottom_15">
                        <label>DNI / RUC</label>
                        <input type="text" value="{{ $dni }}" disabled class="g_input_disabled">
                    </div>
                    <div class="g_columna_8 g_margin_bottom_15">
                        <label>Nombres / Razón Social</label>
                        <input type="text" value="{{ $nombres }}" disabled class="g_input_disabled">
                    </div>
                </div>
                @if(!$ticket)
                    <div class="g_alerta g_alerta_warning">
                        <i class="fa-solid fa-triangle-exclamation"></i> Se recomienda programar citas desde la edición de
                        un Ticket para vincular datos correctamente.
                    </div>
                @endif
            </div>
        </div>

        <div class="g_columna_4">
            <div class="g_panel">
                <h4 class="g_panel_titulo">Asignación y Tiempo</h4>

                <div class="g_margin_bottom_20">
                    <label>Área Responsable <span class="g_requerido">*</span></label>
                    <select wire:model.live="area_id" class="@error('area_id') input-error @enderror">
                        <option value="">Seleccione área</option>
                        @foreach($areas as $a) <option value="{{ $a->id }}">{{ $a->nombre }}</option> @endforeach
                    </select>
                    @error('area_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_margin_bottom_20">
                    <label>Asesor / Gestor <span class="g_requerido">*</span></label>
                    <select wire:model="gestor_id" class="@error('gestor_id') input-error @enderror">
                        <option value="">Seleccione asesor</option>
                        @foreach($gestores as $g) <option value="{{ $g->id }}">{{ $g->name }}</option> @endforeach
                    </select>
                    @error('gestor_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <hr class="g_hr">

                <div class="g_margin_bottom_15">
                    <label>Fecha de la Cita <span class="g_requerido">*</span></label>
                    <input type="date" wire:model.live="fecha" class="@error('fecha') input-error @enderror">
                    @error('fecha') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_fila">
                    <div class="g_columna_6 g_margin_bottom_15">
                        <label>H. Inicio <span class="g_requerido">*</span></label>
                        <input type="time" wire:model.live="hora_inicio"
                            class="@error('hora_inicio') input-error @enderror">
                        @error('hora_inicio') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                    <div class="g_columna_6 g_margin_bottom_15">
                        <label>H. Fin <span class="g_requerido">*</span></label>
                        <input type="time" wire:model="hora_fin" class="@error('hora_fin') input-error @enderror">
                        @error('hora_fin') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="g_margin_bottom_20">
                    <label>Estado Inicial</label>
                    <select wire:model="estado_cita_id">
                        @foreach($estados as $e) <option value="{{ $e->id }}">{{ $e->nombre }}</option> @endforeach
                    </select>
                </div>

                <div class="formulario_botones">
                    <button type="submit" class="g_boton g_boton_primary g_bloque">
                        <i class="fa-solid fa-calendar-check"></i> Programar Cita
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>