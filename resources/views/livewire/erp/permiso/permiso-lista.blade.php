@section('tituloPagina', 'Lista de Permisos')

<div class="g_gap_pagina">
    <x-loading-overlay wire:loading
        wire:target="buscar, perPage, resetFiltros, gotoPage, nextPage, previousPage, exportExcel"
        message="Cargando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Lista de Permisos</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.permiso.vista.todo') }}" class="g_boton g_boton_light">
                Inicio <i class="fa-solid fa-house"></i></a>

            <a href="{{ route('erp.permiso.vista.crear') }}" class="g_boton g_boton_primary">
                Crear <i class="fa-solid fa-square-plus"></i></a>
        </div>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_3">
                    <label>Permiso</label>
                    <input type="text" wire:model.live.debounce.1300ms="buscar">
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
                    Limpiar <i class="fa-solid fa-rotate-left"></i>
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
                            <th>ID</th>
                            <th>Nombre del Permiso</th>
                            <th>Guard</th>
                            <th>Modulo</th>
                            <th></th>
                        </tr>
                    </thead>

                    @if ($items->isNotEmpty())
                        <tbody>
                            @foreach ($items as $index => $item)
                                <tr>
                                    <td>{{ $items->firstItem() + $index }}</td>
                                    <td class="g_resaltar">{{ $item->name }}</td>
                                    <td>{{ $item->guard_name }}</td>
                                    <td>
                                        <span class="g_badge info">
                                            {{ $item->module ?? 'Sin Módulo' }}
                                        </span>
                                    </td>

                                    <td class="centrar_iconos">
                                        <a href="{{ route('erp.permiso.vista.editar', $item->id) }}" class="g_accion_editar">
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
                <p>{{ $buscar ? 'No se encontraron resultados para "' . $buscar . '"' : 'No hay items registrados.' }}</p>
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