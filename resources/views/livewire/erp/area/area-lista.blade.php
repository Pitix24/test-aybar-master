@section('tituloPagina', 'Lista de Áreas')

@section('anchoPantalla', '100%')

<div class="g_gap_pagina">
    <x-loading-overlay wire:loading
        wire:target="buscar, activo, perPage, resetFiltros, gotoPage, nextPage, previousPage, exportExcel"
        message="Cargando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Lista de Áreas</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.home') }}" class="g_boton g_boton_light">
                Inicio <i class="fa-solid fa-house"></i></a>

            <a href="{{ route('erp.area.vista.crear') }}" class="g_boton g_boton_primary">
                Crear <i class="fa-solid fa-square-plus"></i></a>
        </div>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Nombre / Email</label>
                    <input type="text" wire:model.live.debounce.1300ms="buscar" id="buscar" name="buscar">
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
                            <th>Icono</th>
                            <th>Nombre</th>
                            <th>Buzón Email</th>
                            <th>Sedes</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    @if ($items->isNotEmpty())
                        <tbody>
                            @foreach ($items as $index => $item)
                                <tr>
                                    <td>{{ $items->firstItem() + $index }}</td>
                                    <td><i class="{{ $item->icono ?? 'fa-solid fa-shapes' }}"
                                            style="color: {{ $item->color }}"></i></td>
                                    <td class="g_resaltar">{{ $item->nombre }}</td>
                                    <td>{{ $item->email_buzon ?? '-' }}</td>
                                    <td>
                                        @foreach($item->sedes as $sede)
                                            <span class="g_badge g_badge_light">{{ $sede->nombre }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <span class="estado {{ $item->activo ? 'g_activo' : 'g_desactivado' }}"><i
                                                class="fa-solid fa-circle"></i></span>
                                        {{ $item->activo ? 'Activo' : 'Desactivo' }}
                                    </td>

                                    <td class="centrar_iconos">
                                        <div style="display: flex; gap: 8px; justify-content: center;">
                                            <a href="{{ route('erp.area.vista.user', $item->id) }}" class="g_accion_ver"
                                                title="Usuarios">
                                                <span><i class="fa-solid fa-users-gear"></i></span>
                                            </a>
                                            <a href="{{ route('erp.area.vista.solicitud', $item->id) }}" class="g_accion_ver"
                                                title="Tipos de Solicitud">
                                                <span><i class="fa-solid fa-file-invoice"></i></span>
                                            </a>
                                            <a href="{{ route('erp.area.vista.editar', $item->id) }}" class="g_accion_editar"
                                                title="Editar">
                                                <span><i class="fa-solid fa-pencil"></i></span>
                                            </a>
                                        </div>
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