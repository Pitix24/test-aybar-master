<div class="g_gap_pagina">
    <x-loading-overlay wire:loading
        wire:target="buscar, perPage, estado_id, unidad_negocio_id, proyecto_id, admin, fecha_inicio, fecha_fin, tipo_cierre, tiene_validacion, es_asbanc, resetFiltros, exportExcel, gotoPage, nextPage, previousPage"
        message="Cargando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Validación de evidencia de pago portal cliente</h2>

        <div class="cabecera_titulo_botones">
            <button wire:click="resetFiltros" class="g_boton g_boton_danger">
                Refresh Filtros <i class="fa-solid fa-rotate-left"></i>
            </button>
        </div>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Cliente/DNI/Nombres</label>
                    <input type="text" wire:model.live.debounce.1300ms="buscar" id="buscar" name="buscar">
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Empresa </label>
                    <select wire:model.live="unidad_negocio_id">
                        <option value="">TODOS</option>
                        @foreach ($empresas as $empresa)
                            <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Proyecto </label>
                    <select wire:model.live="proyecto_id">
                        <option value="">TODOS</option>
                        @foreach ($proyectos as $proyecto)
                            <option value="{{ $proyecto->id }}">{{ $proyecto->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Gestor</label>
                    <select wire:model.live="admin">
                        <option value="">Todos</option>
                        <option value="sin_asignar">FALTA ASIGNAR</option>
                        @foreach ($usuarios_admin as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Estado </label>
                    <select wire:model.live="estado_id">
                        <option value="">TODOS</option>
                        @foreach ($estados as $estadoItem)
                            <option value="{{ $estadoItem->id }}">{{ $estadoItem->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Fecha inicio</label>
                    <input type="date" wire:model.live="fecha_inicio">
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Fecha fin</label>
                    <input type="date" wire:model.live="fecha_fin">
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Tipo de cierre</label>
                    <select wire:model.live="tipo_cierre">
                        <option value="">TODOS</option>
                        <option value="api">CERRADO CON API</option>
                        <option value="manual">CERRADO MANUAL</option>
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>¿Tiene fecha validación?</label>
                    <select wire:model.live="tiene_validacion">
                        <option value="">TODOS</option>
                        <option value="si">SÍ</option>
                        <option value="no">NO</option>
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>¿SLIN Asbanc?</label>
                    <select wire:model.live="es_asbanc">
                        <option value="">TODOS</option>
                        <option value="si">SÍ</option>
                        <option value="no">NO</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="g_panel">
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
                <select wire:model.live="perPage">
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>

        <div class="g_contenedor_tabla">
            <table class="g_tabla">
                <thead>
                    <tr>
                        <th class="g_celda_centro">Nº</th>
                        <th>Gestor</th>
                        <th>Razón S.</th>
                        <th>Proyecto</th>
                        <th>Etapa</th>
                        <th>Mz.</th>
                        <th>Lt.</th>
                        <th>N° Cuota</th>
                        <th>Cliente</th>
                        <th>DNI</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($evidencias as $index => $item)
                        <tr>
                            <td class="g_celda_centro">{{ $evidencias->firstItem() + $index }}</td>
                            <td class="g_resaltar g_resumir">
                                {{ $item->gestor?->name ?? 'Falta asignar' }}
                            </td>
                            <td class="g_resumir">{{ $item->unidadNegocio?->nombre ?? '—' }}</td>
                            <td class="g_resumir">{{ $item->proyecto?->nombre ?? '—' }}</td>
                            <td class="g_resumir">{{ $item->etapa }}</td>
                            <td class="g_resumir">{{ $item->manzana }}</td>
                            <td class="g_resumir">{{ $item->lote }}</td>
                            <td class="g_resumir">{{ $item->numero_cuota }}</td>
                            <td class="g_resaltar g_resumir">{{ $item->userCliente?->name ?? '—' }}</td>
                            <td> {{ $item->userCliente?->perfilCliente?->dni ?? '—' }}</td>
                            <td>{{ $item->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                @if($item->estado)
                                    <span style="color: {{ $item->estado->color }};">
                                        <i class="{{ $item->estado->icono }}"></i> {{ $item->estado->nombre }}
                                    </span>
                                @else
                                    <span class="g_badge">S/E</span>
                                @endif
                            </td>
                            <td class="g_celda_acciones g_celda_centro">
                                <a href="{{ route('erp.solicitud-evidencia-pago.vista.editar', $item->id) }}"
                                    class="g_accion_editar" title="Editar">
                                    <i class="fa-solid fa-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($evidencias->hasPages())
            <div class="g_paginacion">
                {{ $evidencias->links('vendor.pagination.default-livewire') }}
            </div>
        @endif

        @if ($evidencias->isEmpty())
            <div class="g_vacio">
                <p>No se encontraron resultados.</p>
                <i class="fa-regular fa-face-grin-wink"></i>
            </div>
        @else
            <div class="g_paginacion">
                Mostrando {{ $evidencias->firstItem() ?? 0 }} – {{ $evidencias->lastItem() ?? 0 }}
                de {{ $evidencias->total() }} registros
            </div>
        @endif
    </div>
</div>