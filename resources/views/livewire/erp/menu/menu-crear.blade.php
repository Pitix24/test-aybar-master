<div class="g_gap_pagina">

    <div class="g_panel cabecera_titulo_pagina">
        <h2>{{ isset($editando) ? 'Editar Ítem de Menú' : 'Crear Ítem de Menú' }}</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.menu.vista.todo') }}" class="g_boton g_boton_light">
                Lista <i class="fa-solid fa-list"></i>
            </a>

            @if(isset($editando))
                <a href="{{ route('erp.menu.vista.crear') }}" class="g_boton g_boton_primary">
                    Crear <i class="fa-solid fa-square-plus"></i>
                </a>

                <button type="button" class="g_boton g_boton_danger" onclick="alertaEliminarMenu()">
                    Eliminar <i class="fa-solid fa-trash-can"></i>
                </button>
            @endif

            <button type="button" class="g_boton g_boton_dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <form wire:submit="{{ isset($editando) ? 'update' : 'store' }}" class="formulario">
        <div class="g_fila">
            <div class="g_columna_8">
                <div class="g_panel" x-data="{ activeTab: 'general' }">

                    <div class="g_tab_navegacion">
                        <div class="g_tab_botones">
                            <button type="button" @click="activeTab = 'general'"
                                :class="activeTab === 'general' ? 'g_tab_active' : 'g_tab_inactive'"
                                class="g_tab_boton">
                                <i class="fa-solid fa-info-circle"></i> Información General
                            </button>

                            <button type="button" @click="activeTab = 'rutas'"
                                :class="activeTab === 'rutas' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                                <i class="fa-solid fa-route"></i> Rutas y Enlaces
                            </button>

                            <button type="button" @click="activeTab = 'seguridad'"
                                :class="activeTab === 'seguridad' ? 'g_tab_active' : 'g_tab_inactive'"
                                class="g_tab_boton">
                                <i class="fa-solid fa-shield-halved"></i> Seguridad
                            </button>
                        </div>
                    </div>

                    <!-- TAB: INFORMACIÓN GENERAL -->
                    <div x-show="activeTab === 'general'" x-transition class="g_tab_content">

                        <div class="g_margin_bottom_10">
                            <label for="estado_activo">
                                Estado <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                            </label>

                            <div class="g_switch-wrapper">
                                <label class="g_switch">
                                    <input id="estado_activo" type="checkbox" wire:model.live="activo">
                                    <span class="g_switch-slider"></span>
                                </label>

                                <span class="g_switch-label">
                                    {{ $activo ? 'Activo' : 'Inactivo' }}
                                </span>

                                @error('activo')
                                    <p class="mensaje_error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_columna_6 g_margin_bottom_10">
                                <label for="nombre">Nombre del Ítem <span class="obligatorio"><i
                                            class="fa-solid fa-asterisk"></i></span></label>
                                <input type="text" id="nombre" wire:model.blur="nombre" placeholder="Ej: Usuarios"
                                    class="@error('nombre') input-error @enderror" autocomplete="off">
                                @error('nombre')
                                    <p class="mensaje_error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="g_columna_6 g_margin_bottom_10">
                                <label for="parent_id">Ítem Padre</label>
                                <select id="parent_id" wire:model.live="parent_id"
                                    class="@error('parent_id') input-error @enderror">
                                    <option value="">-- Sin Padre (Nivel 1) --</option>
                                    @foreach($menusPadre as $m)
                                        <option value="{{ $m->id }}">
                                            {{ str_repeat('— ', $m->nivel - 1) }}{{ $m->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('parent_id')
                                    <p class="mensaje_error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_columna_4 g_margin_bottom_10">
                                <label for="icono">Icono (FontAwesome)</label>
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <input type="text" id="icono" wire:model.live="icono" placeholder="fa-solid fa-star"
                                        class="@error('icono') input-error @enderror" autocomplete="off">
                                    <div class="g_panel" style="padding: 10px; margin:0;">
                                        <i class="{{ $icono ?: 'fa-solid fa-question' }} fa-lg"></i>
                                    </div>
                                </div>
                                @error('icono')
                                    <p class="mensaje_error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="g_columna_4 g_margin_bottom_10">
                                <label for="orden">Orden</label>
                                <input type="number" id="orden" wire:model.blur="orden"
                                    class="@error('orden') input-error @enderror">
                                @error('orden')
                                    <p class="mensaje_error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="g_columna_4 g_margin_bottom_10">
                                <label for="nivel">Nivel (Auto)</label>
                                <input type="text" id="nivel" wire:model="nivel" readonly
                                    style="background: var(--color-neutral-100);">
                            </div>
                        </div>
                    </div>

                    <!-- TAB: RUTAS Y ENLACES -->
                    <div x-show="activeTab === 'rutas'" x-transition class="g_tab_content">
                        <div class="g_fila">
                            <div class="g_columna_6 g_margin_bottom_10">
                                <label for="ruta">Ruta de Laravel (Route Name)</label>
                                <input type="text" id="ruta" wire:model.blur="ruta" placeholder="erp.admin.vista.todo"
                                    class="@error('ruta') input-error @enderror" autocomplete="off">
                                <small style="color: var(--color-neutral-400);">Deja vacío si es un ítem agrupador (sin
                                    acción).</small>
                                @error('ruta')
                                    <p class="mensaje_error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="g_columna_6 g_margin_bottom_10">
                                <label for="url">URL Manual (Externa)</label>
                                <input type="text" id="url" wire:model.blur="url" placeholder="https://ejemplo.com"
                                    class="@error('url') input-error @enderror" autocomplete="off">
                                <small style="color: var(--color-neutral-400);">Solo para enlaces externos. No usar con
                                    Ruta.</small>
                                @error('url')
                                    <p class="mensaje_error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- TAB: SEGURIDAD -->
                    <div x-show="activeTab === 'seguridad'" x-transition class="g_tab_content">
                        <div class="g_fila">
                            <div class="g_columna_12 g_margin_bottom_10">
                                <label for="permiso">Permiso Requerido (Spatie)</label>
                                <select id="permiso" wire:model.blur="permiso"
                                    class="@error('permiso') input-error @enderror">
                                    <option value="">-- Sin Permiso (Público) --</option>
                                    @foreach($allPermissions as $permission)
                                        <option value="{{ $permission->name }}">{{ $permission->name }}</option>
                                    @endforeach
                                </select>
                                <small style="color: var(--color-neutral-400);">Si se deja vacío, el ítem será visible
                                    para cualquier usuario autenticado.</small>
                                @error('permiso')
                                    <p class="mensaje_error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="formulario_botones">
                        <button type="submit" class="g_boton g_boton_guardar" wire:loading.attr="disabled"
                            wire:target="{{ isset($editando) ? 'update' : 'store' }}">
                            <span wire:loading.remove wire:target="{{ isset($editando) ? 'update' : 'store' }}">
                                <i class="fa-solid fa-save"></i> {{ isset($editando) ? 'Actualizar' : 'Guardar' }}
                            </span>
                            <span wire:loading wire:target="{{ isset($editando) ? 'update' : 'store' }}">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                                {{ isset($editando) ? 'Actualizando...' : 'Guardando...' }}
                            </span>
                        </button>

                        <a href="{{ route('erp.menu.vista.todo') }}" class="g_boton g_boton_cancelar">
                            <i class="fa-solid fa-times"></i> Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @if(isset($editando))
        @script
        <script>
            window.alertaEliminarMenu = function () {
                Swal.fire({
                    title: '¿Quieres eliminar este ítem?',
                    text: "Se eliminarán también todos sus submenús. No podrás recuperar esta información.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '¡Sí, eliminar!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $wire.eliminarMenuOn();
                    }
                });
            }
        </script>
        @endscript
    @endif
</div>