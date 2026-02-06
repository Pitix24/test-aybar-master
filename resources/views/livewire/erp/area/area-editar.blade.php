@section('tituloPagina', 'Editar Área')

<div class="g_gap_pagina">

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Editar Área</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.area.vista.todo') }}" class="g_boton g_boton_light">
                Lista <i class="fa-solid fa-list"></i></a>

            <a href="{{ route('erp.area.vista.crear') }}" class="g_boton g_boton_primary">
                Crear <i class="fa-solid fa-square-plus"></i></a>

            <button type="button" class="g_boton g_boton_danger" onclick="alertaEliminarArea()">
                Eliminar <i class="fa-solid fa-trash-can"></i>
            </button>

            <button type="button" class="g_boton g_boton_dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <form wire:submit="update" class="formulario">
        <div class="g_fila">
            <div class="g_columna_8">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">General</h4>

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
                                {{ $activo ? 'Activo' : 'Desactivado' }}
                            </span>

                            @error('activo')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label for="nombre">Nombre del Área <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <input type="text" id="nombre" wire:model.blur="nombre"
                                class="@error('nombre') input-error @enderror" autocomplete="off">
                            @error('nombre')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="g_columna_6 g_margin_bottom_10">
                            <label for="email_buzon">Email Buzón (ATC)</label>
                            <input type="email" id="email_buzon" wire:model.blur="email_buzon"
                                class="@error('email_buzon') input-error @enderror" autocomplete="off">
                            @error('email_buzon')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label for="color">Color Representativo</label>
                            <input type="color" id="color" wire:model.blur="color"
                                class="@error('color') input-error @enderror" style="height: 40px; padding: 2px;">
                            @error('color')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="g_columna_6 g_margin_bottom_10">
                            <label for="icono">Icono (FontAwesome)</label>
                            <input type="text" id="icono" wire:model.blur="icono"
                                class="@error('icono') input-error @enderror" autocomplete="off">
                            @error('icono')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Sedes vinculadas</label>
                        <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 5px;">
                            @foreach($sedes as $sede)
                                <label
                                    style="display: flex; align-items: center; gap: 5px; cursor: pointer; padding: 5px 10px; border: 1px solid #e2e8f0; border-radius: 4px; background: white;">
                                    <input type="checkbox" wire:model="selectedSedes" value="{{ $sede->id }}">
                                    <span>{{ $sede->nombre }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('selectedSedes')
                            <p class="mensaje_error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="formulario_botones">
                        <button type="submit" class="g_boton g_boton_guardar" wire:loading.attr="disabled"
                            wire:target="update">
                            <span wire:loading.remove wire:target="update">
                                <i class="fa-solid fa-pencil"></i> Actualizar
                            </span>
                            <span wire:loading wire:target="update">
                                <i class="fa-solid fa-spinner fa-spin"></i> Actualizando...
                            </span>
                        </button>

                        <a href="{{ route('erp.area.vista.todo') }}" class="g_boton g_boton_cancelar">
                            <i class="fa-solid fa-times"></i> Cancelar
                        </a>
                    </div>
                </div>
            </div>

            <div class="g_columna_4">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Previsualización</h4>
                    <div
                        style="padding: 20px; border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 15px;">
                        <i class="{{ $icono ?: 'fa-solid fa-shapes' }}"
                            style="font-size: 2.5rem; color: {{ $color ?: '#3b82f6' }}"></i>
                        <div>
                            <h3 style="margin: 0; color: #1e293b;">{{ $nombre ?: 'Nombre Área' }}</h3>
                            <p style="margin: 2px 0 0 0; color: #64748b; font-size: 0.85rem;">{{ $email_buzon ?:
                                'buzon@empresa.com' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @script
    <script>
        window.alertaEliminarArea = function () {
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
                    $wire.eliminarAreaOn();
                }
            });
        }
    </script>
    @endscript
</div>