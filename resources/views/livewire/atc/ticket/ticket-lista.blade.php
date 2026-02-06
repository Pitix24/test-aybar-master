@section('descripcion', 'Página de gestión de tickets de Aybar')

<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="buscar, estado, prioridad, perPage, resetFiltros, exportExcel"
        message="Cargando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Listado de Tickets</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.home') }}" class="g_boton g_boton_light">
                Inicio <i class="fa-solid fa-house"></i></a>

            <a href="{{ route('erp.ticket.vista.crear') }}" class="g_boton g_boton_primary">
                Nuevo Ticket <i class="fa-solid fa-square-plus"></i></a>
        </div>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Buscar (ID, Asunto, Cliente)</label>
                    <input type="text" wire:model.live.debounce.1300ms="buscar" placeholder="Ingrese término...">
                </div>

                <div class="g_margin_bottom_10 g_columna_3">
                    <label>Estado</label>
                    <select wire:model.live="estado">
                        <option value="">Todos</option>
                        @foreach($estados as $est)
                            <option value="{{ $est->id }}">{{ $est->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_3">
                    <label>Prioridad</label>
                    <select wire:model.live="prioridad">
                        <option value="">Todas</option>
                        @foreach($prioridades as $pri)
                            <option value="{{ $pri->id }}">{{ $pri->nombre }}</option>
                        @endforeach
                    </select>
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

    <div class="g_panel">
        <div class="tabla_cabecera">
            <div class="tabla_cabecera_botones">
                <button wire:click="exportExcel" class="g_boton g_boton_excel" wire:loading.attr="disabled">
                    Excel <i class="fa-regular fa-file-excel"></i>
                </button>

                <button wire:click="resetFiltros" class="g_boton g_boton_danger">
                    Limpiar <i class="fa-solid fa-rotate-left"></i>
                </button>
            </div>
        </div>

        <div class="tabla_contenido">
            <div class="contenedor_tabla">
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Asunto</th>
                            <th>Cliente</th>
                            <th>Área</th>
                            <th>Estado</th>
                            <th>Prioridad</th>
                            <th>Gestor</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td><span class="g_badge g_badge_light">#{{ $item->id }}</span></td>
                                <td class="g_resaltar">{{ $item->asunto_inicial }}</td>
                                <td>{{ $item->cliente?->name ?? 'N/A' }}</td>
                                <td>
                                    @if($item->area)
                                        <span class="g_badge"
                                            style="background-color: {{ $item->area->color }}20; color: {{ $item->area->color }}; border: 1px solid {{ $item->area->color }}">
                                            <i class="{{ $item->area->icono }}"></i> {{ $item->area->nombre }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <span class="g_badge"
                                        style="background-color: {{ $item->estado?->color }}20; color: {{ $item->estado?->color }}; border: 1px solid {{ $item->estado?->color }}">
                                        {{ $item->estado?->nombre }}
                                    </span>
                                </td>
                                <td>
                                    <span style="color: {{ $item->prioridad?->color }}">
                                        <i class="{{ $item->prioridad?->icono }}"></i> {{ $item->prioridad?->nombre }}
                                    </span>
                                </td>
                                <td>{{ $item->gestor?->name ?? 'Sin asignar' }}</td>
                                <td style="font-size: 0.8rem;">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                <td class="centrar_iconos">
                                    <a href="{{ route('erp.ticket.vista.editar', $item->id) }}" class="g_accion_editar">
                                        <span><i class="fa-solid fa-pencil"></i></span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if ($items->hasPages())
            <div class="g_paginacion">
                {{ $items->links('vendor.pagination.default-livewire') }}
            </div>
        @endif

        @if ($items->isEmpty())
            <div class="g_vacio">
                <p>No se encontraron tickets.</p>
                <i class="fa-solid fa-folder-open"></i>
            </div>
        @endif
    </div>
</div>