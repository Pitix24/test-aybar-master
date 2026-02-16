<div class="g_gap_pagina">
    <x-loading-overlay wire:loading message="Cargando información..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Lista de Entrega Fest</h2>

        <div class="cabecera_titulo_botones">
            @can('entrega-fest.exportar-filtro')
                <button wire:click="exportExcelFiltro" class="g_boton light">
                    Excel Filtro <i class="fa-solid fa-file-excel"></i>
                </button>
            @endcan
            @can('entrega-fest.exportar-todo')
                <button wire:click="exportExcelTodo" class="g_boton light">
                    Excel Todo <i class="fa-solid fa-file-excel"></i>
                </button>
            @endcan
            @can('entrega-fest.crear')
                <a href="{{ route('erp.entrega-fest.vista.crear') }}" class="g_boton primary">
                    Crear Nuevo <i class="fa-solid fa-plus"></i>
                </a>
            @endcan
        </div>
    </div>

    <div class="g_panel">
        <div class="g_fila g_margin_bottom_20">
            <div class="g_columna_3">
                <label>Buscar</label>
                <div class="g_input_con_icono_derecha">
                    <input type="text" wire:model.live.debounce.300ms="buscar" placeholder="Nombre o código...">
                    <i class="fa-solid fa-search"></i>
                </div>
            </div>

            <div class="g_columna_2">
                <label>Unidad de Negocio</label>
                <select wire:model.live="unidad_negocio_id">
                    <option value="">Todas</option>
                    @foreach ($unidades as $u)
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

            <div class="g_columna_2">
                <label>Mostrar</label>
                <select wire:model.live="perPage">
                    <option value="10">10 registros</option>
                    <option value="25">25 registros</option>
                    <option value="50">50 registros</option>
                    <option value="100">100 registros</option>
                </select>
            </div>

            <div class="g_columna_1" style="display: flex; align-items: flex-end;">
                <button wire:click="resetFiltros" class="g_boton light" title="Limpiar filtros" style="width: 100%;">
                    <i class="fa-solid fa-eraser"></i>
                </button>
            </div>
        </div>

        <div class="g_contenedor_tabla">
            <table class="g_tabla">
                <thead>
                    <tr>
                        <th>Cód.</th>
                        <th>Evento / Nombre</th>
                        <th>Unidad Negocio / Proyecto</th>
                        <th class="g_celda_centro">Fecha</th>
                        <th class="g_celda_centro">Prospectos</th>
                        <th class="g_celda_centro">Invitados</th>
                        <th class="g_celda_centro">Estado</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($eventos as $e)
                        <tr wire:key="evento-{{ $e->id }}">
                            <td class="g_negrita">#{{ $e->codigo }}</td>
                            <td>
                                <div class="g_negrita">{{ $e->nombre }}</div>
                                <div class="g_texto_pequeno">{{ Str::limit($e->descripcion, 50) }}</div>
                            </td>
                            <td>
                                <div>{{ $e->unidadNegocio->nombre }}</div>
                                <div class="g_texto_pequeno">{{ $e->proyecto->nombre ?? 'Sin proyecto' }}</div>
                            </td>
                            <td class="g_celda_centro">{{ $e->fecha_entrega->format('d/m/Y') }}</td>
                            <td class="g_celda_centro">
                                <span class="g_badge primary">{{ $e->prospectos_count }}</span>
                            </td>
                            <td class="g_celda_centro">
                                <span class="g_badge success">{{ $e->invitados_count }}</span>
                            </td>
                            <td class="g_celda_centro">
                                <span class="g_badge {{ $e->activo ? 'success' : 'error' }}">
                                    {{ $e->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="g_celda_acciones g_celda_centro">
                                @can('entrega-fest.editar')
                                    <a href="{{ route('erp.entrega-fest.vista.editar', $e->id) }}" class="g_accion editar"
                                        title="Editar">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="g_vacio">
                                    <i class="fa-regular fa-face-grin-wink"></i>
                                    <p>No hay eventos registrados.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="g_margin_top_20">
            {{ $eventos->links() }}
        </div>
    </div>
</div>