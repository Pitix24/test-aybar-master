<div>
    <div class="g_margin_bottom_10">
        <h4 class="g_panel_titulo"><i class="fa-solid fa-users-gear"></i> Lista de participantes</h4>
        <div class="g_contenedor_tabla">
            <table class="g_tabla">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Fecha registro</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($participantesSeleccionados as $part)
                        <tr wire:key="part-{{ $part->id }}">
                            <td>
                                <strong>{{ $part->name }}</strong>
                            </td>
                            <td>{{ $part->email }}</td>
                            <td>{{ $part->pivot->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="g_celda_vacia">No hay participantes registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>