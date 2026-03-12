<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            Gestión de Tareas MOP
            <span>{{ $evento->nombre }}</span>
        </h2>
        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.mop.todo', $evento->id) }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>

            <a href="{{ route('erp.entrega-fest.vista.staff', $evento->id) }}" class="g_boton info">
                <i class="fa-solid fa-grip"></i> Panel de Staff
            </a>

            <a href="{{ route('erp.entrega-fest.mop.tareas.crear', $evento->id) }}" class="g_boton primary">
                Crear Tarea <i class="fa-solid fa-square-plus"></i>
            </a>

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Tarea (Nombre)</label>
                    <input type="text" wire:model.live.debounce.400ms="buscar">
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Responsables</label>
                    <select wire:model.live="user_id">
                        <option value="">Todos</option>
                        @foreach($usuarios as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Fases</label>
                    <select wire:model.live="fase">
                        <option value="">Todas</option>
                        <option value="ANTES">Antes</option>
                        <option value="DURANTE">Durante</option>
                        <option value="CIERRE">Cierre</option>
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Estado</label>
                    <select wire:model.live="esta_completado">
                        <option value="">Todos</option>
                        <option value="0">Pendiente</option>
                        <option value="1">Completada</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="g_panel">
        <div class="g_tabla_cabecera">
            <div class="g_tabla_cabecera_botones">
                @can('entrega-fest.exportar-filtro')
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
                        <th>Responsable</th>
                        <th>Titulo</th>
                        <th>Fase</th>
                        <th>Evidencia</th>
                        <th class="g_celda_centro">Hora</th>
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
                                @if($t->esta_completado && $t->getFirstMediaUrl('evidencias'))
                                    <a href="{{ $t->getFirstMediaUrl('evidencias') }}" target="_blank">
                                        <img src="{{ $t->getFirstMediaUrl('evidencias') }}"
                                            style="width:50px; height:38px; object-fit:cover; border-radius:4px; border: 1px solid #ddd;">
                                    </a>
                                @endif
                            </td>
                            <td class="g_celda_centro">
                                @if($t->completado_at)
                                    <span class="g_inferior" style="font-size:10px; opacity:0.7;">
                                        {{ $t->completado_at->format('H:i') }}
                                    </span>
                                @endif
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
                            <td colspan="6" class="g_celda_centro g_inferior">No hay tareas asignadas para este evento.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:10px;">{{ $items->links() }}</div>
    </div>
</div>