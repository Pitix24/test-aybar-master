<div class="g_gap_pagina">
    <x-loading-overlay wire:loading message="Guardando evaluación..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Evaluar Prospecto: {{ $nombre }} {{ $apellidos }}</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.prospecto-entrega-fest.vista.todo') }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>
        </div>
    </div>

    <form wire:submit.prevent="update">
        <div class="g_panel">
            <div class="g_fila">
                <div class="g_margin_bottom_15 g_columna_4">
                    <label>Evento / Entrega Fest <span class="obligatorio">*</span></label>
                    <select wire:model.live="entrega_fest_id" class="@error('entrega_fest_id') select-error @enderror">
                        @foreach ($eventos as $e)
                            <option value="{{ $e->id }}">{{ $e->nombre }} ({{ $e->codigo }})</option>
                        @endforeach
                    </select>
                    @error('entrega_fest_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_margin_bottom_15 g_columna_4">
                    <label>Proyecto <span class="obligatorio">*</span></label>
                    <select wire:model="proyecto_id" class="@error('proyecto_id') select-error @enderror" {{ !$entrega_fest_id ? 'disabled' : '' }}>
                        <option value="">Seleccione el proyecto...</option>
                        @foreach ($proyectos as $p)
                            <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                        @endforeach
                    </select>
                    @error('proyecto_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_margin_bottom_15 g_columna_4">
                    <label>DNI / Documento <span class="obligatorio">*</span></label>
                    <input type="text" wire:model="dni" class="@error('dni') input-error @enderror">
                    @error('dni') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_15 g_columna_4">
                    <label>Nombres <span class="obligatorio">*</span></label>
                    <input type="text" wire:model="nombre" class="@error('nombre') input-error @enderror">
                    @error('nombre') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
                <div class="g_margin_bottom_15 g_columna_4">
                    <label>Apellidos <span class="obligatorio">*</span></label>
                    <input type="text" wire:model="apellidos" class="@error('apellidos') input-error @enderror">
                    @error('apellidos') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
                <div class="g_margin_bottom_15 g_columna_4">
                    <label>DNI / Documento <span class="obligatorio">*</span></label>
                    <input type="text" wire:model="dni" class="@error('dni') input-error @enderror">
                    @error('dni') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_15 g_columna_3">
                    <label>Cód. Cliente</label>
                    <input type="text" wire:model="codigo_cliente" placeholder="Opcional">
                </div>
                <div class="g_margin_bottom_15 g_columna_3">
                    <label>Cód. Cuota</label>
                    <input type="text" wire:model="codigo_cuota" placeholder="Opcional">
                </div>
                <div class="g_margin_bottom_15 g_columna_2">
                    <label>Etapa</label>
                    <input type="text" wire:model="etapa" placeholder="Ej: 1">
                </div>
                <div class="g_margin_bottom_15 g_columna_2">
                    <label>Manzana</label>
                    <input type="text" wire:model="manzana" placeholder="Ej: A">
                </div>
                <div class="g_margin_bottom_15 g_columna_2">
                    <label>Lote</label>
                    <input type="text" wire:model="lote" placeholder="Ej: 15">
                </div>
            </div>

            <hr class="g_margin_top_20 g_margin_bottom_20">
            <h4 class="g_margin_bottom_15"><i class="fa-solid fa-briefcase"></i> Información BackOffice</h4>

            <div class="g_fila">
                <div class="g_margin_bottom_15 g_columna_3">
                    <label>Grupo <span class="obligatorio">*</span></label>
                    <select wire:model="grupo">
                        <option value="A">Grupo A</option>
                        <option value="B">Grupo B</option>
                        <option value="C">Grupo C</option>
                        <option value="D">Grupo D</option>
                    </select>
                </div>
                <div class="g_margin_bottom_15 g_columna_5">
                    <label>Gestor BackOffice</label>
                    <select wire:model="gestor_backoffice_id">
                        <option value="">Seleccione...</option>
                        @foreach ($usuarios as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="g_margin_bottom_15 g_columna_4">
                    <label>Fecha Culminación EECC</label>
                    <input type="datetime-local" wire:model="fecha_culminacion_eecc">
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_15 g_columna_6">
                    <label>Link Carpeta EECC</label>
                    <div style="display: flex; gap: 5px;">
                        <input type="text" wire:model="link_carpeta_eecc" placeholder="https://...">
                        @if($link_carpeta_eecc)
                            <a href="{{ $link_carpeta_eecc }}" target="_blank" class="g_boton light" title="Ir al link">
                                <i class="fa-solid fa-external-link"></i>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="g_margin_bottom_15 g_columna_6">
                    <label>Link EECC Firmado</label>
                    <div style="display: flex; gap: 5px;">
                        <input type="text" wire:model="link_eecc_firmado" placeholder="https://...">
                        @if($link_eecc_firmado)
                            <a href="{{ $link_eecc_firmado }}" target="_blank" class="g_boton light" title="Ir al link">
                                <i class="fa-solid fa-external-link"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_15 g_columna_4">
                    <label>Estado BackOffice <span class="obligatorio">*</span></label>
                    <select wire:model="estado_backoffice">
                        <option value="pendiente">Pendiente</option>
                        <option value="observado">Observado</option>
                        <option value="aprobado">Aprobado</option>
                        <option value="rechazado">Rechazado</option>
                    </select>
                </div>
                <div class="g_margin_bottom_15 g_columna_4">
                    <label>Validador BackOffice</label>
                    <select wire:model="validador_backoffice_id">
                        <option value="">Seleccione...</option>
                        @foreach ($usuarios as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="g_margin_bottom_15 g_columna_4">
                    <label>Fecha Validación EECC</label>
                    <input type="datetime-local" wire:model="fecha_validacion_eecc">
                </div>
            </div>

            <hr class="g_margin_top_20 g_margin_bottom_20">
            <h4 class="g_margin_bottom_15"><i class="fa-solid fa-file-contract"></i> Información Legal</h4>

            <div class="g_fila">
                <div class="g_margin_bottom_15 g_columna_6">
                    <label>Cto. Preliminar Emitido <span class="obligatorio">*</span></label>
                    <select wire:model="estado_contrato_preeliminar_emitido">
                        <option value="pendiente">Pendiente</option>
                        <option value="observado">Observado</option>
                        <option value="aprobado">Aprobado</option>
                        <option value="rechazado">Rechazado</option>
                    </select>
                </div>
                <div class="g_margin_bottom_15 g_columna_6">
                    <label>Firma Cto. Firmado <span class="obligatorio">*</span></label>
                    <select wire:model="estado_firma_contrato_firmado">
                        <option value="pendiente">Pendiente</option>
                        <option value="observado">Observado</option>
                        <option value="aprobado">Aprobado</option>
                        <option value="rechazado">Rechazado</option>
                    </select>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_15 g_columna_6">
                    <label>Fecha Generación Contrato</label>
                    <input type="datetime-local" wire:model="fecha_generacion_contrato">
                </div>
                <div class="g_margin_bottom_15 g_columna_6">
                    <label>Fecha Firma</label>
                    <input type="datetime-local" wire:model="fecha_firma">
                </div>
            </div>

            <hr class="g_margin_top_20 g_margin_bottom_20">

            <div class="g_fila">
                <div class="g_margin_bottom_15 g_columna_4">
                    <label>Estado Prospecto <span class="obligatorio">*</span></label>
                    <select wire:model="estado" style="font-weight: bold; color: var(--color-primary);">
                        <option value="pendiente">Pendiente</option>
                        <option value="observado">Observado</option>
                        <option value="aprobado">Aprobado</option>
                        <option value="rechazado">Rechazado</option>
                    </select>
                </div>
                <div class="g_margin_bottom_15 g_columna_8">
                    <label>Observaciones / Motivo</label>
                    <textarea wire:model="observacion" rows="3"
                        placeholder="Explique el motivo del estado si es observado o rechazado..."></textarea>
                </div>
            </div>

            <div class="formulario_botones">
                <button type="submit" class="g_boton guardar">
                    Actualizar Evaluación <i class="fa-solid fa-floppy-disk"></i>
                </button>
            </div>
        </div>
    </form>
</div>