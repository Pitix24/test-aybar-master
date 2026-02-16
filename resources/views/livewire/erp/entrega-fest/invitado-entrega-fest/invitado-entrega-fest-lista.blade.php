<div class="g_gap_pagina">
    <x-loading-overlay wire:loading message="Actualizando lista..." />

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
        <div class="g_fila g_margin_bottom_20">
            <div class="g_columna_3">
                <label>Buscar Invitado</label>
                <div class="g_input_con_icono_derecha">
                    <input type="text" wire:model.live.debounce.300ms="buscar" placeholder="DNI, Nombre o Código QR...">
                    <i class="fa-solid fa-search"></i>
                </div>
            </div>

            <div class="g_columna_3">
                <label>Evento</label>
                <select wire:model.live="entrega_fest_id">
                    <option value="">Todos los eventos</option>
                    @foreach ($eventos as $e)
                        <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="g_columna_2">
                <label>Confirmado</label>
                <select wire:model.live="confirmado">
                    <option value="">Cualquier estado</option>
                    <option value="1">Sí, confirmado</option>
                    <option value="0">No confirmado</option>
                </select>
            </div>

            <div class="g_columna_2">
                <label>Asistencia</label>
                <select wire:model.live="asistio">
                    <option value="">Todos</option>
                    <option value="1">Asistió</option>
                    <option value="0">Faltó</option>
                </select>
            </div>

            <div class="g_columna_2" style="display: flex; align-items: flex-end;">
                <button wire:click="resetFiltros" class="g_boton light" title="Limpiar filtros" style="width: 100%;">
                    <i class="fa-solid fa-eraser"></i>
                </button>
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
                    @forelse ($invitados as $i)
                        <tr wire:key="invitado-{{ $i->id }}">
                            <td class="g_negrita"><code
                                    style="color: var(--color-primary);">{{ $i->codigo_invitado }}</code></td>
                            <td>
                                <div class="g_negrita">{{ $i->prospecto->nombre_completo }}</div>
                                <div class="g_texto_pequeno">DNI: {{ $i->prospecto->dni }}</div>
                            </td>
                            <td>
                                <div class="g_negrita">{{ $i->entregaFest->nombre }}</div>
                                <div class="g_texto_pequeno">Fecha: {{ $i->entregaFest->fecha_entrega->format('d/m/Y') }}
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
                                        title="Check-in: {{ $i->asistencia->fecha_checkin->format('H:i') }}">
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

        <div class="g_margin_top_20">
            {{ $invitados->links() }}
        </div>
    </div>
</div>