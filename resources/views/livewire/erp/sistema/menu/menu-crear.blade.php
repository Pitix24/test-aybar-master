<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="store" message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Crear Ítem de Menú</h2>

        <div class="cabecera_titulo_botones">
            @can('menu.lista')
                <a href="{{ route('erp.menu.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <form wire:submit="store" class="formulario">
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
                                <input type="text" id="nombre" wire:model.blur="nombre"
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
                                            {{ str_repeat('— ', $m->nivel - 1) }}{{$m->nombre}}
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
                                <input type="text" id="icono" wire:model.live="icono"
                                    class="@error('icono') input-error @enderror" autocomplete="off">
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
                                <input type="text" id="nivel" wire:model="nivel" readonly disabled>
                            </div>
                        </div>
                    </div>

                    <div x-show="activeTab === 'rutas'" x-transition class="g_tab_content">
                        <div class="g_fila">
                            <div class="g_columna_6 g_margin_bottom_10">
                                <label for="ruta">Ruta de Laravel (Route Name)</label>
                                <input type="text" id="ruta" wire:model.blur="ruta"
                                    class="@error('ruta') input-error @enderror" autocomplete="off">
                                <p class="leyenda">Deja vacío si es un ítem agrupador (sin acción).</p>
                                @error('ruta')
                                    <p class="mensaje_error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="g_columna_6 g_margin_bottom_10">
                                <label for="url">URL Manual (Externa)</label>
                                <input type="text" id="url" wire:model.blur="url"
                                    class="@error('url') input-error @enderror" autocomplete="off">
                                <p class="leyenda">Solo para enlaces externos. No usar con Ruta.</p>
                                @error('url')
                                    <p class="mensaje_error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

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
                                <p class="leyenda">Si se deja vacío, el ítem será visible para cualquier usuario
                                    autenticado.</p>
                                @error('permiso')
                                    <p class="mensaje_error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="formulario_botones">
                        @can('menu.crear')
                            <button type="submit" class="g_boton guardar" wire:loading.attr="disabled" wire:target="store">
                                <span wire:loading.remove wire:target="store">
                                    <i class="fa-solid fa-save"></i> Crear
                                </span>
                                <span wire:loading wire:target="store">
                                    <i class="fa-solid fa-spinner fa-spin"></i> Creando...
                                </span>
                            </button>
                        @endcan

                        <button type="button" class="g_boton cancelar" onclick="history.back()">
                            <i class="fa-solid fa-times"></i> Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>