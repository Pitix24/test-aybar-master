<ul style="list-style-type: none; padding-left: 20px; border-left: 2px dashed rgba(59, 130, 246, 0.2); margin-top: 10px; display: grid; gap: 10px;">
    @foreach ($nodes as $node)
        <li style="position: relative; margin-bottom: 8px;">
            <!-- Indicator dot -->
            <span style="position: absolute; left: -26px; top: 16px; width: 10px; height: 10px; border-radius: 50%; background: {{ $node->area?->color ?: '#3b82f6' }}; border: 2px solid #fff; box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);"></span>

            <div class="g_panel" style="margin: 0; padding: 12px 16px; background: #fff; border: 1px solid rgba(0,0,0,0.08); border-radius: 12px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.borderColor='rgba(59, 130, 246, 0.4)';" onmouseout="this.style.borderColor='rgba(0,0,0,0.08)';">
                <div style="display: grid; gap: 4px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <strong style="color: var(--color-neutral-800); font-size: 14px;">{{ $node->name }}</strong>
                        @if(!$node->upper_id)
                            <span class="g_badge dark" style="font-size: 10px; padding: 2px 6px;">Raíz</span>
                        @endif
                    </div>
                    <div style="display: flex; flex-wrap: wrap; gap: 6px; font-size: 11px;">
                        <span class="g_badge info" style="background: {{ $node->area?->color ? $node->area->color . '15' : '#eff6ff' }}; color: {{ $node->area?->color ?: '#3b82f6' }};">{{ $node->area?->nombre ?? 'Sin Área' }}</span>
                    </div>
                </div>

                <div class="g_celda_tags" style="gap: 6px;">
                    <button type="button" class="g_accion editar" wire:click="seleccionarRol({{ $node->id }})" title="Cambiar Superior">
                        <i class="fa-solid fa-link"></i>
                    </button>
                    @if($node->upper_id)
                        <button type="button" class="g_accion eliminar" wire:click="quitarSuperior({{ $node->id }})" title="Quitar Superior">
                            <i class="fa-solid fa-link-slash"></i>
                        </button>
                    @endif
                </div>
            </div>

            @if ($node->subordinados && $node->subordinados->isNotEmpty())
                @include('livewire.erp.sistema.rol.tree-node', ['nodes' => $node->subordinados])
            @endif
        </li>
    @endforeach
</ul>
