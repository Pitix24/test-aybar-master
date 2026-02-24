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
                    <label>Buscar (Nombre, Apellidos, DNI, Cód. Invitado)</label>
                    <input type="text" wire:model.live.debounce.400ms="buscar" placeholder="Ej: Juan Pérez o INV-...">
                </div>

                <div class="g_columna_4">
                    <label>Confirmación</label>
                    <select wire:model.live="confirmado">
                        <option value="">Todos</option>
                        <option value="1">Confirmados</option>
                        <option value="0">Sin Confirmar</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="g_panel">
        <div class="g_tabla_cabecera">
            <div class="g_tabla_cabecera_botones">
                <button wire:click="resetFiltros" class="g_boton danger">
                    Limpiar Filtros <i class="fa-solid fa-rotate-left"></i>
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
                        <th>Prospecto / DNI</th>
                        <th>Proyecto</th>
                        <th class="g_celda_centro">Acompañantes</th>
                        <th class="g_celda_centro">Estado</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($items as $i)
                        <tr wire:key="invitado-{{ $i->id }}">
                            <td class="g_negrita" style="color: var(--color-primary);">{{ $i->codigo_invitado }}</td>
                            <td>
                                <div class="g_negrita">{{ $i->prospecto->nombre_completo ?? 'N/A' }}</div>
                                <div style="font-size: 0.8rem; color: #666;">DNI: {{ $i->prospecto->dni ?? 'N/A' }}</div>
                            </td>
                            <td>{{ $i->prospecto->proyecto->nombre ?? 'N/A' }}</td>
                            <td class="g_celda_centro">
                                <span class="g_badge light">{{ $i->cantidad_acompanantes_permitidos }}</span>
                            </td>
                            <td class="g_celda_centro">
                                <span class="g_badge {{ $i->confirmado ? 'success' : 'warning' }}">
                                    {{ $i->confirmado ? 'Confirmado' : 'Pendiente' }}
                                </span>
                            </td>
                            <td class="g_celda_acciones g_celda_centro">
                                @can('entrega-fest.invitados')
                                    <a href="{{ route('erp.entrega-fest.vista.invitados.editar', [$evento->id, $i->id]) }}"
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
                <p>{{ $buscar ? 'No se encontraron invitados para "' . $buscar . '"' : 'No hay invitados generados para este evento.' }}
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