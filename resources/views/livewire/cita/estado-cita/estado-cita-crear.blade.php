@section('tituloPagina', 'Crear Estado de Cita')

<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Nuevo Estado de Cita</h2>
        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.estado-cita.vista.todo') }}" class="g_boton g_boton_dark">
                <i class="fa-solid fa-arrow-left"></i> Regresar</a>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_6">
            <div class="g_panel">
                <form wire:submit="store" class="formulario">
                    <div class="g_margin_bottom_20">
                        <label for="nombre">Nombre del Estado <span class="g_requerido">*</span></label>
                        <input type="text" id="nombre" wire:model="nombre" placeholder="Ej: Confirmada, Cancelada..."
                            class="@error('nombre') input-error @enderror">
                        @error('nombre') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_20">
                            <label for="color">Color (Hex)</label>
                            <div style="display: flex; gap: 10px;">
                                <input type="color" id="color_picker" wire:model.live="color"
                                    style="width: 50px; height: 45px; padding: 2px;">
                                <input type="text" wire:model="color" placeholder="#000000" style="flex: 1;">
                            </div>
                            @error('color') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>
                        <div class="g_columna_6 g_margin_bottom_20">
                            <label for="icono">Icono (FontAwesome)</label>
                            <input type="text" id="icono" wire:model="icono" placeholder="fa-solid fa-circle">
                            <p style="font-size: 0.75rem; color: #64748b; margin-top: 5px;">
                                Vista previa: <i class="{{ $icono }}" style="color: {{ $color }};"></i>
                            </p>
                            @error('icono') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="g_margin_bottom_20">
                        <label class="g_checkbox_contenedor">
                            <input type="checkbox" wire:model="activo">
                            <span class="g_checkbox_label">Estado Activo</span>
                        </label>
                    </div>

                    <div class="formulario_botones">
                        <button type="submit" class="g_boton g_boton_primary">
                            <i class="fa-solid fa-save"></i> Guardar Estado
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="g_columna_6">
            <div class="g_panel">
                <h4 class="g_panel_titulo">Vista Previa</h4>
                <div style="padding: 30px; border: 1px dashed #e2e8f0; border-radius: 8px; text-align: center;">
                    <div
                        style="display: inline-flex; align-items: center; gap: 12px; padding: 10px 20px; background: white; border-radius: 50px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border: 1px solid #f1f5f9;">
                        <i class="{{ $icono }}" style="color: {{ $color }}; font-size: 1.5rem;"></i>
                        <span
                            style="font-weight: 700; color: #334155; font-size: 1.1rem;">{{ $nombre ?: 'Nombre del Estado' }}</span>
                    </div>
                    <p style="margin-top: 20px; color: #94a3b8; font-size: 0.85rem;">Así se verá el estado en las listas
                        y selectores.</p>
                </div>
            </div>
        </div>
    </div>
</div>