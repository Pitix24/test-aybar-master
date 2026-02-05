@section('tituloPagina', 'Editar Rol')

<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Editar Rol</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.rol.vista.todo') }}" class="g_boton g_boton_light">
                Inicio <i class="fa-solid fa-house"></i></a>

            <a href="{{ route('erp.rol.vista.crear') }}" class="g_boton g_boton_primary">
                Crear <i class="fa-solid fa-square-plus"></i></a>

            @if($role->name !== 'super-admin')
            <button type="button" class="g_boton g_boton_danger" onclick="alertaEliminarRol()">
                Eliminar <i class="fa-solid fa-trash-can"></i>
            </button>
            @endif

            <button type="button" class="g_boton g_boton_dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <form wire:submit="update" class="formulario">
        <div class="g_panel g_gap_pagina">
            <div class="g_fila">
                <div class="g_columna_12">
                    <div class="g_panel">
                        <h4 class="g_panel_titulo">Información del Rol</h4>
                        <div>
                            <label for="name">
                                Nombre del Rol <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                            </label>
                            <input type="text" id="name" wire:model.blur="name"
                                class="@error('name') input-error @enderror" autocomplete="off"
                                {{ $role->name === 'super-admin' ? 'readonly' : '' }}>
                            @error('name')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                            @if($role->name === 'super-admin')
                                <p class="g_ayuda text-danger">El nombre del rol 'super-admin' está protegido y no se puede cambiar.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_columna_12">
                    <div class="g_panel">
                        <h4 class="g_panel_titulo">Gestión de Permisos</h4>
                        
                        @if($role->name === 'super-admin')
                            <div class="g_mensaje g_mensaje_info">
                                <i class="fa-solid fa-info-circle"></i>
                                Los usuarios con el rol <strong>super-admin</strong> tienen acceso total al sistema de forma implícita (vía Gate::before). No es necesario asignar permisos individuales aquí.
                            </div>
                        @else
                            <div class="g_cajas_input" x-data="{ moduloAbierto: null }">
                                <div class="g_grid_modulos">
                                    @foreach($allPermissions as $module => $permissions)
                                        <div class="modulo_acordeon" :class="{ 'abierto': moduloAbierto === '{{ $module }}' }">
                                            <div class="modulo_cabecera" @click="moduloAbierto = (moduloAbierto === '{{ $module }}' ? null : '{{ $module }}')">
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
                                                            <div class="recurso_titulo">{{ str_replace('-', ' ', $recurso) }}</div>
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
                        @endif
                        @error('permissions')
                            <p class="mensaje_error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="formulario_botones">
                <button type="submit" class="g_boton g_boton_guardar" wire:loading.attr="disabled" wire:target="update">
                    <span wire:loading.remove wire:target="update">
                        <i class="fa-solid fa-save"></i> Actualizar
                    </span>
                    <span wire:loading wire:target="update">
                        <i class="fa-solid fa-spinner fa-spin"></i> Actualizando...
                    </span>
                </button>

                <a href="{{ route('erp.rol.vista.todo') }}" class="g_boton g_boton_cancelar">
                    <i class="fa-solid fa-times"></i> Cancelar
                </a>
            </div>
        </div>
    </form>

    @script
    <script>
        window.alertaEliminarRol = function() {
            Swal.fire({
                title: '¿Quieres eliminar este rol?',
                text: "Esto puede afectar a los usuarios que lo tengan asignado.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '¡Sí, eliminar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.eliminarRolOn();
                }
            });
        }
    </script>
    @endscript
</div>
