@section('tituloPagina', 'Crear Rol')

<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Crear Rol</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.rol.vista.todo') }}" class="g_boton g_boton_light">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </a>
        </div>
    </div>

    <form wire:submit="store" class="formulario">
        <div class="g_panel g_gap_pagina">
            <div class="g_fila">
                <div class="g_columna_12">
                    <div class="g_panel">
                        <h4 class="g_panel_titulo">General</h4>
                        <div>
                            <label for="name">
                                Nombre del Rol <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                            </label>
                            <input type="text" id="name" wire:model.blur="name"
                                class="@error('name') input-error @enderror" autocomplete="off"
                                placeholder="ej: supervisor-backoffice">
                            @error('name')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                            <p class="leyenda">Usa minúsculas y guiones (slug) preferiblemente.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_columna_12">
                    <div class="g_panel">
                        <h4 class="g_panel_titulo">Asignación de Permisos</h4>

                        <div class="g_cajas_input">
                            <div class="g_grid_permisos">
                                @foreach($allPermissions as $group => $items)
                                    <div class="grupo_permiso_card">
                                        <h5>{{ $group }}</h5>
                                        <div class="permisos_lista">
                                            @foreach($items as $permission)
                                                <div class="permiso_item">
                                                    <input type="checkbox" id="perm_{{ $permission->id }}"
                                                        value="{{ $permission->name }}" wire:model="permissions">
                                                    <label for="perm_{{ $permission->id }}">
                                                        {{ str_replace('.', ' (', $permission->name) . ')' }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @error('permissions')
                            <p class="mensaje_error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="formulario_botones">
                <button type="submit" class="g_boton g_boton_guardar" wire:loading.attr="disabled" wire:target="store">
                    <span wire:loading.remove wire:target="store">
                        <i class="fa-solid fa-save"></i> Guardar Rol
                    </span>
                    <span wire:loading wire:target="store">
                        <i class="fa-solid fa-spinner fa-spin"></i> Guardando...
                    </span>
                </button>

                <a href="{{ route('erp.rol.vista.todo') }}" class="g_boton g_boton_cancelar">
                    <i class="fa-solid fa-times"></i> Cancelar
                </a>
            </div>
        </div>
    </form>
</div>