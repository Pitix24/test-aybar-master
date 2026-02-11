<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="buscar, unidad_negocio_id, proyecto_id, sede_id, motivo_cita_id, estado_cita_id, gestor_id, area_id, perPage, resetFiltros"
        message="Cargando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Listado de Citas</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.cita.vista.calendario') }}" class="g_boton g_boton_success">
                Calendario <i class="fa-solid fa-calendar-days"></i></a>
            
            <a href="{{ route('erp.cita.vista.crear') }}" class="g_boton g_boton_primary">
                Crear <i class="fa-solid fa-square-plus"></i></a>
        </div>
    </div>

    <!-- FILTROS -->
    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Cliente/DNI/Nombres</label>
                    <input type="text" wire:model.live.debounce.1300ms="buscar" placeholder="Buscar...">
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Empresa</label>
                    <select wire:model.live="unidad_negocio_id">
                        <option value="">Todas</option>
                        @foreach($unidades as $u) <option value="{{ $u->id }}">{{ $u->nombre }}</option> @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Proyecto</label>
                    <select wire:model.live="proyecto_id">
                        <option value="">Todos</option>
                        @foreach($proyectos as $p) <option value="{{ $p->id }}">{{ $p->nombre }}</option> @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Área</label>
                    <select wire:model.live="area_id">
                        <option value="">Todas</option>
                        @foreach($areas as $a) <option value="{{ $a->id }}">{{ $a->nombre }}</option> @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Estado</label>
                    <select wire:model.live="estado_cita_id">
                        <option value="">Todos</option>
                        @foreach($estados as $e) <option value="{{ $e->id }}">{{ $e->nombre }}</option> @endforeach
                    </select>
                </div>
                
                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Motivo</label>
                    <select wire:model.live="motivo_cita_id">
                        <option value="">Todos</option>
                        @foreach($motivos as $m) <option value="{{ $m->id }}">{{ $m->nombre }}</option> @endforeach
                    </select>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Sede</label>
                    <select wire:model.live="sede_id">
                        <option value="">Todas</option>
                        @foreach($sedes as $s) <option value="{{ $s->id }}">{{ $s->nombre }}</option> @endforeach
                    </select>
                </div>
                
                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Gestor</label>
                    <select wire:model.live="gestor_id">
                        <option value="">Todos</option>
                        @foreach($gestores as $g) <option value="{{ $g->id }}">{{ $g->name }}</option> @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Fecha inicio (Desde)</label>
                    <input type="date" wire:model.live="fecha_inicio">
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Fecha fin (Hasta)</label>
                    <input type="date" wire:model.live="fecha_fin">
                </div>
                
                <div class="g_margin_bottom_10 g_columna_4" style="display: flex; align-items: flex-end; gap: 10px;">
                     <button wire:click="resetFiltros" class="g_boton g_boton_danger">
                        Limpiar <i class="fa-solid fa-rotate-left"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- TABLA -->
    <div class="g_panel">
        <div class="g_tabla_cabecera">
            <div class="g_tabla_cabecera_botones">
                <!-- Aquí se podrían poner botones de exportación -->
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
                        <th class="g_celda_centro">ID</th>
                        <th>Fecha y Hora</th>
                        <th>Cliente</th>
                        <th>Motivo / Área</th>
                        <th>Sede</th>
                        <th>Gestor</th>
                        <th class="g_celda_centro">Estado</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr wire:key="cita-row-{{ $item->id }}">
                            <td class="g_celda_centro">
                                <span class="g_badge g_badge_light">#{{ $item->id }}</span>
                            </td>
                            <td>
                                <div style="display: flex; flex-direction: column;">
                                    <span class="g_negrita">{{ $item->fecha_inicio?->format('d/m/Y') }}</span>
                                    <span style="font-size: 0.8rem; color: #64748b;">
                                        {{ $item->fecha_inicio?->format('H:i') }} - {{ $item->fecha_fin?->format('H:i') }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; flex-direction: column;">
                                    <span class="g_negrita g_resumir">{{ $item->nombres }}</span>
                                    <span style="font-size: 0.75rem; color: #94a3b8;">{{ $item->dni }} ({{ strtoupper($item->origen) }})</span>
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; flex-direction: column;">
                                    <span class="g_negrita">{{ $item->motivo?->nombre }}</span>
                                    @if($item->area)
                                        <span class="g_badge g_badge_soft" style="color: {{ $item->area->color }}; width: fit-content; padding: 0;">
                                            <i class="{{ $item->area->icono }}"></i> {{ $item->area->nombre }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>{{ $item->sede?->nombre }}</td>
                            <td>{{ $item->gestor?->name ?? 'Sin asignar' }}</td>
                            <td class="g_celda_centro">
                                <span class="g_badge g_badge_soft" style="color: {{ $item->estado?->color }};">
                                    <i class="{{ $item->estado?->icono }} g_margin_right_5"></i> {{ $item->estado?->nombre }}
                                </span>
                            </td>
                            <td class="g_celda_acciones g_celda_centro">
                                <a href="{{ route('erp.cita.vista.editar', $item->id) }}" class="g_accion_editar" title="Editar">
                                    <i class="fa-solid fa-pencil"></i>
                                </a>
                                @if($item->ticket_id)
                                    <a href="{{ route('erp.ticket.vista.editar', $item->ticket_id) }}" class="g_accion_editar" style="background: #f0f9ff; color: #0369a1;" title="Ver Ticket">
                                        <i class="fa-solid fa-ticket"></i>
                                    </a>
                                @endif
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
                <p>{{ $buscar ? 'No se encontraron resultados para "' . $buscar . '"' : 'No hay citas registradas.' }}</p>
                <i class="fa-regular fa-calendar-xmark"></i>
            </div>
        @else
            <div class="g_paginacion">
                Mostrando {{ $items->firstItem() ?? 0 }} – {{ $items->lastItem() ?? 0 }}
                de {{ $items->total() }} registros
            </div>
        @endif
    </div>
</div>