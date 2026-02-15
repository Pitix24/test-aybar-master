<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="store, name" message="Procesando..." />
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Crear Rol</h2>

        <div class="cabecera_titulo_botones">
            @can('rol.ver')
                <a href="{{ route('erp.rol.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <form wire:submit="store" class="formulario">
        <div class="g_fila">
            <div class="g_columna_8">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">General</h4>

                    <div class="g_margin_bottom_10">
                        <label for="name">
                            Nombre del Rol <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                        </label>
                        <input type="text" id="name" wire:model.blur="name" class="@error('name') input-error @enderror"
                            autocomplete="off">
                        @error('name')
                            <p class="mensaje_error">{{ $message }}</p>
                        @enderror
                        <p class="leyenda">Ej: supervisor-backoffice.</p>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>
                            Asignación de Permisos <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                        </label>

                        <div class="g_cajas_input" x-data="{ moduloAbierto: null }">
                            <div class="g_grid_modulos">
                                @foreach($allPermissions as $module => $permissions)
                                    <div class="modulo_acordeon" :class="{ 'abierto': moduloAbierto === '{{ $module }}' }">
                                        <div class="modulo_cabecera"
                                            @click="moduloAbierto = (moduloAbierto === '{{ $module }}' ? null : '{{ $module }}')">
                                            <h5>
                                                <i class="fa-solid fa-folder-open"></i> {{ $module }}
                                                <small class="g_badge info">
                                                    {{ $permissions->count() }} permisos
                                                </small>
                                            </h5>
                                            <i class="fa-solid fa-chevron-down chevron"></i>
                                        </div>

                                        <div class="modulo_contenido">
                                            <div class="recursos_grid">
                                                @php
                                                    $recursos = $permissions->groupBy(fn($p) => explode('.', $p->name)[0]);
                                                @endphp

                                                @foreach($recursos as $recurso => $items)
                                                    <div class="recurso_grupo">
                                                        <div class="recurso_titulo">
                                                            {{ str_replace('-', ' ', $recurso) }}
                                                        </div>
                                                        <div class="permisos_lista">
                                                            @foreach($items as $permission)
                                                                <div class="permiso_item">
                                                                    <input type="checkbox" id="perm_{{ $permission->id }}"
                                                                        value="{{ $permission->name }}" wire:model="permissions">
                                                                    <label for="perm_{{ $permission->id }}">
                                                                        {{ explode('.', $permission->name)[1] ?? $permission->name }}
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @error('permissions')
                            <p class="mensaje_error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="formulario_botones">
                        @can('rol.crear')
                            <button type="submit" class="g_boton guardar" wire:loading.attr="disabled" wire:target="store">
                                <span wire:loading.remove wire:target="store">
                                    <i class="fa-solid fa-save"></i> Crear
                                </span>
                                <span wire:loading wire:target="store">
                                    <i class="fa-solid fa-spinner fa-spin"></i> Creando...
                                </span>
                            </button>
                        @endcan

                        @can('rol.ver')
                            <a href="{{ route('erp.rol.vista.todo') }}" class="g_boton cancelar">
                                <i class="fa-solid fa-times"></i> Cancelar
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>