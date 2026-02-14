<div>
    <div class="g_margin_bottom_10">
        <label for="searchUser">Buscar y agregar participantes</label>
        <div class="g_select_search">
            <div class="g_posicion_relativa">
                <input type="text" id="searchUser" wire:model.live="searchUser" autocomplete="off"
                    class="g_select_search_input" placeholder="Escriba nombre del usuario...">
            </div>

            @if(!empty($participantesDisponibles))
                <div class="g_select_search_results">
                    @foreach($participantesDisponibles as $user)
                        <div class="g_select_search_item" wire:click="addParticipant({{ $user->id }})">
                            <span>{{ $user->name }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="g_margin_bottom_10">
        <h4 class="g_panel_titulo"><i class="fa-solid fa-users-gear"></i> Lista de participantes</h4>
        <div class="g_contenedor_tabla">
            <table class="g_tabla">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($participantesSeleccionados as $part)
                        <tr wire:key="part-{{ $part->id }}">
                            <td>
                                <strong>{{ $part->name }}</strong>
                            </td>
                            <td>{{ $part->email }}</td>
                            <td class="g_celda_acciones g_celda_centro">
                                <button type="button" wire:click="removeParticipant({{ $part->id }})"
                                    class="g_accion_eliminar" title="Quitar participante">
                                    <i class="fa-solid fa-user-minus"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="g_celda_vacia">No hay participantes agregados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>