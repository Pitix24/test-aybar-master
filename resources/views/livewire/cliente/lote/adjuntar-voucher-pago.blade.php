<div class="contenedor_procesar_imagen">

    <div class="g_panel_parrafo">
        <p>{{ $this->lote['razon_social'] }}/{{ $this->lote['descripcion'] }}</p>
        <p>Mz. {{ $this->lote['id_manzana'] }}, Lt. {{ $this->lote['id_lote'] }}</p>
        <p>N° Cuota. {{ $this->cuota["NroCuota"] }}</p>
        <p>Fech. Venc. {{ $this->cuota["FecVencimiento"] }}</p>
    </div>

    @if (session('success'))
        <div class="g_alerta_succes">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @else
        @if (session()->has('error'))
            <div class="g_alerta_error">
                <i class="fa-solid fa-triangle-exclamation"></i>
                {{ session('error') }}
            </div>
        @endif

        <label class="dropzone">
            <p>Haz clic para que subas la imagen de tu evidencia</p>
            <input type="file" wire:model="imagen" accept="image/*">
        </label>

        @error('imagen')
            <div class="g_alerta_error">
                <i class="fa-solid fa-triangle-exclamation"></i>
                {{ $message }}
            </div>
        @enderror

        @if ($imagen)
            <div class="resultados_grid">
                <div class="preview">
                    <button class="g_boton_cerrar" wire:click="eliminarImagen"><i class="fa-solid fa-xmark"></i></button>
                    <img src="{{ $imagen->temporaryUrl() }}">
                </div>

                <div class="g_gap_pagina">
                    <div class="g_panel_titulo">
                        <h2>Datos detectados</h2>
                    </div>

                    <div class="formulario">
                        <div class="g_fila">
                            <div class="g_margin_bottom_20 g_columna_12">
                                <label>N° Operación</label>
                                <input type="text" disabled value="{{ $datos['numero'] ?? 'No se detecta' }}">
                                @error('datos.numero')
                                    <span class="mensaje_error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_20 g_columna_12">
                                <label>Banco</label>
                                <input type="text" disabled value="{{ $datos['banco'] ?? 'No se detecta' }}">
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_20 g_columna_12">
                                <label>Monto</label>
                                <input type="text" disabled value="{{ $datos['monto'] ?? 'No se detecta' }}">
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_20 g_columna_12">
                                <label>Fecha</label>
                                <input type="text" disabled value="{{ $datos['fecha'] ?? 'No se detecta' }}">
                            </div>
                        </div>

                        @if ($datos)
                            <div class="g_fila">
                                <div class="g_margin_bottom_20 g_columna_12">
                                    <label for="unidad_negocio_id">Razón Social <span class="obligatorio"><i
                                                class="fa-solid fa-asterisk"></i></span></label>
                                    <select wire:model.live="unidad_negocio_id" id="unidad_negocio_id" name="unidad_negocio_id">
                                        <option value="" disabled>Selecciona</option>
                                        @foreach ($empresas as $empresa)
                                            <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('unidad_negocio_id')
                                        <span class="mensaje_error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="g_fila">
                                <div class="g_margin_bottom_20 g_columna_12">
                                    <label for="proyecto_id">Proyecto <span class="obligatorio"><i
                                                class="fa-solid fa-asterisk"></i></span></label>
                                    <select wire:model.live="proyecto_id" id="proyecto_id" name="proyecto_id">
                                        <option value="" disabled>Selecciona</option>
                                        @foreach ($proyectos as $proyecto)
                                            <option value="{{ $proyecto->id }}">{{ $proyecto->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('proyecto_id')
                                        <span class="mensaje_error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        @endif

                        <div class="formulario_botones">
                            @if (!$datos)
                                <button wire:click="procesarImagen" class="g_boton_personalizado verde" wire:loading.attr="disabled"
                                    wire:target="procesarImagen">
                                    <span wire:loading.remove wire:target="procesarImagen">Validar evidencia</span>
                                    <span wire:loading wire:target="procesarImagen">Validando...</span>
                                </button>
                            @else
                                <button wire:click="guardar" class="g_boton_personalizado verde">
                                    Guardar envidencia
                                </button>
                            @endif
                        </div>
                    </div>

                </div>

            </div>
        @endif

    @endif
</div>