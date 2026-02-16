<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="update, validar" message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Editar evidencia pago stock</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.evidencia-pago-antiguo.vista.todo') }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_8">
            <div class="g_panel g_margin_bottom_20">
                <h4 class="g_panel_titulo">Datos del Cliente (ERP)</h4>
                <div class="g_fila">
                    <div class="g_margin_bottom_10 g_columna_4">
                        <label>Cliente</label>
                        <input type="text" disabled value="{{ $evidencia->cliente->name ?? 'No vinculado' }}">
                    </div>
                    <div class="g_margin_bottom_10 g_columna_4">
                        <label>DNI</label>
                        <input type="text" disabled value="{{ $evidencia->cliente->perfilCliente->dni ?? '—' }}">
                    </div>
                    <div class="g_margin_bottom_10 g_columna_4">
                        <label>Email</label>
                        <input type="text" disabled value="{{ $evidencia->cliente->email ?? '—' }}">
                    </div>
                </div>
            </div>

            <div class="g_panel g_margin_bottom_20">
                <h4 class="g_panel_titulo">Datos del Excel (Stock)</h4>
                <div class="g_fila">
                    <div class="g_columna_4 g_margin_bottom_10">
                        <label>Razón social</label>
                        <input type="text" disabled value="{{ $evidencia->razon_social }}">
                    </div>
                    <div class="g_columna_4 g_margin_bottom_10">
                        <label>Proyecto</label>
                        <input type="text" disabled value="{{ $evidencia->proyecto_nombre }}">
                    </div>
                    <div class="g_columna_2 g_margin_bottom_10">
                        <label>Etapa</label>
                        <input type="text" disabled value="{{ $evidencia->etapa }}">
                    </div>
                    <div class="g_columna_2 g_margin_bottom_10">
                        <label>Lote</label>
                        <input type="text" disabled value="{{ $evidencia->lote }}">
                    </div>
                </div>

                <div class="g_fila">
                    <div class="g_columna_4 g_margin_bottom_10">
                        <label>Cliente (Texto)</label>
                        <input type="text" disabled value="{{ $evidencia->nombres_cliente }}">
                    </div>
                    <div class="g_columna_4 g_margin_bottom_10">
                        <label>DNI (Texto)</label>
                        <input type="text" disabled value="{{ $evidencia->dni_cliente }}">
                    </div>
                    <div class="g_columna_4 g_margin_bottom_10">
                        <label>Código Cliente</label>
                        <input type="text" disabled value="{{ $evidencia->codigo_cliente }}">
                    </div>
                </div>

                <div class="g_fila">
                    <div class="g_columna_3 g_margin_bottom_10">
                        <label>N° Operación</label>
                        <input type="text" disabled value="{{ $evidencia->operacion_numero }}">
                    </div>
                    <div class="g_columna_3 g_margin_bottom_10">
                        <label>Banco</label>
                        <input type="text" disabled value="{{ $evidencia->banco }}">
                    </div>
                    <div class="g_columna_3 g_margin_bottom_10">
                        <label>Monto</label>
                        <input type="text" disabled
                            value="{{ $evidencia->moneda }} {{ number_format($evidencia->monto, 2) }}">
                    </div>
                    <div class="g_columna_3 g_margin_bottom_10">
                        <label>Fecha Depósito</label>
                        <input type="text" disabled
                            value="{{ $evidencia->fecha_deposito ? $evidencia->fecha_deposito->format('d/m/Y') : '—' }}">
                    </div>
                </div>
            </div>

            <form wire:submit="update" class="formulario">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Gestión de Evidencia</h4>
                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label for="unidad_negocio_id">Razón Social ERP <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <select wire:model.live="unidad_negocio_id" id="unidad_negocio_id">
                                <option value="">Seleccione empresa</option>
                                @foreach ($empresas as $empresa)
                                    <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                                @endforeach
                            </select>
                            @error('unidad_negocio_id') <span class="mensaje_error">{{ $message }}</span> @enderror
                        </div>

                        <div class="g_columna_6 g_margin_bottom_10">
                            <label for="proyecto_id">Proyecto ERP <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <select wire:model.live="proyecto_id" id="proyecto_id">
                                <option value="">Seleccione proyecto</option>
                                @foreach ($proyectos as $proyecto)
                                    <option value="{{ $proyecto->id }}">{{ $proyecto->nombre }}</option>
                                @endforeach
                            </select>
                            @error('proyecto_id') <span class="mensaje_error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label for="gestor_id">Asignar a Gestor</label>
                            <select wire:model.live="gestor_id" id="gestor_id">
                                <option value="">Sin asignar</option>
                                @foreach ($gestores as $gestor)
                                    <option value="{{ $gestor->id }}">{{ $gestor->name }}</option>
                                @endforeach
                            </select>
                            @error('gestor_id') <span class="mensaje_error">{{ $message }}</span> @enderror
                        </div>

                        <div class="g_columna_6 g_margin_bottom_10">
                            <label for="estado_id">Estado <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <select wire:model.live="estado_id" id="estado_id">
                                <option value="">Seleccione estado</option>
                                @foreach ($estados as $estado)
                                    <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                                @endforeach
                            </select>
                            @error('estado_id') <span class="mensaje_error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label for="observacion">Observación</label>
                        <textarea wire:model.blur="observacion" id="observacion" rows="3"
                            class="@error('observacion') input-error @enderror"></textarea>
                        @error('observacion') <span class="mensaje_error">{{ $message }}</span> @enderror
                    </div>

                    <div class="formulario_botones">
                        @can('evidencia-pago-antiguo.editar')
                            <button type="submit" class="g_boton guardar">
                                <i class="fa-solid fa-save"></i> Actualizar
                            </button>
                        @endcan
                    </div>
                </div>
            </form>
        </div>

        <div class="g_columna_4">
            <div class="g_panel g_margin_bottom_20">
                <h4 class="g_panel_titulo">Comprobante</h4>
                <div class="g_centrar_elemento" style="min-height: 200px;">
                    @if ($evidencia->imagen_url)
                        <a href="{{ $evidencia->imagen_url }}" target="_blank">
                            <img src="{{ $evidencia->imagen_url }}" alt="Evidencia"
                                style="width: 100%; border-radius: 8px; box-shadow: var(--g-box-shadow);">
                        </a>
                        <div class="g_margin_top_10" style="display: flex; gap: 10px; justify-content: center;">
                            <a href="{{ $evidencia->imagen_url }}" target="_blank" class="g_boton light">Ver <i
                                    class="fa-solid fa-eye"></i></a>
                            <a href="{{ $evidencia->imagen_url }}" download class="g_boton primary">Bajar <i
                                    class="fa-solid fa-download"></i></a>
                        </div>
                    @else
                        <div class="g_vacio_min" style="padding: 20px;">
                            <i class="fa-solid fa-image-slash fa-3x" style="opacity: 0.3;"></i>
                            <p>Sin imagen adjunta</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="g_panel">
                <h4 class="g_panel_titulo">Validación</h4>
                <div class="g_margin_bottom_10">
                    <label>Fecha de Validación</label>
                    <input type="text" disabled
                        value="{{ $evidencia->fecha_validacion ? $evidencia->fecha_validacion->format('d/m/Y H:i') : 'Falta validar' }}">
                </div>

                @if (!$evidencia->fecha_validacion)
                    @can('solicitud-evidencia-pago.validar')
                        <button wire:click="validar" class="g_boton success" style="width: 100%;">
                            <i class="fa-solid fa-check-double"></i> Validar Ahora
                        </button>
                    @endcan
                @endif
            </div>
        </div>
    </div>
</div>