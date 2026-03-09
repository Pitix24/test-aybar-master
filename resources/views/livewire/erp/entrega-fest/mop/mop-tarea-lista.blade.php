<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            Gestión de Tareas MOP
            <span>{{ $evento->nombre }}</span>
        </h2>
        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.mop.tareas.crear', $evento->id) }}" class="g_boton guardar">
                Asignar Tarea <i class="fa-solid fa-plus"></i>
            </a>
            <a href="{{ route('erp.entrega-fest.mop.todo', $evento->id) }}" class="g_boton dark">
                <i class="fa-solid fa-arrow-left"></i> Mi Manual
            </a>
        </div>
    </div>

    <div class="g_panel">
        <div class="g_tabla_cabecera">
            <div class="g_tabla_cabecera_botones">
                <button wire:click="resetFiltros" class="g_boton danger">
                    Limpiar <i class="fa-solid fa-rotate-left"></i>
                </button>
            </div>
            <div class="g_tabla_cabecera_filtro formulario">
                <input type="text" wire:model.live="buscar" placeholder="Buscar tarea..." style="width: 300px;">
                <select wire:model.live="fase" style="width: 150px;">
                    <option value="">Todas las fases</option>
                    <option value="ANTES">Antes</option>
                    <option value="DURANTE">Durante</option>
                    <option value="CIERRE">Cierre</option>
                </select>
            </div>
        </div>
    </div>

    <div class="g_panel">
        <div class="g_contenedor_tabla">
            <table class="g_tabla">
                <thead>
                    <tr>
                        <th>Responsable</th>
                        <th>Titulo</th>
                        <th>Fase</th>
                        <th class="g_celda_centro">Estado</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $t)
                        <tr wire:key="tarea-{{ $t->id }}">
                            <td>
                                <p class="g_negrita" style="margin:0;">{{ $t->user->name ?? '—' }}</p>
                            </td>
                            <td>
                                <p class="g_negrita" style="margin:0 0 2px 0;">{{ $t->titulo }}</p>
                                <p class="g_inferior" style="margin:0; font-size:11px;">
                                    {{ Str::limit($t->instruccion, 60) }}
                                </p>
                            </td>
                            <td>
                                <span
                                    class="g_badge {{ $t->fase === 'ANTES' ? 'info' : ($t->fase === 'DURANTE' ? 'warning' : 'success') }} g_mayuscula"
                                    style="font-size:11px;">
                                    {{ $t->fase }}
                                </span>
                            </td>
                            <td class="g_celda_centro">
                                @if($t->esta_completado)
                                    <span class="g_badge success" style="font-size:11px;"><i class="fa-solid fa-check"></i>
                                        Completada</span>
                                @else
                                    <span class="g_badge light" style="font-size:11px;">Pendiente</span>
                                @endif
                            </td>
                            <td class="g_celda_acciones g_celda_centro">
                                <a href="{{ route('erp.entrega-fest.mop.tareas.editar', [$evento->id, $t->id]) }}"
                                    class="g_accion editar" title="Editar">
                                    <i class="fa-solid fa-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="g_celda_centro g_inferior">No hay tareas asignadas para este evento.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:10px;">{{ $items->links() }}</div>
    </div>
</div>