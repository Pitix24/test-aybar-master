<div class="g_gap_pagina">

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Detalles Entrega Fest</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.vista.todo') }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>

            @can('entrega-fest.editar')
                <a href="{{ route('erp.entrega-fest.vista.editar', $evento->id) }}" class="g_boton primary">
                    Editar <i class="fa-solid fa-pencil"></i>
                </a>
            @endcan

             <a href="{{ route('erp.entrega-fest.vista.prospectos', $evento->id) }}" class="g_boton success">
                Prospectos <i class="fa-solid fa-users-viewfinder"></i>
            </a>

            <a href="{{ route('erp.entrega-fest.vista.invitados', $evento->id) }}" class="g_boton cancelar">
                Invitados <i class="fa-solid fa-users"></i></a>

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_8">
            <div class="formulario g_panel">
                <h4 class="g_panel_titulo">Información General</h4>

                <div class="g_margin_bottom_10">
                    <label>Estado</label>
                    <div class="g_switch-wrapper">
                        <label class="g_switch deshabilitado">
                            <input type="checkbox" {{ $evento->activo ? 'checked' : '' }} disabled>
                            <span class="g_switch-slider"></span>
                        </label>
                        <span class="g_switch-label">{{ $evento->activo ? 'Activo' : 'Inactivo' }}</span>
                    </div>
                </div>

                <div class="g_fila">
                    <div class="g_margin_bottom_10 g_columna_6">
                        <label>Nombre del Evento</label>
                        <input type="text" value="{{ $evento->nombre }}" disabled readonly class="g_input_disabled">
                    </div>

                    <div class="g_margin_bottom_10 g_columna_6">
                        <label>Código único</label>
                        <input type="text" value="{{ $evento->codigo }}" disabled readonly class="g_input_disabled">
                    </div>
                </div>

                <div class="g_margin_bottom_10">
                    <label>Descripción del Evento</label>
                    <textarea rows="3" disabled readonly class="g_input_disabled">{{ $evento->descripcion }}</textarea>
                </div>

                <div class="g_fila">
                    <div class="g_margin_bottom_10 g_columna_6">
                        <label>Fecha de Entrega</label>
                        <input type="text" value="{{ $evento->fecha_entrega->format('d/m/Y') }}" disabled readonly
                            class="g_input_disabled">
                    </div>

                    <div class="g_margin_bottom_10 g_columna_6">
                        <label>Responsable</label>
                        <input type="text" value="{{ $evento->gestor->name ?? 'N/A' }}" disabled readonly
                            class="g_input_disabled">
                    </div>
                </div>

                @if ($evento->proyectos->isNotEmpty())
                    <div class="g_margin_bottom_10">
                        <h4 class="g_panel_titulo"><i class="fa-solid fa-layer-group"></i> Proyectos vinculados</h4>

                        <div class="g_contenedor_tabla">
                            <table class="g_tabla">
                                <thead>
                                    <tr>
                                        <th>Empresa</th>
                                        <th>Proyecto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($evento->proyectos as $p)
                                        <tr wire:key="p-agregado-{{ $p->id }}">
                                            <td class="g_negrita">{{ $p->unidadNegocio->nombre ?? 'N/A' }}</td>
                                            <td>{{ $p->nombre }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>