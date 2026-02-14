<div class="g_gap_pagina">
    <x-loading-overlay wire:loading
        wire:target="buscar, tipo_solicitud_id, activo, perPage, resetFiltros, gotoPage, nextPage, previousPage, exportExcel"
        message="Cargando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Lista de Sub-Tipos de Solicitud</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.sub-tipo-solicitud.vista.crear') }}" class="g_boton g_boton_primary">
                Crear <i class="fa-solid fa-square-plus"></i></a>
        </div>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_3">
                    <label>Nombre</label>
                    <input type="text" wire:model.live.debounce.1300ms="buscar" id="buscar" name="buscar">
                </div>

                <div class="g_margin_bottom_10 g_columna_3">
                    <label>Tipo de Solicitud</label>
                    <select wire:model.live="tipo_solicitud_id">
                        <option value="">Todos</option>
                        @foreach ($tipos_solicitud as $tipo)
                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Activo</label>
                    <select wire:model.live="activo">
                        <option value="">Todos</option>
                        <option value="1">Si</option>
                        <option value="0">No</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="g_panel">
        <div class="g_tabla_cabecera">
            <div class="g_tabla_cabecera_botones">
                <button wire:click="exportExcel" class="g_boton g_boton_excel" wire:loading.attr="disabled"
                    wire:target="exportExcel">
                    <span wire:loading.remove wire:target="exportExcel">Excel <i
                            class="fa-regular fa-file-excel"></i></span>
                    <span wire:loading wire:target="exportExcel">Exportando... <i
                            class="fa-solid fa-spinner fa-spin"></i></span>
                </button>

                <button wire:click="resetFiltros" class="g_boton g_boton_danger">
                    Refresh Filtros <i class="fa-solid fa-rotate-left"></i>
                </button>
            </div>

            <div class="g_tabla_cabecera_filtro formulario">
                <select wire:model.live="perPage">
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>

        <div class="g_contenedor_tabla">
            <table class="g_tabla">
                <thead>
                    <tr>
                        <th class="g_celda_centro">Nº</th>
                        <th>Tipo Solicitud</th>
                        <th>Sub-Tipo</th>
                        <th class="g_celda_centro">Tiempo Solución (H)</th>
                        <th class="g_celda_centro">Estado</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($items as $index => $item)
                        <tr>
                            <td class="g_celda_centro">{{ $items->firstItem() + $index }}</td>
                            <td class="g_resaltar g_celda_wrap">{{ $item->tipoSolicitud->nombre }}</td>
                            <td class="g_resaltar g_celda_wrap">{{ $item->nombre }}</td>
                            <td class="g_celda_centro">
                                @if($item->tiempo_solucion)
                                    {{ $item->tiempo_solucion }}
                                @else
                                    <span class="g_inferior">Heredado</span>
                                @endif
                            </td>
                            <td class="g_celda_centro">
                                @if($item->activo)
                                    <span class="g_badge g_badge_success">Activo</span>
                                @else
                                    <span class="g_badge g_badge_danger">Inactivo</span>
                                @endif
                            </td>

                            <td class="g_celda_acciones g_celda_centro">
                                <a href="{{ route('erp.sub-tipo-solicitud.vista.editar', $item->id) }}"
                                    class="g_accion_editar" title="Editar">
                                    <i class="fa-solid fa-pencil"></i>
                                </a>
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
                <p>{{ $buscar ? 'No se encontraron resultados para "' . $buscar . '"' : 'No hay items disponibles.' }}</p>
                <i class="fa-regular fa-face-grin-wink"></i>
            </div>
        @else
            <div class="g_paginacion">
                Mostrando {{ $items->firstItem() ?? 0 }} – {{ $items->lastItem() ?? 0 }}
                de {{ $items->total() }} registros
            </div>
        @endif
    </div>
</div>