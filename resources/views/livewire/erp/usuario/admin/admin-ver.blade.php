<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Ver Usuario Administrativo</h2>

        <div class="cabecera_titulo_botones">
            @can('admin.ver')
                <a href="{{ route('erp.admin.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            @can('admin.editar')
                <a href="{{ route('erp.admin.vista.editar', $user->id) }}" class="g_boton primary">
                    Editar <i class="fa-solid fa-pencil"></i></a>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <div class="formulario">
        <div class="g_fila">
            <div class="g_columna_8">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Información del Usuario</h4>

                    <div class="g_margin_bottom_10">
                        <label for="estado_activo">Estado</label>

                        <div class="g_switch-wrapper">
                            <label class="g_switch">
                                <input id="estado_activo" type="checkbox" @checked($user->activo) readonly disabled>
                                <span class="g_switch-slider"></span>
                            </label>

                            <span class="g_switch-label">
                                {{ $user->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Nombre y Apellidos</label>
                        <input type="text" value="{{ $user->name }}" readonly disabled>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Correo Electrónico</label>
                        <input type="text" value="{{ $user->email }}" readonly disabled>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Roles Asignados</label>
                        <div class="g_celda_tags">
                            @foreach ($user->roles as $role)
                                <span class="g_badge info">{{ $role->name }}</span>
                            @endforeach
                            @if ($user->roles->isEmpty())
                                <span>Sin roles asignados</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="g_columna_4">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Seguridad</h4>
                    <p class="leyenda">
                        La contraseña está cifrada y no puede ser visualizada por razones de seguridad.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>