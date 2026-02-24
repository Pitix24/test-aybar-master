<div class="g_gap_pagina">
    <x-loading-overlay wire:loading message="Actualizando invitación..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Editar Invitado: <span
                style="color: var(--color-primary);">{{ $invitado->prospecto->nombre_completo }}</span></h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.vista.invitados', $evento->id) }}" class="g_boton dark">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </a>
        </div>
    </div>

    <form wire:submit.prevent="update">
        <div class="g_panel">
            <h4 class="g_panel_titulo"><i class="fa-solid fa-id-card-clip"></i> Gestión de Invitación</h4>

            <div class="g_fila">
                <div class="g_margin_bottom_15 g_columna_4">
                    <label>Código de Invitado</label>
                    <input type="text" value="{{ $invitado->codigo_invitado }}" disabled class="g_input_disabled">
                </div>

                <div class="g_margin_bottom_15 g_columna_4">
                    <label>Acompañantes Permitidos <span class="obligatorio">*</span></label>
                    <input type="number" wire:model="cantidad_acompanantes_permitidos">
                    @error('cantidad_acompanantes_permitidos') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_margin_bottom_15 g_columna_4">
                    <label for="confirmado">¿Confirmado por el cliente?</label>
                    <div class="g_switch-wrapper">
                        <label class="g_switch">
                            <input id="confirmado" type="checkbox" wire:model.live="confirmado">
                            <span class="g_switch-slider"></span>
                        </label>
                        <span class="g_switch-label">{{ $confirmado ? 'SÍ' : 'NO' }}</span>
                    </div>
                </div>
            </div>

            <div class="formulario_botones">
                <button type="submit" class="g_boton guardar">
                    <i class="fa-solid fa-save"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </form>
</div>