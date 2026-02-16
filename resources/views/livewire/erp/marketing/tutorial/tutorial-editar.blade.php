<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="update, destroy" message="Procesando cambios..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Editar Tutorial</h2>

        <div class="cabecera_titulo_botones">
            @can('tutorial.lista')
                <a href="{{ route('erp.tutorial.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i>
                </a>
            @endcan

            @can('tutorial.eliminar')
                <button type="button" class="g_boton danger" wire:click="destroy"
                    wire:confirm="¿Está seguro de eliminar este tutorial? Esta acción no se puede deshacer.">
                    Eliminar <i class="fa-solid fa-trash-can"></i>
                </button>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <div class="g_fila">
        <!-- Columna Principal: Formulario -->
        <div class="g_columna_8 g_gap_pagina">
            <form wire:submit="update" class="formulario g_panel" x-data="{ activeTab: 'general' }">
                <!-- Navegación por Tabs -->
                <div class="g_tab_navegacion">
                    <div class="g_tab_botones">
                        <button type="button" @click="activeTab = 'general'"
                            :class="activeTab === 'general' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                            <i class="fa-solid fa-graduation-cap"></i> Información del Tutorial
                        </button>
                    </div>
                </div>

                <!-- Contenido del Tab -->
                <div x-show="activeTab === 'general'" x-transition class="g_tab_content">
                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_12">
                            <label>Título del Tutorial <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <input type="text" wire:model="titulo" class="@error('titulo') input-error @enderror">
                            @error('titulo') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>ID de Video (YouTube) <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <input type="text" wire:model="video_id" class="@error('video_id') input-error @enderror">
                            @error('video_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        <div class="g_margin_bottom_10 g_columna_3">
                            <label>Orden de visualización</label>
                            <input type="number" wire:model="orden" class="@error('orden') input-error @enderror">
                            @error('orden') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        <div class="g_margin_bottom_10 g_columna_3">
                            <label>Estado</label>
                            <div class="g_switch_wrapper" style="margin-top: 5px;">
                                <label class="g_switch">
                                    <input type="checkbox" wire:model="activo">
                                    <span class="g_switch_slider"></span>
                                </label>
                                <span class="g_switch_label">{{ $activo ? 'Activo' : 'Inactivo' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Descripción</label>
                        <textarea wire:model="descripcion" rows="6"
                            class="@error('descripcion') input-error @enderror"></textarea>
                        @error('descripcion') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Botones del Formulario -->
                <div class="formulario_botones">
                    @can('tutorial.editar')
                        <button type="submit" class="g_boton guardar" wire:loading.attr="disabled" wire:target="update">
                            <span wire:loading.remove wire:target="update">
                                <i class="fa-solid fa-pencil"></i> Actualizar Tutorial
                            </span>
                            <span wire:loading wire:target="update">
                                <i class="fa-solid fa-spinner fa-spin"></i> Actualizando...
                            </span>
                        </button>
                    @endcan

                    <button type="button" class="g_boton cancelar" onclick="history.back()">
                        <i class="fa-solid fa-times"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>

        <!-- Columna Lateral: Miniatura -->
        <div class="g_columna_4">
            <div class="g_panel">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-image"></i> Miniatura del Tutorial</h4>

                @if ($imagenActual)
                    <div class="g_margin_bottom_15">
                        <p class="g_texto_secundario g_margin_bottom_5">Miniatura actual:</p>
                        <div style="border: 1px solid #eee; padding: 5px; border-radius: 8px; background: #fff;">
                            <img src="{{ $imagenActual }}"
                                style="width: 100%; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                        </div>
                    </div>
                @endif

                <div class="g_margin_bottom_10">
                    <label
                        class="g_texto_secundario g_margin_bottom_5">{{ $imagenActual ? 'Reemplazar miniatura:' : 'Subir miniatura:' }}</label>
                    <input type="file" id="tutorialImagenEdit" wire:model="imagen" accept="image/*"
                        style="display: none;">

                    <div class="contenedor_dropzone @error('imagen') dropzone-error @enderror"
                        onclick="document.getElementById('tutorialImagenEdit').click()"
                        style="height: 140px; cursor: pointer;">

                        @if ($imagen)
                            <div class="dropzone_item">
                                <i class="fa-solid fa-file-image"></i>
                                <span>{{ $imagen->getClientOriginalName() }}</span>
                                <button type="button" wire:click.stop="$set('imagen', null)" class="dropzone_remove_button">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        @else
                            <div wire:loading.remove wire:target="imagen">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                                <p>Haz clic para cambiar</p>
                                <span>(Máx. 1MB)</span>
                            </div>
                            <div wire:loading wire:target="imagen">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                                <p>Cargando...</p>
                            </div>
                        @endif
                    </div>
                    @error('imagen') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                @if ($imagen)
                    <div class="g_margin_top_10">
                        <p class="g_texto_secundario g_resaltar g_margin_bottom_5">Nueva miniatura (previa):</p>
                        <div
                            style="border: 2px dashed var(--color-success); padding: 5px; border-radius: 8px; background: #f6fff6;">
                            <img src="{{ $imagen->temporaryUrl() }}" style="width: 100%; border-radius: 6px;">
                        </div>
                        <button type="button" wire:click="$set('imagen', null)"
                            class="g_boton action danger g_margin_top_10" style="width: 100%;">
                            <i class="fa-solid fa-trash"></i> Cancelar subida
                        </button>
                    </div>
                @endif
            </div>

            <div class="g_panel g_margin_top_20">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-circle-info"></i> Información de Registro</h4>
                <div class="g_texto_secundario" style="font-size: 0.85rem;">
                    <p class="g_margin_bottom_5"><strong>ID Tutorial:</strong> #{{ $tutorial->id }}</p>
                    <p class="g_margin_bottom_5"><strong>Clicks:</strong> {{ $tutorial->clicks }}</p>
                    <p class="g_margin_bottom_5"><strong>Creado:</strong>
                        {{ $tutorial->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Actualizado:</strong> {{ $tutorial->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>