<div class="g_contenedor_tabla">
    <table class="g_tabla">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Usuario</th>
                <th>Acción</th>
                <th>Detalle</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($historial as $item)
                <tr wire:key="hist-item-{{ $item->id }}">
                    <td class="g_negrita">{{ $item->created_at->format('d/m H:i') }}</td>
                    <td>{{ $item->usuarioHistorial->name ?? 'Sistema' }}</td>
                    <td><span class="g_badge light">{{ $item->accion }}</span></td>
                    <td class="g_celda_wrap">
                        @foreach (explode(' | ', $item->detalle) as $linea)
                            <div>{{ $linea }}</div>
                        @endforeach
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="g_celda_vacia">Sin movimientos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>