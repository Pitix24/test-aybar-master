<div class="g_gap_pagina" style="display:grid; gap:18px;">
    <!-- Modal de Asignación de Superior -->
    @if($rolSeleccionadoId)
    <div style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 50; padding: 16px;">
        <div class="g_panel" style="width: 100%; max-width: 480px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); margin: 0; animation: scaleUp 0.2s ease-out; border-radius: 16px; border: 1px solid rgba(0,0,0,0.08); background: #ffffff;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border-bottom: 1px solid rgba(0,0,0,0.06); padding-bottom: 10px;">
                <h4 class="g_panel_titulo" style="margin: 0; font-size: 18px;">Asignar Rol Superior</h4>
                <button type="button" wire:click="$set('rolSeleccionadoId', null)" style="background: none; border: none; font-size: 20px; cursor: pointer; color: var(--color-neutral-400);">&times;</button>
            </div>

            <div style="margin-bottom: 18px; padding: 10px; background: #f8fafc; border-radius: 10px; font-size: 13px;">
                <strong>Rol a configurar:</strong>
                <span class="g_badge dark" style="margin-left: 4px;">{{ $rolesList->firstWhere('id', $rolSeleccionadoId)?->name }}</span>
            </div>

            <form wire:submit.prevent="guardarSuperior" class="formulario">
                <div class="g_margin_bottom_15">
                    <label for="modal_upper_id" style="font-weight: 600; font-size: 13px; color: var(--color-neutral-700);">Selecciona el superior directo</label>
                    <select id="modal_upper_id" wire:model="upper_id" style="width:100%; padding: 8px 12px; border-radius: 8px; border: 1px solid rgba(0,0,0,0.15); margin-top: 6px;">
                        <option value="">Sin superior (Rol Raíz)</option>
                        @foreach ($rolesDisponibles as $r)
                        <option value="{{ $r->id }}">{{ $r->name }}</option>
                        @endforeach
                    </select>
                    <p class="leyenda" style="margin-top: 6px; font-size: 11px; color: var(--color-neutral-500);">Solo se muestran roles activos de la misma área que no generen ciclos jerárquicos.</p>
                </div>

                <div class="g_fila" style="margin-top: 24px; gap: 10px; justify-content: flex-end; display: flex;">
                    <button type="button" class="g_boton light" wire:click="$set('rolSeleccionadoId', null)">
                        Cancelar
                    </button>
                    <button type="submit" class="g_boton primary">
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Cabecera de Página -->
    <div class="g_panel cabecera_titulo_pagina" style="gap:16px; align-items:flex-start;">
        <div style="display:grid; gap:8px;">
            <h2 style="margin-top:4px;">Jerarquía de Roles</h2>
        </div>

        <div class="cabecera_titulo_botones">
            @can('rol.lista')
            <a href="{{ route('erp.rol.vista.todo') }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    <!-- Indicadores / Resumen -->
    <div class="g_panel" style="padding: 16px;">
        <div class="g_fila" style="margin-bottom: 0;">
            <div class="g_columna_4">
                <div class="g_panel" style="padding:12px; margin: 0; background:linear-gradient(180deg,#f8fafc,#ffffff); border: 1px solid rgba(0,0,0,0.05); border-radius:12px;">
                    <small style="color:var(--color-neutral-500); font-weight:600;">Áreas</small>
                    <div class="g_resaltar" style="font-size:24px;">{{ $resumen['areas'] }}</div>
                </div>
            </div>
            <div class="g_columna_4">
                <div class="g_panel" style="padding:12px; margin: 0; background:linear-gradient(180deg,#ecfdf5,#ffffff); border: 1px solid rgba(0,0,0,0.05); border-radius:12px;">
                    <small style="color:var(--color-neutral-500); font-weight:600;">Roles Totales</small>
                    <div class="g_resaltar" style="font-size:24px;">{{ $resumen['roles'] }}</div>
                </div>
            </div>
            <div class="g_columna_4">
                <div class="g_panel" style="padding:12px; margin: 0; background:linear-gradient(180deg,#fffbeb,#ffffff); border: 1px solid rgba(0,0,0,0.05); border-radius:12px;">
                    <small style="color:var(--color-neutral-500); font-weight:600;">Vínculos Jerárquicos</small>
                    <div class="g_resaltar" style="font-size:24px;">{{ $resumen['vinculos'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros de Búsqueda -->
    <div class="g_panel" style="padding: 16px;">
        <h4 class="g_panel_titulo" style="margin-bottom: 12px; font-size: 15px;">Filtros de Búsqueda</h4>
        <form class="formulario">
            <div class="g_fila">
                <div class="g_columna_4 g_margin_bottom_10">
                    <label for="buscar">Buscar Rol</label>
                    <input id="buscar" type="text" wire:model.live.debounce.400ms="buscar" placeholder="Ej: supervisor, asesor..." style="margin-top: 4px;">
                </div>
                <div class="g_columna_4 g_margin_bottom_10">
                    <label for="selectedAreaId">Filtrar por Área</label>
                    <select id="selectedAreaId" wire:model.live="selectedAreaId" style="margin-top: 4px;">
                        <option value="">Todas las áreas</option>
                        @foreach ($areas as $area)
                        <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if($buscar !== '' || $selectedAreaId !== '' || $selectedLevelId !== '')
            <div style="margin-top: 8px;">
                <button type="button" class="g_boton dark" wire:click="resetFiltros" style="padding: 6px 14px; font-size: 12px;">
                    Limpiar filtros <i class="fa-solid fa-filter-circle-xmark"></i>
                </button>
            </div>
            @endif
        </form>
    </div>

    <!-- Vista de dos columnas (50 / 50) -->
    <div class="g_fila" style="align-items: flex-start; gap: 20px;">
        <!-- Columna Izquierda: Roles agrupados por Área -->
        <div class="g_columna_6" style="display:grid; gap:16px;">
            <div class="g_panel">
                <div style="display:flex; justify-content:space-between; gap:16px; align-items:center; margin-bottom:14px; border-bottom: 1px solid rgba(0,0,0,0.06); padding-bottom: 10px;">
                    <h4 class="g_panel_titulo" style="margin: 0; font-size: 16px;">Roles y Usuarios por Área</h4>
                    <span class="g_badge info">{{ $rolesList->count() }} roles encontrados</span>
                </div>

                @php
                    $rolesPorArea = $rolesList->groupBy('area_id');
                    $areasPorId = $areas->keyBy('id');
                @endphp

                <div style="display:grid; gap:12px;">
                @forelse ($rolesPorArea as $areaId => $rolesArea)
                    @php $area = $areasPorId->get($areaId); @endphp

                    <details class="g_panel" open style="border-left: 4px solid {{ $area?->color ?: '#475569' }}; margin: 0; padding: 14px;">
                        <summary style="cursor:pointer; list-style:none; display:flex; justify-content:space-between; align-items:center;">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <span style="width:10px; height:10px; border-radius:50%; background: {{ $area?->color ?: '#475569' }};"></span>
                                <strong style="font-size:15px; color: var(--color-neutral-800);">{{ $area?->nombre ?? 'Sin Área' }}</strong>
                            </div>
                            <span class="g_badge light" style="font-size: 11px;">{{ $rolesArea->count() }} roles</span>
                        </summary>

                        <div style="margin-top:14px; display:grid; gap:8px;">
                            @foreach ($rolesArea as $role)
                                <div style="background:#ffffff; border: 1px solid rgba(0,0,0,0.06); border-radius: 8px; overflow: hidden;">

                                    <!-- Fila principal: Nombre del rol + Botones -->
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 14px; background: #f8fafc;">
                                        <div style="display: flex; flex-direction: column; gap: 4px;">
                                            <span style="font-weight: 600; font-size: 14px; color: var(--color-neutral-800);">{{ $role->name }}</span>

                                            {{-- Superior --}}
                                            <div style="font-size: 11px; color: var(--color-neutral-500);">
                                                @if($role->superior)
                                                    <span><i class="fa-solid fa-chevron-up" style="font-size: 9px;"></i> Superior: <strong>{{ $role->superior->name }}</strong></span>
                                                @else
                                                    <span style="color: var(--color-neutral-400); font-style: italic;">Sin superior (Rol Raíz)</span>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Botones de acción (siempre visibles) --}}
                                        <div style="display: flex; gap: 6px;">
                                            <button type="button" class="g_accion editar" wire:click="seleccionarRol({{ $role->id }})" title="Asignar Superior">
                                                <i class="fa-solid fa-link"></i>
                                            </button>
                                            @if($role->upper_id)
                                                <button type="button" class="g_accion eliminar" wire:click="quitarSuperior({{ $role->id }})" title="Quitar Superior">
                                                    <i class="fa-solid fa-link-slash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Usuarios (colapsable) --}}
                                    <details style="border-top: 1px solid rgba(0,0,0,0.05);">
                                        <summary style="cursor: pointer; padding: 8px 14px; font-size: 12px; color: var(--color-neutral-600); display: flex; align-items: center; gap: 6px; user-select: none;">
                                            <i class="fa-solid fa-users"></i>
                                            <span>Usuarios ({{ $role->users->count() }})</span>
                                            <i class="fa-solid fa-chevron-down" style="margin-left: auto; transition: transform 0.2s;"></i>
                                        </summary>

                                        <div style="padding: 10px 14px; background: #fafbfc; display: flex; flex-wrap: wrap; gap: 6px;">
                                            @forelse($role->users as $u)
                                                <span class="g_badge light" style="font-size: 11px; padding: 3px 8px;" title="{{ $u->name }}">
                                                    {{ $u->name }}
                                                </span>
                                            @empty
                                                <span style="font-size: 12px; color: var(--color-neutral-400); font-style: italic;">Sin usuarios asignados</span>
                                            @endforelse
                                        </div>
                                    </details>

                                </div>
                            @endforeach
                        </div>
                    </details>
                @empty
                    <div class="g_vacio" style="padding: 30px;">
                        <p>No se encontraron roles con área configurada.</p>
                    </div>
                @endforelse
                </div>
            </div>
        </div>


        <!-- Columna Derecha: Árbol Visual de Jerarquía -->
        <div class="g_columna_6">
            <div class="g_panel" style="min-height: 450px;">
                <div style="display:flex; justify-content:space-between; gap:16px; align-items:center; margin-bottom:14px; border-bottom: 1px solid rgba(0,0,0,0.06); padding-bottom: 10px;">
                    <h4 class="g_panel_titulo" style="margin: 0; font-size: 16px;">Árbol de Jerarquía</h4>
                    <span class="g_badge dark">Estructura Organizacional</span>
                </div>

                @if($arbolJerarquia->isNotEmpty())
                    <div style="background: #fafbfc; border-radius: 12px; border: 1px solid rgba(0,0,0,0.04); padding: 10px; max-height: 600px; overflow-y: auto;">
                        @include('livewire.erp.sistema.rol.tree-node', ['nodes' => $arbolJerarquia])
                    </div>
                @else
                    <div class="g_vacio" style="padding: 60px 20px;">
                        <i class="fa-solid fa-sitemap" style="font-size: 32px; color: var(--color-neutral-300); margin-bottom: 12px;"></i>
                        <p style="font-weight: 600; color: var(--color-neutral-500); margin-bottom: 4px;">No hay estructura que mostrar</p>
                        <p style="font-size: 12px; color: var(--color-neutral-400); max-width: 250px; margin: 0 auto;">Usa el panel de la izquierda para vincular roles superiores e inferiores y comenzar a construir el organigrama.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes scaleUp {
        from {
            transform: scale(0.95);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }
</style>
