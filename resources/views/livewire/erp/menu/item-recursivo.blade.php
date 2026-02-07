<tr>
    <td>
        <div style="display: flex; align-items: center; padding-left: {{ $espacio }}px;">
            @if($item->submenus->isNotEmpty())
                <i class="fa-solid fa-folder-tree g_margin_right_10" style="color: var(--color-brand-primary);"></i>
            @else
                <i class="fa-solid fa-link g_margin_right_10" style="color: var(--color-neutral-400);"></i>
            @endif

            <div style="display: flex; flex-direction: column;">
                <span class="g_resaltar">
                    <i class="{{ $item->icono }} g_margin_right_5"></i> {{ $item->nombre }}
                </span>
                @if($item->roles || $item->permisos)
                    <div class="g_celda_tags" style="margin-top: 4px;">
                        @foreach($item->roles ?? [] as $r)
                            <span class="g_badge g_badge_light" style="font-size: 10px;">R: {{ $r }}</span>
                        @endforeach
                        @foreach($item->permisos ?? [] as $p)
                            <span class="g_badge g_badge_warning" style="font-size: 10px;">P: {{ $p }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </td>
    <td>
        <code style="font-size: 11px;">{{ $item->ruta }}</code>
        <br>
        <span style="color: var(--color-neutral-400); font-size: 11px;">{{ $item->url }}</span>
    </td>
    <td class="g_celda_centro">
        <span class="g_badge g_badge_info">{{ $item->nivel }}</span>
    </td>
    <td class="g_celda_centro">
        {{ $item->orden }}
    </td>
    <td class="g_celda_centro">
        <button wire:click="toggleActivo({{ $item->id }})" class="g_boton_invisible">
            @if($item->activo)
                <span class="g_badge g_badge_success">Activo</span>
            @else
                <span class="g_badge g_badge_danger">Inactivo</span>
            @endif
        </button>
    </td>
    <td class="g_celda_acciones g_celda_centro centro">
        @can('menu-editar')
            <a href="{{ route('erp.menu.vista.editar', $item->id) }}" class="g_accion_editar" title="Editar">
                <i class="fa-solid fa-pencil"></i>
            </a>
        @endcan
        @can('menu-eliminar')
            <button
                wire:click="$dispatch('confirmarEliminar', { id: {{ $item->id }}, mensaje: '¿Eliminar este ítem y todos sus submenús?' })"
                class="g_accion_eliminar" title="Eliminar">
                <i class="fa-solid fa-trash-can"></i>
            </button>
        @endcan
    </td>
</tr>

@foreach ($item->submenus as $submenu)
    @include('livewire.erp.menu.item-recursivo', ['item' => $submenu, 'espacio' => $espacio + 25])
@endforeach