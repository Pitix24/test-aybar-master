<div class="g_panel">
    @if (session()->has('success'))
        <div class="g_alerta_succes">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="g_panel_titulo">
        <h2>Dirección</h2>
    </div>

    <div class="formulario">
        <div class="g_fila">
            <div class="g_margin_top_20 g_columna_4">
                <label for="region_id">Departamento <span class="obligatorio"><i
                            class="fa-solid fa-asterisk"></i></span></label>
                <select wire:model.live="region_id" id="region_id" name="region_id">
                    <option value="">Selecciona</option>
                    @foreach ($departamentos as $departamento)
                        <option value="{{ $departamento->id }}">{{ $departamento->nombre }}</option>
                    @endforeach
                </select>
                @error('region_id')
                    <span class="mensaje_error">{{ $message }}</span>
                @enderror
            </div>

            <div class="g_margin_top_20 g_columna_4">
                <label for="provincia_id">Provincia <span class="obligatorio"><i
                            class="fa-solid fa-asterisk"></i></span></label>
                <select wire:model.live="provincia_id" id="provincia_id" name="provincia_id">
                    <option value="">Selecciona</option>
                    @foreach ($provincias as $provincia)
                        <option value="{{ $provincia->id }}">{{ $provincia->nombre }}</option>
                    @endforeach
                </select>
                @error('provincia_id')
                    <span class="mensaje_error">{{ $message }}</span>
                @enderror
            </div>

            <div class="g_margin_top_20 g_columna_4">
                <label for="distrito_id">Distrito <span class="obligatorio"><i
                            class="fa-solid fa-asterisk"></i></span></label>
                <select wire:model.live="distrito_id" id="distrito_id" name="distrito_id">
                    <option value="">Selecciona</option>
                    @foreach ($distritos as $distrito)
                        <option value="{{ $distrito->id }}">{{ $distrito->nombre }}</option>
                    @endforeach
                </select>
                @error('distrito_id')
                    <span class="mensaje_error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="g_fila">
            <div class="g_margin_top_20 g_columna_6">
                <label for="direccion">Avenida / Calle / Jirón <span class="obligatorio"><i
                            class="fa-solid fa-asterisk"></i></span></label>
                <input type="text" wire:model.live="direccion" id="direccion" name="direccion">
                @error('direccion')
                    <span class="mensaje_error">{{ $message }}</span>
                @enderror
            </div>

            <div class="g_margin_top_20 g_columna_6">
                <label for="direccion_numero">Número <span class="obligatorio"><i
                            class="fa-solid fa-asterisk"></i></span></label>
                <input type="text" wire:model.live="direccion_numero" id="direccion_numero" name="direccion_numero">
                @error('direccion_numero')
                    <span class="mensaje_error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="g_fila">
            <div class="g_margin_top_20 g_columna_6">
                <label for="opcional">Dpto. / Interior / Piso / Lote</label>
                <input type="text" wire:model.live="opcional" id="opcional" name="opcional"
                    placeholder="Ejem: Casa 1 piso, lote 15.">
                @error('opcional')
                    <span class="mensaje_error">{{ $message }}</span>
                @enderror
            </div>

            <div class="g_margin_top_20 g_columna_6">
                <label for="codigo_postal">Código postal <span class="obligatorio"><i
                            class="fa-solid fa-asterisk"></i></span></label>
                <input type="text" wire:model.live="codigo_postal" id="codigo_postal" name="codigo_postal">
                @error('codigo_postal')
                    <span class="mensaje_error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="g_fila">
            <div class="g_margin_top_20 g_columna_12">
                <label for="instrucciones">Referencia de la ubicación</label>
                <textarea id="instrucciones" name="instrucciones" wire:model.live="instrucciones" rows="3"
                    placeholder="Referencia..."></textarea>
                @error('instrucciones')
                    <span class="mensaje_error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="g_margin_top_20 formulario_botones">
            <button wire:click="saveDireccion" class="guardar">
                {{ $direccion_seleccionada ? 'Guardar Cambios' : 'Guardar Dirección' }}
            </button>
        </div>
    </div>
</div>