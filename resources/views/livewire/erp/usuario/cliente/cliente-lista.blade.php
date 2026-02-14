<div class="g_gap_pagina">
    <x-loading-overlay wire:loading
        wire:target="buscar,email,activo,verificado,tratamiento,politica,fecha_inicio,fecha_fin,perPage,resetFiltros,exportExcel"
        message="Cargando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Clientes portal</h2>

        <div class="cabecera_titulo_botones">
        </div>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_3">
                    <label>Cliente / Nombre / DNI</label>
                    <input type="text" wire:model.live.debounce.1300ms="buscar">
                </div>

                <div class="g_margin_bottom_10 g_columna_3">
                    <label>Email</label>
                    <input type="text" wire:model.live.debounce.1300ms="email">
                </div>

                <div class="g_margin_bottom_10 g_columna_3">
                    <label>Activo</label>
                    <select wire:model.live="activo">
                        <option value="">Todos</option>
                        <option value="1">Sí</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_3">
                    <label>Email verificado</label>
                    <select wire:model.live="verificado">
                        <option value="">Todos</option>
                        <option value="1">Sí</option>
                        <option value="0">No</option>
                    </select>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_3">
                    <label>Tratamiento D.P.</label>
                    <select wire:model.live="tratamiento">
                        <option value="">Todos</option>
                        <option value="1">Sí</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_3">
                    <label>Política Comercial</label>
                    <select wire:model.live="politica">
                        <option value="">Todos</option>
                        <option value="1">Sí</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_3">
                    <label>Fecha inicio</label>
                    <input type="date" wire:model.live="fecha_inicio">
                </div>

                <div class="g_margin_bottom_10 g_columna_3">
                    <label>Fecha fin</label>
                    <input type="date" wire:model.live="fecha_fin">
                </div>
            </div>
        </div>
    </div>

    <div class="g_panel">
        <div class="g_tabla_cabecera">
            <div class="g_tabla_cabecera_botones">
                <button wire:click="exportExcel" class="g_boton g_boton_excel" wire:loading.attr="disabled"
                    wire:target="exportExcel">
                    <span wire:loading.remove wire:target="exportExcel">
                        Excel <i class="fa-regular fa-file-excel"></i>
                    </span>
                    <span wire:loading wire:target="exportExcel">
                        Exportando... <i class="fa-solid fa-spinner fa-spin"></i>
                    </span>
                </button>

                <button wire:click="resetFiltros" class="g_boton g_boton_danger">
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
                        <th class="g_celda_centro">Nº</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>DNI</th>
                        <th>Fecha creación</th>
                        <th class="g_celda_centro">Email verificado</th>
                        <th class="g_celda_centro">Trat. D.P.</th>
                        <th class="g_celda_centro">P. Comercial</th>
                        <th class="g_celda_centro">Estado</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($items as $index => $item)
                        <tr>
                            <td class="g_celda_centro">{{ $items->firstItem() + $index }}</td>
                            <td class="g_resaltar">{{ $item->name }}</td>
                            <td>{{ $item->email }}</td>
                            <td>{{ $item->cliente->dni ?? '-' }}</td>
                            <td>{{ $item->created_at }}</td>

                            <td class="g_celda_centro">
                                @if($item->email_verified_at)
                                    <span class="g_badge g_badge_success">Sí</span>
                                @else
                                    <span class="g_badge g_badge_danger">No</span>
                                @endif
                            </td>

                            <td class="g_celda_centro">
                                <span class="g_badge {{ $item->politica_uno ? 'g_badge_success' : 'g_badge_light' }}">
                                    {{ $item->politica_uno ? 'SI' : 'NO' }}
                                </span>
                            </td>

                            <td class="g_celda_centro">
                                <span class="g_badge {{ $item->politica_dos ? 'g_badge_success' : 'g_badge_light' }}">
                                    {{ $item->politica_dos ? 'SI' : 'NO' }}
                                </span>
                            </td>

                            <td class="g_celda_centro">
                                @if($item->activo)
                                    <span class="g_badge g_badge_success">Activo</span>
                                @else
                                    <span class="g_badge g_badge_danger">Inactivo</span>
                                @endif
                            </td>

                            <td class="g_celda_acciones g_celda_centro">
                                <a href="{{ route('erp.cliente.vista.editar', $item) }}" class="g_accion_editar"
                                    title="Editar">
                                    <i class="fa-solid fa-pencil"></i>
                                </a>
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
                <p>{{ $buscar ? 'No se encontraron resultados para "' . $buscar . '"' : 'No hay clientes registrados.' }}
                </p>
                <i class="fa-regular fa-face-grin-wink"></i>
            </div>
        @else
            <div class="g_paginacion">
                Mostrando {{ $items->firstItem() }} – {{ $items->lastItem() }}
                de {{ $items->total() }} registros
            </div>
        @endif
    </div>
</div>