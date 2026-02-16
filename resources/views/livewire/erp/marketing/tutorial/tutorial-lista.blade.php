<div class="g_gap_pagina">
    <x-loading-overlay wire:loading
        wire:target="buscar, perPage, activo, resetFiltros, exportExcelFiltro, exportExcelTodo, gotoPage, nextPage, previousPage, toggleActivo"
        message="Cargando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Gestión de Tutoriales</h2>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_columna_8">
                    <label>Buscar por título o Video ID</label>
                    <input type="text" wire:model.live.debounce.500ms="buscar" placeholder="Ej: Cómo descargar...">
                </div>
                <div class="g_columna_4">
                    <label>Estado</label>
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
                @can('tutorial.crear')
                    <a href="{{ route('erp.tutorial.vista.crear') }}" class="g_boton guardar">
                        Nuevo Tutorial <i class="fa-solid fa-plus"></i>
                    </a>
                @endcan

                @can('tutorial.exportar-filtro')
                    <button wire:click="exportExcelFiltro" class="g_boton excel" wire:loading.attr="disabled"
                        wire:target="exportExcelFiltro">
                        <span wire:loading.remove wire:target="exportExcelFiltro">Excel Filtrados <i
                                class="fa-regular fa-file-excel"></i></span>
                        <span wire:loading wire:target="exportExcelFiltro">Generando... <i
                                class="fa-solid fa-spinner fa-spin"></i></span>
                    </button>
                @endcan

                @can('tutorial.exportar-todo')
                    <button wire:click="exportExcelTodo" class="g_boton dark" wire:loading.attr="disabled"
                        wire:target="exportExcelTodo">
                        <span wire:loading.remove wire:target="exportExcelTodo">Excel Todo <i
                                class="fa-solid fa-file-export"></i></span>
                        <span wire:loading wire:target="exportExcelTodo">Generando... <i
                                class="fa-solid fa-spinner fa-spin"></i></span>
                    </button>
                @endcan

                <button wire:click="resetFiltros" class="g_boton danger">
                    Limpiar <i class="fa-solid fa-rotate-left"></i>
                </button>
            </div>

            <div class="g_tabla_cabecera_filtro formulario">
                <div>
                    <label>Mostrar</label>
                    <select wire:model.live="perPage">
                        <option value="10">10</option>
                        <option value="25">25</option>
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
                        <th>Tutorial</th>
                        <th>Video ID</th>
                        <th class="g_celda_centro">Clicks</th>
                        <th class="g_celda_centro">Estado</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tutoriales as $tutorial)
                        <tr wire:key="tutorial-{{ $tutorial->id }}">
                            <td class="g_celda_centro">
                                <span class="g_badge light">{{ $tutorial->orden }}</span>
                            </td>
                            <td>
                                <div class="g_negrita">{{ $tutorial->titulo }}</div>
                                <div class="g_texto_secundario g_resumir">{{ Str::limit($tutorial->descripcion, 60) }}</div>
                            </td>
                            <td><code>{{ $tutorial->video_id }}</code></td>
                            <td class="g_celda_centro">
                                <span class="g_badge primary">{{ $tutorial->clicks }}</span>
                            </td>
                            <td class="g_celda_centro">
                                <button wire:click="toggleActivo({{ $tutorial->id }})"
                                    class="g_badge {{ $tutorial->activo ? 'success' : 'error' }}"
                                    title="Click para cambiar estado">
                                    {{ $tutorial->activo ? 'Activo' : 'Inactivo' }}
                                </button>
                            </td>
                            <td class="g_celda_acciones g_celda_centro">
                                @can('tutorial.ver')
                                    <a href="{{ route('erp.tutorial.vista.ver', $tutorial->id) }}" class="g_accion ver"
                                        title="Ver detalle">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                @endcan
                                @can('tutorial.editar')
                                    <a href="{{ route('erp.tutorial.vista.editar', $tutorial->id) }}" class="g_accion editar"
                                        title="Editar">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="g_celda_centro">No se encontraron tutoriales.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($tutoriales->hasPages())
            <div class="g_paginacion">
                {{ $tutoriales->links() }}
            </div>
        @endif

        @if (!$tutoriales->isEmpty())
            <div class="g_paginacion">
                Mostrando {{ $tutoriales->firstItem() }} – {{ $tutoriales->lastItem() }}
                de {{ $tutoriales->total() }} registros
            </div>
        @endif
    </div>
</div>