<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="update, updatePassword, eliminarAdminOn" message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Editar Usuario Administrativo</h2>

        <div class="cabecera_titulo_botones">
            @can('admin.lista')
                <a href="{{ route('erp.admin.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            @can('admin.crear')
                <a href="{{ route('erp.admin.vista.crear') }}" class="g_boton primary">
                    Crear <i class="fa-solid fa-square-plus"></i></a>
            @endcan

            @can('admin.eliminar')
                <button type="button" class="g_boton danger" onclick="confirmarEliminarAdmin()">
                    Eliminar <i class="fa-solid fa-trash-can"></i>
                </button>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_8">
            <form wire:submit="update" class="formulario">
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
                                {{ $activo ? 'Activo' : 'Inactivo' }}
                            </span>

                            @error('activo')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label for="name">
                                Nombre y Apellidos <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                            </label>
                            <input type="text" id="name" wire:model.blur="name"
                                class="@error('name') input-error @enderror" autocomplete="off">
                            @error('name')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="g_columna_6 g_margin_bottom_10">
                            <label for="email">
                                Correo Electrónico <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                            </label>
                            <input type="email" id="email" wire:model.blur="email"
                                class="@error('email') input-error @enderror" autocomplete="off">
                            @error('email')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>
                            Asignar Roles <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                        </label>

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

                    <div class="formulario_botones">
                        @can('admin.editar')
                            <button type="submit" class="g_boton guardar" wire:loading.attr="disabled" wire:target="update">
                                <span wire:loading.remove wire:target="update">
                                    <i class="fa-solid fa-save"></i> Actualizar
                                </span>
                                <span wire:loading wire:target="update">
                                    <i class="fa-solid fa-spinner fa-spin"></i> Actualizando...
                                </span>
                            </button>
                        @endcan

                        @can('admin.lista')
                            <a href="{{ route('erp.admin.vista.todo') }}" class="g_boton cancelar">
                                <i class="fa-solid fa-times"></i> Cancelar
                            </a>
                        @endcan
                    </div>
                </div>
            </form>
        </div>

        @can('admin.cambiar-clave')
            <div class="g_columna_4">
                <form wire:submit="updatePassword" class="formulario">
                    <div class="g_panel">
                        <h4 class="g_panel_titulo">Seguridad</h4>

                        <div class="g_margin_bottom_10">
                            <label for="password">Nueva Contraseña</label>
                            <input type="password" id="password" wire:model.blur="password"
                                class="@error('password') input-error @enderror" autocomplete="off">
                            @error('password')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                            <p class="leyenda">Mínimo 8 caracteres</p>
                        </div>

                        <div class="formulario_botones">

                            <button type="submit" class="g_boton guardar" wire:loading.attr="disabled"
                                wire:target="updatePassword">
                                <span wire:loading.remove wire:target="updatePassword">
                                    <i class="fa-solid fa-key"></i> Cambiar Contraseña
                                </span>
                                <span wire:loading wire:target="updatePassword">
                                    <i class="fa-solid fa-spinner fa-spin"></i> Cambiando...
                                </span>
                            </button>

                        </div>
                    </div>
                </form>
            </div>
        @endcan
    </div>

    @script
    <script>
        window.confirmarEliminarAdmin = function () {
            Swal.fire({
                title: '¿Quieres eliminar este usuario?',
                text: "Se perderán los roles asignados y el histórico asociado. Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
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