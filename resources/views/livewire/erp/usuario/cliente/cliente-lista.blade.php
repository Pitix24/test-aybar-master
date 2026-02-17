<div class="g_gap_pagina">
    <x-loading-overlay wire:loading
        wire:target="buscar, email, activo, verificado, tratamiento, politica, desde, hasta, perPage, resetFiltros, exportExcelFiltro, exportExcelTodo"
        message="Cargando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Gestión de Clientes Portal</h2>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Cliente (Nombre o DNI)</label>
                    <input type="text" wire:model.live.debounce.1300ms="buscar">
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Fecha creación inicio</label>
                    <input type="date" wire:model.live="desde">
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Fecha creación fin</label>
                    <input type="date" wire:model.live="hasta">
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Activo</label>
                    <select wire:model.live="activo">
                        <option value="">Todos</option>
                        <option value="1">Activos</option>
                        <option value="0">Inactivos</option>
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Email verificado</label>
                    <select wire:model.live="verificado">
                        <option value="">Todos</option>
                        <option value="1">Sí</option>
                        <option value="0">No</option>
                    </select>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Email</label>
                    <input type="text" wire:model.live.debounce.1300ms="email">
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Tratamiento D.P.</label>
                    <select wire:model.live="tratamiento">
                        <option value="">Todos</option>
                        <option value="1">Autorizado</option>
                        <option value="0">No Autorizado</option>
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Política Comercial</label>
                    <select wire:model.live="politica">
                        <option value="">Todos</option>
                        <option value="1">Autorizado</option>
                        <option value="0">No Autorizado</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="g_panel">
        <div class="g_tabla_cabecera">
            <div class="g_tabla_cabecera_botones">
                @can('cliente.exportar-filtro')
                    <button wire:click="exportExcelFiltro" class="g_boton excel" wire:loading.attr="disabled"
                        wire:target="exportExcelFiltro">
                        <span wire:loading.remove wire:target="exportExcelFiltro">Exportar Filtrados <i
                                class="fa-regular fa-file-excel"></i></span>
                        <span wire:loading wire:target="exportExcelFiltro">Generando... <i
                                class="fa-solid fa-spinner fa-spin"></i></span>
                    </button>
                @endcan

                @can('cliente.exportar-todo')
                    <button wire:click="exportExcelTodo" class="g_boton dark" wire:loading.attr="disabled"
                        wire:target="exportExcelTodo">
                        <span wire:loading.remove wire:target="exportExcelTodo">Exportar Todo <i
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
                        <th class="g_celda_centro">Nº</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>DNI</th>
                        <th class="g_celda_centro">Fecha creación</th>
                        <th class="g_celda_centro">Verificado</th>
                        <th class="g_celda_centro">Trat. D.P.</th>
                        <th class="g_celda_centro">P. Com.</th>
                        <th class="g_celda_centro">Estado</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($items as $index => $item)
                        <tr>
                            <td class="g_celda_centro">{{ $items->firstItem() + $index }}</td>
                            <td class="g_resaltar">{{ $item->name }}</td>
                            <td>{{ $item->email }}</td>
                            <td class="g_resaltar">{{ $item->dni ?? '-' }}</td>
                            <td class="g_celda_centro">
                                {{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-' }}
                            </td>

                            <td class="g_celda_centro">
                                @if ($item->email_verified_at)
                                    <span class="g_badge info">Sí</span>
                                @else
                                    <span class="g_badge danger">No</span>
                                @endif
                            </td>

                            <td class="g_celda_centro">
                                <span class="g_badge {{ $item->politica_uno ? 'info' : 'light' }}">
                                    {{ $item->politica_uno ? 'SÍ' : 'NO' }}
                                </span>
                            </td>

                            <td class="g_celda_centro">
                                <span class="g_badge {{ $item->politica_dos ? 'info' : 'light' }}">
                                    {{ $item->politica_dos ? 'SÍ' : 'NO' }}
                                </span>
                            </td>

                            <td class="g_celda_centro">
                                @if ($item->activo)
                                    <span class="g_badge success">Activo</span>
                                @else
                                    <span class="g_badge danger">Inactivo</span>
                                @endif
                            </td>

                            <td class="g_celda_centro">
                                @can('cliente.ver')
                                    <a href="{{ route('erp.cliente.vista.ver', $item->id) }}" class="g_accion ver" title="Ver">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                @endcan

                                @can('cliente.editar')
                                    <a href="{{ route('erp.cliente.vista.editar', $item->id) }}" class="g_accion editar"
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
                <p>{{ $buscar ? 'No se encontraron resultados para "' . $buscar . '"' : 'No hay clientes registrados.' }}
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