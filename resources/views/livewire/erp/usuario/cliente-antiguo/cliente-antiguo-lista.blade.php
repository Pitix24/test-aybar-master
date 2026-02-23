<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Clientes antiguo (DB2)</h2>

        <div class="cabecera_titulo_botones">
            @can('cliente-antiguo.crear')
                <a href="{{ route('erp.cliente-antiguo.vista.crear') }}" class="g_boton primary">
                    Crear <i class="fa-solid fa-square-plus"></i></a>
            @endcan

            <div class="g_dropdown_contenedor" x-data="{ open: false }">
                <button type="button" class="g_boton success" @click="open = !open">
                    Exportar <i class="fa-solid fa-file-export"></i>
                </button>
                <div class="g_dropdown_menu" x-show="open" @click.away="open = false" x-transition>
                    @can('cliente-antiguo.exportar-filtro')
                        <button type="button" wire:click="exportExcelFiltro" class="g_dropdown_item">
                            <i class="fa-solid fa-filter"></i> Exportar con Filtros (Excel)
                        </button>
                    @endcan
                    @can('cliente-antiguo.exportar-todo')
                        <button type="button" wire:click="exportExcelTodo" class="g_dropdown_item">
                            <i class="fa-solid fa-database"></i> Exportar Todo (Excel)
                        </button>
                    @endcan
                </div>
            </div>

            <button type="button" wire:click="resetFiltros" class="g_boton dark">
                Limpiar <i class="fa-solid fa-rotate-left"></i>
            </button>
        </div>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Buscar (Nombre/DNI/Razón Social)</label>
                    <input type="text" wire:model.live.debounce.500ms="buscar" placeholder="Ingrese nombre o DNI..."
                        autocomplete="off">
                </div>

                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Código Cliente</label>
                    <input type="text" wire:model.live.debounce.500ms="codigo_cliente" placeholder="Ingrese código..."
                        autocomplete="off">
                </div>

                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Registros por página</label>
                    <select wire:model.live="perPage">
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="tabla_contenido">
            <div class="contenedor_tabla">
                <table class="g_tabla">
                    <thead>
                        <tr>
                            <th>Nº</th>
                            <th>Razón Social</th>
                            <th>Código Cliente</th>
                            <th>Nombre</th>
                            <th>Proyecto</th>
                            <th>Etapa</th>
                            <th>N° Lote</th>
                            <th>Estado Lote</th>
                            <th>DNI/RUC</th>
                            <th class="g_celda_centro">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $index => $item)
                            <tr>
                                <td>{{ $items->firstItem() + $index }}</td>
                                <td class="g_resaltar">{{ $item->razon_social }}</td>
                                <td class="g_negrita">{{ $item->codigo_cliente }}</td>
                                <td class="g_celda_wrap">{{ $item->nombre }}</td>
                                <td>
                                    <span class="g_badge g_badge_light">{{ $item->proyecto }}</span>
                                    @if($item->codigo_proyecto)
                                        <br><small class="g_texto_muted">({{ $item->codigo_proyecto }})</small>
                                    @endif
                                </td>
                                <td>{{ $item->etapa }}</td>
                                <td>{{ $item->numero_lote }}</td>
                                <td>
                                    @if($item->estado_lote)
                                        <span
                                            class="g_badge {{ $item->estado_lote == 'VENDIDO' ? 'g_badge_success' : 'g_badge_warning' }}">
                                            {{ $item->estado_lote }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="g_negrita">{{ $item->dni }}</td>
                                <td class="g_celda_centro">
                                    @can('cliente-antiguo.editar')
                                        <a href="{{ route('erp.cliente-antiguo.vista.editar', $item->id) }}"
                                            class="g_accion editar" title="Editar registro">
                                            <i class="fa-solid fa-pencil"></i>
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="g_vacio">
                                        <i class="fa-regular fa-face-grin-wink"></i>
                                        <p>No se encontraron registros en la base de datos antigua.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($items->hasPages())
            <div class="g_paginacion">
                {{ $items->links('vendor.pagination.default-livewire') }}
            </div>
        @endif
    </div>
</div>