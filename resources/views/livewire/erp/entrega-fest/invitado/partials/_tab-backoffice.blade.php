<div class="g_gap_pagina">
    <form wire:submit.prevent="updateBackoffice" class="formulario">
        <div class="g_fila">
            <div class="g_margin_bottom_10 g_columna_4">
                <label>Grupo Asignado <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                <select wire:model="grupo">
                    <option value="A">Grupo A</option>
                    <option value="B">Grupo B</option>
                    <option value="C">Grupo C</option>
                    <option value="D">Grupo D</option>
                </select>
            </div>
            <div class="g_margin_bottom_10 g_columna_4">
                <label>Gestor de Cuenta</label>
                <select wire:model="gestor_backoffice_id">
                    <option value="">Sin asignar</option>
                    @foreach ($usuarios as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="g_margin_bottom_10 g_columna_4">
                <label>Culminación EECC</label>
                <input type="datetime-local" wire:model="fecha_culminacion_eecc">
            </div>
        </div>

        <div class="g_fila">
            <div class="g_margin_bottom_10 g_columna_6">
                <label>Enlace Carpeta EECC</label>
                <input type="text" wire:model="link_carpeta_eecc">
                @if ($link_carpeta_eecc)
                    <a href="{{ $link_carpeta_eecc }}" target="_blank" rel="noopener noreferrer" class="g_boton info">
                        <i class="fa-solid fa-folder-open"></i> Abrir carpeta EECC
                    </a>
                @else
                    <span class="g_badge light">Sin enlace registrado</span>
                @endif
            </div>
            <div class="g_margin_bottom_10 g_columna_6">
                <label>Enlace EECC Firmado</label>
                <input type="text" wire:model="link_eecc_firmado">
                @if ($link_eecc_firmado)
                    <a href="{{ $link_eecc_firmado }}" target="_blank" rel="noopener noreferrer" class="g_boton info">
                        <i class="fa-solid fa-file-pdf"></i> Abrir EECC firmado
                    </a>
                @else
                    <span class="g_badge light">Sin enlace registrado</span>
                @endif
            </div>
        </div>

        <div class="g_tab_form_buttons">
            <button type="submit" class="g_boton guardar">
                <i class="fa-solid fa-save"></i> Guardar Avance BackOffice
            </button>
        </div>
    </form>

    <form wire:submit.prevent="updateBackofficeSupervisor" class="formulario">
        <div class="g_fila">
            <div class="g_margin_bottom_10 g_columna_4">
                <label>Estado Administrativo <span class="obligatorio"><i
                            class="fa-solid fa-asterisk"></i></span></label>
                <select wire:model="estado_backoffice">
                    @foreach (\App\Models\ProspectoEntregaFest::ESTADO_BACKOFFICE as $valor => $info)
                        <option value="{{ $valor }}">{{ $info['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="g_margin_bottom_10 g_columna_4">
                <label>Validador</label>
                <input type="text" value="{{ $prospecto->validador?->name ?? auth()->user()->name }}" disabled readonly>
            </div>
            <div class="g_margin_bottom_10 g_columna_4">
                <label>Fecha Validación</label>
                <input type="datetime-local" wire:model="fecha_validacion_eecc" disabled readonly>
                @if (!$fecha_validacion_eecc)
                    <p class="leyenda" style="margin-top: 5px;">
                        <i class="fa-solid fa-clock"></i> Se usará la fecha/hora actual al validar.
                    </p>
                @endif
            </div>
        </div>

        <div class="g_tab_form_buttons">
            <button type="submit" class="g_boton guardar">
                <i class="fa-solid fa-save"></i> Validar
            </button>
        </div>
    </form>
</div>