@section('tituloPagina', 'Vincular Tipos de Solicitud')

@section('anchoPantalla', '100%')

<div class="g_gap_pagina">
    <x-loading-overlay wire:loading
        wire:target="searchAgregados, searchDisponibles, agregarTipo, quitarTipo, exportExcel"
        message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <div>
            <h2>Gestión de Tipos de Solicitud</h2>
            <p style="margin: 0; color: #64748b;">Área: <strong
                    style="color: {{ $area->color }}">{{ $area->nombre }}</strong></p>
        </div>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.area.vista.todo') }}" class="g_boton g_boton_light">
                Lista Áreas <i class="fa-solid fa-list"></i></a>

            <a href="{{ route('erp.area.vista.editar', $area->id) }}" class="g_boton g_boton_secondary">
                Editar Área <i class="fa-solid fa-pencil"></i></a>

            <button type="button" class="g_boton g_boton_dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <div class="g_fila">
        <!-- AGREGADOS -->
        <div class="g_columna_6">
            <div class="g_panel">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h4 class="g_panel_titulo" style="margin: 0;">Asignados ({{ $tiposAgregados->count() }})</h4>

                    <button wire:click="exportExcel" class="g_boton g_boton_excel g_boton_pequeno"
                        wire:loading.attr="disabled" wire:target="exportExcel">
                        <i class="fa-regular fa-file-excel"></i> Exportar
                    </button>
                </div>

                <div class="tabla_cabecera">
                    <div class="tabla_cabecera_buscar formulario" style="width: 100%;">
                        <div style="position: relative; width: 100%;">
                            <input type="text" wire:model.live.debounce.800ms="searchAgregados"
                                placeholder="Buscar en asignados..." style="padding-left: 35px;">
                            <i class="fa-solid fa-magnifying-glass"
                                style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                        </div>
                    </div>
                </div>

                <div class="tabla_contenido">
                    <div class="contenedor_tabla" style="max-height: 500px; overflow-y: auto;">
                        <table class="tabla">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>Tipo de Solicitud</th>
                                    <th style="width: 100px;">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tiposAgregados as $index => $tipo)
                                    <tr wire:key="agregado-{{ $tipo->id }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td class="g_resaltar">{{ $tipo->nombre }}</td>
                                        <td class="centrar_iconos">
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
                </div>

                @if ($tiposAgregados->isEmpty())
                    <div class="g_vacio" style="padding: 40px 0;">
                        <p>No hay tipos asignados.</p>
                        <i class="fa-regular fa-face-meh"></i>
                    </div>
                @endif
            </div>
        </div>

        <!-- DISPONIBLES -->
        <div class="g_columna_6">
            <div class="g_panel">
                <h4 class="g_panel_titulo">Disponibles</h4>

                <div class="tabla_cabecera">
                    <div class="tabla_cabecera_buscar formulario" style="width: 100%;">
                        <div style="position: relative; width: 100%;">
                            <input type="text" wire:model.live.debounce.800ms="searchDisponibles"
                                placeholder="Buscar en disponibles..." style="padding-left: 35px;">
                            <i class="fa-solid fa-magnifying-glass"
                                style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                        </div>
                    </div>
                </div>

                <div class="tabla_contenido">
                    <div class="contenedor_tabla" style="max-height: 500px; overflow-y: auto;">
                        <table class="tabla">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>Tipo de Solicitud</th>
                                    <th style="width: 100px;">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tiposDisponibles as $index => $tipo)
                                    <tr wire:key="disponible-{{ $tipo->id }}">
                                        <td>{{ $tiposDisponibles->firstItem() + $index }}</td>
                                        <td class="g_resaltar">{{ $tipo->nombre }}</td>
                                        <td class="centrar_iconos">
                                            <button wire:click="agregarTipo({{ $tipo->id }})"
                                                class="g_boton g_boton_primary g_boton_pequeno">
                                                Agregar <i class="fa-solid fa-plus"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($tiposDisponibles->hasPages())
                    <div class="g_paginacion">
                        {{ $tiposDisponibles->links('vendor.pagination.default-livewire') }}
                    </div>
                @endif

                @if ($tiposDisponibles->isEmpty())
                    <div class="g_vacio" style="padding: 40px 0;">
                        <p>No hay tipos disponibles.</p>
                        <i class="fa-regular fa-face-smile"></i>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .g_boton_pequeno {
        padding: 5px 12px;
        font-size: 0.85rem;
    }
</style>