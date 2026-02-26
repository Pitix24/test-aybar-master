<div class="g_gap_pagina">
    <x-loading-overlay wire:loading
        wire:target="buscar, perPage, estado_id, unidad_negocio_id, resetFiltros, exportCavali, exportExcelFiltro, exportExcelTodo, gotoPage, nextPage, previousPage"
        message="Cargando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Listado de Envíos CAVALI</h2>

        <div class="cabecera_titulo_botones">
            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Buscar (Fecha Y-m-d / Empresa)</label>
                    <input type="text" wire:model.live.debounce.1300ms="buscar">
                </div>

                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Unidad de Negocio</label>
                    <select wire:model.live="unidad_negocio_id">
                        <option value="">Todas</option>
                        @foreach ($unidades_negocios as $unidad)
                            <option value="{{ $unidad->id }}">{{ $unidad->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Estado</label>
                    <select wire:model.live="estado_id">
                        <option value="">Todos</option>
                        @foreach ($estados as $estado)
                            <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="g_panel">
        <div class="g_tabla_cabecera">
            <div class="g_tabla_cabecera_botones">
                @can('envio-cavali-solicitud.exportar-filtro')
                    <button wire:click="exportExcelFiltro" class="g_boton excel" wire:loading.attr="disabled"
                        wire:target="exportExcelFiltro">
                        <span wire:loading.remove wire:target="exportExcelFiltro">Excel Filtrados <i
                                class="fa-regular fa-file-excel"></i></span>
                        <span wire:loading wire:target="exportExcelFiltro">Generando... <i
                                class="fa-solid fa-spinner fa-spin"></i></span>
                    </button>
                @endcan

                @can('envio-cavali-solicitud.exportar-todo')
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
                        <th class="g_celda_centro">Nº</th>
                        <th>Fecha Corte</th>
                        <th>Unidad de Negocio</th>
                        <th class="g_celda_centro">Solicit.</th>
                        <th class="g_celda_centro">Estado</th>
                        <th>Fecha Envío</th>
                        <th>Archivo</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($items as $item)
                        <tr wire:key="envio-{{ $item->id }}">
                            <td class="g_celda_centro">
                                <span class="g_badge light">#{{ $item->id }}</span>
                            </td>
                            <td class="g_negrita g_inferior">{{ $item->fecha_corte->format('d/m/Y') }}</td>
                            <td class="g_resumir">{{ $item->unidadNegocio?->nombre ?? '—' }}</td>
                            <td class="g_celda_centro">
                                <span class="g_badge light">{{ $item->solicitudes_count }}</span>
                            </td>
                            <td class="g_celda_centro">
                                @if ($item->estado)
                                    <span class="g_badge g_badge_soft" style="color: {{ $item->estado->color ?? '#666' }}">
                                        @if($item->estado->icono) <i class="{{ $item->estado->icono }}"></i> @endif
                                        {{ $item->estado->nombre }}
                                    </span>
                                @else
                                    <span class="g_badge light">Pendiente</span>
                                @endif
                            </td>
                            <td class="g_inferior">{{ $item->enviado_at?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td class="g_resumir g_inferior">{{ $item->archivo_nombre ?? '—' }}</td>
                            <td class="g_celda_acciones g_celda_centro">
                                @can('envio-cavali.detalle')
                                    <a href="{{ route('erp.envio-cavali.vista.detalle', $item->id) }}" class="g_accion ver"
                                        title="Ver Detalle">
                                        <i class="fa-solid fa-eye"></i>
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
                <p>{{ $buscar ? 'No se encontraron resultados para "' . $buscar . '"' : 'No hay envíos disponibles.' }}</p>
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