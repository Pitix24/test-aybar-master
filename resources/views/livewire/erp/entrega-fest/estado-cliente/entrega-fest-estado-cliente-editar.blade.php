<div class="g_gap_pagina">
    <x-loading-overlay wire:loading message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Editar Estado: <span>{{ $estado_model->nombre }}</span></h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.estado-cliente.todo') }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>

            <button type="button" class="g_boton danger" onclick="confirmarEliminarEstado()">
                Eliminar <i class="fa-solid fa-trash-can"></i>
            </button>

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    <div class="g_panel">
        <form wire:submit.prevent="update" class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_20 g_columna_6">
                    <label>Nombre del Estado <span class="obligatorio"><i
                                class="fa-solid fa-asterisk"></i></span></label>
                    <input type="text" wire:model="nombre" class="@error('nombre') input-error @enderror">
                    @error('nombre')
                        <p class="mensaje_error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="g_margin_bottom_20 g_columna_3">
                    <label>Color</label>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input type="color" wire:model.live="color" style="width: 50px; height: 38px; padding: 2px;">
                        <input type="text" wire:model="color" class="@error('color') input-error @enderror">
                    </div>
                    @error('color')
                        <p class="mensaje_error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="g_margin_bottom_20 g_columna_3">
                    <label>¿Está Activo?</label>
                    <select wire:model="activo">
                        <option value="1">Sí, Activo</option>
                        <option value="0">No, Inactivo</option>
                    </select>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_columna_12">
                    <div class="g_panel_informativo light">
                        <p><strong>Vista Previa del Badge:</strong></p>
                        <div style="margin-top: 10px;">
                            <span class="g_badge g_badge_soft" style="color: {{ $color }};">
                                {{ $nombre ?: 'EJEMPLO' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="g_tab_form_buttons">
                <button type="submit" class="g_boton guardar">
                    Actualizar Estado <i class="fa-solid fa-save"></i>
                </button>
            </div>
        </form>
    </div>

    <script>
        function confirmarEliminarEstado() {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede deshacer y podría afectar los prospectos asociados.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.dispatch('eliminarEstadoClienteOn');
                }
            })
        }
    </script>
</div>
