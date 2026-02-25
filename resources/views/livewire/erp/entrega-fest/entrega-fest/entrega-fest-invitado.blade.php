<div class="g_gap_pagina">
    <x-loading-overlay wire:loading message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Invitados: <span style="color: var(--color-primary);">{{ $evento->nombre }}</span></h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.vista.todo') }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>
            @can('entrega-fest.invitados')
                <a href="{{ route('erp.entrega-fest.vista.invitados.crear', $evento->id) }}" class="g_boton primary">
                    Generar Invitado <i class="fa-solid fa-id-card"></i>
                </a>
            @endcan
        </div>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Buscar (Nombre, DNI, Cód. Invitado)</label>
                    <input type="text" wire:model.live.debounce.400ms="buscar" placeholder="Ej: Juan Pérez o INV-...">
                </div>

                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Asistencia (Confirmación Web)</label>
                    <select wire:model.live="estado_confirmacion">
                        <option value="">Todos</option>
                        <option value="pendiente">PENDIENTE</option>
                        <option value="confirmado">CONFIRMADO</option>
                        <option value="no_asiste">NO ASISTE</option>
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Transporte</label>
                    <select wire:model.live="transporte">
                        <option value="">Todos</option>
                        <option value="bus">BUS AYBAR</option>
                        <option value="propio">MOVILIDAD PROPIA</option>
                        <option value="na">NO APLICA / N/A</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="g_panel">
        <div class="g_tabla_cabecera">
            <div class="g_tabla_cabecera_botones">
                <button wire:click="resetFiltros" class="g_boton danger" title="Limpiar Filtros">
                    <i class="fa-solid fa-rotate-left"></i>
                </button>

                @can('entrega-fest.invitados')
                    <button wire:click="exportExcelFiltro" class="g_boton success" title="Exportar Vista Actual">
                        Excel <i class="fa-solid fa-file-excel"></i>
                    </button>
                    <button wire:click="exportExcelTodo" class="g_boton dark" title="Exportar Todo el Evento">
                        Todo <i class="fa-solid fa-download"></i>
                    </button>
                @endcan
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
                        <th>Cód. Invitado</th>
                        <th>Prospecto / DNI</th>
                        <th>Proyecto</th>
                        <th class="g_celda_centro">Acompañantes</th>
                        <th class="g_celda_centro">Asistencia</th>
                        <th class="g_celda_centro">Transporte</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($items as $i)
                        <tr wire:key="invitado-{{ $i->id }}">
                            <td class="g_negrita" style="color: var(--color-primary);">{{ $i->codigo_invitado }}</td>
                            <td>
                                <div class="g_negrita">{{ $i->prospecto->nombres ?? 'N/A' }}</div>
                                <div style="font-size: 0.8rem; color: #666;">DNI: {{ $i->prospecto->dni ?? 'N/A' }}</div>
                            </td>
                            <td>{{ $i->prospecto->proyecto->nombre ?? 'N/A' }}</td>
                            <td class="g_celda_centro">
                                <span class="g_badge light">{{ $i->cantidad_acompanantes_permitidos }}</span>
                            </td>
                            <td class="g_celda_centro">
                                @php
                                    $claseConf = match ($i->estado_confirmacion) {
                                        'pendiente' => 'primary',
                                        'confirmado' => 'success',
                                        'no_asiste' => 'danger',
                                        default => 'light',
                                    };
                                @endphp
                                <span class="g_badge {{ $claseConf }}">{{ strtoupper($i->estado_confirmacion) }}</span>
                            </td>
                            <td class="g_celda_centro">
                                @php
                                    $transporteTexto = match ($i->transporte) {
                                        'bus' => 'BUS',
                                        'propio' => 'PROPIO',
                                        'na' => 'N/A',
                                        default => $i->transporte,
                                    };
                                @endphp
                                <span class="g_badge light">{{ strtoupper($transporteTexto) }}</span>
                            </td>
                            <td class="g_celda_acciones g_celda_centro">
                                @can('entrega-fest.invitados')
                                    <a href="{{ route('erp.entrega-fest.vista.invitados.editar', [$evento->id, $i->id]) }}"
                                        class="g_accion editar" title="Ver Detalles">
                                        <i class="fa-solid fa-eye"></i>
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
                <p>{{ $buscar ? 'No se encontraron invitados para "' . $buscar . '"' : 'No hay invitados registrados con estos filtros.' }}
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
</div>