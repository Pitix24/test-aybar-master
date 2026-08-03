<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="guardar" message="Guardando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Editar Documento: {{ $documento->titulo }}</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.cliente-documento.vista.todo') }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <form class="formulario g_panel g_gap_pagina">
        <div class="g_fila">
            <div class="g_columna_8">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Información General</h4>

                    <div class="g_margin_bottom_10">
                        <label>Título <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                        <input type="text" wire:model="titulo" placeholder="Ej: Plano de Lotización"
                            class="@error('titulo') input-error @enderror">
                        @error('titulo') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Proyecto <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                            <select wire:model="proyecto_id" class="@error('proyecto_id') input-error @enderror">
                                <option value="">Seleccione...</option>
                                @foreach($proyectos as $p)
                                <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                                @endforeach
                            </select>
                            @error('proyecto_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Tipo de Documento <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                            <select wire:model="tipo_cliente_documentos_id" class="@error('tipo_cliente_documentos_id') input-error @enderror">
                                <option value="">Seleccione...</option>
                                @foreach($tipos as $t)
                                <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                                @endforeach
                            </select>
                            @error('tipo_cliente_documentos_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Descripción</label>
                        <textarea wire:model="descripcion" rows="3" placeholder="Descripción breve..."
                            class="@error('descripcion') input-error @enderror"></textarea>
                        @error('descripcion') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Icono (Opcional)</label>
                            <input type="text" wire:model="icono" placeholder="fa-solid fa-file-pdf"
                                class="@error('icono') input-error @enderror">
                            @error('icono') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Orden de visualización</label>
                            <input type="number" wire:model="orden"
                                class="@error('orden') input-error @enderror">
                            @error('orden') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_6">
                            <div class="g_switch-wrapper">
                                <label class="g_switch">
                                    <input type="checkbox" wire:model.live="solo_lectura">
                                    <span class="g_switch-slider"></span>
                                </label>
                                <span class="g_switch-label">
                                    {{ $solo_lectura ? 'Sí' : 'No' }} - Solo Lectura
                                </span>
                            </div>
                            @error('solo_lectura') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        <div class="g_margin_bottom_10 g_columna_6">
                            <div class="g_switch-wrapper">
                                <label class="g_switch">
                                    <input type="checkbox" wire:model.live="activo">
                                    <span class="g_switch-slider"></span>
                                </label>
                                <span class="g_switch-label">
                                    {{ $activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                            @error('activo') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="g_columna_4">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Archivo PDF</h4>

                    <div class="g_margin_bottom_10">
                        <input type="file" id="documentoArchivo" wire:model="archivo_nuevo" accept="application/pdf"
                            style="display: none;">

                        @if($documento->archivoPdf && !$archivo_nuevo)
                        <div style="margin-bottom: 10px;">
                            <div class="dropzone_item">
                                <i class="fa-solid fa-file-pdf"></i>
                                <p style="margin: 10px 0; font-size: 14px;">PDF actual</p>
                                <a href="{{ route('erp.cliente-documento.vista.stream', $this->documento->id) }}" target="_blank" class="g_boton light"
                                    style="font-size: 12px;">
                                    <i class="fa-solid fa-download"></i> Descargar
                                </a>
                            </div>
                        </div>
                        @endif

                        <div class="contenedor_dropzone @error('archivo_nuevo') dropzone-error @enderror"
                            onclick="document.getElementById('documentoArchivo').click()"
                            style="height: 160px; cursor: pointer;">
                            @if ($archivo_nuevo)
                            <div class="dropzone_item">
                                <i class="fa-solid fa-file-pdf"></i>
                                <span>{{ $archivo_nuevo->getClientOriginalName() }}</span>
                                <button type="button" wire:click.stop="$set('archivo_nuevo', null)"
                                    class="dropzone_remove_button">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                            @else
                            <div wire:loading.remove wire:target="archivo_nuevo">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                                <p>Haz clic para subir o reemplazar PDF</p>
                                <span>(Máx. 50MB)</span>
                            </div>
                            <div wire:loading wire:target="archivo_nuevo">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                                <p>Subiendo...</p>
                            </div>
                            @endif
                        </div>
                        @error('archivo_nuevo') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="formulario_botones">
            <button type="button" class="g_boton guardar" wire:loading.attr="disabled" wire:target="guardar"
                wire:click="guardar">
                <span wire:loading.remove wire:target="guardar">
                    <i class="fa-solid fa-save"></i> Guardar Cambios
                </span>
                <span wire:loading wire:target="guardar">
                    <i class="fa-solid fa-spinner fa-spin"></i> Guardando...
                </span>
            </button>

            <button type="button" class="g_boton cancelar" onclick="history.back()">
                <i class="fa-solid fa-times"></i> Cancelar
            </button>
        </div>
    </form>
</div>
