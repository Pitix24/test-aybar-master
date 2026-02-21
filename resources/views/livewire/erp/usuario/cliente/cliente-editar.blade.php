<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="update, enviarRecuperarClave, eliminarClienteOn"
        message="Procesando..." />

    <!-- CABECERA -->
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Editar Cliente Portal</h2>

        <div class="cabecera_titulo_botones">
            @can('cliente.lista')
                <a href="{{ route('erp.cliente.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            <a href="{{ route('erp.cliente.vista.consultar', $dni) }}" class="g_boton secondary">
                Portal Cliente <i class="fa-solid fa-user"></i></a>

            @can('cliente.eliminar')
                <button type="button" class="g_boton danger" onclick="confirmarEliminarCliente()">
                    Eliminar <i class="fa-solid fa-trash-can"></i>
                </button>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_8 g_gap_pagina formulario">
            <form wire:submit="update">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Perfil</h4>

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

                    <h4 class="g_panel_titulo">Datos Personales</h4>

                    <div class="g_margin_bottom_10">
                        <label for="name">
                            Nombre y Apellidos <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                        </label>
                        <input type="text" id="name" wire:model.blur="name" class="@error('name') input-error @enderror"
                            autocomplete="off">
                        @error('name')
                            <p class="mensaje_error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label for="dni">
                                DNI <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                            </label>
                            <input type="text" id="dni" wire:model.blur="dni"
                                class="@error('dni') input-error @enderror" autocomplete="off">
                            @error('dni')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="g_columna_6 g_margin_bottom_10">
                            <label for="telefono_principal">Celular / Teléfono</label>
                            <input type="text" id="telefono_principal" wire:model.blur="telefono_principal"
                                class="@error('telefono_principal') input-error @enderror" autocomplete="off">
                            @error('telefono_principal')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label for="email">
                            Correo Electrónico <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                        </label>
                        <input type="email" id="email" wire:model.blur="email"
                            class="@error('email') input-error @enderror" autocomplete="off">
                        @error('email')
                            <p class="mensaje_error">{{ $message }}</p>
                        @enderror
                    </div>


                    <div class="formulario_botones">
                        <button type="submit" class="g_boton guardar" wire:loading.attr="disabled" wire:target="update">
                            <span wire:loading.remove wire:target="update">
                                <i class="fa-solid fa-save"></i> Actualizar Datos
                            </span>
                            <span wire:loading wire:target="update">
                                <i class="fa-solid fa-spinner fa-spin"></i> Actualizando...
                            </span>
                        </button>

                        <button type="button" class="g_boton cancelar" onclick="history.back()">
                            <i class="fa-solid fa-times"></i> Cancelar
                        </button>
                    </div>
                </div>
            </form>

            @if ($direccion)
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Dirección Registrada</h4>

                    <div class="g_fila">
                        <div class="g_columna_4 g_margin_bottom_10">
                            <label>Departamento</label>
                            <input type="text" disabled value="{{ $direccion?->region?->nombre }}" class="g_input">
                        </div>

                        <div class="g_columna_4 g_margin_bottom_10">
                            <label>Provincia</label>
                            <input type="text" disabled value="{{ $direccion?->provincia?->nombre }}" class="g_input">
                        </div>

                        <div class="g_columna_4 g_margin_bottom_10">
                            <label>Distrito</label>
                            <input type="text" disabled value="{{ $direccion?->distrito?->nombre }}" class="g_input">
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_8 g_margin_bottom_10">
                            <label>Avenida / Calle / Jirón</label>
                            <input type="text" disabled value="{{ $direccion?->direccion }}" class="g_input">
                        </div>

                        <div class="g_columna_4 g_margin_bottom_10">
                            <label>Número</label>
                            <input type="text" disabled value="{{ $direccion?->direccion_numero }}" class="g_input">
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Dpto. / Interior / Piso / Lote</label>
                            <input type="text" disabled value="{{ $direccion?->opcional }}" class="g_input">
                        </div>

                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Código Postal</label>
                            <input type="text" disabled value="{{ $direccion?->codigo_postal }}" class="g_input">
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Referencia de Ubicación</label>
                        <textarea disabled rows="2" class="g_input">{{ $direccion?->referencia }}</textarea>
                    </div>
                </div>
            @endif
        </div>

        <!-- COLUMNA DERECHA -->
        <div class="g_columna_4">
            <form wire:submit="enviarRecuperarClave" class="formulario">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Seguridad</h4>

                    <div class="g_margin_bottom_10">
                        <label>Correo Electrónico Actual</label>
                        <input type="text" disabled value="{{ $email }}" class="g_input">
                        <p class="leyenda">Se enviará un enlace de restablecimiento a esta dirección.</p>
                    </div>

                    <div class="formulario_botones">
                        <button type="submit" class="g_boton guardar" wire:loading.attr="disabled"
                            wire:target="enviarRecuperarClave">
                            <span wire:loading.remove wire:target="enviarRecuperarClave">
                                <i class="fa-solid fa-envelope"></i> Enviar Recuperación
                            </span>
                            <span wire:loading wire:target="enviarRecuperarClave">
                                <i class="fa-solid fa-spinner fa-spin"></i> Enviando...
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @script
    <script>
        window.confirmarEliminarCliente = function () {
            Swal.fire({
                title: '¿Quieres eliminar este cliente?',
                text: "Esta acción eliminará el acceso del usuario al portal y sus datos asociados. No se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '¡Sí, eliminar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.eliminarClienteOn();
                }
            });
        }
    </script>
    @endscript
</div>