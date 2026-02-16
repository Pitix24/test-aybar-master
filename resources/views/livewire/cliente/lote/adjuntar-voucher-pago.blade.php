<div class="informacion_contenedor">
    @if (session('success'))
        <div class="g_alerta success g_margin_bottom_10">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="g_alerta error g_margin_bottom_10">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="g_resaltado_caja info">
        <span class="g_resaltado_caja_titulo">Detalle de la cuota</span>
        <div class="informacion_resumen_grid">
            <div class="informacion_resumen_item">
                <span class="informacion_resumen_label">Proyecto</span>
                <span class="informacion_resumen_valor">{{ $this->lote['descripcion'] }}</span>
            </div>
            <div class="informacion_resumen_item">
                <span class="informacion_resumen_label">Ubicación</span>
                <span class="informacion_resumen_valor">Mz. {{ $this->lote['id_manzana'] }} - Lt.
                    {{ $this->lote['id_lote'] }}</span>
            </div>
            <div class="informacion_resumen_item">
                <span class="informacion_resumen_label">N° Cuota</span>
                <span class="informacion_resumen_valor">{{ $this->cuota['NroCuota'] }}</span>
            </div>
            <div class="informacion_resumen_item">
                <span class="informacion_resumen_label">Vencimiento</span>
                <span class="informacion_resumen_valor">{{ $this->cuota['FecVencimiento'] }}</span>
            </div>
        </div>
    </div>

    @if (!session('success'))
        @if (!$imagen)
            <div class="g_margin_bottom_10">
                <label class="contenedor_dropzone" for="voucher_input">
                    <i class="fa-solid fa-cloud-arrow-up fa-3x"></i>
                    <p>Haz clic para subir la imagen de tu voucher</p>
                    <input type="file" id="voucher_input" wire:model="imagen" accept="image/*" style="display: none;">
                </label>
                @error('imagen')
                    <p class="mensaje_error">{{ $message }}</p>
                @enderror
            </div>
        @else
                <div class="g_fila">
                    <div class="g_columna_6 g_margin_bottom_10">
                        <div class="g_evidencia_visor_panel">
                            <div class="g_evidencia_previa">
                                <button title="Eliminar imagen" class="g_imagen_remover" wire:click="eliminarImagen">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                                <img src="{{ $imagen->temporaryUrl() }}" alt="Previsualización">
                            </div>
                        </div>
                    </div>

                    <div class="g_columna_6">
                        <div class="g_panel_titulo g_margin_bottom_10">
                            <h4>Confirmar datos extraídos</h4>
                        </div>

                        <div class="formulario">
                            <div class="g_margin_bottom_10">
                                <label>N° Operación</label>
                                <input type="text" disabled value="{{ $datos['numero'] ?? 'Esperando validación...' }}">
                                @error('datos.numero')
                                    <span class="mensaje_error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="g_margin_bottom_10">
                                <label>Banco</label>
                                <input type="text" disabled value="{{ $datos['banco'] ?? 'Esperando validación...' }}">
                            </div>

                            <div class="g_margin_bottom_10">
                                <label>Monto detectado</label>
                                <input type="text" disabled value="{{ $datos['monto'] ?? 'Esperando validación...' }}">
                            </div>

                            <div class="g_margin_bottom_10">
                                <label>Fecha detectada</label>
                                <input type="text" disabled value="{{ $datos['fecha'] ?? 'Esperando validación...' }}">
                            </div>

                            @if ($datos)
                                <div class="g_margin_bottom_10">
                                    <label>Razón Social <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                                    <select wire:model.live="unidad_negocio_id"
                                        class="@error('unidad_negocio_id') input-error @enderror">
                                        <option value="">Seleccione empresa...</option>
                                        @foreach ($empresas as $empresa)
                                            <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('unidad_negocio_id')
                                        <span class="mensaje_error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="g_margin_bottom_10">
                                    <label>Proyecto <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                                    <select wire:model.live="proyecto_id" class="@error('proyecto_id') input-error @enderror">
                                        <option value="">Seleccione proyecto...</option>
                                        @foreach ($proyectos as $proyecto)
                                            <option value="{{ $proyecto->id }}">{{ $proyecto->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('proyecto_id')
                                        <span class="mensaje_error">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>

                        <div class="formulario_botones g_margin_top_10">
                            @if (!$datos)
                                <button wire:click="procesarImagen" class="g_boton info" wire:loading.attr="disabled"
                                    wire:target="procesarImagen">
                                    <span wire:loading.remove wire:target="procesarImagen">
                                        <i class="fa-solid fa-magnifying-glass-chart"></i> VALIDAR COMPROBANTE
                                    </span>
                                    <span wire:loading wire:target="procesarImagen">
                                        <i class="fa-solid fa-spinner fa-spin"></i> PROCESANDO...
                                    </span>
                                </button>
                            @else
                                <button wire:click="guardar" class="g_boton guardar" wire:loading.attr="disabled" wire:target="guardar">
                                    <span wire:loading.remove wire:target="guardar">
                                        <i class="fa-solid fa-save"></i> SUBIR EVIDENCIA
                                    </span>
                                    <span wire:loading wire:target="guardar">
                                        <i class="fa-solid fa-spinner fa-spin"></i> SUBIENDO...
                                    </span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>