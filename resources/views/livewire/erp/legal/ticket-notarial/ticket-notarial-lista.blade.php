<div class="g_gap_pagina">
    <x-loading-overlay wire:loading
        wire:target="buscar, unidad_negocio_id, proyecto_id, estado_id, gestor_id, desde, hasta, perPage, resetFiltros"
        message="Cargando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Listado de Cartas Notariales</h2>

        <div class="cabecera_titulo_botones">
            @can('ticket-notarial.crear')
            <a href="{{ route('erp.ticket-notarial.vista.crear') }}" class="g_boton primary">
                Crear <i class="fa-solid fa-square-plus"></i></a>
            @endcan
        </div>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Cliente (DNI o Nombres) / N°Ticket</label>
                    <input type="text" wire:model.live.debounce.1300ms="buscar" id="buscar" name="buscar">
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Empresa</label>
                    <select wire:model.live="unidad_negocio_id">
                        <option value="">Todos</option>
                        @foreach ($unidades as $item)
                        <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Proyecto</label>
                    <select wire:model.live="proyecto_id">
                        <option value="">Todos</option>
                        @foreach ($proyectos as $item)
                        <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Estado</label>
                    <select wire:model.live="estado_id">
                        <option value="">Todos</option>
                        @foreach ($estados as $estadoItem)
                        <option value="{{ $estadoItem->id }}">{{ $estadoItem->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Gestor</label>
                    <select wire:model.live="gestor_id">
                        <option value="">Todos</option>
                        @foreach ($gestores as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="g_fila">
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
                @can('ticket-notarial.accion-exportar-filtro')
                <button wire:click="exportExcelFiltro" class="g_boton excel" wire:loading.attr="disabled"
                    wire:target="exportExcelFiltro">
                    <span wire:loading.remove wire:target="exportExcelFiltro">Excel Filtrados <i
                            class="fa-regular fa-file-excel"></i></span>
                    <span wire:loading wire:target="exportExcelFiltro">Generando... <i
                            class="fa-solid fa-spinner fa-spin"></i></span>
                </button>
                @endcan

                @can('ticket-notarial.accion-exportar-todo')
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
                        <th>Empresa</th>
                        <th>Proyecto</th>
                        <th>Área</th>
                        <th>Sub tipo</th>
                        <th class="g_celda_centro">Estado</th>
                        <th>Gestor</th>
                        <th>Creado</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                    <tr>
                        <td class="g_celda_centro">
                            <span class="g_badge light">#{{ $item->id }}</span>
                        </td>
                        <td class="g_resumir">
                            <div class="g_negrita">{{ $item->nombres }}</div>
                            <div class="g_inferior">{{ $item->dni ?? '-' }}</div>
                        </td>
                        <td class="g_resumir">{{ $item->unidadNegocio?->nombre ?? '-' }}</td>
                        <td class="g_resumir">{{ $item->proyecto?->nombre ?? '-' }}</td>
                        <td>
                            @if ($item->area)
                            <span class="g_badge g_badge_soft" style="color: {{ $item->area->color }};">
                                <i class="{{ $item->area->icono }}"></i> {{ $item->area->nombre }}
                            </span>
                            @else
                            <span class="g_badge light">-</span>
                            @endif
                        </td>
                        <td class="g_resumir">{{ $item->subTipoSolicitud?->nombre ?? '-' }}</td>
                        <td class="g_celda_centro">
                            <span class="g_badge g_badge_soft" style="color: {{ $item->estado?->color }};">
                                {{ $item->estado?->nombre ?? '-' }}
                            </span>
                        </td>
                        <td>{{ $item->gestor?->name ?? 'Sin asignar' }}</td>
                        <td class="g_inferior">
                            {{ $item->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="g_celda_acciones g_celda_centro centro">
                            @can('ticket-notarial.ver')
                            <a href="{{ route('erp.ticket-notarial.vista.ver', $item->id) }}" class="g_accion ver"
                                title="Ver detalle">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            @endcan

                            @can('ticket-notarial.editar')
                            <a href="{{ route('erp.ticket-notarial.vista.editar', $item->id) }}" class="g_accion editar"
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
            <p>{{ $buscar ? 'No se encontraron resultados para "' . $buscar . '"' : 'No hay cartas notariales
                disponibles.' }}</p>
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
