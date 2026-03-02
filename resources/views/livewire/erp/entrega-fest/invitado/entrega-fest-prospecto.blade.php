<div class="g_gap_pagina">
    <x-loading-overlay wire:loading message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Prospectos: <span>{{ $evento->nombre }}</span></h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.vista.panel', $evento->id) }}" class="g_boton info">
                <i class="fa-solid fa-grip"></i> Panel de Gestión
            </a>

            <a href="{{ route('erp.entrega-fest.vista.prospectos.crear', $evento->id) }}" class="g_boton primary">
                Crear <i class="fa-solid fa-square-plus"></i>
            </a>

             <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Buscar (Nombres, Celular, Email)</label>
                    <input type="text" wire:model.live.debounce.400ms="buscar">
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Proyecto</label>
                    <select wire:model.live="proyecto_id">
                        <option value="">Todos los proyectos del evento</option>
                        @foreach ($proyectos as $p)
                            <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Estado Prospecto</label>
                    <select wire:model.live="estado">
                        <option value="">Todos</option>
                        <option value="pendiente">PENDIENTE</option>
                        <option value="observado">OBSERVADO</option>
                        <option value="aprobado">APROBADO</option>
                        <option value="rechazado">RECHAZADO</option>
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Firma Contrato</label>
                    <select wire:model.live="estado_contrato_preeliminar_emitido">
                        <option value="">Todos</option>
                        <option value="pendiente">PENDIENTE</option>
                        <option value="observado">OBSERVADO</option>
                        <option value="aprobado">APROBADO</option>
                        <option value="rechazado">RECHAZADO</option>
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Grupo</label>
                    <select wire:model.live="grupo">
                        <option value="">Todos</option>
                        <option value="A">Grupo A</option>
                        <option value="B">Grupo B</option>
                        <option value="C">Grupo C</option>
                        <option value="D">Grupo D</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="g_panel">
        <div class="g_tabla_cabecera">
            <div class="g_tabla_cabecera_botones">
                <button wire:click="exportExcelFiltro" class="g_boton excel" wire:loading.attr="disabled"
                    wire:target="exportExcelFiltro">
                    <span wire:loading.remove wire:target="exportExcelFiltro">Excel Filtrados <i
                            class="fa-regular fa-file-excel"></i></span>
                    <span wire:loading wire:target="exportExcelFiltro">Generando... <i
                            class="fa-solid fa-spinner fa-spin"></i></span>
                </button>

                <button wire:click="exportExcelTodo" class="g_boton dark" wire:loading.attr="disabled"
                        wire:target="exportExcelTodo">
                        <span wire:loading.remove wire:target="exportExcelTodo">Excel Todo <i
                                class="fa-solid fa-file-export"></i></span>
                        <span wire:loading wire:target="exportExcelTodo">Generando... <i
                                class="fa-solid fa-spinner fa-spin"></i></span>
                </button>

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
                        <th>N°</th>
                        <th>DNI</th>
                        <th>Cliente</th>
                        <th>Proyecto</th>
                        <th>Lote/Mz</th>
                        <th class="g_celda_centro">BackOffice</th>
                        <th class="g_celda_centro">Estado Contrato Preliminar</th>
                        <th class="g_celda_centro">Fecha para Firmar</th>
                        <th class="g_celda_centro">Fecha Firmado</th>
                        <th class="g_celda_centro">Invitado</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($items as $index => $p)
                        <tr wire:key="prospecto-{{ $p->id }}">
                            <td class="g_celda_centro">{{ $items->firstItem() + $index }}</td>
                            <td>{{ $p->dni }}</td>
                            <td>
                                <div class="g_negrita">
                                    {{ $p->nombre_completo }}
                                </div>
                                <div>{{ $p->email }}</div>
                                <div>{{ $p->celular }}</div>
                            </td>
                            <td>{{ $p->proyecto->nombre ?? 'N/A' }}</td>
                            <td>{{ $p->lote }}{{ $p->manzana }}</td>
                            <td class="g_celda_centro">
                                @php
                                    $claseBO = match ($p->estado_backoffice) {
                                        'pendiente' => 'primary',
                                        'observado' => 'warning',
                                        'aprobado' => 'success',
                                        'rechazado' => 'danger',
                                        default => 'light',
                                    };
                                @endphp
                                <span class="g_badge {{ $claseBO }}">{{ strtoupper($p->estado_backoffice) }}</span>
                            </td>
                            <td class="g_celda_centro">
                                @php
                                    $claseEstado = match ($p->estado_contrato_preeliminar_emitido) {
                                        'pendiente' => 'primary',
                                        'observado' => 'warning',
                                        'aprobado' => 'success',
                                        'rechazado' => 'danger',
                                        default => 'light',
                                    };
                                @endphp
                                <span class="g_badge {{ $claseEstado }}">{{ strtoupper($p->estado_contrato_preeliminar_emitido) }}</span>
                            </td>
                            <td>{{ $p->fecha_firma ? date('d/m/Y', strtotime($p->fecha_firma)) : '' }}</td>
                            <td>{{ $p->fecha_generacion_contrato ? date('d/m/Y', strtotime($p->fecha_generacion_contrato)) : '' }}</td>
                            <td class="g_celda_centro">
                                @if ($p->invitado)
                                    <span class="g_badge success" title="{{ $p->invitado->estado_confirmacion }}">SÍ</span>
                                @else
                                    <span class="g_badge danger">NO</span>
                                @endif
                            </td>
                            <td class="g_celda_acciones g_celda_centro">
                                @can('entrega-fest.prospectos')
                                    <a href="{{ route('erp.entrega-fest.vista.prospectos.editar', [$evento->id, $p->id]) }}"
                                        class="g_accion editar" title="Editar / Evaluar">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                @endcan
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
                <p>{{ $buscar ? 'No se encontraron prospectos para "' . $buscar . '"' : 'No hay prospectos registrados en este evento.' }}
                </p>
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