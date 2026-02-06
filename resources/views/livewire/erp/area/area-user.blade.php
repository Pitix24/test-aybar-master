@section('tituloPagina', 'Asignar Usuarios a Área')

<div class="g_gap_pagina">

    <div class="g_panel cabecera_titulo_pagina">
        <div>
            <h2>Gestión de Usuarios</h2>
            <p style="margin: 0; color: #64748b;">Área: <strong style="color: {{ $area->color }}">{{ $area->nombre }}</strong></p>
        </div>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.area.vista.todo') }}" class="g_boton g_boton_light">
                Lista Áreas <i class="fa-solid fa-list"></i></a>
            
            <button type="button" class="g_boton g_boton_primary" wire:click="syncUsers">
                Guardar Cambios <i class="fa-solid fa-save"></i>
            </button>
        </div>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_columna_6">
                    <label>Buscar Usuario</label>
                    <input type="text" wire:model.live.debounce.500ms="buscar" placeholder="Nombre del usuario...">
                </div>
            </div>
        </div>
    </div>

    <div class="g_panel">
        <div class="tabla_contenido">
            <div class="contenedor_tabla">
                <table class="tabla">
                    <thead>
                        <tr>
                            <th style="width: 50px;">Asignado</th>
                            <th style="width: 50px;">Principal</th>
                            <th>Nombre del Usuario</th>
                            <th>Email</th>
                            <th>Rol</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr class="{{ in_array($user->id, $selectedUsers) ? 'g_fila_seleccionada' : '' }}">
                                <td class="centrar_iconos">
                                    <input type="checkbox" wire:click="toggleUser({{ $user->id }})" 
                                        {{ in_array($user->id, $selectedUsers) ? 'checked' : '' }}
                                        style="width: 20px; height: 20px; cursor: pointer;">
                                </td>
                                <td class="centrar_iconos">
                                    @if(in_array($user->id, $selectedUsers))
                                        <input type="radio" name="principal" wire:click="setPrincipal({{ $user->id }})"
                                            {{ $principalUserId == $user->id ? 'checked' : '' }}
                                            style="width: 20px; height: 20px; cursor: pointer;">
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="g_resaltar">{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="g_badge g_badge_light">{{ $user->rol }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="formulario_botones" style="margin-top: 20px;">
            <button type="button" class="g_boton g_boton_guardar" wire:click="syncUsers" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="syncUsers">
                    <i class="fa-solid fa-save"></i> Guardar Cambios
                </span>
                <span wire:loading wire:target="syncUsers">
                    <i class="fa-solid fa-spinner fa-spin"></i> Guardando...
                </span>
            </button>
        </div>
    </div>
</div>

<style>
    .g_fila_seleccionada {
        background-color: #f0f9ff !important;
    }
</style>
