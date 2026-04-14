<div class="g_gap_pagina">
    <x-loading-overlay wire:loading
        wire:target="buscar, unidad_negocio_id, proyecto_id, estado_id, area_id, solicitud_id, sub_tipo_solicitud_id, canal_id, usuario_admin_id, prioridad_id, con_citas, con_derivados, con_hijos, desde, hasta, perPage, resetFiltros, exportExcelFiltro, exportExcelTodo, gotoPage, nextPage, previousPage"
        message="Cargando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Listado de Tickets</h2>

        <div class="cabecera_titulo_botones">
            @can('ticket.vista-importar-tickets')
                <a href="{{ route('erp.ticket.vista.importar') }}" class="g_boton secondary">
                    Importar <i class="fa-solid fa-file-excel"></i></a>
            @endcan

            @can('ticket.vista-crear')
                <a href="{{ route('erp.ticket.vista.crear') }}" class="g_boton primary">
                    Crear <i class="fa-solid fa-square-plus"></i></a>
            @endcan
        </div>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Cliente (DNI o Nombres)</label>
                    <input type="text" wire:model.live.debounce.1300ms="buscar" id="buscar" name="buscar">
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Empresa </label>
                    <select wire:model.live="unidad_negocio_id">
                        <option value="">Todos</option>
                        @foreach ($unidades_negocios as $item)
                            <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Proyecto </label>
                    <select wire:model.live="proyecto_id">
                        <option value="">Todos</option>
                        @foreach ($proyectos as $item)
                            <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Gestor </label>
                    <select wire:model.live="usuario_admin_id">
                        <option value="">Todos</option>
                        @foreach ($usuarios_admin as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Estado </label>
                    <select wire:model.live="estado_id">
                        <option value="">Todos</option>
                        @foreach ($estados as $estadoItem)
                            <option value="{{ $estadoItem->id }}">{{ $estadoItem->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Area </label>
                    <select wire:model.live="area_id">
                        <option value="">Todos</option>
                        @foreach ($areas as $item)
                            <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Solicitud </label>
                    <select wire:model.live="solicitud_id">
                        <option value="">Todos</option>
                        @foreach ($solicitudes as $item)
                            <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Sub tipo solicitud </label>
                    <select wire:model.live="sub_tipo_solicitud_id">
                        <option value="">Todos</option>
                        @foreach ($sub_tipo_solicitudes as $item)
                            <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Canal </label>
                    <select wire:model.live="canal_id">
                        <option value="">Todos</option>
                        @foreach ($canales as $item)
                            <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Prioridad</label>
                    <select wire:model.live="prioridad_id">
                        <option value="">Todos</option>
                        @foreach ($prioridades as $item)
                            <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Citas</label>
                    <select wire:model.live="con_citas">
                        <option value="">Todos</option>
                        <option value="1">Con citas</option>
                        <option value="0">Sin citas</option>
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Derivados</label>
                    <select wire:model.live="con_derivados">
                        <option value="">Todos</option>
                        <option value="1">Con derivados</option>
                        <option value="0">Sin derivados</option>
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Hijos</label>
                    <select wire:model.live="con_hijos">
                        <option value="">Todos</option>
                        <option value="1">Con hijos</option>
                        <option value="0">Sin hijos</option>
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
            </div>
        </div>
    </div>

    <div class="g_panel">
        <div class="g_tabla_cabecera">
            <div class="g_tabla_cabecera_botones">
                @can('ticket.accion-exportar-filtro')
                    <button wire:click="exportExcelFiltro" class="g_boton excel" wire:loading.attr="disabled"
                        wire:target="exportExcelFiltro">
                        <span wire:loading.remove wire:target="exportExcelFiltro">Excel Filtrados <i
                                class="fa-regular fa-file-excel"></i></span>
                        <span wire:loading wire:target="exportExcelFiltro">Generando... <i
                                class="fa-solid fa-spinner fa-spin"></i></span>
                    </button>
                @endcan

                @can('ticket.accion-exportar-todo')
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
                        <th class="g_celda_centro">Ticket</th>
                        <th>Cliente</th>
                        <th>Área</th>
                        <th>Solicitud</th>
                        <th>Canal</th>
                        <th class="g_celda_centro">Estado</th>
                        <th>Gestor</th>
                        <th>Prioridad</th>
                        <th>Fecha</th>
                        <th>Derivado</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <td class="g_celda_centro">
                                <span class="g_badge light">#{{ $item->id }}</span>
                            </td>
                            <td class="g_negrita g_resumir">{{ $item->nombres }}</td>
                            <td>
                                @if($item->area)
                                    <span class="g_badge g_badge_soft" style="color: {{ $item->area->color }};">
                                        <i class="{{ $item->area->icono }}"></i> {{ $item->area->nombre }}
                                    </span>
                                @else
                                    <span class="g_badge light">-</span>
                                @endif
                            </td>
                            <td class="g_resumir g_inferior">{{ $item->tipoSolicitud->nombre }}</td>
                            <td>{{ $item->canal->nombre }}</td>
                            <td class="g_celda_centro">
                                <span class="g_badge g_badge_soft" style="color: {{ $item->estado?->color }};">
                                    {{ $item->estado?->nombre }}
                                </span>
                            </td>
                            <td>{{ $item->gestor?->name ?? 'Sin asignar' }}</td>
                            <td>
                                <span class="g_badge g_badge_soft" style="color: {{ $item->prioridad?->color }};">
                                    <i class="{{ $item->prioridad?->icono }}"></i> {{ $item->prioridad?->nombre }}
                                </span>
                            </td>
                            <td class="g_inferior g_celda_centro">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                            <td class="g_celda_centro">
                                <span class="g_badge {{ $item->tiene_derivados ? 'success' : 'light' }}">
                                    {{ $item->tiene_derivados ? 'SI' : 'NO' }}
                                </span>
                            </td>
                            <td class="g_celda_acciones g_celda_centro centro">
                                @can('ticket.vista-ver')
                                    <a href="{{ route('erp.ticket.vista.ver', $item->id) }}" class="g_accion ver"
                                        title="Ver detalle">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                @endcan

                                @can('ticket.vista-editar')
                                    <a href="{{ route('erp.ticket.vista.editar', $item->id) }}" class="g_accion editar"
                                        title="Editar">
                                        <i class="fa-solid fa-pencil"></i>
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