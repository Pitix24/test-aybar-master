@section('tituloPagina', 'Atender Cita')

<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="update" message="Actualizando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <div>
            <h2>Cita #{{ $cita->id }}</h2>
            <p style="margin: 0; color: #64748b;">
                Programada por: <span class="g_negrita">{{ $cita->creadoPor?->name ?? 'Sistema' }}</span>
            </p>
        </div>
        <div class="cabecera_titulo_botones">
            <button type="button" class="g_boton g_boton_danger" onclick="confirmarEliminar()">
                Eliminar <i class="fa-solid fa-trash"></i></button>

            @if($cita->ticket_id)
                <a href="{{ route('erp.ticket.vista.editar', $cita->ticket_id) }}" class="g_boton g_boton_secondary">
                    Ver Ticket #{{ $cita->ticket_id }} <i class="fa-solid fa-ticket"></i></a>
            @endif

            <a href="{{ route('erp.cita.vista.todo') }}" class="g_boton g_boton_dark">
                <i class="fa-solid fa-arrow-left"></i> Regresar</a>
        </div>
    </div>

    <form wire:submit="update" class="g_fila">
        <div class="g_columna_8">
            <!-- INFORMACIÓN DE LA SOLICITUD (Solo lectura mayormente) -->
            <div class="g_panel">
                <h4 class="g_panel_titulo">Información de la Solicitud</h4>
                <div class="g_margin_bottom_20">
                    <label>Asunto Solicitud</label>
                    <p class="g_texto_resaltado">{{ $cita->asunto_solicitud }}</p>
                </div>
                <div class="g_margin_bottom_20">
                    <label>Descripción de la Solicitud</label>
                    <div
                        style="background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0; color: #475569; line-height: 1.5;">
                        {{ $cita->descripcion_solicitud }}
                    </div>
                </div>

                <div class="g_fila">
                    <div class="g_columna_6">
                        <label>Motivo</label>
                        <select wire:model="motivo_cita_id">
                            @foreach($motivos as $m) <option value="{{ $m->id }}">{{ $m->nombre }}</option> @endforeach
                        </select>
                    </div>
                    <div class="g_columna_6">
                        <label>Sede</label>
                        <select wire:model="sede_id">
                            @foreach($sedes as $s) <option value="{{ $s->id }}">{{ $s->nombre }}</option> @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- RESPUESTA / ATENCIÓN -->
            <div class="g_panel g_margin_top_20">
                <h4 class="g_panel_titulo" style="color: var(--primary);">Atención y Respuesta</h4>

                <div class="g_margin_bottom_20">
                    <label for="asunto_respuesta">Resumen de Atención (Asunto)</label>
                    <input type="text" id="asunto_respuesta" wire:model="asunto_respuesta"
                        placeholder="Ej: Cita realizada con éxito, Pendiente de documentos...">
                    @error('asunto_respuesta') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_margin_bottom_20">
                    <label for="descripcion_respuesta">Detalle de la Atención</label>
                    <textarea id="descripcion_respuesta" wire:model="descripcion_respuesta" rows="6"
                        placeholder="Escriba aquí los acuerdos, resultados o comentarios de la cita..."></textarea>
                    @error('descripcion_respuesta') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="g_columna_4">
            <div class="g_panel">
                <h4 class="g_panel_titulo">Estado y Asignación</h4>

                <div class="g_margin_bottom_20">
                    <label>Estado de la Cita</label>
                    <select wire:model="estado_cita_id" style="font-weight: 700; color: var(--primary);">
                        @foreach($estados as $e) <option value="{{ $e->id }}">{{ $e->nombre }}</option> @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_20">
                    <label>Área</label>
                    <select wire:model.live="area_id">
                        @foreach($areas as $a) <option value="{{ $a->id }}">{{ $a->nombre }}</option> @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_20">
                    <label>Asesor Asignado</label>
                    <select wire:model="gestor_id">
                        <option value="">Sin asignar</option>
                        @foreach($gestores as $g) <option value="{{ $g->id }}">{{ $g->name }}</option> @endforeach
                    </select>
                </div>

                <hr class="g_hr">

                <h4 class="g_panel_titulo">Reprogramación</h4>

                <div class="g_margin_bottom_15">
                    <label>Fecha</label>
                    <input type="date" wire:model="fecha">
                </div>
                <div class="g_fila">
                    <div class="g_columna_6">
                        <label>H. Inicio</label>
                        <input type="time" wire:model="hora_inicio">
                    </div>
                    <div class="g_columna_6">
                        <label>H. Fin</label>
                        <input type="time" wire:model="hora_fin">
                    </div>
                </div>

                <div class="formulario_botones g_margin_top_20">
                    <button type="submit" class="g_boton g_boton_primary g_bloque">
                        <i class="fa-solid fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </div>

            <div class="g_panel g_margin_top_20">
                <h4 class="g_panel_titulo">Datos del Cliente</h4>
                <div class="g_margin_bottom_10">
                    <label style="font-size: 0.7rem;">CLIENTE</label>
                    <p style="font-weight: 700; margin: 0;">{{ $cita->nombres }}</p>
                    <p style="font-size: 0.8rem; color: #64748b; margin: 0;">{{ $cita->dni }}</p>
                </div>
                <div class="g_margin_bottom_10">
                    <label style="font-size: 0.7rem;">PROYECTO</label>
                    <p style="font-size: 0.85rem; margin: 0;">{{ $cita->proyecto?->nombre ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </form>

    @script
    <script>
        window.confirmarEliminar = function () {
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