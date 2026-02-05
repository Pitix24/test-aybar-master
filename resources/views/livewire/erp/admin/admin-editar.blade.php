@section('tituloPagina', 'Editar Usuario Admin')

<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Editar Usuario Admin</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.admin.vista.todo') }}" class="g_boton g_boton_light">
                Inicio <i class="fa-solid fa-house"></i></a>

            <a href="{{ route('erp.admin.vista.crear') }}" class="g_boton g_boton_primary">
                Crear <i class="fa-solid fa-square-plus"></i></a>

            <button type="button" class="g_boton g_boton_danger" onclick="confirmarEliminarAdmin()">
                Eliminar <i class="fa-solid fa-trash-can"></i>
            </button>

            <button type="button" class="g_boton g_boton_dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <form wire:submit="update" class="formulario">
        <div class="g_panel g_gap_pagina">
            <div class="g_fila">
                <div class="g_columna_8 g_gap_pagina">
                    <div class="g_panel">
                        <h4 class="g_panel_titulo">General</h4>
                        <div class="g_fila">
                            <div class="g_columna_6">
                                <div>
                                    <label for="name">
                                        Nombre y Apellidos <span class="obligatorio"><i
                                                class="fa-solid fa-asterisk"></i></span>
                                    </label>
                                    <input type="text" id="name" wire:model.blur="name"
                                        class="@error('name') input-error @enderror" autocomplete="off">
                                    @error('name')
                                        <p class="mensaje_error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="g_columna_6">
                                <div>
                                    <label for="email">
                                        Correo Electrónico <span class="obligatorio"><i
                                                class="fa-solid fa-asterisk"></i></span>
                                    </label>
                                    <input type="email" id="email" wire:model.blur="email"
                                        class="@error('email') input-error @enderror" autocomplete="off">
                                    @error('email')
                                        <p class="mensaje_error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_columna_6">
                                <div>
                                    <label for="password">
                                        Contraseña <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                                    </label>
                                    <input type="password" id="password" wire:model.blur="password"
                                        class="@error('password') input-error @enderror" autocomplete="off">
                                    @error('password')
                                        <p class="mensaje_error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="g_panel">
                        <h4 class="g_panel_titulo">Asignar Roles <span class="obligatorio"><i
                                    class="fa-solid fa-asterisk"></i></span></h4>

                        <div class="g_grid_permisos">
                            @foreach ($roles as $role)
                                <div class="permiso_item">
                                    <label class="cursor_pointer">
                                        <input type="checkbox" wire:model="selected_roles" value="{{ $role->name }}">
                                        <span class="fw-bold">{{ $role->name }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('selected_roles')
                            <p class="mensaje_error">{{ $message }}</p>
                        @enderror
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
                        <i class="fa-solid fa-save"></i> Actualizar
                    </span>
                    <span wire:loading wire:target="update">
                        <i class="fa-solid fa-spinner fa-spin"></i> Actualizando...
                    </span>
                </button>

                <a href="{{ route('erp.admin.vista.todo') }}" class="g_boton g_boton_cancelar">
                    <i class="fa-solid fa-times"></i> Cancelar
                </a>
            </div>
        </div>
    </form>

    @script
    <script>
        window.confirmarEliminarAdmin = function () {
            Swal.fire({
                title: '¿Quieres eliminar este usuario?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '¡Sí, eliminar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.eliminarAdminOn();
                }
            });
        }
    </script>
    @endscript
</div>