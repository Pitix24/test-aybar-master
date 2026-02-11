<div class="g_gap_pagina">
    <x-loading-overlay wire:loading
        wire:target="buscar, perPage, estado, unidad_negocio_id, resetFiltros, gotoPage, nextPage, previousPage"
        message="Cargando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Envíos CAVALI</h2>

        <div class="cabecera_titulo_botones">
            <button wire:click="resetFiltros" class="g_boton g_boton_danger">
                Refresh Filtros <i class="fa-solid fa-rotate-left"></i>
            </button>
        </div>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Buscar (Fecha/Empresa)</label>
                    <input type="text" wire:model.live.debounce.1300ms="buscar" id="buscar" name="buscar" placeholder="Ej: 2026-01-29">
                </div>

                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Estado</label>
                    <select wire:model.live="estado">
                        <option value="">TODOS</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="enviado">Enviado</option>
                        <option value="observado">Observado</option>
                        <option value="aceptado">Aceptado</option>
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Unidad de Negocio</label>
                    <select wire:model.live="unidad_negocio_id">
                        <option value="">TODAS</option>
                        @foreach ($unidadesNegocio as $unidad)
                            <option value="{{ $unidad->id }}">{{ $unidad->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="g_panel">
        <div class="g_tabla_cabecera">
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
                        <th>Fecha Corte</th>
                        <th>Unidad de Negocio</th>
                        <th>Estado</th>
                        <th class="g_celda_centro">Solicitudes</th>
                        <th>Fecha Envío</th>
                        <th>Archivo</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($items as $index => $item)
                        <tr>
                            <td class="g_celda_centro">{{ $items->firstItem() + $index }}</td>
                            <td class="g_negrita">{{ $item->fecha_corte->format('d/m/Y') }}</td>
                            <td class="g_resumir">{{ $item->unidadNegocio?->nombre ?? '—' }}</td>
                            <td>
                                @php
                                    $badgeClass = match ($item->estado) {
                                        'pendiente' => 'g_badge_warning',
                                        'enviado' => 'g_badge_info',
                                        'observado' => 'g_badge_danger',
                                        'aceptado' => 'g_badge_success',
                                        default => 'g_badge_secondary'
                                    };
                                @endphp
                                <span class="g_badge {{ $badgeClass }}">{{ ucfirst($item->estado ?? 'pendiente') }}</span>
                            </td>
                            <td class="g_celda_centro g_negrita">{{ $item->solicitudes_count }}</td>
                            <td>{{ $item->enviado_at?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td class="g_resumir">{{ $item->archivo_nombre ?? '—' }}</td>
                            <td class="g_celda_acciones g_celda_centro">
                                <a href="{{ route('erp.envio-cavali-solicitud.vista.editar', $item->id) }}"
                                    class="g_accion_editar" title="Ver Detalle">
                                    <i class="fa-solid fa-eye"></i>
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
                <p>No se encontraron envíos.</p>
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
