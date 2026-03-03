@if($cop_modo === 'crear')
    <div class="g_panel"
        style="background:#f0f7ff; border:1px solid #c3d9f7; margin-bottom:16px; padding:16px; border-radius:10px;">
        <h5 style="margin-bottom:12px; color:var(--color-primary);"><i class="fa-solid fa-user-plus"></i> Nuevo
            Copropietario</h5>
        <div class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_3">
                    <label>DNI <span class="obligatorio">*</span></label>
                    <input type="text" wire:model.blur="cop_dni" class="@error('cop_dni') input-error @enderror"
                        placeholder="12345678">
                    @error('cop_dni') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
                <div class="g_margin_bottom_10 g_columna_9">
                    <label>Nombres Completos <span class="obligatorio">*</span></label>
                    <input type="text" wire:model.blur="cop_nombres" class="@error('cop_nombres') input-error @enderror"
                        placeholder="Ej: María Torres Lara">
                    @error('cop_nombres') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_6">
                    <label>Correo Electrónico</label>
                    <input type="email" wire:model.blur="cop_email" class="@error('cop_email') input-error @enderror"
                        placeholder="correo@ejemplo.com">
                    @error('cop_email') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
                <div class="g_margin_bottom_10 g_columna_6">
                    <label>Celular</label>
                    <input type="text" wire:model.blur="cop_celular" class="@error('cop_celular') input-error @enderror"
                        placeholder="987654321">
                    @error('cop_celular') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
            </div>
            <div style="display:flex; gap:10px;">
                <button wire:click="storeCopropietario" class="g_boton guardar" style="font-size:0.85rem;">
                    <i class="fa-solid fa-save"></i> Guardar
                </button>
                <button wire:click="cancelarCopropietario" class="g_boton danger" style="font-size:0.85rem;">
                    <i class="fa-solid fa-xmark"></i> Cancelar
                </button>
            </div>
        </div>
    </div>
@endif

<div class="g_tabla_cabecera">
    <div class="g_tabla_cabecera_botones">
        @if($cop_modo !== 'crear')
            <button wire:click="abrirFormCrear" class="g_boton primary">
                <i class="fa-solid fa-plus"></i> Agregar Copropietario
            </button>
        @endif
    </div>
</div>
{{-- Tabla de copropietarios --}}
<div class="g_contenedor_tabla">
    <table class="g_tabla">
        <thead>
            <tr>
                <th>DNI</th>
                <th>Nombres</th>
                <th>Correo</th>
                <th>Celular</th>
                <th class="g_celda_centro">Invitación</th>
                <th class="g_celda_centro">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($copropietarios as $cop)
                @if($cop_modo === 'editar' && $cop_editando_id == $cop['id'])
                    {{-- Fila en modo edición --}}
                    <tr wire:key="cop-edit-{{ $cop['id'] }}" style="background:#fffbea;">
                        <td style="padding:6px 8px;">
                            <div class="formulario">
                                <input type="text" wire:model.blur="cop_dni" style="min-width:90px;"
                                    class="@error('cop_dni') input-error @enderror" placeholder="DNI">
                                @error('cop_dni') <p class="mensaje_error" style="font-size:0.7rem;">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                        </td>
                        <td style="padding:6px 8px;">
                            <div class="formulario">
                                <input type="text" wire:model.blur="cop_nombres" style="min-width:160px;"
                                    class="@error('cop_nombres') input-error @enderror" placeholder="Nombres">
                                @error('cop_nombres') <p class="mensaje_error" style="font-size:0.7rem;">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                        </td>
                        <td style="padding:6px 8px;">
                            <div class="formulario">
                                <input type="email" wire:model.blur="cop_email" style="min-width:160px;"
                                    class="@error('cop_email') input-error @enderror" placeholder="correo@ejemplo.com">
                                @error('cop_email') <p class="mensaje_error" style="font-size:0.7rem;">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                        </td>
                        <td style="padding:6px 8px;">
                            <div class="formulario">
                                <input type="text" wire:model.blur="cop_celular" style="min-width:110px;"
                                    class="@error('cop_celular') input-error @enderror" placeholder="987654321">
                                @error('cop_celular') <p class="mensaje_error" style="font-size:0.7rem;">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                        </td>
                        <td></td>
                        <td class="g_celda_acciones g_celda_centro" style="white-space:nowrap;">
                            <button wire:click="updateCopropietario" class="g_accion guardar" title="Guardar cambios">
                                <i class="fa-solid fa-check"></i>
                            </button>
                            <button wire:click="cancelarCopropietario" class="g_accion eliminar" title="Cancelar">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </td>
                    </tr>
                @else
                    {{-- Fila normal --}}
                    <tr wire:key="cop-{{ $cop['id'] }}">
                        <td class="g_negrita">{{ $cop['dni'] }}</td>
                        <td>{{ $cop['nombres'] }}</td>
                        <td style="font-size:0.85rem; color:#555;">
                            {{ $cop['email'] ?? '—' }}
                        </td>
                        <td style="font-size:0.85rem; color:#555;">
                            {{ $cop['celular'] ?? '—' }}
                        </td>
                        <td class="g_celda_centro">
                            @php
                                $copModel = \App\Models\CopropietarioEntregaFest::find($cop['id']);
                            @endphp
                            @if($copModel?->invitado)
                                <span class="g_badge success" style="font-size:0.7rem;">Con invitación</span>
                            @else
                                <span class="g_badge light" style="font-size:0.7rem;">Sin invitación</span>
                            @endif
                        </td>
                        <td class="g_celda_acciones g_celda_centro">
                            <button wire:click="editarCopropietario({{ $cop['id'] }})" class="g_accion editar" title="Editar">
                                <i class="fa-solid fa-pencil"></i>
                            </button>
                            <button wire:click="eliminarCopropietario({{ $cop['id'] }})"
                                wire:confirm="¿Eliminar este copropietario?" class="g_accion eliminar" title="Eliminar">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="6" class="g_celda_centro" style="padding:30px; color:#999;">
                        <i class="fa-solid fa-people-group" style="font-size:1.5rem; display:block; margin-bottom:8px;"></i>
                        Este lote no tiene copropietarios registrados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>