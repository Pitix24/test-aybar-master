@section('tituloPagina', 'Lista de proyectos')

@section('anchoPantalla', '100%')

<div class="g_gap_pagina">
    <x-loading-overlay wire:loading
        wire:target="buscar, unidad_negocio_id, grupo_proyecto_id, activo, perPage, resetFiltros, gotoPage, nextPage, previousPage, exportExcel"
        message="Cargando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Lista de proyectos</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.proyecto.vista.todo') }}" class="g_boton g_boton_light">
                Inicio <i class="fa-solid fa-house"></i></a>

            <a href="{{ route('erp.proyecto.vista.crear') }}" class="g_boton g_boton_primary">
                Crear <i class="fa-solid fa-square-plus"></i></a>
        </div>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Proyecto</label>
                    <input type="text" wire:model.live.debounce.1300ms="buscar" id="buscar" name="buscar">
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Unidad de negocio </label>
                    <select wire:model.live="unidad_negocio_id">
                        <option value="">Todos</option>
                        @foreach ($unidades_negocios as $unidad_negocio)
                            <option value="{{ $unidad_negocio->id }}">{{ $unidad_negocio->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Grupo proyecto </label>
                    <select wire:model.live="grupo_proyecto_id">
                        <option value="">Todos</option>
                        @foreach ($grupo_proyectos as $grupo_proyecto)
                            <option value="{{ $grupo_proyecto->id }}">{{ $grupo_proyecto->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Activo</label>
                    <select wire:model.live="activo">
                        <option value="">Todos</option>
                        <option value="1">Si</option>
                        <option value="0">No</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="g_panel">
        <div class="tabla_cabecera">
            <div class="tabla_cabecera_botones">
                <button wire:click="exportExcel" class="g_boton g_boton_excel" wire:loading.attr="disabled"
                    wire:target="exportExcel">
                    <span wire:loading.remove wire:target="exportExcel">Excel <i
                            class="fa-regular fa-file-excel"></i></span>
                    <span wire:loading wire:target="exportExcel">Exportando... <i
                            class="fa-solid fa-spinner fa-spin"></i></span>
                </button>

                <button wire:click="resetFiltros" class="g_boton g_boton_danger">
                    Refresh Filtros <i class="fa-solid fa-rotate-left"></i>
                </button>
            </div>

            <div class="tabla_cabecera_buscar formulario">
                <select wire:model.live="perPage">
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>

        <div class="tabla_contenido">
            <div class="contenedor_tabla">
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>Nº</th>
                            <th>Unidad de negocio</th>
                            <th>Grupo proyecto</th>
                            <th>Proyecto</th>
                            <th>ID Slin</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>

                    @if ($items->isNotEmpty())
                        <tbody>
                            @foreach ($items as $index => $item)
                                <tr>
                                    <td>{{ $items->firstItem() + $index }}</td>
                                    <td class="g_resaltar">{{ $item->unidadNegocio->nombre }}</td>
                                    <td class="g_resaltar">{{ $item->grupoProyecto->nombre }}</td>
                                    <td class="g_resaltar">{{ $item->nombre }}</td>
                                    <td class="g_resaltar">{{ $item->slin_id ?? '-' }}</td>
                                    <td>
                                        <span class="estado {{ $item->activo ? 'g_activo' : 'g_desactivado' }}"><i
                                                class="fa-solid fa-circle"></i></span>
                                        {{ $item->activo ? 'Activo' : 'Desactivo' }}
                                    </td>

                                    <td class="centrar_iconos">
                                        <a href="{{ route('erp.proyecto.vista.editar', $item->id) }}" class="g_accion_editar">
                                            <span><i class="fa-solid fa-pencil"></i></span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    @endif
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