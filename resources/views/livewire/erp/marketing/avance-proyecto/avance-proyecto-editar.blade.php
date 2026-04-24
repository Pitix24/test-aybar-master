<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="update" message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Editar Avance de Proyecto</h2>

        <div class="cabecera_titulo_botones">
            @can('avance-proyecto.lista')
                <a href="{{ route('erp.avance-proyecto.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i>
                </a>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <form wire:submit="update" class="formulario g_panel g_gap_pagina">
        <div class="g_fila">
            <div class="g_columna_8">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Ubicación y Jerarquía</h4>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Unidad de Negocio <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                            <select wire:model.live="unidad_negocio_id" class="@error('unidad_negocio_id') input-error @enderror">
                                <option value="">Seleccione una unidad</option>
                                @foreach($unidades as $u)
                                    <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                                @endforeach
                            </select>
                            @error('unidad_negocio_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Grupo de Proyecto</label>
                            <select wire:model.live="grupo_proyecto_id" class="@error('grupo_proyecto_id') input-error @enderror" {{ empty($grupos) ? 'disabled' : '' }}>
                                <option value="">General (Toda la Unidad)</option>
                                @foreach($grupos as $g)
                                    <option value="{{ $g->id }}">{{ $g->nombre }}</option>
                                @endforeach
                            </select>
                            @error('grupo_proyecto_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Proyecto</label>
                            <select wire:model.live="proyecto_id" class="@error('proyecto_id') input-error @enderror" {{ empty($proyectos) ? 'disabled' : '' }}>
                                <option value="">General (Todo el Grupo)</option>
                                @foreach($proyectos as $p)
                                    <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                                @endforeach
                            </select>
                            @error('proyecto_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="g_panel">
                    <h4 class="g_panel_titulo">Información del Video</h4>

                    <div class="g_margin_bottom_10">
                        <label>Título del Avance <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                        <input type="text" wire:model="titulo" class="@error('titulo') input-error @enderror">
                        @error('titulo') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>ID de Video (YouTube) <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                            <input type="text" wire:model="video_id" class="@error('video_id') input-error @enderror">
                            @error('video_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Orden</label>
                            <input type="number" wire:model="orden" class="@error('orden') input-error @enderror">
                            @error('orden') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Descripción</label>
                        <textarea wire:model="descripcion" rows="4" class="@error('descripcion') input-error @enderror"></textarea>
                        @error('descripcion') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="g_columna_4">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Miniatura del Avance</h4>

                    <div class="g_margin_bottom_10">
                        @if ($imagenActual && !$imagen)
                            <div class="g_margin_bottom_10" style="position: relative;">
                                <img src="{{ $imagenActual }}" alt="Miniatura Actual"
                                    style="width: 100%; height: 160px; object-fit: cover; border-radius: 8px;">
                                <div
                                    style="position: absolute; top: 5px; right: 5px; background: rgba(0,0,0,0.5); color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem;">
                                    Actual
                                </div>
                            </div>
                        @endif

                        <input type="file" id="avanceImagen" wire:model="imagen" accept="image/*"
                            style="display: none;">

                        <div class="contenedor_dropzone @error('imagen') dropzone-error @enderror"
                            onclick="document.getElementById('avanceImagen').click()"
                            style="height: 160px; cursor: pointer;">
                            @if ($imagen)
                                <div class="dropzone_item">
                                    <i class="fa-solid fa-file-image"></i>
                                    <span>{{ $imagen->getClientOriginalName() }}</span>
                                    <button type="button" wire:click.stop="$set('imagen', null)"
                                        class="dropzone_remove_button">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                            @else
                                <div wire:loading.remove wire:target="imagen">
                                    <i class="fa-solid fa-cloud-arrow-up"></i>
                                    <p>Haz clic para {{ $imagenActual ? 'cambiar' : 'subir' }} imagen</p>
                                    <span>(Máx. 1MB - JPG, PNG)</span>
                                </div>
                                <div wire:loading wire:target="imagen">
                                    <i class="fa-solid fa-spinner fa-spin"></i>
                                    <p>Subiendo...</p>
                                </div>
                            @endif
                        </div>
                        @error('imagen') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="g_panel">
                    <h4 class="g_panel_titulo">Estado</h4>

                    <div class="g_margin_bottom_10">
                        <label for="estado_activo">Estado</label>
                        <div class="g_switch-wrapper">
                            <label class="g_switch">
                                <input id="estado_activo" type="checkbox" wire:model.live="activo">
                                <span class="g_switch-slider"></span>
                            </label>
                            <span class="g_switch-label">{{ $activo ? 'Activo' : 'Inactivo' }}</span>
                        </div>
                        @error('activo') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="formulario_botones">
            <button type="submit" class="g_boton guardar" wire:loading.attr="disabled" wire:target="update">
                <span wire:loading.remove wire:target="update">
                    <i class="fa-solid fa-save"></i> Guardar Cambios
                </span>
                <span wire:loading wire:target="update">
                    <i class="fa-solid fa-spinner fa-spin"></i> Guardando...
                </span>
            </button>

            <button type="button" class="g_boton cancelar" onclick="history.back()">
                <i class="fa-solid fa-times"></i> Cancelar
            </button>
        </div>
    </form>
</div>
