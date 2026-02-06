<div class="g_gap_pagina">
    <x-loading-overlay wire:loading
        wire:target="searchAgregados, searchDisponibles, agregarTipo, quitarTipo, exportExcel"
        message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Gestión de Tipos de Solicitud
            <span>Área: {{ $area->nombre }}</span>
        </h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.area.vista.todo') }}" class="g_boton g_boton_light">
                Lista <i class="fa-solid fa-list"></i></a>

            <a href="{{ route('erp.area.vista.crear') }}" class="g_boton g_boton_primary">
                Crear <i class="fa-solid fa-square-plus"></i></a>

            <a href="{{ route('erp.area.vista.editar', $area->id) }}" class="g_boton g_boton_secondary">
                Editar <i class="fa-solid fa-pencil"></i></a>

            <button type="button" class="g_boton g_boton_dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_6">
            <div class="g_panel">
                <h4 class="g_panel_titulo">Asignados ({{ $tiposAgregados->count() }})</h4>

                <div class="g_tabla_cabecera">
                    <div class="g_tabla_cabecera_botones">
                        <button wire:click="exportExcel" class="g_boton g_boton_excel" wire:loading.attr="disabled"
                            wire:target="exportExcel">
                            <span wire:loading.remove wire:target="exportExcel">Excel <i
                                    class="fa-regular fa-file-excel"></i></span>
                            <span wire:loading wire:target="exportExcel">Exportando... <i
                                    class="fa-solid fa-spinner fa-spin"></i></span>
                        </button>
                    </div>

                    <div class="g_tabla_cabecera_filtro formulario">
                        <input type="text" wire:model.live.debounce.800ms="searchAgregados">
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
                                    <td class="g_celda_centro">{{ $index + 1 }}</td>
                                    <td class="g_resaltar">{{ $tipo->nombre }}</td>
                                    <td class="g_celda_acciones g_celda_centro centro">
                                        <button wire:click="quitarTipo({{ $tipo->id }})"
                                            class="g_boton g_boton_danger g_boton_pequeno">
                                            Quitar <i class="fa-solid fa-minus"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($tiposAgregados->isEmpty())
                    <div class="g_vacio">
                        <p>No hay tipos asignados.</p>
                        <i class="fa-regular fa-face-meh"></i>
                    </div>
                @endif
            </div>
        </div>

        <div class="g_columna_6">
            <div class="g_panel">

                <h4 class="g_panel_titulo">Disponibles ({{ $tiposDisponibles->count() }})</h4>

                <div class="g_tabla_cabecera">
                    <div class="g_tabla_cabecera_botones"></div>
                    <div class="g_tabla_cabecera_filtro formulario">
                        <input type="text" wire:model.live.debounce.800ms="searchDisponibles">
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
                                        <button wire:click="agregarTipo({{ $tipo->id }})"
                                            class="g_boton g_boton_success g_boton_pequeno">
                                            Agregar <i class="fa-solid fa-plus"></i>
                                        </button>
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
                        <p>No hay tipos disponibles.</p>
                        <i class="fa-regular fa-face-smile"></i>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>