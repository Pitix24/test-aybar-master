<div class="g_centrar_pagina">
    <x-loading-overlay wire:loading wire:target="enviar" message="Registrando su reclamo..." />

    <div class="g_pading_pagina g_gap_pagina">
        <div class="g_contenedor_columna">
            @if ($success)
                <div class="g_alerta success">
                    <i class="fa-solid fa-circle-check"></i>
                    <div>{{ session('success') }}</div>
                </div>

                @if ($reclamo_registrado)
                    <div class="g_panel" style="margin-top: 20px;">
                        <div class="g_panel_titulo">
                            <h2 style="color: var(--color-light-exito);"><i class="fa-solid fa-file-circle-check"></i> Detalles
                                de su registro</h2>
                        </div>

                        <div class="informacion_resumen_grid">
                            <div class="informacion_resumen_item">
                                <span class="informacion_resumen_label">ID interno</span>
                                <span class="informacion_resumen_valor">{{ $reclamo_registrado->ticket }}</span>
                            </div>
                            <div class="informacion_resumen_item">
                                <span class="informacion_resumen_label">Ticket N°</span>
                                <span class="informacion_resumen_valor">{{ $reclamo_registrado->codigo_ticket }}</span>
                            </div>
                            <div class="informacion_resumen_item">
                                <span class="informacion_resumen_label">Fecha de envío</span>
                                <span
                                    class="informacion_resumen_valor">{{ $reclamo_registrado->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="informacion_resumen_item">
                                <span class="informacion_resumen_label">Tipo de solicitud</span>
                                <span
                                    class="informacion_resumen_valor">{{ $reclamo_registrado->tipo_pedido ? ucfirst($reclamo_registrado->tipo_pedido) : 'N/D' }}</span>
                            </div>
                            <div class="informacion_resumen_item" style="grid-column: span 2;">
                                <span class="informacion_resumen_label">Nombre completo</span>
                                <span class="informacion_resumen_valor">{{ trim(($reclamo_registrado->nombre ?? '') . ' ' . ($reclamo_registrado->apellido_paterno ?? '') . ' ' . ($reclamo_registrado->apellido_materno ?? '')) !== '' ? trim(($reclamo_registrado->nombre ?? '') . ' ' . ($reclamo_registrado->apellido_paterno ?? '') . ' ' . ($reclamo_registrado->apellido_materno ?? '')) : 'N/D' }}</span>
                            </div>
                            <div class="informacion_resumen_item">
                                <span class="informacion_resumen_label">Manzana</span>
                                <span class="informacion_resumen_valor">{{ $reclamo_registrado->manzana ?: 'N/D' }}</span>
                            </div>
                            <div class="informacion_resumen_item">
                                <span class="informacion_resumen_label">Lote</span>
                                <span class="informacion_resumen_valor">{{ $reclamo_registrado->lote ?: 'N/D' }}</span>
                            </div>
                        </div>

                        <hr style="opacity: 0.1; margin: 15px 0;">

                        <div class="informacion_resumen_item">
                            <span class="informacion_resumen_label">Descripción</span>
                            <span class="informacion_resumen_valor"
                                style="font-weight: normal; font-style: italic;">{{ $reclamo_registrado->descripcion ?: 'N/D' }}</span>
                        </div>

                        <div class="g_margin_top_20 g_resaltado_caja info">
                            <span class="g_resaltado_caja_titulo">Importante</span>
                            <div class="g_margin_top_10" style="font-size: 0.9em; opacity: 0.95; line-height: 1.6;">
                                <p><i class="fa-solid fa-calendar-check"></i> <strong>Plazo de respuesta:</strong> Nuestro equipo dará respuesta a su reclamo en un plazo no mayor a <strong>15 días hábiles</strong>.</p>
                            </div>
                        </div>

                        <div class="g_margin_top_20">
                            <div class="formulario_botones">
                                <button onclick="window.print()" class="g_boton dark">
                                    <i class="fa-solid fa-print"></i> Imprimir Constancia
                                </button>
                                <a href="{{ route('home') }}" class="g_boton light">
                                    <i class="fa-solid fa-house"></i> Ir al inicio
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="g_panel cabecera_titulo_pagina"
                    style="background: linear-gradient(135deg, #02424E 0%, #036b7e 100%); color: white; border: none;">
                    <div style="padding: 10px;">
                        <h1 style="color: white; margin-bottom: 5px;">LIBRO DE RECLAMACIONES</h1>
                        <p style="opacity: 0.8; font-size: 0.9em;">Conforme a lo establecido en el Código de Protección y
                            Defensa del Consumidor.</p>
                    </div>
                </div>

                <form wire:submit.prevent="enviar" class="g_gap_pagina formulario">

                    <div class="g_resaltado_caja info">
                        <span class="g_resaltado_caja_titulo">Información importante</span>
                        <div class="g_margin_top_10" style="font-size: 0.9em; opacity: 0.95; line-height: 1.5;">
                            <p><i class="fa-solid fa-circle-info"></i> Ningún campo es obligatorio para registrar su reclamo.</p>
                            <p><i class="fa-solid fa-address-card"></i> Si completa sus datos de contacto, podremos realizar un mejor seguimiento.</p>
                        </div>
                    </div>

                    <div class="g_panel">
                        <div class="g_panel_titulo">
                            <h2><i class="fa-solid fa-building"></i> 1.- Identificación del Proveedor</h2>
                        </div>
                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_12">
                                <label>Proyecto</label>
                                <select wire:model.live="proyecto_id" class="@error('proyecto_id') input-error @enderror">
                                    <option value="">-- Seleccionar --</option>
                                    @foreach($lista_proyectos as $pr)
                                        <option value="{{ $pr->id }}">{{ $pr->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('proyecto_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        @if($unidad_razon_social)
                            <div class="g_margin_top_20 g_panel"
                                style="background-color: rgba(0,0,0,0.02); border-style: dashed;">
                                <div class="informacion_resumen_grid">
                                    <div class="informacion_resumen_item" style="grid-column: span 2;">
                                        <span class="informacion_resumen_label">Razón Social</span>
                                        <span class="informacion_resumen_valor">{{ $unidad_razon_social }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="g_fila g_margin_top_20">
                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>Manzana</label>
                                <input type="text" wire:model="manzana" maxlength="5"
                                    class="@error('manzana') input-error @enderror"
                                    placeholder="Ej: A1">
                                @error('manzana') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>

                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>Lote</label>
                                <input type="text" wire:model="lote" inputmode="numeric" pattern="[0-9]*" maxlength="5"
                                    class="@error('lote') input-error @enderror"
                                    placeholder="Ej: 00125">
                                @error('lote') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="g_panel">
                        <div class="g_panel_titulo">
                            <h2><i class="fa-solid fa-user"></i> 2.- Información del consumidor reclamante</h2>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Nombres</label>
                                <input type="text" wire:model="nombre" class="@error('nombre') input-error @enderror">
                                @error('nombre') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>

                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Apellido paterno</label>
                                <input type="text" wire:model="apellido_paterno"
                                    class="@error('apellido_paterno') input-error @enderror">
                                @error('apellido_paterno') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>

                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Apellido materno</label>
                                <input type="text" wire:model="apellido_materno"
                                    class="@error('apellido_materno') input-error @enderror">
                                @error('apellido_materno') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_8">
                                <label>Domicilio</label>
                                <input type="text" wire:model="domicilio" class="@error('domicilio') input-error @enderror">
                                @error('domicilio') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>

                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Teléfono</label>
                                <input type="text" wire:model="telefono" class="@error('telefono') input-error @enderror">
                                @error('telefono') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Correo electrónico</label>
                                <input type="email" wire:model="email" class="@error('email') input-error @enderror">
                                @error('email') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>

                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Tipo de documento</label>
                                <select wire:model="tipo_documento" class="@error('tipo_documento') input-error @enderror">
                                    <option value="dni">DNI</option>
                                    <option value="ruc">RUC</option>
                                    <option value="ce">Carné de Extranjería</option>
                                </select>
                                @error('tipo_documento') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>

                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Número Documento</label>
                                <input type="text" wire:model="numero_documento"
                                    class="@error('numero_documento') input-error @enderror">
                                @error('numero_documento') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="g_panel">
                        <div class="g_panel_titulo">
                            <h2><i class="fa-solid fa-tag"></i> 3.- Identificación del bien contratado</h2>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_3">
                                <label><strong class="g_negrita">Tipo de bien contratado</strong></label>
                                <div style="display: flex; gap: 20px; align-items: center; margin-top: 10px;">
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px;">
                                        <input type="radio" wire:model="tipo_bien_contratado" value="producto"> Producto
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px;">
                                        <input type="radio" wire:model="tipo_bien_contratado" value="servicio"> Servicio
                                    </label>
                                </div>
                                @error('tipo_bien_contratado') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="g_margin_bottom_10">
                            <label>Monto Reclamado</label>
                            <input type="number" step="0.01" wire:model="monto_reclamado"
                                class="@error('monto_reclamado') input-error @enderror" placeholder="Ej: 150.00">
                            @error('monto_reclamado') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label>Descripción del producto o servicio</label>
                            <textarea wire:model="descripcion" rows="3" class="@error('descripcion') input-error @enderror"
                                placeholder="Breve descripción del bien o servicio..."></textarea>
                            @error('descripcion') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="g_panel">
                        <div class="g_panel_titulo">
                            <h2><i class="fa-solid fa-circle-exclamation"></i> 4.- Detalle de la reclamación</h2>
                        </div>

                        <label><strong class="g_negrita">Tipo de solicitud:</strong></label>
                        <div class="g_fila">

                            <div class="g_margin_bottom_10 g_columna_6">
                                <label style="cursor: pointer; display: flex; align-items: center; gap: 8px;">
                                    <input type="radio" wire:model="tipo_pedido" value="reclamo">
                                    <strong>RECLAMO:</strong> Disconformidad relacionada a los productos o servicios.
                                </label>
                            </div>

                            <div class="g_margin_bottom_10 g_columna_6">
                                <label style="cursor: pointer; display: flex; align-items: center; gap: 8px;">
                                    <input type="radio" wire:model="tipo_pedido" value="queja">
                                    <strong>QUEJA:</strong> Disconformidad no relacionada a los productos o servicios.
                                </label>
                            </div>
                        </div>
                        @error('tipo_pedido') <p class="mensaje_error">{{ $message }}</p> @enderror

                        <div class="g_margin_top_20">
                            <label>Detalle del reclamo o queja</label>
                            <textarea wire:model="detalle" rows="4" class="@error('detalle') input-error @enderror"
                                placeholder="Explique detalladamente lo ocurrido..."></textarea>
                            @error('detalle') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        <div class="g_margin_top_20">
                            <label>Pedido del consumidor</label>
                            <textarea wire:model="pedido" rows="3" class="@error('pedido') input-error @enderror"
                                placeholder="¿Qué acción espera por parte de la empresa?"></textarea>
                            @error('pedido') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        <div class="g_margin_top_20 g_resaltado_caja info">
                            <span class="g_resaltado_caja_titulo">Términos y condiciones</span>
                            <div style="display: flex; align-items: flex-start; gap: 12px; margin-top: 10px;">
                                <input type="checkbox" wire:model="conformidad"
                                    style="width: 20px; height: 20px; margin-top: 2px;">
                                <label style="font-weight: 600; cursor: pointer; color: inherit;">
                                    Me encuentro conforme con los términos de mi reclamo o queja (opcional).
                                </label>
                            </div>
                            @error('conformidad') <p class="mensaje_error">{{ $message }}</p> @enderror

                            <div class="g_margin_top_10" style="font-size: 0.85em; opacity: 0.9; line-height: 1.5;">
                                <p><i class="fa-solid fa-info-circle"></i> La formulación del reclamo no impide acudir a
                                    otras vías de solución de controversias ni es requisito previo para interponer una
                                    denuncia ante el INDECOPI.</p>
                                <p><i class="fa-solid fa-calendar-check"></i> El proveedor deberá dar respuesta al reclamo
                                    en un plazo no mayor a quince (15) días hábiles, el cual es improrrogable.</p>
                            </div>
                        </div>
                    </div>

                    <div class="g_margin_top_20">
                        <div class="formulario_botones centrar">
                            <button type="submit" class="g_boton guardar" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="enviar">
                                    <i class="fa-solid fa-paper-plane"></i> ENVIAR MI RECLAMO
                                </span>
                                <span wire:loading wire:target="enviar">
                                    <i class="fa-solid fa-spinner fa-spin"></i> PROCESANDO...
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>