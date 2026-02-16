<div class="g_gap_pagina">
    <x-loading-overlay wire:loading
        wire:target="buscar, perPage, estado_id, unidad_negocio_id, proyecto_id, gestor_id, fecha_inicio, fecha_fin, tipo_cierre, tiene_validacion, es_asbanc, cantidad_evidencias, cantidad_correos, resetFiltros, exportExcelFiltro, exportExcelTodo, gotoPage, nextPage, previousPage"
        message="Cargando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Validación de evidencia de pago portal cliente</h2>
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
                        <option value="">Todos</option>
                        @foreach ($unidades_negocios as $empresa)
                            <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Proyecto </label>
                    <select wire:model.live="proyecto_id">
                        <option value="">Todos</option>
                        @foreach ($proyectos as $proyecto)
                            <option value="{{ $proyecto->id }}">{{ $proyecto->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Gestor</label>
                    <select wire:model.live="gestor_id">
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
                        <option value="">Todos</option>
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
                        <option value="">Todos</option>
                        <option value="api">Cerrado con API</option>
                        <option value="manual">Cerrado manual</option>
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>¿Tiene fecha validación?</label>
                    <select wire:model.live="tiene_validacion">
                        <option value="">Todos</option>
                        <option value="si">Sí</option>
                        <option value="no">No</option>
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>¿SLIN Asbanc?</label>
                    <select wire:model.live="es_asbanc">
                        <option value="">Todos</option>
                        <option value="si">Sí</option>
                        <option value="no">No</option>
                    </select>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Cant. Evidencias</label>
                    <select wire:model.live="cantidad_evidencias">
                        <option value="">Todos</option>
                        <option value="0">Sin evidencias</option>
                        @for ($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}">{{ $i }} {{ $i == 1 ? 'Evidencia' : 'Evidencias' }}</option>
                        @endfor
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Cant. Emails</label>
                    <select wire:model.live="cantidad_correos">
                        <option value="">Todos</option>
                        <option value="0">Sin emails</option>
                        @for ($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}">{{ $i }} {{ $i == 1 ? 'Email' : 'Emails' }}</option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="g_panel">
        <div class="g_tabla_cabecera">
            <div class="g_tabla_cabecera_botones">
                @can('solicitud-evidencia-pago.exportar-filtro')
                    <button wire:click="exportExcelFiltro" class="g_boton excel" wire:loading.attr="disabled"
                        wire:target="exportExcelFiltro">
                        <span wire:loading.remove wire:target="exportExcelFiltro">Excel Filtrados <i
                                class="fa-regular fa-file-excel"></i></span>
                        <span wire:loading wire:target="exportExcelFiltro">Generando... <i
                                class="fa-solid fa-spinner fa-spin"></i></span>
                    </button>
                @endcan

                @can('solicitud-evidencia-pago.exportar-todo')
                    <button wire:click="exportExcelTodo" class="g_boton dark" wire:loading.attr="disabled"
                        wire:target="exportExcelTodo">
                        <span wire:loading.remove wire:target="exportExcelTodo">Excel Todo <i
                                class="fa-solid fa-file-export"></i></span>
                        <span wire:loading wire:target="exportExcelTodo">Generando... <i
                                class="fa-solid fa-spinner fa-spin"></i></span>
                    </button>
                @endcan

                <button wire:click="resetFiltros" class="g_boton danger">
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
                        <th>Gestor</th>
                        <th>Empresa</th>
                        <th>Proyecto</th>
                        <th class="g_celda_centro">Etapa</th>
                        <th class="g_celda_centro">Mz</th>
                        <th class="g_celda_centro">Lt</th>
                        <th class="g_celda_centro">Cuota</th>
                        <th class="g_celda_centro">Evid.</th>
                        <th class="g_celda_centro">Emails</th>
                        <th>Cliente</th>
                        <th>DNI</th>
                        <th class="g_celda_centro">Estado</th>
                        <th>Fecha</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <td class="g_celda_centro">
                                <span class="g_badge light">#{{ $item->id }}</span>
                            </td>
                            <td class="g_negrita g_resumir">
                                {{ $item->gestor?->name ?? 'Falta asignar' }}
                            </td>
                            <td class="g_resumir g_inferior">{{ $item->unidadNegocio?->nombre ?? '—' }}</td>
                            <td class="g_resumir g_inferior">{{ $item->proyecto?->nombre ?? '—' }}</td>
                            <td class="g_celda_centro">{{ $item->etapa }}</td>
                            <td class="g_celda_centro">{{ $item->manzana }}</td>
                            <td class="g_celda_centro">{{ $item->lote }}</td>
                            <td class="g_celda_centro">{{ $item->numero_cuota }}</td>
                            <td class="g_celda_centro">
                                <span class="g_badge {{ $item->evidencias_count > 0 ? 'primary' : 'light' }}">
                                    {{ $item->evidencias_count }}
                                </span>
                            </td>
                            <td class="g_celda_centro">
                                <span class="g_badge {{ $item->correos_count > 0 ? 'info' : 'light' }}">
                                    {{ $item->correos_count }}
                                </span>
                            </td>
                            <td class="g_resaltar g_resumir">{{ $item->userCliente?->name ?? '—' }}</td>
                            <td class="g_resaltar">{{ $item->userCliente?->perfilCliente?->dni ?? '—' }}</td>
                            <td class="g_celda_centro">
                                @if($item->estado)
                                    <span class="g_badge g_badge_soft" style="color: {{ $item->estado->color }};">
                                        {{ $item->estado->nombre }}
                                    </span>
                                @else
                                    <span class="g_badge light">S/E</span>
                                @endif
                            </td>
                            <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                            <td class="g_celda_acciones g_celda_centro">
                                <a href="{{ route('erp.solicitud-evidencia-pago.vista.ver', $item->id) }}"
                                    class="g_accion ver" title="Ver Detalle">
                                    <i class="fa-solid fa-eye"></i>
                                </a>

                                <a href="{{ route('erp.solicitud-evidencia-pago.vista.editar', $item->id) }}"
                                    class="g_accion editar" title="Editar">
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