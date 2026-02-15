<div class="g_gap_pagina">
    <x-loading-overlay wire:loading
        wire:target="buscar, perPage, desde, hasta, resetFiltros, gotoPage, nextPage, previousPage, exportExcel, exportExcelTodo"
        message="Cargando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Lista de Roles</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.rol.vista.crear') }}" class="g_boton primary">
                Crear <i class="fa-solid fa-square-plus"></i></a>
        </div>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Rol(Nombre/ID)</label>
                    <input type="text" wire:model.live.debounce.1300ms="buscar">
                </div>
                <div class="g_margin_bottom_10 g_columna_3">
                    <label>Desde</label>
                    <input type="date" wire:model.live="desde">
                </div>
                <div class="g_margin_bottom_10 g_columna_3">
                    <label>Hasta</label>
                    <input type="date" wire:model.live="hasta">
                </div>
            </div>
        </div>
    </div>

    <div class="g_panel">
        <div class="g_tabla_cabecera">
            <div class="g_tabla_cabecera_botones">
                @can('rol.exportar')
                    <button wire:click="exportExcel" class="g_boton excel" wire:loading.attr="disabled"
                        wire:target="exportExcel">
                        <span wire:loading.remove wire:target="exportExcel">Filtrados <i
                                class="fa-regular fa-file-excel"></i></span>
                        <span wire:loading wire:target="exportExcel">Generando... <i
                                class="fa-solid fa-spinner fa-spin"></i></span>
                    </button>

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
                        <th class="g_celda_centro">N°</th>
                        <th>Nombre del Rol</th>
                        <th>Guard</th>
                        <th>Permisos</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($items as $index => $item)
                        <tr>
                            <td class="g_celda_centro">{{ $items->firstItem() + $index }}</td>
                            <td class="g_resaltar">{{ $item->name }}</td>
                            <td>{{ $item->guard_name }}</td>
                            <td>
                                <span class="g_badge light">
                                    {{ $item->permissions_count }} permisos
                                </span>
                            </td>

                            <td class="g_celda_centro">
                                @can('rol.editar')
                                    <a href="{{ route('erp.rol.vista.editar', $item->id) }}" class="g_accion editar"
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
                <p>{{ $buscar ? 'No se encontraron resultados para "' . $buscar . '"' : 'No hay items registrados.' }}</p>
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