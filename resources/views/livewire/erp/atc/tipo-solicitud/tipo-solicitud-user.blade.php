<div class="g_gap_pagina">
    <x-loading-overlay wire:loading
        wire:target="searchAgregados,searchDisponibles,perPageAsignados,perPageDisponibles,resetFiltrosAgregados,resetFiltrosDisponibles,agregarUsuario,quitarUsuario,marcarPrincipal,gotoPage,nextPage,previousPage"
        message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Gestión de Usuarios
            <span>Tipo de Solicitud: {{ $tipoSolicitud->nombre }}</span>
        </h2>

        <div class="cabecera_titulo_botones">
            @can('tipo-solicitud.lista')
                <a href="{{ route('erp.tipo-solicitud.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            @can('tipo-solicitud.editar')
                <a href="{{ route('erp.tipo-solicitud.vista.editar', $tipoSolicitud->id) }}" class="g_boton secondary">
                    Editar <i class="fa-solid fa-pencil"></i></a>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <div class="g_fila">
        {{-- ASIGNADOS --}}
        <div class="g_columna_6">
            <div class="g_panel">
                <h4 class="g_panel_titulo">Asignados ({{ $usuariosAgregados->total() }})</h4>

                <div class="g_tabla_cabecera">
                    <div class="g_tabla_cabecera_botones">
                        <button wire:click="resetFiltrosAgregados" class="g_boton danger" title="Limpiar Filtros">
                            <i class="fa-solid fa-rotate-left"></i>
                        </button>
                    </div>

                    <div class="g_tabla_cabecera_filtro formulario">
                        <div>
                            <label>Admin (Nombre o Email)</label>
                            <input type="text" wire:model.live.debounce.800ms="searchAgregados">
                        </div>
                        <div class="g_margin_right_10">
                            <label>Mostrar</label>
                            <select wire:model.live="perPageAsignados">
                                <option value="15">15</option>
                                <option value="30">30</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="g_contenedor_tabla">
                    <table class="g_tabla">
                        <thead>
                            <tr>
                                <th class="g_celda_centro">N°</th>
                                <th>Usuario</th>
                                <th>Correo</th>
                                <th class="g_celda_centro" title="Responsable Principal">Resp.</th>
                                <th class="g_celda_centro">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($usuariosAgregados as $index => $user)
                                <tr wire:key="agregado-{{ $user->id }}">
                                    <td class="g_celda_centro">{{ $usuariosAgregados->firstItem() + $index }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td class="g_celda_centro">
                                        @can('tipo-solicitud.marcar-principal-usuario')
                                            <input type="radio" name="principal_radio"
                                                wire:click="marcarPrincipal({{ $user->id }})"
                                                {{ $user->pivot->is_principal ? 'checked' : '' }}
                                                title="Marcar como responsable principal">
                                        @endcan
                                    </td>
                                    <td class="g_celda_acciones g_celda_centro centro">
                                        @can('tipo-solicitud.eliminar-usuarios')
                                            <button wire:click="quitarUsuario({{ $user->id }})"
                                                class="g_boton danger" title="Quitar del tipo de solicitud">
                                                <i class="fa-solid fa-user-minus"></i>
                                            </button>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($usuariosAgregados->hasPages())
                    <div class="g_paginacion">
                        {{ $usuariosAgregados->links('vendor.pagination.default-livewire') }}
                    </div>
                @endif

                @if ($usuariosAgregados->isEmpty())
                    <div class="g_vacio">
                        <p>No se encontraron asignados.</p>
                        <i class="fa-regular fa-face-smile"></i>
                    </div>
                @endif
            </div>
        </div>

        {{-- DISPONIBLES --}}
        <div class="g_columna_6">
            <div class="g_panel">
                <h4 class="g_panel_titulo">Disponibles ({{ $usuariosDisponibles->total() }})</h4>

                <div class="g_tabla_cabecera">
                    <div class="g_tabla_cabecera_botones">
                        <button wire:click="resetFiltrosDisponibles" class="g_boton danger" title="Limpiar Filtros">
                            <i class="fa-solid fa-rotate-left"></i>
                        </button>
                    </div>
                    <div class="g_tabla_cabecera_filtro formulario">
                        <div>
                            <label>Admin (Nombre o Email)</label>
                            <input type="text" wire:model.live.debounce.800ms="searchDisponibles">
                        </div>
                        <div class="g_margin_right_10">
                            <label>Mostrar</label>
                            <select wire:model.live="perPageDisponibles">
                                <option value="15">15</option>
                                <option value="30">30</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="g_contenedor_tabla">
                    <table class="g_tabla">
                        <thead>
                            <tr>
                                <th class="g_celda_centro">N°</th>
                                <th>Usuario</th>
                                <th>Correo</th>
                                <th class="g_celda_centro">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($usuariosDisponibles as $index => $user)
                                <tr wire:key="disponible-{{ $user->id }}">
                                    <td class="g_celda_centro">{{ $usuariosDisponibles->firstItem() + $index }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td class="g_celda_acciones g_celda_centro centro">
                                        @can('tipo-solicitud.agregar-usuarios')
                                            <button wire:click="agregarUsuario({{ $user->id }})"
                                                class="g_boton success">
                                                <i class="fa-solid fa-user-plus"></i>
                                            </button>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($usuariosDisponibles->hasPages())
                    <div class="g_paginacion">
                        {{ $usuariosDisponibles->links('vendor.pagination.default-livewire') }}
                    </div>
                @endif

                @if ($usuariosDisponibles->isEmpty())
                    <div class="g_vacio">
                        <p>No se encontraron usuarios disponibles.</p>
                        <i class="fa-regular fa-face-smile"></i>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
