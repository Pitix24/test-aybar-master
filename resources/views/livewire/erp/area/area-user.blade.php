@section('tituloPagina', 'Asignar Usuarios a Área')

@section('anchoPantalla', '100%')

<div class="g_gap_pagina">
    <x-loading-overlay wire:loading
        wire:target="searchAgregados, searchDisponibles, agregarUsuario, quitarUsuario, marcarPrincipal, exportExcel"
        message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <div>
            <h2>Gestión de Usuarios</h2>
            <p style="margin: 0; color: #64748b;">Área: <strong style="color: {{ $area->color }}">{{ $area->nombre }}</strong></p>
        </div>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.area.vista.todo') }}" class="g_boton g_boton_light">
                Lista Áreas <i class="fa-solid fa-list"></i></a>

            <a href="{{ route('erp.area.vista.editar', $area->id) }}" class="g_boton g_boton_secondary">
                Editar Área <i class="fa-solid fa-pencil"></i></a>

            <button type="button" class="g_boton g_boton_dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <div class="g_fila">
        <!-- ASIGNADOS -->
        <div class="g_columna_6">
            <div class="g_panel">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h4 class="g_panel_titulo" style="margin: 0;">Usuarios Asignados ({{ $usuariosAgregados->count() }})</h4>
                    
                    <button wire:click="exportExcel" class="g_boton g_boton_excel g_boton_pequeno" 
                        wire:loading.attr="disabled" wire:target="exportExcel">
                        <i class="fa-regular fa-file-excel"></i> Exportar
                    </button>
                </div>

                <div class="tabla_cabecera">
                    <div class="tabla_cabecera_buscar formulario" style="width: 100%;">
                        <div style="position: relative; width: 100%;">
                            <input type="text" wire:model.live.debounce.800ms="searchAgregados"
                                placeholder="Buscar en asignados..." style="padding-left: 35px;">
                            <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                        </div>
                    </div>
                </div>

                <div class="tabla_contenido">
                    <div class="contenedor_tabla" style="max-height: 500px; overflow-y: auto;">
                        <table class="tabla">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>Usuario</th>
                                    <th style="width: 80px;" title="Responsable Principal">Resp.</th>
                                    <th style="width: 100px;">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($usuariosAgregados as $index => $user)
                                    <tr wire:key="agregado-{{ $user->id }}" class="{{ $user->pivot->is_principal ? 'g_fila_principal' : '' }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div style="display: flex; flex-direction: column;">
                                                <span class="g_resaltar" style="font-weight: 600;">{{ $user->name }}</span>
                                                <span style="font-size: 0.75rem; color: #64748b;">{{ $user->email }}</span>
                                            </div>
                                        </td>
                                        <td class="centrar_iconos">
                                            <input type="radio" name="principal_radio" wire:click="marcarPrincipal({{ $user->id }})"
                                                {{ $user->pivot->is_principal ? 'checked' : '' }} 
                                                style="width: 18px; height: 18px; cursor: pointer;" title="Marcar como responsable principal">
                                        </td>
                                        <td class="centrar_iconos">
                                            <button wire:click="quitarUsuario({{ $user->id }})"
                                                class="g_boton g_boton_danger g_boton_pequeno" title="Quitar del área">
                                                <i class="fa-solid fa-user-minus"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($usuariosAgregados->isEmpty())
                    <div class="g_vacio" style="padding: 40px 0;">
                        <p>No hay usuarios asignados a esta área.</p>
                        <i class="fa-solid fa-users-slash"></i>
                    </div>
                @endif
            </div>
        </div>

        <!-- DISPONIBLES -->
        <div class="g_columna_6">
            <div class="g_panel">
                <h4 class="g_panel_titulo">Personal Disponible</h4>

                <div class="tabla_cabecera">
                    <div class="tabla_cabecera_buscar formulario" style="width: 100%;">
                        <div style="position: relative; width: 100%;">
                            <input type="text" wire:model.live.debounce.800ms="searchDisponibles"
                                placeholder="Buscar por nombre o email..." style="padding-left: 35px;">
                            <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                        </div>
                    </div>
                </div>

                <div class="tabla_contenido">
                    <div class="contenedor_tabla" style="max-height: 500px; overflow-y: auto;">
                        <table class="tabla">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>Usuario</th>
                                    <th>Rol</th>
                                    <th style="width: 100px;">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($usuariosDisponibles as $index => $user)
                                    <tr wire:key="disponible-{{ $user->id }}">
                                        <td>{{ $usuariosDisponibles->firstItem() + $index }}</td>
                                        <td>
                                            <div style="display: flex; flex-direction: column;">
                                                <span class="g_resaltar">{{ $user->name }}</span>
                                                <span style="font-size: 0.75rem; color: #64748b;">{{ $user->email }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="g_badge g_badge_light">{{ $user->rol }}</span>
                                        </td>
                                        <td class="centrar_iconos">
                                            <button wire:click="agregarUsuario({{ $user->id }})"
                                                class="g_boton g_boton_primary g_boton_pequeno">
                                                Agregar <i class="fa-solid fa-user-plus"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($usuariosDisponibles->hasPages())
                    <div class="g_paginacion">
                        {{ $usuariosDisponibles->links('vendor.pagination.default-livewire') }}
                    </div>
                @endif
                
                @if ($usuariosDisponibles->isEmpty())
                    <div class="g_vacio" style="padding: 40px 0;">
                        <p>No se encontraron usuarios disponibles.</p>
                        <i class="fa-regular fa-face-smile"></i>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .g_boton_pequeno {
        padding: 5px 12px;
        font-size: 0.85rem;
    }
    .g_fila_principal {
        background-color: #f0f9ff !important;
        border-left: 4px solid #3b82f6 !important;
    }
    .g_fila_principal td {
        color: #1e40af;
    }
</style>
