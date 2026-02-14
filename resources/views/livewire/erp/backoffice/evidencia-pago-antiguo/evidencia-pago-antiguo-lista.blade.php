<div class="g_gap_pagina">
    <x-loading-overlay wire:loading
        wire:target="buscar, buscar_lote, perPage, estado_id, unidad_negocio_id, proyecto_id, tiene_fecha_deposito, tiene_imagen, tiene_numero_operacion, tiene_codigo_cuenta, resetFiltros, exportExcel, gotoPage, nextPage, previousPage"
        message="Cargando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Validación de evidencia de pago stock</h2>

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
                    <label>DNI/Nombres</label>
                    <input type="text" wire:model.live.debounce.1300ms="buscar" id="buscar" name="buscar">
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

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Razón Social </label>
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
                    <label>¿Tiene fecha deposito?</label>
                    <select wire:model.live="tiene_fecha_deposito">
                        <option value="">TODOS</option>
                        <option value="si">SÍ</option>
                        <option value="no">NO</option>
                    </select>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Lote</label>
                    <input type="text" wire:model.live.debounce.1300ms="buscar_lote" id="buscar_lote"
                        name="buscar_lote">
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>¿Tiene imagen?</label>
                    <select wire:model.live="tiene_imagen">
                        <option value="">TODOS</option>
                        <option value="si">SÍ</option>
                        <option value="no">NO</option>
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>¿Tiene número operación?</label>
                    <select wire:model.live="tiene_numero_operacion">
                        <option value="">TODOS</option>
                        <option value="si">SÍ</option>
                        <option value="no">NO</option>
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>¿Tiene código cuenta?</label>
                    <select wire:model.live="tiene_codigo_cuenta">
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
                        <th>Razón S.</th>
                        <th>Proyecto</th>
                        <th>Etapa</th>
                        <th>Lt.</th>
                        <th>N° Cuota</th>
                        <th class="g_celda_centro">Imagen</th>
                        <th>Cliente</th>
                        <th>DNI</th>
                        <th>N° Operación</th>
                        <th>Banco</th>
                        <th>Monto</th>
                        <th>Fecha Dep.</th>
                        <th>Estado</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($evidencias as $index => $item)
                        <tr>
                            <td class="g_celda_centro">{{ $evidencias->firstItem() + $index }}</td>
                            <td class="g_resumir">{{ $item->unidadNegocio?->nombre ?? '—' }}</td>
                            <td class="g_resumir">{{ $item->proyecto?->nombre ?? '—' }}</td>
                            <td class="g_resumir">{{ $item->etapa }}</td>
                            <td class="g_resumir">{{ $item->lote }}</td>
                            <td class="g_resumir">{{ $item->numero_cuota }}</td>
                            <td class="g_celda_centro">
                                @if ($item->imagen_url)
                                    <a href="{{ $item->imagen_url }}" target="_blank" title="Ver evidencia"
                                        style="color: var(--g-primary-color);">
                                        <i class="fa-regular fa-file-image fa-xl"></i>
                                    </a>
                                @else
                                    <span class="g_badge">Sin imagen</span>
                                @endif
                            </td>
                            <td class="g_resaltar g_resumir">{{ $item->nombres_cliente ?? '—' }}</td>
                            <td>{{ $item->dni_cliente ?? '—' }}</td>
                            <td>{{ $item->operacion_numero ?? '—' }}</td>
                            <td>{{ $item->banco ?? '—' }}</td>
                            <td class="g_negrita">{{ $item->moneda }} {{ number_format($item->monto, 2) }}</td>
                            <td>{{ $item->fecha_deposito ? $item->fecha_deposito->format('d/m/Y') : '—' }}</td>
                            <td>
                                @if($item->estado)
                                    <span style="color: {{ $item->estado->color }}; font-weight: 600;">
                                        <i class="{{ $item->estado->icono }}"></i> {{ $item->estado->nombre }}
                                    </span>
                                @else
                                    <span class="g_badge">S/E</span>
                                @endif
                            </td>
                            <td class="g_celda_acciones g_celda_centro">
                                <a href="{{ route('erp.evidencia-pago-antiguo.vista.editar', $item->id) }}"
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