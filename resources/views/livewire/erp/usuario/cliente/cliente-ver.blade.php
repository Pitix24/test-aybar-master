<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Ver Cliente Portal</h2>

        <div class="cabecera_titulo_botones">
            @can('cliente.ver')
                <a href="{{ route('erp.cliente.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            @can('cliente.editar')
                <a href="{{ route('erp.cliente.vista.editar', $user->id) }}" class="g_boton primary">
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
                    <h4 class="g_panel_titulo">Información del Cliente</h4>

                    <div class="g_margin_bottom_10">
                        <label>Estado</label>
                        <br>
                        @if ($user->activo)
                            <span class="g_badge success">Activo</span>
                        @else
                            <span class="g_badge danger">Inactivo</span>
                        @endif
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Nombre y Apellidos</label>
                            <input type="text" value="{{ $user->name }}" readonly disabled>
                        </div>

                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>DNI</label>
                            <input type="text" value="{{ $user->perfilCliente->dni ?? '-' }}" readonly disabled>
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Correo Electrónico</label>
                        <input type="text" value="{{ $user->email }}" readonly disabled>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_4 g_margin_bottom_10">
                            <label>Email Verificado</label>
                            <br>
                            @if ($user->email_verified_at)
                                <span class="g_badge success">Verificado
                                    ({{ $user->email_verified_at->format('d/m/Y') }})</span>
                            @else
                                <span class="g_badge danger">No Verificado</span>
                            @endif
                        </div>

                        <div class="g_columna_4 g_margin_bottom_10">
                            <label>Tratamiento D.P.</label>
                            <br>
                            <span class="g_badge {{ $user->politica_uno ? 'success' : 'light' }}">
                                {{ $user->politica_uno ? 'Autorizado' : 'No Autorizado' }}
                            </span>
                        </div>

                        <div class="g_columna_4 g_margin_bottom_10">
                            <label>Política Comercial</label>
                            <br>
                            <span class="g_badge {{ $user->politica_dos ? 'success' : 'light' }}">
                                {{ $user->politica_dos ? 'Autorizado' : 'No Autorizado' }}
                            </span>
                        </div>
                    </div>

                    <div class="formulario_botones">
                        <button type="button" class="g_boton dark" onclick="history.back()">
                            <i class="fa-solid fa-arrow-left"></i> Regresar
                        </button>
                    </div>
                </div>
            </div>

            <div class="g_columna_4">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Auditoría</h4>
                    <div class="g_margin_bottom_10">
                        <label>ID de Usuario</label>
                        <p class="leyenda">#{{ $user->id }}</p>
                    </div>
                    <div class="g_margin_bottom_10">
                        <label>Fecha de Registro</label>
                        <p class="leyenda">{{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : '-' }}</p>
                    </div>
                    <div>
                        <label>Última Actualización</label>
                        <p class="leyenda">{{ $user->updated_at ? $user->updated_at->format('d/m/Y H:i') : '-' }}</p>
                    </div>
                </div>

                <div class="g_panel g_margin_top_20">
                    <h4 class="g_panel_titulo">Seguridad</h4>
                    <p class="leyenda">
                        Los clientes gestionan sus propias contraseñas a través del portal.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>