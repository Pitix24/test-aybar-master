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
        <div class="g_fila g_margin_bottom_20">
            <div class="g_columna_4">
                <label>Buscar Persona</label>
                <div class="g_input_con_icono_derecha">
                    <input type="text" wire:model.live.debounce.300ms="buscar" placeholder="DNI, Nombre o Apellidos...">
                    <i class="fa-solid fa-search"></i>
                </div>
            </div>

            <div class="g_columna_3">
                <label>Evento (Entrega Fest)</label>
                <select wire:model.live="entrega_fest_id">
                    <option value="">Todos los eventos</option>
                    @foreach ($eventos as $e)
                        <option value="{{ $e->id }}">{{ $e->nombre }} ({{ $e->codigo }})</option>
                    @endforeach
                </select>
            </div>

            <div class="g_columna_2">
                <label>Estado</label>
                <select wire:model.live="estado">
                    <option value="">Todos</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="observado">Observado</option>
                    <option value="aprobado">Aprobado</option>
                    <option value="rechazado">Rechazado</option>
                </select>
            </div>

            <div class="g_columna_2">
                <label>Registros</label>
                <select wire:model.live="perPage">
                    <option value="15">15 registros</option>
                    <option value="30">30 registros</option>
                    <option value="50">50 registros</option>
                </select>
            </div>

            <div class="g_columna_1" style="display: flex; align-items: flex-end;">
                <button wire:click="resetFiltros" class="g_boton light" title="Limpiar filtros" style="width: 100%;">
                    <i class="fa-solid fa-eraser"></i>
                </button>
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
                    @forelse ($prospectos as $p)
                        <tr wire:key="prospecto-{{ $p->id }}">
                            <td class="g_negrita">{{ $p->dni }}</td>
                            <td>
                                <div class="g_negrita">{{ $p->nombre_completo }}</div>
                            </td>
                            <td>
                                <div class="g_negrita">{{ $p->entregaFest->nombre }}</div>
                                <div class="g_texto_pequeno">{{ $p->entregaFest->proyecto->nombre ?? 'Sin proyecto' }}</div>
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
                                    <button type="button" class="g_accion editar" title="Editar / Evaluar">
                                        <i class="fa-solid fa-magnifying-glass-chart"></i>
                                    </button>
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

        <div class="g_margin_top_20">
            {{ $prospectos->links() }}
        </div>
    </div>
</div>