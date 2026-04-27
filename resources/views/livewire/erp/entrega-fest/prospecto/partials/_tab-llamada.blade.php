<div class="g_gap_pagina">
    <form wire:submit.prevent="updateLlamada" class="formulario">
        <div class="g_fila">
            <div class="g_margin_bottom_10 g_columna_6">
                <label>Responsable de Llamada</label>
                <select wire:model="responsable_llamada_id">
                    <option value="">Sin asignar</option>
                    @foreach ($usuariosLlamada as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
                @if ($responsable_llamada_fecha_asignacion)
                    <p class="leyenda" style="margin-top: 5px;">
                        <i class="fa-solid fa-clock"></i> Asignado el:
                        {{ date('d/m/Y H:i', strtotime($responsable_llamada_fecha_asignacion)) }}
                    </p>
                @endif
            </div>
            <div class="g_margin_bottom_10 g_columna_6">
                <label>Fecha de Asignación</label>
                <input type="datetime-local" wire:model="responsable_llamada_fecha_asignacion">
            </div>
        </div>

        <div class="g_tab_form_buttons">
            <button type="submit" class="g_boton guardar">
                <i class="fa-solid fa-save"></i> Guardar Información de Llamada
            </button>
        </div>
    </form>
</div>
