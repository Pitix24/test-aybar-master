<div class="g_gap_pagina">
    <x-loading-overlay wire:loading
        wire:target="buscar, activo, perPage, desde, hasta, resetFiltros, exportExcelFiltro, exportExcelTodo, gotoPage, nextPage, previousPage"
        message="Cargando información..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Motivos de Cita</h2>

        <div class="cabecera_titulo_botones">
            @can('motivo-cita.crear')
                <a href="{{ route('erp.motivo-cita.vista.crear') }}" class="g_boton primary">
                    Crear <i class="fa-solid fa-square-plus"></i></a>
            @endcan
        </div>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_columna_2">
                    <label>Buscar (Nombre o ID)</label>
                    <input type="text" wire:model.live.debounce.1300ms="buscar">
                </div>

                <div class="g_columna_2">
                    <label>Desde</label>
                    <input type="date" wire:model.live="desde">
                </div>

                <div class="g_columna_2">
                    <label>Hasta</label>
                    <input type="date" wire:model.live="hasta">
                </div>

                <div class="g_columna_2">
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
                @can('motivo-cita.exportar-filtro')
                    <button wire:click="exportExcelFiltro" class="g_boton excel" wire:loading.attr="disabled"
                        wire:target="exportExcelFiltro">
                        <span wire:loading.remove wire:target="exportExcelFiltro">Excel Filtrados <i
                                class="fa-regular fa-file-excel"></i></span>
                        <span wire:loading wire:target="exportExcelFiltro">Generando... <i
                                class="fa-solid fa-spinner fa-spin"></i></span>
                    </button>
                @endcan

                @can('motivo-cita.exportar-todo')
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
                        <th class="g_celda_centro">N°</th>
                        <th class="g_celda_centro">Icono</th>
                        <th>Nombre</th>
                        <th>Color</th>
                        <th class="g_celda_centro">Estado</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($items as $index => $item)
                        <tr wire:key="motivo-{{ $item->id }}">
                            <td class="g_celda_centro">{{ $items->firstItem() + $index }}</td>
                            <td class="g_celda_centro">
                                <i class="{{ $item->icono ?? 'fa-solid fa-circle' }}"
                                    style="color: {{ $item->color ?? '#64748b' }};"></i>
                            </td>
                            <td class="g_resaltar">{{ $item->nombre }}</td>
                            <td>
                                <span class="g_badge g_badge_soft" style="color: {{ $item->color }};">
                                    {{ strtoupper($item->color) }}
                                </span>
                            </td>
                            <td class="g_celda_centro">
                                @if($item->activo)
                                    <span class="g_badge success">Activo</span>
                                @else
                                    <span class="g_badge danger">Inactivo</span>
                                @endif
                            </td>

                            <td class="g_celda_acciones g_celda_centro centro">
                                @can('motivo-cita.ver')
                                    <a href="{{ route('erp.motivo-cita.vista.ver', $item->id) }}" class="g_accion ver"
                                        title="Ver detalle">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                @endcan

                                @can('motivo-cita.editar')
                                    <a href="{{ route('erp.motivo-cita.vista.editar', $item->id) }}" class="g_accion editar"
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
                <p>{{ $buscar ? 'No se encontraron resultados para "' . $buscar . '"' : 'No hay registros.' }}</p>
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