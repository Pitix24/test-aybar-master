<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="update, eliminarTipoSolicitudOn" message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Editar Tipo de Solicitud</h2>

        <div class="cabecera_titulo_botones">
            @can('tipo-solicitud.lista')
                <a href="{{ route('erp.tipo-solicitud.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            @can('tipo-solicitud.crear')
                <a href="{{ route('erp.tipo-solicitud.vista.crear') }}" class="g_boton primary">
                    Crear <i class="fa-solid fa-square-plus"></i></a>
            @endcan

            @can('tipo-solicitud.eliminar')
                <button type="button" class="g_boton danger" onclick="confirmarEliminarTipoSolicitud()">
                    Eliminar <i class="fa-solid fa-trash-can"></i>
                </button>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <form wire:submit="update" class="formulario">
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

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label for="nombre">
                                Nombre <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                            </label>
                            <input type="text" id="nombre" wire:model.blur="nombre"
                                class="@error('nombre') input-error @enderror" autocomplete="off">
                            @error('nombre')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="g_columna_6 g_margin_bottom_10">
                            <label for="tiempo_solucion">
                                Tiempo Solución (Horas) <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span>
                            </label>
                            <input type="number" id="tiempo_solucion" wire:model.blur="tiempo_solucion"
                                class="@error('tiempo_solucion') input-error @enderror" autocomplete="off">
                            @error('tiempo_solucion')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>
                            Áreas Relacionadas
                        </label>

                        <div class="g_grid_permisos @error('selectedAreas') input-error @enderror">
                            @foreach ($areas as $area)
                                <div class="permiso_item">
                                    <label class="cursor_pointer">
                                        <input type="checkbox" wire:model="selectedAreas" value="{{ $area->id }}">
                                        <span class="fw-bold">{{ $area->nombre }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('selectedAreas')
                            <p class="mensaje_error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="formulario_botones">
                        @can('tipo-solicitud.editar')
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
        </div>
    </form>

    @script
    <script>
        window.confirmarEliminarTipoSolicitud = function () {
            Swal.fire({
                title: '¿Quieres eliminar este tipo de solicitud?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '¡Sí, eliminar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.eliminarTipoSolicitudOn();
                }
            });
        }
    </script>
    @endscript
</div>