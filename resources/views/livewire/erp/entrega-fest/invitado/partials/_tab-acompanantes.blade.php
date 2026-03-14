@if($acomp_modo === 'crear')
    <form wire:submit.prevent="storeAcompanante" class="formulario g_panel" style="margin-bottom:16px;">
        <h4 class="g_panel_titulo">
            <i class="fa-solid fa-user-plus"></i> Nuevo Acompañante
        </h4>

        <div class="g_fila">
            <div class="g_margin_bottom_10 g_columna_6">
                <label>DNI <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                <input type="text" wire:model.blur="acomp_dni" class="@error('acomp_dni') input-error @enderror"
                    placeholder="12345678">
                @error('acomp_dni') <p class="mensaje_error">{{ $message }}</p> @enderror
            </div>
            <div class="g_margin_bottom_10 g_columna_6">
                <label>Nombres Completos <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                <input type="text" wire:model.blur="acomp_nombres" class="@error('acomp_nombres') input-error @enderror"
                    placeholder="Ej: Juan Pérez">
                @error('acomp_nombres') <p class="mensaje_error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="g_fila">
            <div class="g_margin_bottom_10 g_columna_6">
                <label>Correo Electrónico (Opcional)</label>
                <input type="email" wire:model.blur="acomp_email" class="@error('acomp_email') input-error @enderror"
                    placeholder="correo@ejemplo.com">
                @error('acomp_email') <p class="mensaje_error">{{ $message }}</p> @enderror
            </div>
            <div class="g_margin_bottom_10 g_columna_6">
                <label>Celular (Opcional)</label>
                <input type="text" wire:model.blur="acomp_celular" class="@error('acomp_celular') input-error @enderror"
                    placeholder="987654321">
                @error('acomp_celular') <p class="mensaje_error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="formulario_botones">
            <button type="submit" class="g_boton guardar" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="storeAcompanante">
                    <i class="fa-solid fa-save"></i> Guardar
                </span>
                <span wire:loading wire:target="storeAcompanante">
                    <i class="fa-solid fa-spinner fa-spin"></i> Guardando...
                </span>
            </button>
            <button type="button" wire:click="cancelarAcompanante" class="g_boton cancelar">
                <i class="fa-solid fa-times"></i> Cancelar
            </button>
        </div>
    </form>
@endif

<div class="g_tabla_cabecera">
    <div class="g_tabla_cabecera_botones">
        @if($acomp_modo !== 'crear')
            @if(count($acompanantes) >= $cantidad_acompanantes_permitidos)
                <div class="g_alert info" style="margin: 0; padding: 10px;">
                    <i class="fa-solid fa-circle-info"></i> Límite de {{ $cantidad_acompanantes_permitidos }} acompañantes alcanzado.
                </div>
            @else
                <button wire:click="abrirFormCrear" class="g_boton primary">
                    <i class="fa-solid fa-plus"></i> Agregar Acompañante
                </button>
            @endif
        @endif
    </div>
</div>
{{-- Tabla de acompañantes --}}
<div class="g_contenedor_tabla">
    <table class="g_tabla">
        <thead>
            <tr>
                <th>DNI</th>
                <th>Nombres</th>
                <th>Correo</th>
                <th>Celular</th>
                <th class="g_celda_centro">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($acompanantes as $acomp)
                @if($acomp_modo === 'editar' && $acomp_editando_id == $acomp['id'])
                    {{-- Fila en modo edición --}}
                    <tr wire:key="acomp-edit-{{ $acomp['id'] }}" style="background:#fffbea;">
                        <td>
                            <div class="formulario">
                                <input type="text" wire:model.blur="acomp_dni" style="min-width:90px;"
                                    class="@error('acomp_dni') input-error @enderror" placeholder="DNI">
                                @error('acomp_dni') <p class="mensaje_error">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                        </td>
                        <td>
                            <div class="formulario">
                                <input type="text" wire:model.blur="acomp_nombres" style="min-width:160px;"
                                    class="@error('acomp_nombres') input-error @enderror" placeholder="Nombres">
                                @error('acomp_nombres') <p class="mensaje_error">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                        </td>
                        <td>
                            <div class="formulario">
                                <input type="email" wire:model.blur="acomp_email" style="min-width:160px;"
                                    class="@error('acomp_email') input-error @enderror" placeholder="correo@ejemplo.com">
                                @error('acomp_email') <p class="mensaje_error">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                        </td>
                        <td>
                            <div class="formulario">
                                <input type="text" wire:model.blur="acomp_celular" style="min-width:110px;"
                                    class="@error('acomp_celular') input-error @enderror" placeholder="987654321">
                                @error('acomp_celular') <p class="mensaje_error">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                        </td>
                        <td class="g_celda_acciones g_celda_centro" style="white-space:nowrap;">
                            <button wire:click="updateAcompanante" class="g_accion success" title="Guardar cambios">
                                <i class="fa-solid fa-check"></i>
                            </button>
                            <button wire:click="cancelarAcompanante" class="g_accion eliminar" title="Cancelar">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </td>
                    </tr>
                @else
                    {{-- Fila normal --}}
                    <tr wire:key="acomp-{{ $acomp['id'] }}">
                        <td class="g_negrita">{{ $acomp['dni'] }}</td>
                        <td>{{ $acomp['nombres'] }}</td>
                        <td>
                            {{ $acomp['email'] ?? '—' }}
                        </td>
                        <td>
                            {{ $acomp['celular'] ?? '—' }}
                        </td>
                        <td class="g_celda_acciones g_celda_centro">
                            @can('invitado.editar')
                                <button wire:click="editarAcompanante({{ $acomp['id'] }})" class="g_accion editar" title="Editar">
                                    <i class="fa-solid fa-pencil"></i>
                                </button>
                                <button wire:click="eliminarAcompanante({{ $acomp['id'] }})"
                                    wire:confirm="¿Eliminar este acompañante?" class="g_accion eliminar" title="Eliminar">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            @endcan
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #666; padding: 20px;">
                        No hay acompañantes registrados
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
