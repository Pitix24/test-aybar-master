<div class="g_gap_pagina">

    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            Plantillas Globales MOP
            <span>Biblioteca de Tareas</span>
        </h2>
        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.vista.staff.mop.plantillas.crear') }}" class="g_boton guardar">
                Nueva Plantilla <i class="fa-solid fa-plus"></i>
            </a>
            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="g_panel">
        <div class="g_tabla_cabecera">
            <div class="g_tabla_cabecera_botones">
                <a href="{{ route('erp.entrega-fest.vista.staff.mop.plantillas.crear') }}" class="g_boton guardar">
                    Nueva Plantilla <i class="fa-solid fa-plus"></i>
                </a>
                <button wire:click="resetFiltros" class="g_boton danger">
                    Limpiar <i class="fa-solid fa-rotate-left"></i>
                </button>
            </div>
            <div class="g_tabla_cabecera_filtro formulario">
                <input type="text" wire:model.live="buscar" placeholder="Buscar por rol o instruccion..."
                    style="width: 300px;">
                <select wire:model.live="fase" style="width: 150px;">
                    <option value="">Todas las fases</option>
                    <option value="ANTES">Antes</option>
                    <option value="DURANTE">Durante</option>
                    <option value="CIERRE">Cierre</option>
                </select>
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
                                <span
                                    class="g_badge {{ $p->fase === 'ANTES' ? 'info' : ($p->fase === 'DURANTE' ? 'warning' : 'success') }} g_mayuscula"
                                    style="font-size:11px;">
                                    {{ $p->fase }}
                                </span>
                            </td>
                            <td>{{ Str::limit($p->instruccion, 80) }}</td>
                            <td class="g_celda_centro">{{ $p->prioridad }}</td>
                            <td class="g_celda_acciones g_celda_centro">
                                <a href="{{ route('erp.entrega-fest.vista.staff.mop.plantillas.editar', $p->id) }}"
                                    class="g_accion editar" title="Editar">
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