<div class="g_gap_pagina">
    <x-loading-overlay wire:loading message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Bancarización: <span>{{ $evento->nombre }}</span></h2>

        <div class="cabecera_titulo_botones">
            @can('entrega-fest.ver-panel')
                <a href="{{ route('erp.entrega-fest.vista.panel', $evento->id) }}" class="g_boton info">
                    <i class="fa-solid fa-grip"></i> Panel de Gestión
                </a>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Buscar por Cliente (DNI, Nombres) o Cuota</label>
                    <input type="text" wire:model.live.debounce.400ms="buscar"
                        placeholder="Escriba aquí para buscar...">
                </div>

                <div class="g_margin_bottom_10 g_columna_3">
                    <label>Proyecto</label>
                    <select wire:model.live="proyecto_id">
                        <option value="">Todos los Proyectos</option>
                        @foreach ($proyectos as $proy)
                            <option value="{{ $proy->id }}">{{ $proy->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_3">
                    <label>Estado</label>
                    <select wire:model.live="estado">
                        <option value="">Todos los Estados</option>
                        @foreach (App\Models\ProspectoBancarizacionEntregaFest::ESTADO as $key => $val)
                            <option value="{{ $key }}">{{ $val['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="g_panel">
        <div class="g_tabla_cabecera">
            <div class="g_tabla_cabecera_botones">
                @can('prospecto.exportar-filtro')
                    <button wire:click="exportExcelFiltro" class="g_boton excel" wire:loading.attr="disabled"
                        wire:target="exportExcelFiltro">
                        <span wire:loading.remove wire:target="exportExcelFiltro">Excel Filtrados <i
                                class="fa-regular fa-file-excel"></i></span>
                        <span wire:loading wire:target="exportExcelFiltro">Generando... <i
                                class="fa-solid fa-spinner fa-spin"></i></span>
                    </button>
                @endcan

                @can('prospecto.exportar-todo')
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
                        <th class="g_celda_centro">#</th>
                        <th>DNI</th>
                        <th>Cliente</th>
                        <th>Proyecto</th>
                        <th>Mz-Lt</th>
                        <th>Cuota</th>
                        <th>Importe</th>
                        <th class="g_celda_centro">Fecha Depósito Real</th>
                        <th class="g_celda_centro">Estado</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($items as $index => $item)
                        <tr wire:key="banc-{{ $item->id }}">
                            <td class="g_celda_centro text-muted">{{ $items->firstItem() + $index }}</td>
                            <td>{{ $item->prospecto->dni }}</td>
                            <td>
                                <div class="g_negrita">{{ $item->prospecto->nombres }}</div>
                            </td>
                            <td>{{ $item->prospecto->proyecto->nombre ?? 'N/A' }}</td>
                            <td>{{ $item->prospecto->manzana }}-{{ $item->prospecto->lote }}</td>
                            <td><span class="g_badge info">{{ $item->cuota }}</span></td>
                            <td class="g_negrita">S/ {{ number_format($item->importe, 2) }}</td>
                            <td class="g_celda_centro">{{ $item->fecha_deposito_real->format('d/m/Y') }}</td>
                            <td class="g_celda_centro">
                                <span class="g_badge g_badge_soft"
                                    style="background-color: {{ $item->badgeEstado() }}20; color: {{ $item->badgeEstado() }};">
                                    {{ App\Models\ProspectoBancarizacionEntregaFest::ESTADO[$item->estado]['label'] ?? $item->estado }}
                                </span>
                            </td>
                            <td class="g_celda_acciones g_celda_centro">
                                @can('prospecto.editar')
                                    <a href="{{ route('erp.entrega-fest.prospecto.editar', [$evento->id, $item->prospecto_entrega_fest_id]) }}"
                                        class="g_accion editar" title="Ir a evaluar prospecto">
                                        <i class="fa-solid fa-user-pen"></i>
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
                <p>{{ $buscar ? 'No se encontraron registros para "' . $buscar . '"' : 'No hay registros de bancarización para este evento.' }}
                </p>
                <i class="fa-solid fa-money-bill-transfer"></i>
            </div>
        @else
            <div class="g_paginacion">
                Mostrando {{ $items->firstItem() ?? 0 }} – {{ $items->lastItem() ?? 0 }}
                de {{ $items->total() }} registros
            </div>
        @endif
    </div>
</div>