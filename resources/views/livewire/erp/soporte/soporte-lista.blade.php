<div class="g_gap_pagina">
    <x-loading-overlay wire:loading
        wire:target="buscar, tipo_id, estado_id, prioridad_id, gestor_id, desde, hasta, perPage, resetFiltros, gotoPage, nextPage, previousPage"
        message="Cargando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Listado de Tickets de Soporte</h2>

        <div class="cabecera_titulo_botones">
            @can('soporte.crear')
                <a href="{{ route('erp.soporte.vista.crear') }}" class="g_boton primary">
                    Nuevo Ticket <i class="fa-solid fa-square-plus"></i>
                </a>
            @endcan
        </div>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_3">
                    <label>Buscar</label>
                    <input type="text" wire:model.live.debounce.600ms="buscar" placeholder="Código, título...">
                </div>

                <div class="g_margin_bottom_10 g_columna_3">
                    <label>Tipo</label>
                    <select wire:model.live="tipo_id">
                        <option value="">Todos</option>
                        @foreach ($tipos as $t)
                            <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_3">
                    <label>Estado</label>
                    <select wire:model.live="estado_id">
                        <option value="">Todos</option>
                        @foreach ($estados as $e)
                            <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_3">
                    <label>Prioridad</label>
                    <select wire:model.live="prioridad_id">
                        <option value="">Todas</option>
                        @foreach ($prioridades as $p)
                            <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_3">
                    <label>Gestor</label>
                    <select wire:model.live="gestor_id">
                        <option value="">Todos</option>
                        @foreach ($gestores as $gestor)
                            <option value="{{ $gestor->id }}">{{ $gestor->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_3">
                    <label>Área</label>
                    <select wire:model.live="area_id">
                        <option value="">Todas</option>
                        @foreach ($areas as $a)
                            <option value="{{ $a->id }}">{{ $a->nombre }}</option>
                        @endforeach
                    </select>
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
            <div class="g_fila">
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

    <div class="g_panel">
        <div class="g_tabla_cabecera">
            <div class="g_tabla_cabecera_botones">
                <button wire:click="resetFiltros" class="g_boton danger">
                    Limpiar <i class="fa-solid fa-rotate-left"></i>
                </button>
            </div>
        </div>

        <div class="g_contenedor_tabla">
            <table class="g_tabla">
                <thead>
                    <tr>
                        <th class="g_celda_centro">N°</th>
                        <th class="g_celda_centro">Código</th>
                        <th>Título</th>
                        <th class="g_celda_centro">Tipo</th>
                        <th class="g_celda_centro">Prioridad</th>
                        <th class="g_celda_centro">Estado</th>
                        <th>Área</th>
                        <th>Gestor</th>
                        <th>Solicitante</th>
                        <th class="g_celda_centro">Fecha</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($soportes as $index => $soporte)
                        <tr wire:key="soporte-{{ $soporte->id }}">
                            <td class="g_celda_centro">{{ $soportes->firstItem() + $index }}</td>
                            <td class="g_celda_centro g_resaltar">{{ $soporte->codigo }}</td>
                            <td class="g_negrita g_resumir">{{ $soporte->titulo }}</td>
                            <td class="g_celda_centro">
                                @php
                                    $tipoColor = $soporte->tipoSoporte?->color ?? '#6c757d';
                                @endphp
                                <span class="g_badge"
                                    style="background: #ffffff; color: {{ $tipoColor }}; border: 1px solid {{ $tipoColor }};">
                                    @if($soporte->tipoSoporte?->icono)
                                        <i class="{{ $soporte->tipoSoporte->icono }}" style="color: {{ $tipoColor }};"></i>
                                    @endif
                                    {{ $soporte->tipoSoporte?->nombre ?? '—' }}
                                </span>
                            </td>
                            <td class="g_celda_centro">
                                @php
                                    $prioColor = $soporte->prioridadSoporte?->color ?? '#6c757d';
                                @endphp
                                <span class="g_badge" style="background: {{ $prioColor }}; color: #ffffff;">
                                    @if($soporte->prioridadSoporte?->icono)
                                        <i class="{{ $soporte->prioridadSoporte->icono }}" style="color: #ffffff;"></i>
                                    @endif
                                    {{ $soporte->prioridadSoporte?->nombre ?? '—' }}
                                </span>
                            </td>
                            <td class="g_celda_centro">
                                @php
                                    $estadoColor = $soporte->estadoSoporte?->color ?? '#6c757d';
                                @endphp
                                <span class="g_badge"
                                    style="background: #ffffff; color: {{ $estadoColor }}; border: 1px solid {{ $estadoColor }};">
                                    @if($soporte->estadoSoporte?->icono)
                                        <i class="{{ $soporte->estadoSoporte->icono }}" style="color: {{ $estadoColor }};"></i>
                                    @endif
                                    {{ $soporte->estadoSoporte?->nombre ?? '—' }}
                                </span>
                            </td>
                            <td class="g_resumir">{{ $soporte->area?->nombre ?? '—' }}</td>
                            <td class="g_resumir">{{ $soporte->gestor?->name ?? '—' }}</td>
                            <td class="g_resumir g_inferior">{{ $soporte->solicitante?->name ?? '—' }}</td>
                            <td class="g_inferior g_celda_centro">{{ optional($soporte->created_at)->format('d/m/Y H:i') }}
                            </td>
                            <td class="g_celda_acciones g_celda_centro">
                                @can('soporte.ver')
                                    <a href="{{ route('erp.soporte.vista.ver', $soporte) }}" class="g_accion ver" title="Ver">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                @endcan
                                @can('soporte.editar')
                                    <a href="{{ route('erp.soporte.vista.editar', $soporte) }}" class="g_accion editar"
                                        title="Editar">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="g_celda_centro">No hay tickets de soporte registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($soportes->hasPages())
            <div class="g_paginacion">
                {{ $soportes->links('vendor.pagination.default-livewire') }}
            </div>
        @endif
    </div>
</div>