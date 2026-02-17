<div class="g_gap_pagina">
    <x-loading-overlay wire:loading
        wire:target="buscar, entrega_fest_id, confirmado, asistio, perPage, resetFiltros"
        message="Actualizando lista..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Lista de Invitados Oficiales</h2>

        <div class="cabecera_titulo_botones">
            @can('invitado-entrega-fest.exportar')
                <button type="button" class="g_boton light">
                    Exportar QR´s <i class="fa-solid fa-qrcode"></i>
                </button>
            @endcan
        </div>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_3">
                    <label>Buscar Invitado</label>
                    <div class="g_input_con_icono_derecha">
                        <input type="text" wire:model.live.debounce.400ms="buscar"
                            placeholder="DNI, Nombre o Código QR...">
                        <i class="fa-solid fa-search"></i>
                    </div>
                </div>

                <div class="g_margin_bottom_10 g_columna_3">
                    <label>Evento (Entrega Fest)</label>
                    <select wire:model.live="entrega_fest_id">
                        <option value="">Todos los eventos</option>
                        @foreach ($eventos as $e)
                            <option value="{{ $e->id }}">{{ $e->nombre }} ({{ $e->codigo }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Confirmado</label>
                    <select wire:model.live="confirmado">
                        <option value="">Todos</option>
                        <option value="1">Sí, confirmado</option>
                        <option value="0">No confirmado</option>
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Asistencia</label>
                    <select wire:model.live="asistio">
                        <option value="">Todos</option>
                        <option value="1">Asistió</option>
                        <option value="0">Faltó</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="g_panel">
        <div class="g_tabla_cabecera">
            <div class="g_tabla_cabecera_botones">
                @can('invitado-entrega-fest.exportar-filtro')
                    <button wire:click="exportExcelFiltro" class="g_boton excel" wire:loading.attr="disabled"
                        wire:target="exportExcelFiltro">
                        <span wire:loading.remove wire:target="exportExcelFiltro">Excel Filtrados <i
                                class="fa-regular fa-file-excel"></i></span>
                        <span wire:loading wire:target="exportExcelFiltro">Generando... <i
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
                        <th style="width: 100px;">Cód. QR</th>
                        <th>Persona / Invitado</th>
                        <th>Evento / Proyecto</th>
                        <th class="g_celda_centro">Acompañantes</th>
                        <th class="g_celda_centro">Confirmado</th>
                        <th class="g_celda_centro">Asistencia</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $i)
                        <tr wire:key="invitado-{{ $i->id }}">
                            <td class="g_negrita">
                                <code style="color: var(--color-primary);">{{ $i->codigo_invitado }}</code>
                            </td>
                            <td>
                                <div class="g_negrita">{{ $i->prospecto->nombre_completo ?? 'N/A' }}</div>
                                <div class="g_texto_pequeno">DNI: {{ $i->prospecto->dni ?? 'N/A' }}</div>
                            </td>
                            <td>
                                <div class="g_negrita">{{ $i->entregaFest->nombre ?? 'N/A' }}</div>
                                <div class="g_texto_pequeno">Fecha: {{ $i->entregaFest?->fecha_entrega?->format('d/m/Y') ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="g_celda_centro">
                                <span class="g_badge dark">{{ $i->acompanantes_count }} /
                                    {{ $i->cantidad_acompanantes_permitidos }}</span>
                            </td>
                            <td class="g_celda_centro">
                                <span class="g_badge {{ $i->confirmado ? 'success' : 'primary' }}">
                                    {{ $i->confirmado ? 'Confirmado' : 'Pendiente' }}
                                </span>
                            </td>
                            <td class="g_celda_centro">
                                @if($i->asistencia)
                                    <span class="g_badge success"
                                        title="Check-in: {{ $i->asistencia->fecha_checkin?->format('H:i') }}">
                                        <i class="fa-solid fa-check-double"></i> ASISTIÓ
                                    </span>
                                @else
                                    <span class="g_badge light">FALTÓ</span>
                                @endif
                            </td>
                            <td class="g_celda_acciones g_celda_centro">
                                @can('invitado-entrega-fest.ver')
                                    <button type="button" class="g_accion primary" title="Ver Detalle/QR">
                                        <i class="fa-solid fa-qrcode"></i>
                                    </button>
                                @endcan
                                @can('invitado-acompanante-entrega-fest.lista')
                                    <button type="button" class="g_accion light" title="Gestionar Acompañantes">
                                        <i class="fa-solid fa-users"></i>
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="g_vacio">
                                    <i class="fa-solid fa-user-clock"></i>
                                    <p>No hay invitados registrados para este evento.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
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