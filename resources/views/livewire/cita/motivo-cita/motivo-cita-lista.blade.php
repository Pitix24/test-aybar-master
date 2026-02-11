@section('tituloPagina', 'Motivos de Cita')

<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Motivos de Cita</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.motivo-cita.vista.crear') }}" class="g_boton g_boton_primary">
                Nuevo Motivo <i class="fa-solid fa-square-plus"></i></a>
        </div>
    </div>

    <div class="g_panel">
        <div class="tabla_cabecera">
            <div class="tabla_cabecera_buscar">
                <input type="text" wire:model.live.debounce.500ms="buscar" placeholder="Buscar por nombre...">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
        </div>

        <div class="g_contenedor_tabla">
            <table class="g_tabla">
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th>Nombre / Categoría</th>
                        <th style="width: 100px;">Color</th>
                        <th style="width: 100px;">Icono</th>
                        <th style="width: 120px;">Estado</th>
                        <th style="width: 100px;" class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr wire:key="motivo-{{ $item->id }}">
                            <td class="g_negrita">#{{ $item->id }}</td>
                            <td>
                                <span class="g_negrita">{{ $item->nombre }}</span>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div
                                        style="width: 20px; height: 20px; border-radius: 4px; background: {{ $item->color ?? '#64748b' }}; border: 1px solid #e2e8f0;">
                                    </div>
                                    <span style="font-size: 0.8rem; color: #64748b;">{{ $item->color }}</span>
                                </div>
                            </td>
                            <td class="g_celda_centro">
                                <i class="{{ $item->icono ?? 'fa-solid fa-tag' }}"
                                    style="color: {{ $item->color }}; font-size: 1.2rem;"></i>
                            </td>
                            <td>
                                @if($item->activo)
                                    <span class="g_badge g_badge_success"><i class="fa-solid fa-check-circle"></i> Activo</span>
                                @else
                                    <span class="g_badge g_badge_light"><i class="fa-solid fa-times-circle"></i> Inactivo</span>
                                @endif
                            </td>
                            <td class="g_celda_acciones g_celda_centro">
                                <a href="{{ route('erp.motivo-cita.vista.editar', $item->id) }}" class="g_accion_editar"
                                    title="Editar">
                                    <i class="fa-solid fa-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    @if($items->isEmpty())
                        <tr>
                            <td colspan="6">
                                <div class="g_vacio">
                                    <p>No se encontraron motivos de cita.</p>
                                    <i class="fa-solid fa-folder-open"></i>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        @if ($items->hasPages())
            <div class="g_paginacion">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</div>