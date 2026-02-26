<div class="g_gap_pagina">
    <x-loading-overlay wire:loading message="Generando invitación..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Generar Invitado: <span style="color: var(--color-primary);">{{ $evento->nombre }}</span></h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.vista.prospectos', $evento->id) }}" class="g_boton success">
                Prospectos <i class="fa-solid fa-users-viewfinder"></i>
            </a>

            <a href="{{ route('erp.entrega-fest.vista.asistencia', $evento->id) }}" class="g_boton info">
                Asistencia <i class="fa-solid fa-user-check"></i></a>

            <a href="{{ route('erp.entrega-fest.vista.invitados', $evento->id) }}" class="g_boton dark">
                <i class="fa-solid fa-arrow-left"></i> Cancelar
            </a>
        </div>
    </div>

    <form wire:submit.prevent="store">
        <div class="g_panel">
            <h4 class="g_panel_titulo"><i class="fa-solid fa-id-card"></i> Datos de Invitación</h4>

            <div class="g_fila">
                <div class="g_margin_bottom_15 g_columna_8">
                    <label>Prospecto <span class="obligatorio">*</span></label>
                    <select wire:model="prospecto_entrega_fest_id"
                        class="@error('prospecto_entrega_fest_id') select-error @enderror">
                        <option value="">Seleccione el prospecto...</option>
                        @foreach ($prospectos as $p)
                            <option value="{{ $p->id }}">{{ $p->nombre_completo }} ({{ $p->dni }}) -
                                {{ $p->proyecto->nombre ?? '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('prospecto_entrega_fest_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                    <p class="leyenda">Solo aparecen prospectos del evento que aún no tienen invitación.</p>
                </div>

                <div class="g_margin_bottom_15 g_columna_4">
                    <label>Acompañantes Permitidos <span class="obligatorio">*</span></label>
                    <input type="number" wire:model="cantidad_acompanantes_permitidos">
                    @error('cantidad_acompanantes_permitidos') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="g_margin_bottom_15">
                <label for="confirmado">¿Confirmado por el cliente?</label>
                <div class="g_switch-wrapper">
                    <label class="g_switch">
                        <input id="confirmado" type="checkbox" wire:model.live="confirmado">
                        <span class="g_switch-slider"></span>
                    </label>
                    <span class="g_switch-label">{{ $confirmado ? 'SÍ' : 'NO' }}</span>
                </div>
            </div>

            <div class="formulario_botones">
                <button type="submit" class="g_boton guardar">
                    Generar Invitación <i class="fa-solid fa-id-badge"></i>
                </button>
            </div>
        </div>
    </form>
</div>