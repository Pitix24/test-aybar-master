@section('tituloPagina', 'Crear Usuario Admin')

<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Crear Usuario Admin</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.admin.vista.todo') }}" class="g_boton g_boton_light">
                Inicio <i class="fa-solid fa-house"></i></a>

            <button type="button" class="g_boton g_boton_dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <form wire:submit="store" class="formulario">
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
                            <label for="email">
                                Correo Electrónico <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                            </label>
                            <input type="email" id="email" wire:model.blur="email"
                                class="@error('email') input-error @enderror" autocomplete="off">
                            @error('email')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="g_columna_6 g_margin_bottom_10">
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
                        <button type="submit" class="g_boton g_boton_guardar" wire:loading.attr="disabled"
                            wire:target="store">
                            <span wire:loading.remove wire:target="store">
                                <i class="fa-solid fa-save"></i> Guardar
                            </span>
                            <span wire:loading wire:target="store">
                                <i class="fa-solid fa-spinner fa-spin"></i> Guardando...
                            </span>
                        </button>

                        <a href="{{ route('erp.admin.vista.todo') }}" class="g_boton g_boton_cancelar">
                            <i class="fa-solid fa-times"></i> Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>