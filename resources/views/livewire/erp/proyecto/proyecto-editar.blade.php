@section('tituloPagina', 'Editar proyecto')

<div class="g_gap_pagina">

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Editar proyecto</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.proyecto.vista.todo') }}" class="g_boton g_boton_light">
                Inicio <i class="fa-solid fa-house"></i></a>

            <button type="button" class="g_boton g_boton_danger" onclick="alertaEliminarProyecto()">
                Eliminar <i class="fa-solid fa-trash-can"></i>
            </button>

            <button type="button" class="g_boton g_boton_dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <form wire:submit="update" class="formulario">
        <div class="g_panel g_gap_pagina">
            <div class="g_fila">
                <div class="g_columna_8">
                    <div class="g_panel">
                        <h4 class="g_panel_titulo">General</h4>

                        <div class="g_margin_bottom_10">
                            <label for="unidad_negocio_id">
                                Unidad de negocio <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                            </label>
                            <select id="unidad_negocio_id" wire:model.live="unidad_negocio_id"
                                class="@error('unidad_negocio_id') input-error @enderror" required>
                                <option value="" selected disabled>Seleccionar una unidad de negocio</option>
                                @foreach ($unidad_negocios as $item)
                                    <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                @endforeach
                            </select>
                            @error('unidad_negocio_id')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="g_margin_bottom_10">
                            <label for="grupo_proyecto_id">
                                Grupo proyecto<span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                            </label>
                            <select id="grupo_proyecto_id" wire:model.live="grupo_proyecto_id"
                                class="@error('grupo_proyecto_id') input-error @enderror" required>
                                <option value="" selected disabled>Seleccionar un grupo</option>
                                @foreach ($grupo_proyectos as $item)
                                    <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                @endforeach
                            </select>
                            @error('grupo_proyecto_id')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="g_margin_bottom_10">
                            <label for="nombre">Nombre <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <input type="text" id="nombre" wire:model.blur="nombre"
                                class="@error('nombre') input-error @enderror" autocomplete="off" required>
                            @error('nombre')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="g_margin_bottom_10">
                            <label for="slin_id">SLIN ID</label>
                            <input type="text" id="slin_id" wire:model.blur="slin_id"
                                class="@error('slin_id') input-error @enderror" autocomplete="off">
                            @error('slin_id')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                </div>

                <div class="g_columna_4 g_columna_invertir">
                    <div class="g_panel">
                        <h4 class="g_panel_titulo">Activo</h4>
                        <div>
                            <select id="activo" wire:model.live="activo" class="@error('activo') input-error @enderror">
                                <option value="0">DESACTIVADO</option>
                                <option value="1">ACTIVO</option>
                            </select>
                            @error('activo')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="formulario_botones">
                <button type="submit" class="g_boton g_boton_guardar" wire:loading.attr="disabled" wire:target="update">
                    <span wire:loading.remove wire:target="update">
                        <i class="fa-solid fa-pencil"></i> Actualizar
                    </span>
                    <span wire:loading wire:target="update">
                        <i class="fa-solid fa-spinner fa-spin"></i> Actualizando...
                    </span>
                </button>

                <a href="{{ route('erp.proyecto.vista.todo') }}" class="g_boton g_boton_cancelar">
                    <i class="fa-solid fa-times"></i> Cancelar
                </a>
            </div>
        </div>
    </form>

    @push('scripts')
        <script>
            function alertaEliminarProyecto() {
                Swal.fire({
                    title: '¿Quieres eliminar?',
                    text: "No podrás recuperarlo.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '¡Sí, eliminar!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('eliminarProyectoOn');

                        Swal.fire(
                            '¡Eliminado!',
                            'Eliminaste correctamente.',
                            'success'
                        )
                    }
                });
            }
        </script>
    @endpush
</div>