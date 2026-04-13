<div class="g_gap_pagina">
    <x-loading-overlay wire:loading
        wire:target="buscar, estado_legal, clasificacion, gestor_id, unidad_negocio_id, proyecto_id, desde, hasta, perPage, resetFiltros, gotoPage, nextPage, previousPage"
        message="Cargando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Tickets Libro Reclamacion</h2>

        <div class="cabecera_titulo_botones">
            @can('ticket-libro-reclamacion.crear')
                <a href="{{ route('erp.libro-reclamacion.vista.crear') }}" class="g_boton primary">
                    Crear <i class="fa-solid fa-square-plus"></i>
                </a>
            @endcan
        </div>
    </div>

    <div class="g_panel formulario">
        <div class="g_fila">
            <div class="g_columna_3">
                <label>Buscar</label>
                <input type="text" wire:model.live.debounce.1000ms="buscar" placeholder="Codigo, documento, cliente o correo">
            </div>

            <div class="g_columna_2">
                <label>Estado legal</label>
                <select wire:model.live="estado_legal">
                    <option value="">Todos</option>
                    <option value="NUEVO">Nuevo</option>
                    <option value="EN_GESTION">En gestion</option>
                    <option value="OBSERVADO">Observado</option>
                    <option value="RESUELTO">Resuelto</option>
                    <option value="NO_PROCEDE">No procede</option>
                    <option value="CERRADO">Cerrado</option>
                </select>
            </div>

            <div class="g_columna_2">
                <label>Clasificacion</label>
                <select wire:model.live="clasificacion">
                    <option value="">Todos</option>
                    <option value="PROCEDE">Procede</option>
                    <option value="NO_PROCEDE">No procede</option>
                    <option value="PENDIENTE_REVISION">Pendiente revision</option>
                </select>
            </div>

            <div class="g_columna_2">
                <label>Gestor</label>
                <select wire:model.live="gestor_id">
                    <option value="">Todos</option>
                    @foreach ($gestores as $gestor)
                        <option value="{{ $gestor->id }}">{{ $gestor->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="g_columna_3">
                <label>Unidad de negocio</label>
                <select wire:model.live="unidad_negocio_id">
                    <option value="">Todos</option>
                    @foreach ($unidades as $unidad)
                        <option value="{{ $unidad->id }}">{{ $unidad->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="g_columna_3">
                <label>Proyecto</label>
                <select wire:model.live="proyecto_id">
                    <option value="">Todos</option>
                    @foreach ($proyectos as $proyecto)
                        <option value="{{ $proyecto->id }}">{{ $proyecto->nombre }}</option>
                    @endforeach
                </select>
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
                <label>Mostrar</label>
                <select wire:model.live="perPage">
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>

            <div class="g_columna_2" style="display:flex;align-items:flex-end;">
                <button wire:click="resetFiltros" type="button" class="g_boton danger">
                    Limpiar <i class="fa-solid fa-rotate-left"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="g_panel">
        <div class="g_contenedor_tabla">
            <table class="g_tabla">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Codigo</th>
                        <th>Cliente</th>
                        <th>Proyecto</th>
                        <th>Estado</th>
                        <th>Clasificacion</th>
                        <th>Gestor</th>
                        <th>Fecha</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $index => $item)
                        <tr wire:key="libro-ticket-{{ $item->id }}">
                            <td>{{ $items->firstItem() + $index }}</td>
                            <td class="g_resaltar">{{ $item->codigo }}</td>
                            <td>{{ $item->cliente_nombre ?: $item->cliente?->name ?: 'N/D' }}</td>
                            <td>{{ $item->proyecto?->nombre ?: 'N/D' }}</td>
                            <td>
                                <span class="g_badge info">{{ str_replace('_', ' ', $item->estado_legal) }}</span>
                            </td>
                            <td>
                                @if ($item->clasificacion === 'NO_PROCEDE')
                                    <span class="g_badge danger">{{ str_replace('_', ' ', $item->clasificacion) }}</span>
                                @elseif ($item->clasificacion === 'PROCEDE')
                                    <span class="g_badge success">{{ str_replace('_', ' ', $item->clasificacion) }}</span>
                                @else
                                    <span class="g_badge warning">{{ str_replace('_', ' ', $item->clasificacion) }}</span>
                                @endif
                            </td>
                            <td>{{ $item->gestor?->name ?: 'N/D' }}</td>
                            <td>{{ optional($item->created_at)->format('d/m/Y H:i') }}</td>
                            <td class="g_celda_acciones g_celda_centro centro">
                                @can('ticket-libro-reclamacion.ver')
                                    <a href="{{ route('erp.libro-reclamacion.vista.ver', $item->id) }}" class="g_accion ver" title="Ver">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                @endcan

                                @can('ticket-libro-reclamacion.editar')
                                    <a href="{{ route('erp.libro-reclamacion.vista.editar', $item->id) }}" class="g_accion editar" title="Editar">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="g_celda_centro">No hay registros para mostrar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($items->hasPages())
            <div class="g_paginacion">
                {{ $items->links('vendor.pagination.default-livewire') }}
            </div>
        @endif
    </div>
</div>
