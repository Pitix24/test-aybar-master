<div class="g_gap_pagina">
    <x-loading-overlay wire:loading
        wire:target="buscar, perPage, unidad_negocio_id, proyecto_id, resetFiltros, gotoPage, nextPage, previousPage"
        message="Cargando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Solicitudes de letras digitales portal cliente</h2>

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
                    <label>Cliente/DNI/Nombres</label>
                    <input type="text" wire:model.live.debounce.1300ms="buscar" id="buscar" name="buscar"
                        placeholder="Buscar...">
                </div>

                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Empresa</label>
                    <select wire:model.live="unidad_negocio_id">
                        <option value="">TODAS</option>
                        @foreach ($empresas as $empresa)
                            <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Proyecto</label>
                    <select wire:model.live="proyecto_id">
                        <option value="">TODOS</option>
                        @foreach ($proyectos as $proyecto)
                            <option value="{{ $proyecto->id }}">{{ $proyecto->nombre }}</option>
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
                        <th>Razón S.</th>
                        <th>Proyecto</th>
                        <th>Etapa</th>
                        <th>Mz.</th>
                        <th>Lt.</th>
                        <th>N° Cuota</th>
                        <th>Cliente</th>
                        <th>DNI</th>
                        <th>Fecha</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($items as $index => $item)
                        <tr>
                            <td class="g_celda_centro">{{ $items->firstItem() + $index }}</td>
                            <td class="g_resumir">{{ $item->unidadNegocio?->nombre ?? '—' }}</td>
                            <td class="g_resumir">{{ $item->proyecto?->nombre ?? '—' }}</td>
                            <td class="g_resumir">{{ $item->etapa }}</td>
                            <td class="g_resumir">{{ $item->manzana }}</td>
                            <td class="g_resumir">{{ $item->lote }}</td>
                            <td class="g_resumir">{{ $item->numero_cuota }}</td>
                            <td class="g_resaltar g_resumir">{{ $item->userCliente?->name ?? '—' }}</td>
                            <td> {{ $item->userCliente?->perfilCliente?->dni ?? '—' }}</td>
                            <td>{{ $item->created_at->format('Y-m-d H:i') }}</td>
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
                <p>No se encontraron solicitudes.</p>
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