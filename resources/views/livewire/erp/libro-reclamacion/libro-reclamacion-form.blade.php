<div class="g_contenedor_columna">
    @php $viewMode = isset($isView) && $isView; @endphp
    <!-- 1. Identificación del Proveedor -->
    <div class="g_panel">
        <div class="g_panel_titulo">
            <h2>Identificación del Proveedor</h2>
        </div>
        <div class="g_panel_body">
            <div class="g_fila">
                <div class="g_columna_6 g_margin_bottom_10">
                    <label>Proyecto</label>
                    @if($viewMode)
                    <input type="text" value="{{ $ticket->proyecto?->nombre ?: 'N/D' }}" disabled>
                    @else
                    <select wire:model.live="proyecto_id">
                        <option value="">-- Seleccionar --</option>
                        @foreach($proyectos as $p)
                        <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                        @endforeach
                    </select>
                    @endif
                </div>

                <div class="g_columna_6 g_margin_bottom_10">
                    <label>Código de ticket</label>
                    @if($viewMode)
                    <input type="text" value="{{ $ticket->codigo ?: 'TCK' }}" disabled>
                    @else
                    <input type="text" value="{{ $codigo ?: 'TCK' }}" disabled>
                    @endif
                </div>

                <div class="g_columna_3 g_margin_bottom_10">
                    <label>Manzana</label>
                    @if($viewMode)
                    <input type="text" value="{{ $ticket->manzana ?: 'N/D' }}" disabled>
                    @else
                    <input type="text" wire:model.blur="manzana">
                    @endif
                </div>

                <div class="g_columna_3 g_margin_bottom_10">
                    <label>Lote</label>
                    @if($viewMode)
                    <input type="text" value="{{ $ticket->lote ?: 'N/D' }}" disabled>
                    @else
                    <input type="text" wire:model.blur="lote">
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Información del consumidor reclamante -->
    <div class="g_panel">
        <div class="g_panel_titulo">
            <h2>Información del Consumidor Reclamante</h2>
        </div>
        <div class="g_panel_body">
            <div class="g_fila">
                <div class="g_columna_4 g_margin_bottom_10">
                    <label>Nombres</label>
                    @if($viewMode)
                    <input type="text" value="{{ $ticket->cliente_nombre ?: 'N/D' }}" disabled>
                    @else
                    <input type="text" wire:model.blur="nombre">
                    @endif
                </div>

                <div class="g_columna_4 g_margin_bottom_10">
                    <label>Apellido paterno</label>
                    @if($viewMode)
                    <input type="text" value="{{ data_get($ticket, 'cliente_apellido_paterno') ?: 'N/D' }}" disabled>
                    @else
                    <input type="text" wire:model.blur="apellido_paterno">
                    @endif
                </div>

                <div class="g_columna_4 g_margin_bottom_10">
                    <label>Apellido materno</label>
                    @if($viewMode)
                    <input type="text" value="{{ data_get($ticket, 'cliente_apellido_materno') ?: 'N/D' }}" disabled>
                    @else
                    <input type="text" wire:model.blur="apellido_materno">
                    @endif
                </div>
            </div>

            <div class="g_fila">
                <div class="g_columna_6 g_margin_bottom_10">
                    <label>Domicilio</label>
                    @if($viewMode)
                    <input type="text" value="{{ $ticket->cliente_direccion ?: 'N/D' }}" disabled>
                    @else
                    <input type="text" wire:model.blur="domicilio">
                    @endif
                </div>

                <div class="g_columna_6 g_margin_bottom_10">
                    <label>Teléfono</label>
                    @if($viewMode)
                    <input type="text" value="{{ $ticket->cliente_celular ?: 'N/D' }}" disabled>
                    @else
                    <input type="text" wire:model.blur="telefono">
                    @endif
                </div>
            </div>

            <div class="g_fila">
                <div class="g_columna_6 g_margin_bottom_10">
                    <label>Correo electrónico</label>
                    @if($viewMode)
                    <input type="text" value="{{ $ticket->cliente_email ?: 'N/D' }}" disabled>
                    @else
                    <input type="text" wire:model.blur="email">
                    @endif
                </div>

                <div class="g_columna_3 g_margin_bottom_10">
                    <label>Tipo de documento</label>
                    @if($viewMode)
                    <input type="text" value="{{ strtoupper($ticket->cliente_tipo_documento ?? 'N/D') }}" disabled>
                    @else
                    <select wire:model.live="tipo_documento">
                        <option value="">- Seleccione -</option>
                        <option value="dni">DNI</option>
                        <option value="ruc">RUC</option>
                        <option value="ce">CE</option>
                    </select>
                    @endif
                </div>

                <div class="g_columna_3 g_margin_bottom_10">
                    <label>Número Documento</label>
                    @if($viewMode)
                    <input type="text" value="{{ $ticket->cliente_documento ?: 'N/D' }}" disabled>
                    @else
                    <input type="text" wire:model.blur="numero_documento">
                    @endif
                </div>
            </div>

            <div class="g_margin_top_10">
                <label style="cursor: pointer; display:flex; align-items:center; gap:8px;">
                    @if($viewMode)
                    <input type="checkbox" {{ $ticket->es_cliente_menor ? 'checked' : '' }} disabled>
                    @else
                    <input type="checkbox" wire:model.live="es_cliente_menor">
                    @endif
                    <strong>¿Es el reclamante menor de edad?</strong>
                </label>
            </div>

            @if ($viewMode ? $ticket->es_cliente_menor : $es_cliente_menor)
            <div class="g_margin_top_10 g_alerta warning">
                <i class="fa-solid fa-exclamation-triangle"></i>
                <strong>Datos del Padre, Madre o Representante Legal</strong>
            </div>

            <div class="g_fila g_margin_top_10">
                <div class="g_columna_4 g_margin_bottom_10">
                    <label>Nombre del representante legal <span style="color:#d32f2f;">*</span></label>
                    @if($viewMode)
                    <input type="text" value="{{ $ticket->representante_legal_nombre ?: 'N/D' }}" disabled>
                    @else
                    <input type="text" wire:model.blur="representante_legal_nombre">
                    @endif
                    @error('representante_legal_nombre') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_columna_4 g_margin_bottom_10">
                    <label>Apellido paterno del representante legal <span style="color:#d32f2f;">*</span></label>
                    @if($viewMode)
                    <input type="text" value="{{ $ticket->representante_legal_apellido_paterno ?: 'N/D' }}" disabled>
                    @else
                    <input type="text" wire:model.blur="representante_legal_apellido_paterno">
                    @endif
                    @error('representante_legal_apellido_paterno') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_columna_4 g_margin_bottom_10">
                    <label>Apellido materno del representante legal <span style="color:#d32f2f;">*</span></label>
                    @if($viewMode)
                    <input type="text" value="{{ $ticket->representante_legal_apellido_materno ?: 'N/D' }}" disabled>
                    @else
                    <input type="text" wire:model.blur="representante_legal_apellido_materno">
                    @endif
                    @error('representante_legal_apellido_materno') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- 3. Identificación del bien contratado -->
    <div class="g_panel">
        <div class="g_panel_titulo">
            <h2>Identificación del Bien Contratado</h2>
        </div>
        <div class="g_panel_body">
            <div class="g_fila">
                <div class="g_columna_6 g_margin_bottom_10">
                    <label>Tipo de bien contratado</label>
                    @if($viewMode)
                    <input type="text" value="{{ strtoupper((string)$ticket->tipo_bien_contratado ?: 'N/D') }}"
                        disabled>
                    @else
                    <div style="display:flex; gap:1rem;">
                        <label><input type="radio" wire:model.live="tipo_bien_contratado" value="PRODUCTO">
                            Producto</label>
                        <label><input type="radio" wire:model.live="tipo_bien_contratado" value="SERVICIO">
                            Servicio</label>
                    </div>
                    @endif
                </div>

                <div class="g_columna_6 g_margin_bottom_10">
                    <label>Monto Reclamado</label>
                    @if($viewMode)
                    <input type="text" value="{{ $ticket->monto_reclamado ?? 'N/D' }}" disabled>
                    @else
                    <input type="text" wire:model.blur="monto_reclamado">
                    @endif
                </div>
            </div>

            <div class="g_fila">
                <div class="g_columna_12 g_margin_bottom_10">
                    <label>Descripción del producto o servicio</label>
                    @if($viewMode)
                    <textarea disabled>{{ $ticket->descripcion ?: 'N/D' }}</textarea>
                    @else
                    <textarea wire:model.blur="descripcion"></textarea>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Detalle de la reclamación -->
    <div class="g_panel">
        <div class="g_panel_titulo">
            <h2>Detalle de la Reclamación</h2>
        </div>
        <div class="g_panel_body">
            <div class="g_fila">
                <div class="g_columna_12 g_margin_bottom_10">
                    <label>Tipo de solicitud</label>
                    @if($viewMode)
                    <input type="text" value="{{ strtoupper((string)$ticket->tipo_pedido ?: 'N/D') }}" disabled>
                    @else
                    <div class="g_fila">
                        <label style="margin-right:1rem;"><input type="radio" wire:model.live="tipo_pedido"
                                value="RECLAMO"> RECLAMO</label>
                        <label><input type="radio" wire:model.live="tipo_pedido" value="QUEJA"> QUEJA</label>
                    </div>
                    @endif
                </div>
            </div>

            <div class="g_fila">
                <div class="g_columna_12 g_margin_bottom_10">
                    <label>Detalle del reclamo o queja</label>
                    @if($viewMode)
                    <textarea disabled>{{ $ticket->detalle ?: 'N/D' }}</textarea>
                    @else
                    <textarea wire:model.blur="detalle"></textarea>
                    @endif
                </div>
            </div>

            <div class="g_fila">
                <div class="g_columna_12 g_margin_bottom_10">
                    <label>Pedido del consumidor</label>
                    @if($viewMode)
                    <textarea disabled>{{ $ticket->pedido ?: 'N/D' }}</textarea>
                    @else
                    <textarea wire:model.blur="pedido"></textarea>
                    @endif
                </div>
            </div>

            @if(!$viewMode)
            <div class="g_fila">
                <div class="g_columna_12" style="text-align:right; margin-top:10px;">
                    <button wire:click.prevent="store" class="g_boton_primary">Guardar reclamo</button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>