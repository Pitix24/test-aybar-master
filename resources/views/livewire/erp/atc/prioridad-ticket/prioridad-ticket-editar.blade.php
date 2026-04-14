<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="update, eliminarPrioridadTicketOn" message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Editar Prioridad de Ticket</h2>

        <div class="cabecera_titulo_botones">
            @can('prioridad-ticket.vista-lista')
                <a href="{{ route('erp.prioridad-ticket.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            @can('prioridad-ticket.vista-crear')
                <a href="{{ route('erp.prioridad-ticket.vista.crear') }}" class="g_boton primary">
                    Crear <i class="fa-solid fa-square-plus"></i></a>
            @endcan

            @can('prioridad-ticket.accion-eliminar')
                <button type="button" class="g_boton danger" onclick="confirmarEliminarPrioridadTicket()">
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
                            <label for="tiempo_permitido">
                                Tiempo Permitido (Horas) <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span>
                            </label>
                            <input type="number" id="tiempo_permitido" wire:model.blur="tiempo_permitido"
                                class="@error('tiempo_permitido') input-error @enderror" autocomplete="off">
                            @error('tiempo_permitido')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label for="color">
                                Color Informativo
                            </label>
                            <input type="color" id="color" wire:model.blur="color"
                                class="@error('color') input-error @enderror">
                            @error('color')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="g_columna_6 g_margin_bottom_10">
                            <label for="icono">
                                Icono (FontAwesome)
                            </label>
                            <input type="text" id="icono" wire:model.blur="icono"
                                class="@error('icono') input-error @enderror" autocomplete="off">
                            @error('icono')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                            <p class="leyenda">Ej: fa-solid fa-fire, fa-solid fa-clock</p>
                        </div>
                    </div>

                    <div class="formulario_botones">
                        @can('prioridad-ticket.accion-editar')
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
        window.confirmarEliminarPrioridadTicket = function () {
            Swal.fire({
                title: '¿Quieres eliminar esta prioridad?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '¡Sí, eliminar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.eliminarPrioridadTicketOn();
                }
            });
        }
    </script>
    @endscript
</div>