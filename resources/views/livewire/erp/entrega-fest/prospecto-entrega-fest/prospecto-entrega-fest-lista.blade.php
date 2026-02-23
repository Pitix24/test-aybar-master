<div class="g_gap_pagina">
    <x-loading-overlay wire:loading message="Filtrando prospectos..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Lista de Prospectos</h2>

        <div class="cabecera_titulo_botones">
            @can('prospecto-entrega-fest.crear')
                <button type="button" class="g_boton primary">
                    Importar Excel <i class="fa-solid fa-file-import"></i>
                </button>
            @endcan
        </div>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Buscar Persona</label>
                    <div class="g_input_con_icono_derecha">
                        <input type="text" wire:model.live.debounce.300ms="buscar"
                            placeholder="DNI, Nombre o Apellidos...">
                    </div>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Evento (Entrega Fest)</label>
                    <select wire:model.live="entrega_fest_id">
                        <option value="">Todos los eventos</option>
                        @foreach ($eventos as $e)
                            <option value="{{ $e->id }}">{{ $e->nombre }} ({{ $e->codigo }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Estado</label>
                    <select wire:model.live="estado">
                        <option value="">Todos</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="observado">Observado</option>
                        <option value="aprobado">Aprobado</option>
                        <option value="rechazado">Rechazado</option>
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Registros</label>
                    <select wire:model.live="perPage">
                        <option value="15">15 registros</option>
                        <option value="30">30 registros</option>
                        <option value="50">50 registros</option>
                    </select>
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
                            <th style="width: 100px;">DNI</th>
                            <th>Nombres y Apellidos</th>
                            <th>Evento / Proyecto</th>
                            <th class="g_celda_centro">Estado</th>
                            <th>Registrado por / Fecha</th>
                            <th class="g_celda_centro">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $p)
                            <tr wire:key="prospecto-{{ $p->id }}">
                                <td class="g_negrita">{{ $p->dni }}</td>
                                <td>
                                    <div class="g_negrita">{{ $p->nombre_completo }}</div>
                                    <div class="g_texto_pequeno">
                                        @if($p->etapa || $p->manzana || $p->lote)
                                            Etapa: {{ $p->etapa ?? '-' }} | Mz: {{ $p->manzana ?? '-' }} | Lt:
                                            {{ $p->lote ?? '-' }}
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="g_negrita">{{ $p->entregaFest->nombre }}</div>
                                    <div class="g_texto_pequeno">{{ $p->proyecto->nombre ?? 'Sin proyecto' }}
                                    </div>
                                </td>
                                <td class="g_celda_centro">
                                    @php
                                        $badgeClass = match ($p->estado) {
                                            'aprobado' => 'success',
                                            'rechazado' => 'error',
                                            'observado' => 'warning',
                                            default => 'primary'
                                        };
                                    @endphp
                                    <span class="g_badge {{ $badgeClass }}">{{ ucfirst($p->estado) }}</span>
                                </td>
                                <td>
                                    <div class="g_texto_secundario">{{ $p->user->name ?? 'Sistema' }}</div>
                                    <div class="g_texto_pequeno">{{ $p->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td class="g_celda_acciones g_celda_centro">
                                    @can('prospecto-entrega-fest.editar')
                                        <a href="{{ route('erp.prospecto-entrega-fest.vista.editar', $p->id) }}"
                                            class="g_accion editar" title="Editar / Evaluar">
                                            <i class="fa-solid fa-magnifying-glass-chart"></i>
                                        </a>
                                    @endcan
                                    @if($p->estado == 'aprobado' && !$p->invitado)
                                        @can('invitado-entrega-fest.crear')
                                            <button type="button" wire:click="generarInvitado({{ $p->id }})"
                                                wire:confirm="¿Está seguro de convertir este prospecto en INVITADO OFICIAL?"
                                                class="g_accion success" title="Generar Invitado">
                                                <i class="fa-solid fa-user-check"></i>
                                            </button>
                                        @endcan
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="g_vacio">
                                        <i class="fa-solid fa-user-slash"></i>
                                        <p>No se encontraron prospectos con los filtros aplicados.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
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