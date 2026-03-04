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
            <p class="leyenda">
                @if ($link_carpeta_eecc)
                    <a href="{{ $link_carpeta_eecc }}" target="_blank" rel="noopener noreferrer">Abrir carpeta EECC</a>
                @else
                    <span>Sin enlace registrado</span>
                @endif
            </p>
        </div>
        <div class="g_margin_bottom_10 g_columna_6">
            <label>Enlace EECC Firmado</label>
            <input type="text" wire:model="link_eecc_firmado">
            <p class="leyenda">
                @if ($link_eecc_firmado)
                    <a href="{{ $link_eecc_firmado }}" target="_blank" rel="noopener noreferrer">Abrir EECC firmado</a>
                @else
                    <span>Sin enlace registrado</span>
                @endif
            </p>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_margin_bottom_10 g_columna_4">
            <label>Estado Administrativo <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
            <select wire:model="estado_backoffice">
                @foreach (\App\Models\ProspectoEntregaFest::ESTADO_BACKOFFICE as $valor => $info)
                    <option value="{{ $valor }}">{{ $info['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="g_margin_bottom_10 g_columna_4">
            <label>Validador</label>
            <select wire:model="validador_backoffice_id">
                <option value="">Sin asignar</option>
                @foreach ($usuarios as $u)
                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="g_margin_bottom_10 g_columna_4">
            <label>Fecha Validación</label>
            <input type="datetime-local" wire:model="fecha_validacion_eecc">
        </div>
    </div>

    <div class="g_tab_form_buttons">
        <button type="submit" class="g_boton guardar">
            <i class="fa-solid fa-save"></i> Guardar Avance BackOffice
        </button>
    </div>
</form>