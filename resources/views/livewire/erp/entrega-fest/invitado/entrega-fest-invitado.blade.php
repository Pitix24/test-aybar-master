<div class="g_gap_pagina">
    <x-loading-overlay wire:loading message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Invitados: <span style="color: var(--color-primary);">{{ $evento->nombre }}</span></h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.vista.panel', $evento->id) }}" class="g_boton info">
                <i class="fa-solid fa-grip"></i> Panel de Gestión
            </a>

            <a href="{{ route('erp.entrega-fest.asistencia.todo', $evento->id) }}" class="g_boton success">
                Asistencia <i class="fa-solid fa-user-group"></i>
            </a>

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Buscar (Nombre, DNI, Cód. Invitado)</label>
                    <input type="text" wire:model.live.debounce.400ms="buscar">
                </div>

                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Asistencia</label>
                    <select wire:model.live="confirmado">
                        <option value="">Todos</option>
                        <option value="1">CONFIRMADO</option>
                        <option value="0">NO ASISTE</option>
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Transporte</label>
                    <select wire:model.live="transporte">
                        <option value="">Todos</option>
                        <option value="BUS">BUS AYBAR</option>
                        <option value="PROPIO">MOVILIDAD PROPIA</option>
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
                        <th>Cód. Invitado</th>
                        <th>Tipo</th>
                        <th>Estado Bacckoffice</th>
                        <th>Invitado / DNI</th>
                        <th>Lote / Proyecto</th>
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
                                            @if ($i->prospecto_entrega_fest_id)
                                                <span class="g_badge success">TITULAR</span>
                                            @else
                                                <span class="g_badge info">COPROP.</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $estadoKey = $i->estado_backoffice;
                                                $estadoInfo = \App\Models\ProspectoEntregaFest::ESTADO_BACKOFFICE[$estadoKey] ?? null;
                                            @endphp
                                            @if ($estadoInfo)
                                                <span class="g_badge"
                                                    style="background-color: {{ $estadoInfo['color'] }}; color: white; font-size: 0.75rem;">
                                                    {{ $estadoInfo['label'] }}
                                                </span>
                                                <x-tooltip text="{{ $estadoInfo['mensaje'] }}" />
                                            @else
                                                <span class="g_badge light" style="font-size: 0.75rem;">N/D</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="g_negrita">{{ $i->nombre_completo }}</div>
                                            <div style="font-size: 0.8rem; color: #666;">
                                                DNI: {{ $i->prospecto?->dni ?? $i->copropietario?->dni ?? 'N/A' }}
                                            </div>
                                            <div style="font-size: 0.8rem; color: #666;">
                                                Celular: {{ $i->prospecto?->celular ?? $i->copropietario?->celular ?? 'N/A' }}
                                            </div>
                                            <div style="font-size: 0.8rem; color: #666;">
                                                Correo: {{ $i->prospecto?->email ?? $i->copropietario?->email ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-size: 0.85rem;">
                                                {{ $i->prospecto?->proyecto?->nombre
                        ?? $i->copropietario?->prospecto?->proyecto?->nombre
                        ?? 'N/A' }}
                                            </div>
                                            <div style="font-size: 0.75rem; color: #777;">
                                                Mz: {{ $i->manzana ?? '—' }} / Lt: {{ $i->lote ?? '—' }}
                                            </div>
                                        </td>
                                        <td class="g_celda_centro">
                                            @php
                                                $totalPermitido = $i->cantidad_acompanantes_permitidos;
                                                $registrados = $i->acompanantes->count();
                                                $restantes = $totalPermitido - $registrados;
                                            @endphp
                                            <div
                                                style="font-weight: bold; color: var(--color-primary); font-size: 1.1rem; margin-bottom: 5px;">
                                                {{ $registrados }} / {{ $totalPermitido }}
                                            </div>

                                            @if($registrados > 0)
                                                <div
                                                    style="font-size: 0.75rem; text-align: left; background: #f9f9f9; padding: 6px; border-radius: 6px; border: 1px solid #eee; display: inline-block;">
                                                    @foreach($i->acompanantes as $ac)
                                                        <div style="margin-bottom: 2px;">
                                                            <i class="fa-solid fa-user-check" style="color: #10B981; font-size: 0.65rem;"></i>
                                                            {{ Str::limit($ac->nombres, 15) }}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif

                                            @if($restantes > 0 && $totalPermitido > 0)
                                                <div style="font-size: 0.70rem; color: #f59e0b; margin-top: 5px;">
                                                    Faltan {{ $restantes }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="g_celda_centro">
                                            @if ($i->confirmado)
                                                <span class="g_badge success">CONFIRMADO</span>
                                            @else
                                                <span class="g_badge danger">NO ASISTE</span>
                                            @endif
                                        </td>
                                        <td class="g_celda_centro">
                                            @php
                                                $transporteTexto = match ($i->transporte) {
                                                    'BUS' => 'BUS',
                                                    'PROPIO' => 'PROPIO',
                                                    default => $i->transporte,
                                                };
                                            @endphp
                                            <span class="g_badge light">{{ $transporteTexto }}</span>
                                        </td>
                                        <td class="g_celda_acciones g_celda_centro">
                                            @can('invitado.editar')
                                                <a href="{{ route('erp.entrega-fest.invitado.editar', [$evento->id, $i->id]) }}"
                                                    class="g_accion editar" title="Editar">
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