<div class="g_panel">
    @if (session()->has('success'))
        <div class="g_alerta success g_margin_bottom_10">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="g_alerta error g_margin_bottom_10">
            <i class="fa-solid fa-circle-exclamation"></i>
            {{ session('error') }}
        </div>
    @endif

    @if (session()->has('info'))
        <div class="g_alerta info g_margin_bottom_10">
            <i class="fa-solid fa-circle-info"></i>
            {{ session('info') }}
        </div>
    @endif

    <div class="g_panel_titulo">
        <h2>Dirección</h2>
    </div>

    <div class="formulario">
        <div class="g_fila">
            <div class="g_margin_bottom_10 g_columna_6">
                <label for="pais_id">País <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                <select wire:model.live="pais_id" id="pais_id" name="pais_id"
                    class="@error('pais_id') input-error @enderror">
                    <option value="">Selecciona</option>
                    @foreach ($paises as $pais)
                        <option value="{{ $pais->id }}">{{ $pais->nombre }}</option>
                    @endforeach
                </select>
                @error('pais_id')
                    <p class="mensaje_error">{{ $message }}</p>
                @enderror
            </div>

            <div class="g_margin_bottom_10 g_columna_6">
                <label for="region_id">Departamento @if ($pais_id == 1)
                    <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                @endif
                </label>
                <select wire:model.live="region_id" id="region_id" name="region_id"
                    class="@error('region_id') input-error @enderror" @if ($pais_id != 1) disabled @endif>
                    <option value="">Selecciona</option>
                    @foreach ($departamentos as $departamento)
                        <option value="{{ $departamento->id }}">{{ $departamento->nombre }}</option>
                    @endforeach
                </select>
                @error('region_id')
                    <p class="mensaje_error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="g_fila">
            <div class="g_margin_bottom_10 g_columna_6">
                <label for="provincia_id">Provincia @if ($pais_id == 1)
                    <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                @endif
                </label>
                <select wire:model.live="provincia_id" id="provincia_id" name="provincia_id"
                    class="@error('provincia_id') input-error @enderror" @if ($pais_id != 1 || $region_id == 27) disabled
                    @endif>
                    <option value="">Selecciona</option>
                    @foreach ($provincias as $provincia)
                        <option value="{{ $provincia->id }}">{{ $provincia->nombre }}</option>
                    @endforeach
                </select>
                @error('provincia_id')
                    <p class="mensaje_error">{{ $message }}</p>
                @enderror
            </div>

            <div class="g_margin_bottom_10 g_columna_6">
                <label for="distrito_id">Distrito @if ($pais_id == 1)
                    <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                @endif
                </label>
                <select wire:model.live="distrito_id" id="distrito_id" name="distrito_id"
                    class="@error('distrito_id') input-error @enderror" @if ($pais_id != 1 || $region_id == 27) disabled
                    @endif>
                    <option value="">Selecciona</option>
                    @foreach ($distritos as $distrito)
                        <option value="{{ $distrito->id }}">{{ $distrito->nombre }}</option>
                    @endforeach
                </select>
                @error('distrito_id')
                    <p class="mensaje_error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="g_fila">
            <div class="g_margin_bottom_10 g_columna_6">
                <label for="direccion">Avenida / Calle / Jirón <span class="obligatorio"><i
                            class="fa-solid fa-asterisk"></i></span></label>
                <input type="text" wire:model.blur="direccion" id="direccion" name="direccion"
                    class="@error('direccion') input-error @enderror" autocomplete="off">
                @error('direccion')
                    <p class="mensaje_error">{{ $message }}</p>
                @enderror
            </div>

            <div class="g_margin_bottom_10 g_columna_6">
                <label for="direccion_numero">Número <span class="obligatorio"><i
                            class="fa-solid fa-asterisk"></i></span></label>
                <input type="text" wire:model.blur="direccion_numero" id="direccion_numero" name="direccion_numero"
                    class="@error('direccion_numero') input-error @enderror" autocomplete="off">
                @error('direccion_numero')
                    <p class="mensaje_error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="g_fila">
            <div class="g_margin_bottom_10 g_columna_6">
                <label for="opcional">Dpto. / Interior / Piso / Lote</label>
                <input type="text" wire:model.blur="opcional" id="opcional" name="opcional"
                    class="@error('opcional') input-error @enderror" placeholder="Ejem: Casa 1 piso, lote 15."
                    autocomplete="off">
                @error('opcional')
                    <p class="mensaje_error">{{ $message }}</p>
                @enderror
            </div>

            <div class="g_margin_bottom_10 g_columna_6">
                <label for="codigo_postal">Código postal <span class="obligatorio"><i
                            class="fa-solid fa-asterisk"></i></span></label>
                <input type="text" wire:model.blur="codigo_postal" id="codigo_postal" name="codigo_postal"
                    class="@error('codigo_postal') input-error @enderror" autocomplete="off">
                @error('codigo_postal')
                    <p class="mensaje_error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="g_fila">
            <div class="g_margin_bottom_10 g_columna_12">
                <label for="referencia">Referencia de la ubicación</label>
                <textarea id="referencia" name="referencia" wire:model.blur="referencia" rows="3"
                    class="@error('referencia') input-error @enderror" placeholder="Referencia..."></textarea>
                @error('referencia')
                    <p class="mensaje_error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="formulario_botones">
            <button wire:click="saveDireccion" class="g_boton guardar" wire:loading.attr="disabled"
                wire:target="saveDireccion">
                <span wire:loading.remove wire:target="saveDireccion">
                    {{ $direccion_seleccionada ? 'Guardar Cambios' : 'Guardar Dirección' }}
                </span>
                <span wire:loading wire:target="saveDireccion">
                    <i class="fa-solid fa-spinner fa-spin"></i> Guardando...
                </span>
            </button>
        </div>
    </div>
</div>