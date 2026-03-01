<div class="g_gap_pagina">

    <div class="g_panel cabecera_titulo_pagina">
        <h2><span>MOP</span> Plantillas Globales</h2>
        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.mop.plantillas.crear') }}" class="g_boton guardar">
                <i class="fa-solid fa-plus"></i> Nueva Plantilla
            </a>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="g_panel">
        <div class="g_fila" style="gap:10px;">
            <div class="g_columna_6">
                <input type="text" wire:model.live="buscar" placeholder="Buscar por rol o instruccion...">
            </div>
            <div class="g_columna_3">
                <select wire:model.live="fase">
                    <option value="">Todas las fases</option>
                    <option value="ANTES">Antes</option>
                    <option value="DURANTE">Durante</option>
                    <option value="CIERRE">Cierre</option>
                </select>
            </div>
            <div class="g_columna_3">
                <button wire:click="resetFiltros" class="g_boton light">
                    <i class="fa-solid fa-rotate-left"></i> Limpiar
                </button>
            </div>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="g_panel">
        <div class="g_contenedor_tabla">
            <table class="g_tabla">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Rol / Cargo</th>
                        <th>Fase</th>
                        <th>Instruccion</th>
                        <th class="g_celda_centro">Prioridad</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $p)
                        <tr wire:key="plantilla-{{ $p->id }}">
                            <td class="g_inferior">{{ $p->id }}</td>
                            <td class="g_negrita">{{ $p->rol_nombre }}</td>
                            <td>
                                <span class="g_badge {{ $p->fase === 'ANTES' ? 'info' : ($p->fase === 'DURANTE' ? 'warning' : 'success') }} g_mayuscula" style="font-size:11px;">
                                    {{ $p->fase }}
                                </span>
                            </td>
                            <td>{{ Str::limit($p->instruccion, 80) }}</td>
                            <td class="g_celda_centro">{{ $p->prioridad }}</td>
                            <td class="g_celda_acciones g_celda_centro">
                                <a href="{{ route('erp.entrega-fest.mop.plantillas.editar', $p->id) }}" class="g_accion editar" title="Editar">
                                    <i class="fa-solid fa-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="g_celda_centro g_inferior">No hay plantillas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:10px;">{{ $items->links() }}</div>
    </div>
</div>