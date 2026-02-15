<div class="g_gap_pagina">
    <x-loading-overlay wire:loading
        wire:target="searchAgregados, searchDisponibles, resetFiltrosAgregados, resetFiltrosDisponibles, agregarTipo, quitarTipo, exportExcel"
        message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Gestión de Tipos de Solicitud
            <span>Área: {{ $area->nombre }}</span>
        </h2>

        <div class="cabecera_titulo_botones">
            @can('area.lista')
                <a href="{{ route('erp.area.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            @can('area.crear')
                <a href="{{ route('erp.area.vista.crear') }}" class="g_boton primary">
                    Crear <i class="fa-solid fa-square-plus"></i></a>
            @endcan

            @can('area.editar')
                <a href="{{ route('erp.area.vista.editar', $area->id) }}" class="g_boton secondary">
                    Editar <i class="fa-solid fa-pencil"></i></a>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_6">
            <div class="g_panel">
                <h4 class="g_panel_titulo">Asignados ({{ $tiposAgregados->total() }})</h4>

                <div class="g_tabla_cabecera">
                    <div class="g_tabla_cabecera_botones">
                        @can('area.exportar-solicitudes')
                            <button wire:click="exportExcel" class="g_boton excel" wire:loading.attr="disabled"
                                wire:target="exportExcel">
                                <span wire:loading.remove wire:target="exportExcel">Excel <i
                                        class="fa-regular fa-file-excel"></i></span>
                                <span wire:loading wire:target="exportExcel">Exportando... <i
                                        class="fa-solid fa-spinner fa-spin"></i></span>
                            </button>
                        @endcan

                        <button wire:click="resetFiltrosAgregados" class="g_boton danger" title="Limpiar Filtros">
                            <i class="fa-solid fa-rotate-left"></i>
                        </button>
                    </div>

                    <div class="g_tabla_cabecera_filtro formulario">
                        <div class="g_margin_right_10">
                            <label>Mostrar</label>
                            <select wire:model.live="perPageAgregados">
                                <option value="15">15</option>
                                <option value="30">30</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                        <div>
                            <label>Tipo solicitud</label>
                            <input type="text" wire:model.live.debounce.800ms="searchAgregados">
                        </div>
                    </div>
                </div>

                <div class="g_contenedor_tabla">
                    <table class="g_tabla">
                        <thead>
                            <tr>
                                <th class="g_celda_centro">N°</th>
                                <th>Tipo de Solicitud</th>
                                <th class="g_celda_centro">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tiposAgregados as $index => $tipo)
                                <tr wire:key="agregado-{{ $tipo->id }}">
                                    <td class="g_celda_centro">{{ $tiposAgregados->firstItem() + $index }}</td>
                                    <td class="g_resaltar">{{ $tipo->nombre }}</td>
                                    <td class="g_celda_acciones g_celda_centro centro">
                                        @can('area.eliminar-solicitudes')
                                            <button wire:click="quitarTipo({{ $tipo->id }})" class="g_boton danger">
                                                <i class="fa-solid fa-minus"></i>
                                            </button>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($tiposAgregados->hasPages())
                    <div class="g_paginacion">
                        {{ $tiposAgregados->links('vendor.pagination.default-livewire') }}
                    </div>
                @endif

                @if ($tiposAgregados->isEmpty())
                    <div class="g_vacio">
                        <p>No se encontraron tipos asignados.</p>
                        <i class="fa-regular fa-face-meh"></i>
                    </div>
                @endif
            </div>
        </div>

        <div class="g_columna_6">
            <div class="g_panel">

                <h4 class="g_panel_titulo">Disponibles ({{ $tiposDisponibles->total() }})</h4>

                <div class="g_tabla_cabecera">
                    <div class="g_tabla_cabecera_botones">
                        <button wire:click="resetFiltrosDisponibles" class="g_boton danger" title="Limpiar Filtros">
                            <i class="fa-solid fa-rotate-left"></i>
                        </button>
                    </div>
                    <div class="g_tabla_cabecera_filtro formulario">
                        <div class="g_margin_right_10">
                            <label>Mostrar</label>
                            <select wire:model.live="perPageDisponibles">
                                <option value="15">15</option>
                                <option value="30">30</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                        <div>
                            <label>Tipo solicitud</label>
                            <input type="text" wire:model.live.debounce.800ms="searchDisponibles">
                        </div>
                    </div>
                </div>

                <div class="g_contenedor_tabla">
                    <table class="g_tabla">
                        <thead>
                            <tr>
                                <th class="g_celda_centro">N°</th>
                                <th>Tipo de Solicitud</th>
                                <th class="g_celda_centro">Acción</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($tiposDisponibles as $index => $tipo)
                                <tr wire:key="disponible-{{ $tipo->id }}">
                                    <td class="g_celda_centro">{{ $tiposDisponibles->firstItem() + $index }}</td>
                                    <td class="g_resaltar">{{ $tipo->nombre }}</td>
                                    <td class="g_celda_acciones g_celda_centro centro">
                                        @can('area.agregar-solicitudes')
                                            <button wire:click="agregarTipo({{ $tipo->id }})" class="g_boton success">
                                                <i class="fa-solid fa-plus"></i>
                                            </button>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($tiposDisponibles->hasPages())
                    <div class="g_paginacion">
                        {{ $tiposDisponibles->links('vendor.pagination.default-livewire') }}
                    </div>
                @endif

                @if ($tiposDisponibles->isEmpty())
                    <div class="g_vacio">
                        <p>No se encontraron tipos disponibles.</p>
                        <i class="fa-regular fa-face-smile"></i>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>