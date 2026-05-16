<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="update" message="Actualizando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Editar Reglamento</h2>

        <div class="cabecera_titulo_botones">
            @can('reglamento.lista')
            <a href="{{ route('erp.reglamento.vista.todo') }}" class="g_boton light">
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
                        <label>Proyecto <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                        <select wire:model="proyecto_id" class="@error('proyecto_id') input-error @enderror">
                            <option value="">Selecciona un proyecto</option>
                            @foreach($proyectos as $proyecto)
                            <option value="{{ $proyecto->id }}">{{ $proyecto->nombre }}</option>
                            @endforeach
                        </select>
                        @error('proyecto_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Título del Reglamento <span class="obligatorio"><i
                                    class="fa-solid fa-asterisk"></i></span></label>
                        <input type="text" wire:model="titulo" class="@error('titulo') input-error @enderror">
                        @error('titulo') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>

                    <div class="g_fila">
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
                </div>
            </div>

            <div class="g_columna_4">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Archivo PDF</h4>

                    <div class="g_margin_bottom_10">
                        <input type="file" id="reglamentoArchivo" wire:model="archivo" accept="application/pdf"
                            style="display: none;">

                        <div class="contenedor_dropzone @error('archivo') dropzone-error @enderror"
                            onclick="document.getElementById('reglamentoArchivo').click()"
                            style="height: 160px; cursor: pointer;">
                            @if ($archivo)
                            <div class="dropzone_item">
                                <i class="fa-solid fa-file-pdf"></i>
                                <span>{{ $archivo->getClientOriginalName() }}</span>
                                <button type="button" wire:click.stop="$set('archivo', null)"
                                    class="dropzone_remove_button">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                            @elseif ($archivoActual)
                            <div class="dropzone_item">
                                <i class="fa-solid fa-file-pdf"></i>
                                <p style="margin: 10px 0; font-size: 14px;">PDF actual</p>
                                <a href="{{ $archivoActual }}" target="_blank" class="g_boton light"
                                    style="font-size: 12px;">
                                    <i class="fa-solid fa-download"></i> Descargar
                                </a>
                                <button type="button" wire:click.stop="$set('archivoActual', null)"
                                    class="dropzone_remove_button">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                            @else
                            <div wire:loading.remove wire:target="archivo">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                                <p>Haz clic para reemplazar PDF</p>
                                <span>(Máx. 50MB)</span>
                            </div>
                            <div wire:loading wire:target="archivo">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                                <p>Subiendo...</p>
                            </div>
                            @endif
                        </div>
                        @error('archivo') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="formulario_botones">
            @can('reglamento.editar')
            <button type="submit" class="g_boton guardar" wire:loading.attr="disabled" wire:target="update">
                <span wire:loading.remove wire:target="update">
                    <i class="fa-solid fa-save"></i> Guardar Cambios
                </span>
                <span wire:loading wire:target="update">
                    <i class="fa-solid fa-spinner fa-spin"></i> Guardando...
                </span>
            </button>
            @endcan

            @can('reglamento.eliminar')
            <button type="button" class="g_boton danger" onclick="confirmarEliminarReglamento()">
                <i class="fa-solid fa-trash"></i> Eliminar
            </button>
            @endcan

            <button type="button" class="g_boton cancelar" onclick="history.back()">
                <i class="fa-solid fa-times"></i> Cancelar
            </button>
        </div>
    </form>

    <script>
        function confirmarEliminarReglamento() {
            Swal.fire({
                title: '¿Eliminar Reglamento?',
                text: 'Esta acción no se puede deshacer. El reglamento será eliminado permanentemente.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.dispatch('eliminarReglamentoOn');
                }
            });
        }
    </script>
</div>