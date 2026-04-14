<div>
    <div class="g_panel" style="margin-top: 20px;">
        <h3 class="g_panel_titulo">
            <i class="fa-solid fa-money-bill-transfer"></i> Movimientos de Bancarización
        </h3>

        {{-- Formulario para agregar --}}
        <form wire:submit.prevent="addBancarizacion" class="formulario" style="margin-bottom: 20px; padding: 15px; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">
            <div class="g_fila">
                <div class="g_columna_4">
                    <label>Cuota <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                    <input type="text" wire:model="cuota" placeholder="Ej: Cuota 1" class="@error('cuota') input-error @enderror">
                    @error('cuota') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
                <div class="g_columna_4">
                    <label>Importe <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                    <input type="number" step="0.01" wire:model="importe" placeholder="0.00" class="@error('importe') input-error @enderror">
                    @error('importe') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
                <div class="g_columna_4">
                    <label>Fecha Depósito Real <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                    <input type="date" wire:model="fecha_deposito_real" class="@error('fecha_deposito_real') input-error @enderror">
                    @error('fecha_deposito_real') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
            </div>
            <div style="margin-top: 10px; text-align: right;">
                <button type="submit" class="g_boton primary">
                    <i class="fa-solid fa-plus"></i> Agregar Registro
                </button>
            </div>
        </form>

        <div class="g_contenedor_tabla">
            <table class="g_tabla">
                <thead>
                    <tr>
                        <th>Proyecto</th>
                        <th>Lote</th>
                        <th>Manzana</th>
                        <th>Cuota</th>
                        <th>Importe</th>
                        <th>Fecha Depósito Real</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bancarizaciones as $banc)
                        @if($editingId === $banc->id)
                            <tr wire:key="edit-{{ $banc->id }}" style="background: #fffbeb;">
                                <td>{{ $prospecto->proyecto?->nombre }}</td>
                                <td>{{ $prospecto->lote }}</td>
                                <td>{{ $prospecto->manzana }}</td>
                                <td>
                                    <input type="text" wire:model="editCuota" class="g_input_tabla @error('editCuota') input-error @enderror">
                                </td>
                                <td>
                                    <input type="number" step="0.01" wire:model="editImporte" class="g_input_tabla @error('editImporte') input-error @enderror">
                                </td>
                                <td>
                                    <input type="date" wire:model="editFecha" class="g_input_tabla @error('editFecha') input-error @enderror">
                                </td>
                                <td class="g_celda_acciones g_celda_centro">
                                    <button wire:click="update" class="g_accion success" title="Guardar">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                    <button wire:click="cancel" class="g_accion eliminar" title="Cancelar">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </td>
                            </tr>
                        @else
                            <tr wire:key="row-{{ $banc->id }}">
                                <td>{{ $prospecto->proyecto?->nombre }}</td>
                                <td>{{ $prospecto->lote }}</td>
                                <td>{{ $prospecto->manzana }}</td>
                                <td class="g_negrita">{{ $banc->cuota }}</td>
                                <td>S/ {{ number_format($banc->importe, 2) }}</td>
                                <td>{{ $banc->fecha_deposito_real->format('d/m/Y') }}</td>
                                <td class="g_celda_acciones g_celda_centro">
                                    <button wire:click="edit({{ $banc->id }})" class="g_accion editar" title="Editar">
                                        <i class="fa-solid fa-pencil"></i>
                                    </button>
                                    <button wire:click="remove({{ $banc->id }})" 
                                            wire:confirm="¿Está seguro de eliminar este registro?"
                                            class="g_accion eliminar" title="Eliminar">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" class="g_celda_centro">No hay registros de bancarización.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
