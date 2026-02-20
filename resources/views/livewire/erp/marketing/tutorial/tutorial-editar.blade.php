<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="update, eliminarTutorialOn" message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Editar Tutorial</h2>

        <div class="cabecera_titulo_botones">
            @can('tutorial.lista')
                <a href="{{ route('erp.tutorial.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            @can('tutorial.crear')
                <a href="{{ route('erp.tutorial.vista.crear') }}" class="g_boton primary">
                    Crear <i class="fa-solid fa-square-plus"></i></a>
            @endcan

            @can('tutorial.eliminar')
                <button type="button" class="g_boton danger" onclick="confirmarEliminarTutorial()">
                    Eliminar <i class="fa-solid fa-trash-can"></i>
                </button>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <form wire:submit="update" class="formulario g_gap_pagina">
        <div class="g_fila">
            <div class="g_columna_8">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Información General</h4>

                    <div class="g_margin_bottom_10">
                        <label for="estado_activo">
                            Estado <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                        </label>

                        <div class="g_switch-wrapper">
                            <label class="g_switch">
                                <input id="estado_activo" type="checkbox" wire:model.live="activo">
                                <span class="g_switch-slider"></span>
                            </label>

                            <span class="g_switch-label">
                                {{ $activo ? 'Activo' : 'Inactivo' }}
                            </span>

                            @error('activo')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Título del Tutorial <span class="obligatorio"><i
                                    class="fa-solid fa-asterisk"></i></span></label>
                        <input type="text" wire:model="titulo" class="@error('titulo') input-error @enderror">
                        @error('titulo') <p class="mensaje_error">{{ $message }}</p> @enderror
                        <p class="leyenda">Ej: Cómo realizar un pago por transferencia</p>
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>ID de Video (YouTube) <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <input type="text" wire:model="video_id" class="@error('video_id') input-error @enderror">
                            @error('video_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                            <p class="leyenda">Ej: dQw4w9WgXcQ</p>
                        </div>

                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Orden de visualización</label>
                            <input type="number" wire:model="orden" class="@error('orden') input-error @enderror">
                            @error('orden') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Descripción</label>
                        <textarea wire:model="descripcion" rows="6"
                            class="@error('descripcion') input-error @enderror"></textarea>
                        @error('descripcion') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>


                    <div class="formulario_botones">
                        @can('tutorial.editar')
                            <button type="submit" class="g_boton guardar" wire:loading.attr="disabled" wire:target="update">
                                <span wire:loading.remove wire:target="update">
                                    <i class="fa-solid fa-save"></i> Actualizar
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
                </div>
            </div>

            <div class="g_columna_4">
                <div class="g_panel">
                    <h4 class="g_panel_titulo g_margin_top_20">Miniatura del Tutorial</h4>

                    <div class="g_margin_bottom_10">
                        <input type="file" id="tutorialImagen" wire:model="imagen" accept="image/*"
                            style="display: none;">

                        <div class="contenedor_dropzone mediano @error('imagen') dropzone-error @enderror"
                            onclick="document.getElementById('tutorialImagen').click()">
                            @if ($imagen)
                                <div class="dropzone_item">
                                    <i class="fa-solid fa-file-image"></i>
                                    <span>{{ $imagen->getClientOriginalName() }}</span>
                                    <button type="button" wire:click.stop="$set('imagen', null)"
                                        class="dropzone_remove_button">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                            @elseif ($imagenActual)
                                <img src="{{ $imagenActual }}" class="g_imagen_actual">
                                <div class="g_cambiar_imagen g_boton danger">
                                    Click para cambiar
                                </div>
                            @else
                                <div wire:loading.remove wire:target="imagen">
                                    <i class="fa-solid fa-cloud-arrow-up"></i>
                                    <p>Haz clic para subir imagen</p>
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
            </div>
        </div>
    </form>

    @script
    <script>
        window.confirmarEliminarTutorial = function () {
            Swal.fire({
                title: '¿Quieres eliminar este tutorial?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '¡Sí, eliminar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.eliminarTutorialOn();
                }
            });
        }
    </script>
    @endscript
</div>