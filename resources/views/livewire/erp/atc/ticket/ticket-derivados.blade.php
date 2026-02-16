<div class="g_contenedor_tabla">
    <table class="g_tabla">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>De Área</th>
                <th>A Área</th>
                <th>Deriva</th>
                <th>Recibe</th>
                <th>Motivo</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($derivados as $der)
                <tr wire:key="der-list-{{ $der->id }}">
                    <td class="g_negrita">{{ $der->created_at->format('d/m H:i') }}</td>
                    <td>{{ $der->deArea->nombre ?? 'N/A' }}</td>
                    <td><span class="g_badge g_badge_primary">{{ $der->aArea->nombre ?? 'N/A' }}</span>
                    </td>
                    <td><small>{{ $der->usuarioDeriva->name ?? 'N/A' }}</small></td>
                    <td><small>{{ $der->usuarioRecibe->name ?? 'N/A' }}</small></td>
                    <td class="g_celda_wrap">{{ $der->motivo ?? 'Sin motivo' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="g_celda_vacia">No hay derivaciones registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>