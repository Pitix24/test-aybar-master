@if($cop_modo === 'crear')
    <form wire:submit.prevent="storeCopropietario" class="formulario g_panel" style="margin-bottom:16px;">
        <h4 class="g_panel_titulo">
            <i class="fa-solid fa-user-plus"></i> Nuevo Copropietario
        </h4>

        <div class="g_fila">
            <div class="g_margin_bottom_10 g_columna_6">
                <label>DNI <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                <input type="text" wire:model.blur="cop_dni" class="@error('cop_dni') input-error @enderror"
                    placeholder="12345678">
                @error('cop_dni') <p class="mensaje_error">{{ $message }}</p> @enderror
            </div>
            <div class="g_margin_bottom_10 g_columna_6">
                <label>Nombres Completos <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
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

        <div class="formulario_botones">
            <button type="submit" class="g_boton guardar" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="storeCopropietario">
                    <i class="fa-solid fa-save"></i> Guardar
                </span>
                <span wire:loading wire:target="storeCopropietario">
                    <i class="fa-solid fa-spinner fa-spin"></i> Guardando...
                </span>
            </button>
            <button type="button" wire:click="cancelarCopropietario" class="g_boton cancelar">
                <i class="fa-solid fa-times"></i> Cancelar
            </button>
        </div>
    </form>
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
            @foreach($copropietarios as $cop)
                @if($cop_modo === 'editar' && $cop_editando_id == $cop['id'])
                    {{-- Fila en modo edición --}}
                    <tr wire:key="cop-edit-{{ $cop['id'] }}" style="background:#fffbea;">
                        <td>
                            <div class="formulario">
                                <input type="text" wire:model.blur="cop_dni" style="min-width:90px;"
                                    class="@error('cop_dni') input-error @enderror" placeholder="DNI">
                                @error('cop_dni') <p class="mensaje_error">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                        </td>
                        <td>
                            <div class="formulario">
                                <input type="text" wire:model.blur="cop_nombres" style="min-width:160px;"
                                    class="@error('cop_nombres') input-error @enderror" placeholder="Nombres">
                                @error('cop_nombres') <p class="mensaje_error">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                        </td>
                        <td>
                            <div class="formulario">
                                <input type="email" wire:model.blur="cop_email" style="min-width:160px;"
                                    class="@error('cop_email') input-error @enderror" placeholder="correo@ejemplo.com">
                                @error('cop_email') <p class="mensaje_error">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                        </td>
                        <td>
                            <div class="formulario">
                                <input type="text" wire:model.blur="cop_celular" style="min-width:110px;"
                                    class="@error('cop_celular') input-error @enderror" placeholder="987654321">
                                @error('cop_celular') <p class="mensaje_error">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                        </td>
                        <td></td>
                        <td class="g_celda_acciones g_celda_centro" style="white-space:nowrap;">
                            <button wire:click="updateCopropietario" class="g_accion success" title="Guardar cambios">
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
                        <td>
                            <div class="g_negrita">{{ $cop['nombres'] }}</div>
                            <div class="g_gap_small" style="margin-top: 4px; display: flex; gap: 8px; align-items: center;">
                                <!--  LINK DE PREINVITACION E INVITACION AL COPROPIETARIO --->
                                @php
                                    $link_pre = route('entrega-fest.pre-invitacion.copropietario', [
                                        'slug' => $evento->slug,
                                        'copropietarioId' => $cop['id']
                                    ]);
                                    $link_inv = route('entrega-fest.asistencia-invitacion.copropietario', [
                                        'slug' => $evento->slug,
                                        'copropietarioId' => $cop['id'],
                                    ]);
                                @endphp
                                
                                <div style="display: flex; align-items: center; gap: 4px;" x-data="{ copied: false }">
                                    <span style="font-size: 0.7rem; color: var(--g-texto-suave);">Pre:</span>
                                    <button type="button" class="g_boton_icono info extra_small" 
                                        @click="navigator.clipboard.writeText('{{ $link_pre }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                        title="Copiar Link Pre-Invitación">
                                        <i class="fa-solid" :class="copied ? 'fa-check' : 'fa-copy'"></i>
                                    </button>
                                    <a href="{{ $link_pre }}" target="_blank" class="g_boton_icono dark extra_small" title="Ver Link Pre-Invitación">
                                        <i class="fa-solid fa-external-link"></i>
                                    </a>
                                </div>

                                <div style="display: flex; align-items: center; gap: 4px;" x-data="{ copied: false }">
                                    <span style="font-size: 0.7rem; color: var(--g-texto-suave);">Inv:</span>
                                    <button type="button" class="g_boton_icono info extra_small" 
                                        @click="navigator.clipboard.writeText('{{ $link_inv }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                        title="Copiar Link Invitación">
                                        <i class="fa-solid" :class="copied ? 'fa-check' : 'fa-copy'"></i>
                                    </button>
                                    <a href="{{ $link_inv }}" target="_blank" class="g_boton_icono dark extra_small" title="Ver Link Invitación">
                                        <i class="fa-solid fa-external-link"></i>
                                    </a>
                                </div>
                            </div>
                        </td>
                        <td>
                            {{ $cop['email'] ?? '—' }}
                        </td>
                        <td>
                            {{ $cop['celular'] ?? '—' }}
                        </td>
                        <td class="g_celda_centro">
                            @php
                                $copModel = \App\Models\CopropietarioEntregaFest::find($cop['id']);
                            @endphp
                            @if($copModel?->invitado)
                                <span class="g_badge success">Con invitación</span>
                            @else
                                <span class="g_badge light">Sin invitación</span>
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
            @endforeach
        </tbody>
    </table>
</div>