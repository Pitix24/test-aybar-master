<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Ver Rol</h2>

        <div class="cabecera_titulo_botones">
            @can('rol.lista')
                <a href="{{ route('erp.rol.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            @can('rol.editar')
                <a href="{{ route('erp.rol.vista.editar', $role->id) }}" class="g_boton primary">
                    Editar <i class="fa-solid fa-pencil"></i></a>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <div class="formulario">
        <div class="g_fila">
            <div class="g_columna_12">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">General</h4>

                    <div class="g_margin_bottom_10">
                        <label for="name">Nombre del Rol</label>
                        <input type="text" id="name" value="{{ $role->name }}" readonly disabled>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Asignación de Permisos</label>

                        <div class="g_cajas_input" x-data="{ moduloAbierto: null }">
                            <div class="g_grid_modulos">
                                @foreach($allPermissions as $module => $permissionsGroup)
                                    @php
                                        $permisosAsignadosEnModulo = $permissionsGroup->filter(fn($p) => in_array($p->name, $permissions));
                                    @endphp
                                    <div class="modulo_acordeon" :class="{ 'abierto': moduloAbierto === '{{ $module }}' }">
                                        <div class="modulo_cabecera"
                                            @click="moduloAbierto = (moduloAbierto === '{{ $module }}' ? null : '{{ $module }}')">
                                            <h5>
                                                <i class="fa-solid fa-folder-open"></i> {{ $module }}
                                                <small class="g_badge {{ $permisosAsignadosEnModulo->count() > 0 ? 'info' : 'light' }}">
                                                    {{ $permisosAsignadosEnModulo->count() }} / {{ $permissionsGroup->count() }} permisos
                                                </small>
                                            </h5>
                                            <i class="fa-solid fa-chevron-down chevron"></i>
                                        </div>

                                        <div class="modulo_contenido">
                                            <div class="recursos_grid">
                                                @php
                                                    $recursos = $permissionsGroup->groupBy(fn($p) => explode('.', $p->name)[0]);
                                                @endphp

                                                @foreach($recursos as $recurso => $items)
                                                    <div class="recurso_grupo">
                                                        <div class="recurso_titulo">
                                                            {{ str_replace('-', ' ', $recurso) }}
                                                        </div>
                                                        <div class="permisos_lista">
                                                            @foreach($items as $permission)
                                                                @php $estaAsignado = in_array($permission->name, $permissions); @endphp
                                                                <div class="permiso_item">
                                                                    <input type="checkbox" id="perm_{{ $permission->id }}"
                                                                        {{ $estaAsignado ? 'checked' : '' }} disabled>
                                                                    <label for="perm_{{ $permission->id }}">
                                                                        {{ explode('.', $permission->name)[1] ?? $permission->name }}
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>