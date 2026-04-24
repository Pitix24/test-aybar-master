<div class="g_gap_pagina">
    <x-loading-overlay wire:loading
        wire:target="buscar, perPage, unidad_id, grupo_id, proyecto_id, activo, resetFiltros, gotoPage, nextPage, previousPage, toggleActivo"
        message="Cargando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Gestión de Avances de Proyectos</h2>

        <div class="cabecera_titulo_botones">
            @can('avance-proyecto.crear')
                <a href="{{ route('erp.avance-proyecto.vista.crear') }}" class="g_boton primary">
                    Crear <i class="fa-solid fa-square-plus"></i></a>
            @endcan
        </div>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Buscar (Título)</label>
                    <input type="text" wire:model.live.debounce.1300ms="buscar" placeholder="Ej: Avance de obra...">
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Unidad de Negocio</label>
                    <select wire:model.live="unidad_id">
                        <option value="">Todas</option>
                        @foreach($unidades as $u)
                            <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Grupo de Proyecto</label>
                    <select wire:model.live="grupo_id" {{ empty($grupos) ? 'disabled' : '' }}>
                        <option value="">Todos</option>
                        @foreach($grupos as $g)
                            <option value="{{ $g->id }}">{{ $g->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Proyecto</label>
                    <select wire:model.live="proyecto_id" {{ empty($proyectos) ? 'disabled' : '' }}>
                        <option value="">Todos</option>
                        @foreach($proyectos as $p)
                            <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Activo</label>
                    <select wire:model.live="activo">
                        <option value="">Todos</option>
                        <option value="1">Activos</option>
                        <option value="0">Inactivos</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="g_panel">
        <div class="g_tabla_cabecera">
            <div class="g_tabla_cabecera_botones">
                <button wire:click="resetFiltros" class="g_boton danger">
                    Limpiar <i class="fa-solid fa-rotate-left"></i>
                </button>
            </div>

            <div class="g_tabla_cabecera_filtro formulario">
                <div>
                    <label>Mostrar</label>
                    <select wire:model.live="perPage">
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="g_contenedor_tabla">
            <table class="g_tabla">
                <thead>
                    <tr>
                        <th class="g_celda_centro">Orden</th>
                        <th>Avance</th>
                        <th>Ubicación (Jerarquía)</th>
                        <th>Video ID</th>
                        <th class="g_celda_centro">Clicks</th>
                        <th class="g_celda_centro">Activo</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr wire:key="avance-{{ $item->id }}">
                            <td class="g_celda_centro">
                                <span class="g_badge light">{{ $item->orden }}</span>
                            </td>
                            <td>
                                <div class="g_negrita">{{ $item->titulo }}</div>
                                <div class="g_texto_secundario g_resumir">{{ Str::limit($item->descripcion, 60) }}</div>
                            </td>
                            <td>
                                <div class="g_badge outline secondary" title="Unidad de Negocio">
                                    {{ $item->unidadNegocio->nombre }}
                                </div>
                                @if($item->grupoProyecto)
                                    <i class="fa-solid fa-chevron-right g_texto_secundario" style="font-size: 0.7rem"></i>
                                    <div class="g_badge outline info" title="Grupo de Proyecto">
                                        {{ $item->grupoProyecto->nombre }}
                                    </div>
                                @endif
                                @if($item->proyecto)
                                    <i class="fa-solid fa-chevron-right g_texto_secundario" style="font-size: 0.7rem"></i>
                                    <div class="g_badge outline primary" title="Proyecto">
                                        {{ $item->proyecto->nombre }}
                                    </div>
                                @endif
                            </td>
                            <td><code>{{ $item->video_id }}</code></td>
                            <td class="g_celda_centro">
                                <span class="g_badge primary">{{ $item->clicks }}</span>
                            </td>
                            <td class="g_celda_centro">
                                @can('avance-proyecto.editar')
                                    <button wire:click="toggleActivo({{ $item->id }})"
                                        class="g_badge {{ $item->activo ? 'success' : 'error' }}"
                                        title="Click para cambiar estado">
                                        {{ $item->activo ? 'Activo' : 'Inactivo' }}
                                    </button>
                                @endcan
                            </td>
                            <td class="g_celda_centro">
                                @can('avance-proyecto.ver')
                                    <a href="{{ route('erp.avance-proyecto.vista.ver', $item->id) }}" class="g_accion ver"
                                        title="Ver detalle">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                @endcan

                                @can('avance-proyecto.editar')
                                    <a href="{{ route('erp.avance-proyecto.vista.editar', $item->id) }}" class="g_accion editar"
                                        title="Editar">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($items->hasPages())
            <div class="g_paginacion">
                {{ $items->links('vendor.pagination.default-livewire') }}
            </div>
        @endif

        @if ($items->isEmpty())
            <div class="g_vacio">
                <p>{{ $buscar ? 'No se encontraron resultados para "' . $buscar . '"' : 'No hay registros.' }}
                </p>
                <i class="fa-regular fa-face-meh"></i>
            </div>
        @else
            <div class="g_paginacion">
                Mostrando {{ $items->firstItem() ?? 0 }} – {{ $items->lastItem() ?? 0 }}
                de {{ $items->total() }} registros
            </div>
        @endif
    </div>
</div>
