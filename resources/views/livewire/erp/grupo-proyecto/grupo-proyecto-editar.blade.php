<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Editar Grupo Proyecto</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.grupo-proyecto.vista.todo') }}" class="g_boton g_boton_light">
                Lista <i class="fa-solid fa-house"></i></a>

            <a href="{{ route('erp.grupo-proyecto.vista.crear') }}" class="g_boton g_boton_primary">
                Crear <i class="fa-solid fa-square-plus"></i></a>

            <button type="button" class="g_boton g_boton_danger" onclick="alertaEliminarGrupoProyecto()">
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

                        <div>
                            <label for="nombre">
                                Nombre <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                            </label>
                            <input type="text" id="nombre" wire:model.blur="nombre"
                                class="@error('nombre') input-error @enderror" autocomplete="off">
                            @error('nombre')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="g_columna_4 g_columna_invertir">
                    <div class="g_panel">
                        <h4 class="g_panel_titulo">Activo</h4>
                        <div>
                            <select id="activo" wire:model.live="activo">
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
                        <i class="fa-solid fa-save"></i> Actualizar
                    </span>
                    <span wire:loading wire:target="update">
                        <i class="fa-solid fa-spinner fa-spin"></i> Actualizando...
                    </span>
                </button>

                <a href="{{ route('erp.grupo-proyecto.vista.todo') }}" class="g_boton g_boton_cancelar">
                    <i class="fa-solid fa-times"></i> Cancelar
                </a>
            </div>
        </div>
    </form>

    @script
    <script>
        window.alertaEliminarGrupoProyecto = function () {
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
                    $wire.eliminarGrupoProyectoOn();
                }
            });
        }
    </script>
    @endscript
</div>