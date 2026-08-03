<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="guardar" message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Crear Documento</h2>

        <div class="cabecera_titulo_botones">
            @can('cliente_documento.lista')
            <a href="{{ route('erp.cliente-documento.vista.todo') }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    <form wire:submit.prevent="guardar" class="formulario g_panel g_gap_pagina">
        <div class="g_fila">
            {{-- COLUMNA IZQUIERDA: INFORMACIÓN PRINCIPAL --}}
            <div class="g_columna_8">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Información General</h4>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_12">
                            <label>Título del Documento <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                            <input type="text" wire:model="titulo" class="@error('titulo') input-error @enderror" placeholder="Ej: Plano de Lotización - Etapa 2">
                            @error('titulo') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Proyecto <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                            <select wire:model="proyecto_id" class="@error('proyecto_id') input-error @enderror">
                                <option value="">Selecciona un proyecto</option>
                                @foreach($proyectos as $p)
                                <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                                @endforeach
                            </select>
                            @error('proyecto_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Tipo de Documento <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                            <select wire:model="tipo_cliente_documentos_id" class="@error('tipo_cliente_documentos_id') input-error @enderror">
                                <option value="">Selecciona el tipo</option>
                                @foreach($tipos as $t)
                                <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                                @endforeach
                            </select>
                            @error('tipo_cliente_documentos_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Descripción</label>
                        <textarea wire:model="descripcion" rows="4" class="@error('descripcion') input-error @enderror" placeholder="Agrega detalles o notas sobre este documento..."></textarea>
                        @error('descripcion') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Icono (Opcional)</label>
                            <input type="text" wire:model="icono" class="@error('icono') input-error @enderror" placeholder="Ej: fa-solid fa-file-pdf">
                            <p class="leyenda">Clase FontAwesome para reemplazar el ícono por defecto.</p>
                            @error('icono') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Orden de visualización</label>
                            <input type="number" wire:model="orden" class="@error('orden') input-error @enderror" placeholder="0">
                            @error('orden') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Panel de Configuraciones --}}
                <div class="g_panel g_margin_top_20">
                    <h4 class="g_panel_titulo">Configuraciones</h4>
                    <div class="g_fila">
                        <div class="g_margin_bottom_20">
                            <label for="solo_lectura_check" style="margin-bottom: 8px; display: block;">
                                Privacidad del Documento
                            </label>
                            <div class="g_switch-wrapper" title="Si está activo, el cliente no podrá descargar ni imprimir el PDF">
                                <label class="g_switch">
                                    <input id="solo_lectura_check" type="checkbox" wire:model.live="solo_lectura">
                                    <span class="g_switch-slider"></span>
                                </label>
                                <span class="g_switch-label">
                                    {{ $solo_lectura ? 'Solo Lectura (Seguro)' : 'Permitir Descarga' }}
                                </span>
                            </div>
                            @error('solo_lectura') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        <div class="g_margin_bottom_10">
                            <label for="estado_activo_check" style="margin-bottom: 8px; display: block;">
                                Estado de Visibilidad
                            </label>
                            <div class="g_switch-wrapper">
                                <label class="g_switch">
                                    <input id="estado_activo_check" type="checkbox" wire:model.live="activo">
                                    <span class="g_switch-slider"></span>
                                </label>
                                <span class="g_switch-label">
                                    {{ $activo ? 'Público (Activo)' : 'Oculto (Inactivo)' }}
                                </span>
                            </div>
                            @error('activo') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- COLUMNA DERECHA: ARCHIVO Y CONFIGURACIONES --}}
            <div class="g_columna_4">
                {{-- Panel de Archivo --}}
                <div class="g_panel g_margin_bottom_20">
                    <h4 class="g_panel_titulo">Archivo PDF <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></h4>

                    <div class="g_margin_bottom_10">
                        <input type="file" id="documentoArchivo" wire:model="archivo" accept="application/pdf" style="display: none;">

                        <div class="contenedor_dropzone @error('archivo') dropzone-error @enderror"
                            onclick="document.getElementById('documentoArchivo').click()"
                            style="height: 160px; cursor: pointer;">
                            @if ($archivo)
                            <div class="dropzone_item">
                                <i class="fa-solid fa-file-pdf" style="color: #e11d48; font-size: 2rem;"></i>
                                <span style="margin-top: 10px; word-break: break-all; text-align: center;">{{ $archivo->getClientOriginalName() }}</span>
                                <button type="button" wire:click.stop="$set('archivo', null)" class="dropzone_remove_button">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                            @else
                            <div wire:loading.remove wire:target="archivo">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                                <p>Haz clic para subir PDF</p>
                                <span>(Máx. 10MB)</span>
                            </div>
                            <div wire:loading wire:target="archivo">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                                <p>Subiendo...</p>
                            </div>
                            @endif
                        </div>
                        @error('archivo') <p class="mensaje_error" style="text-align: center; margin-top: 5px;">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="formulario_botones">
            @can('cliente_documento.crear')
            <button type="submit" class="g_boton guardar" wire:loading.attr="disabled" wire:target="guardar, archivo">
                <span wire:loading.remove wire:target="guardar">
                    <i class="fa-solid fa-save"></i> Guardar
                </span>
                <span wire:loading wire:target="guardar">
                    <i class="fa-solid fa-spinner fa-spin"></i> Guardando...
                </span>
            </button>
            @endcan

            <button type="button" class="g_boton cancelar" onclick="history.back()">
                <i class="fa-solid fa-times"></i> Cancelar
            </button>
        </div>
    </form>
</div>
