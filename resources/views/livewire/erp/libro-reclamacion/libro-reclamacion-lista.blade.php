<div class="g_gap_pagina">
    <x-loading-overlay wire:loading
        wire:target="buscar, estado_filtro, clasificacion, gestor_id, unidad_negocio_id, proyecto_id, desde, hasta, perPage, resetFiltros, gotoPage, nextPage, previousPage"
        message="Cargando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Listado de Tickets Libro Reclamacion</h2>

        <div class="cabecera_titulo_botones">
            @if (config('libro_reclamacion.crear_erp_habilitado'))
            @can('libro-reclamacion.crear')
            <a href="{{ route('erp.libro-reclamacion.vista.crear') }}" class="g_boton primary">
                Crear <i class="fa-solid fa-square-plus"></i>
            </a>
            @endcan
            @endif
        </div>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Cliente</label>
                    <input type="text" wire:model.live.debounce.1000ms="buscar"
                        placeholder="Codigo, documento, cliente o correo">
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Estado</label>
                    <select wire:model.live="estado_filtro">
                        <option value="">Todos</option>
                        @foreach ($estadosTicket as $estado)
                        <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                        @endforeach
                        <option value="NO_PROCEDE">No Procede</option>
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Clasificacion</label>
                    <select wire:model.live="clasificacion">
                        <option value="">Todos</option>
                        <option value="PROCEDE">Procede</option>
                        <option value="NO_PROCEDE">No procede</option>
                        <option value="PENDIENTE_REVISION">Pendiente verificacion</option>
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Gestor</label>
                    <select wire:model.live="gestor_id">
                        <option value="">Todos</option>
                        @foreach ($gestores as $gestor)
                        <option value="{{ $gestor->id }}">{{ $gestor->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Unidad de negocio</label>
                    <select wire:model.live="unidad_negocio_id">
                        <option value="">Todos</option>
                        @foreach ($unidades as $unidad)
                        <option value="{{ $unidad->id }}">{{ $unidad->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Proyecto</label>
                    <select wire:model.live="proyecto_id">
                        <option value="">Todos</option>
                        @foreach ($proyectos as $proyecto)
                        <option value="{{ $proyecto->id }}">{{ $proyecto->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Desde</label>
                    <input type="date" wire:model.live="desde">
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Hasta</label>
                    <input type="date" wire:model.live="hasta">
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Mostrar</label>
                    <select wire:model.live="perPage">
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    @include('livewire.erp.libro-reclamacion.partials.kpis', ['stats' => $stats])

    <div class="g_panel">
        <div class="g_tabla_cabecera">
            <div class="g_tabla_cabecera_botones">
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
                        <th class="g_celda_centro">Codigo</th>
                        <th>Cliente</th>
                        <th>Proyecto</th>
                        <th class="g_celda_centro">Estado</th>
                        <th class="g_celda_centro">Clasif.</th>
                        <th>Gestor</th>
                        <th class="g_celda_centro">Ticket</th>
                        <th class="g_celda_centro">Fecha</th>
                        <th class="g_celda_centro" style="width: 80px;">Menor</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $index => $item)
                    <tr wire:key="libro-ticket-{{ $item->ticket }}">
                        <td class="g_celda_centro">{{ $items->firstItem() + $index }}</td>
                        <td class="g_celda_centro g_resaltar">{{ $item->codigo_ticket }}</td>
                        <td class="g_negrita g_resumir">{{ $item->cliente_nombre ?: 'N/D' }}</td>
                        <td class="g_resumir g_inferior">{{ $item->proyecto?->nombre ?: 'N/D' }}</td>
                        <td class="g_celda_centro">
                            <span class="g_badge g_badge_soft" style="color: {{ $item->estadoActualColor() }};">
                                {{ $item->estadoActualNombre() }}
                            </span>
                        </td>
                        <td class="g_celda_centro">
                            @if ($item->clasificacion === 'NO_PROCEDE')
                            <span class="g_badge danger">{{ str_replace('_', ' ', $item->clasificacion) }}</span>
                            @elseif ($item->clasificacion === 'PROCEDE')
                            <span class="g_badge success">{{ str_replace('_', ' ', $item->clasificacion) }}</span>
                            @else
                            <span class="g_badge warning">{{ $item->clasificacion === 'PENDIENTE_REVISION' ? 'PENDIENTE
                                VERIFICACION' : str_replace('_', ' ', $item->clasificacion) }}</span>
                            @endif
                        </td>
                        <td class="g_negrita g_resumir">{{ $item->gestor?->name ?: 'N/D' }}</td>
                        <td class="g_celda_centro">
                            @can('ticket.ver')
                            @if ($item->ticketRelacionado)
                            <a href="{{ route('erp.ticket.vista.ver', $item->ticketRelacionado->id) }}"
                                class="g_accion ver" title="Ver Ticket">
                                <i class="fa-solid fa-ticket"></i>
                            </a>
                            @else
                            <span class="g_badge light">-</span>
                            @endif
                            @else
                            <span class="g_badge light">{{ $item->ticket_id ?: '-' }}</span>
                            @endcan
                        </td>
                        <td class="g_inferior g_celda_centro">{{ optional($item->created_at)->format('d/m/Y H:i') }}
                        </td>
                        <td class="g_celda_centro">
                            @if ($item->es_cliente_menor)
                            <span class="g_badge danger">
                                <i class="fa-solid fa-triangle-exclamation"></i> Menor
                            </span>
                            @else
                            <span class="g_badge light">Mayor</span>
                            @endif
                        </td>
                        <td class="g_celda_acciones g_celda_centro">
                            @can('libro-reclamacion.ver')
                            <a href="{{ route('erp.libro-reclamacion.vista.ver', $item->ticket) }}" class="g_accion ver"
                                title="Ver">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            @endcan
                        </td>
                        <td class="g_celda_acciones g_celda_centro">
                            @can('libro-reclamacion.editar')
                            <a href="{{ route('erp.libro-reclamacion.vista.editar', $item->ticket) }}" class="g_accion editar"
                                title="Editar">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            @endcan
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="g_celda_centro">No hay registros para mostrar.</td>
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
