<div class="g_gap_pagina">
    <x-loading-overlay wire:loading message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Entrega Fest</h2>

        <div class="cabecera_titulo_botones">
            @can('entrega-fest.crear')
                <a href="{{ route('erp.entrega-fest.vista.crear') }}" class="g_boton primary">
                    Crear <i class="fa-solid fa-square-plus"></i></a>
                </a>
            @endcan
        </div>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Código</label>
                    <input type="text" wire:model.live.debounce.400ms="buscar">
                </div>

                <div class="g_columna_2">
                    <label>Empresa</label>
                    <select wire:model.live="unidad_negocio_id">
                        <option value="">Todos</option>
                        @foreach ($unidades_negocios as $u)
                            <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_columna_2">
                    <label>Proyecto</label>
                    <select wire:model.live="proyecto_id" {{ !$unidad_negocio_id ? 'disabled' : '' }}>
                        <option value="">Todos</option>
                        @foreach ($proyectos as $p)
                            <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_columna_2">
                    <label>Estado</label>
                    <select wire:model.live="activo">
                        <option value="">Todos</option>
                        <option value="1">Activos</option>
                        <option value="0">Inactivos</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="g_panel">
        <div class="g_tabla_cabecera">
            <div class="g_tabla_cabecera_botones">
                @can('solicitud-evidencia-pago.exportar-filtro')
                    <button wire:click="exportExcelFiltro" class="g_boton excel" wire:loading.attr="disabled"
                        wire:target="exportExcelFiltro">
                        <span wire:loading.remove wire:target="exportExcelFiltro">Excel Filtrados <i
                                class="fa-regular fa-file-excel"></i></span>
                        <span wire:loading wire:target="exportExcelFiltro">Generando... <i
                                class="fa-solid fa-spinner fa-spin"></i></span>
                    </button>
                @endcan

                @can('solicitud-evidencia-pago.exportar-todo')
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
                        <th>Cód.</th>
                        <th>Nombre del Evento / Descripción</th>
                        <th>Proyecto / Responsable</th>
                        <th class="g_celda_centro">Fecha Entrega</th>
                        <th class="g_celda_centro">Prospectos</th>
                        <th class="g_celda_centro">Invitados</th>
                        <th class="g_celda_centro">Estado</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($items as $e)
                        <tr wire:key="evento-{{ $e->id }}">
                            <td class="g_negrita" style="color: var(--color-primary);">#{{ $e->codigo }}</td>
                            <td>
                                <div class="g_negrita">{{ $e->nombre }}</div>
                                <div>{{ Str::limit($e->descripcion, 70) }}</div>
                                <div>
                                    <a href="{{ route('erp.entrega-fest.vista.panel', $e->id) }}" class="g_boton info">
                                        <i class="fa-solid fa-grip"></i> Panel de Gestión
                                    </a>

                                    <a href="{{ route('erp.entrega-fest.vista.staff.dashboard', $e->id) }}"
                                        class="g_boton danger">
                                        <i class="fa-solid fa-shield-halved"></i> Panel de Staff
                                    </a>
                                </div>
                            </td>
                            <td>
                                <div class="g_negrita">
                                    {{ $e->proyectos->pluck('nombre')->implode(', ') ?: 'N/A' }}
                                </div>
                                <div>{{ $e->gestor->name ?? 'N/A' }}</div>
                            </td>
                            <td class="g_celda_centro">
                                <span class="g_negrita">{{ $e->fecha_entrega->format('d/m/Y') }}</span>
                            </td>
                            <td class="g_celda_centro">
                                <span class="g_badge info">{{ $e->prospectos_count }}</span>
                            </td>
                            <td class="g_celda_centro">
                                <span class="g_badge primary">{{ $e->invitados_count }}</span>
                            </td>
                            <td class="g_celda_centro">
                                <span class="g_badge {{ $e->activo ? 'success' : 'error' }}">
                                    {{ $e->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="g_celda_acciones g_celda_centro">
                                @can('entrega-fest.ver')
                                    <a href="{{ route('erp.entrega-fest.vista.ver', $e->id) }}" class="g_accion ver"
                                        title="Ver Evento">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                @endcan

                                @can('entrega-fest.editar')
                                    <a href="{{ route('erp.entrega-fest.vista.editar', $e->id) }}" class="g_accion editar"
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