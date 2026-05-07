<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="asignarGestor,marcarComoResuelto,cerrar"
        message="Guardando cambios..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Ver Ticket de Soporte #{{ $soporte->codigo }}</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.soporte.vista.todo') }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>

            @if (Auth::user()->is_admin || $soporte->solicitante_id === Auth::id())
            <a href="{{ route('erp.soporte.vista.editar', $soporte) }}" class="g_boton primary">
                Editar <i class="fa-solid fa-pencil"></i>
            </a>
            @endif

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    <form class="formulario g_panel">
        <div class="g_fila">
            <div class="g_margin_bottom_10 g_columna_3">
                <label>Tipo</label>
                <select disabled>
                    <option value="{{ $soporte->tipo_soporte_id }}">{{ $soporte->tipoSoporte?->nombre ?? 'N/A' }}
                    </option>
                </select>
            </div>

            <div class="g_margin_bottom_10 g_columna_3">
                <label>Prioridad</label>
                <select disabled>
                    <option value="{{ $soporte->prioridad_soporte_id }}">{{ $soporte->prioridadSoporte?->nombre ?? 'N/A'
                        }}</option>
                </select>
            </div>

            <div class="g_margin_bottom_10 g_columna_3">
                <label>Estado</label>
                <select disabled>
                    <option value="{{ $soporte->estado_soporte_id }}">{{ $soporte->estadoSoporte?->nombre ?? 'N/A' }}
                    </option>
                </select>
            </div>

            <div class="g_margin_bottom_10 g_columna_3">
                <label>Área</label>
                <select disabled>
                    <option value="{{ $soporte->area_id }}">{{ $soporte->area?->nombre ?? 'Sin área asignada' }}
                    </option>
                </select>
            </div>
        </div>

        <div class="g_margin_bottom_10">
            <label>Título</label>
            <input type="text" value="{{ $soporte->titulo }}" disabled>
        </div>

        <div class="g_margin_bottom_10">
            <label>Descripción</label>
            <textarea wire:model.blur="descripcion" rows="6" class="@error('descripcion') input-error @enderror"
                disabled>{{ $soporte->descripcion }}</textarea>
            @error('descripcion') <p class="mensaje_error">{{ $message }}</p> @enderror
        </div>

        <div class="g_margin_bottom_10">
            <h4 class="g_panel_titulo"><i class="fa-solid fa-circle-info"></i> Información del Ticket</h4>

            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Código</label>
                    <input class="input-disabled" type="text" value="{{ $soporte->codigo }}" disabled>
                </div>

                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Solicitante</label>
                    <input class="input-disabled" type="text" value="{{ $soporte->solicitante?->name ?? 'N/A' }}"
                        disabled>
                </div>

                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Gestor</label>
                    <input class="input-disabled" type="text" value="{{ $soporte->gestor?->name ?? 'N/A' }}" disabled>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Creado</label>
                    <input class="input-disabled" type="text"
                        value="{{ $soporte->created_at?->format('d/m/Y H:i') ?? '—' }}"
                        class="@error('created_at') input-error @enderror" disabled>
                    @error('created_at') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Asignado</label>
                    <input class="input-disabled" type="text"
                        value="{{ $soporte->assigned_at?->format('d/m/Y H:i') ?? 'Pendiente' }}"
                        class="@error('assigned_at') input-error @enderror" disabled>
                    @error('assigned_at') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Resuelto</label>
                    <input class="input-disabled" type="text"
                        value="{{ $soporte->resuelto_at?->format('d/m/Y H:i') ?? 'Pendiente' }}"
                        class="@error('resuelto_at') input-error @enderror" disabled>
                    @error('resuelto_at') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="formulario_botones">
            <button type="button" class="g_boton cancelar" onclick="history.back()">
                <i class="fa-solid fa-times"></i> Cancelar
            </button>
        </div>
    </form>
</div>