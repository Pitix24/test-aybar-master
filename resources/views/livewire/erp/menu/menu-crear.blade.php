<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="guardar" message="Guardando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>{{ isset($editando) ? 'Editar Ítem de Menú' : 'Crear Ítem de Menú' }}</h2>
        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.menu.vista.todo') }}" class="g_boton g_boton_danger">
                Cancelar <i class="fa-solid fa-xmark"></i>
            </a>
        </div>
    </div>

    <form wire:submit.prevent="guardar">
        <div class="g_fila">
            <!-- COLUMNA IZQUIERDA: GENERAL -->
            <div class="g_columna_8">
                <div class="g_panel">
                    <h3 class="g_margin_bottom_20">Información General</h3>

                    <div class="formulario">
                        <div class="g_fila">
                            <div class="g_columna_6 g_margin_bottom_20">
                                <label>Nombre del Ítem <span class="text-danger">*</span></label>
                                <input type="text" wire:model="nombre" placeholder="Ej: Usuarios">
                                @error('nombre') <span class="g_error">{{ $message }}</span> @enderror
                            </div>

                            <div class="g_columna_6 g_margin_bottom_20">
                                <label>Ítem Padre</label>
                                <select wire:model.live="parent_id">
                                    <option value="">-- Sin Padre (Nivel 1) --</option>
                                    @foreach($menusPadre as $m)
                                        <option value="{{ $m->id }}">
                                            {{ str_repeat('— ', $m->nivel - 1) }}{{ $m->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('parent_id') <span class="g_error">{{ $message }}</span> @enderror
                            </div>

                            <div class="g_columna_4 g_margin_bottom_20">
                                <label>Icono (FA Class)</label>
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <input type="text" wire:model.live="icono" placeholder="fa-solid fa-star">
                                    <div class="g_panel" style="padding: 10px; margin:0;">
                                        <i class="{{ $icono ?: 'fa-solid fa-question' }} fa-lg"></i>
                                    </div>
                                </div>
                                @error('icono') <span class="g_error">{{ $message }}</span> @enderror
                            </div>

                            <div class="g_columna_4 g_margin_bottom_20">
                                <label>Orden</label>
                                <input type="number" wire:model="orden">
                                @error('orden') <span class="g_error">{{ $message }}</span> @enderror
                            </div>

                            <div class="g_columna_4 g_margin_bottom_20">
                                <label>Nivel (Auto)</label>
                                <input type="text" wire:model="nivel" readonly
                                    style="background: var(--color-neutral-100);">
                            </div>
                        </div>

                        <h3 class="g_margin_bottom_20 g_margin_top_20">Rutas y Enlaces</h3>
                        <div class="g_fila">
                            <div class="g_columna_6 g_margin_bottom_20">
                                <label>Ruta de Laravel (Route Name)</label>
                                <input type="text" wire:model="ruta" placeholder="erp.admin.vista.todo">
                                <small style="color: var(--color-neutral-400);">Usa '#' si es un ítem agrupador.</small>
                                @error('ruta') <span class="g_error">{{ $message }}</span> @enderror
                            </div>

                            <div class="g_columna_6 g_margin_bottom_20">
                                <label>URL Manual</label>
                                <input type="text" wire:model="url" placeholder="/erp/admin">
                                <small style="color: var(--color-neutral-400);">Opcional, se usa para marcar el ítem
                                    como activo.</small>
                                @error('url') <span class="g_error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA: SEGURIDAD -->
            <div class="g_columna_4">
                <div class="g_panel">
                    <h3 class="g_margin_bottom_20">Control de Acceso (Spatie)</h3>

                    <div class="formulario">
                        <div class="g_margin_bottom_20">
                            <label>Roles Permitidos</label>
                            <div
                                style="max-height: 200px; overflow-y: auto; padding: 10px; border: 1px solid var(--color-neutral-200); border-radius: 8px;">
                                @foreach($allRoles as $role)
                                    <label
                                        style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 4px 0;">
                                        <input type="checkbox" wire:model="roles" value="{{ $role->name }}">
                                        {{ $role->name }}
                                    </label>
                                @endforeach
                            </div>
                            <small>Si no marcas ninguno, será visible para todos los que pasen el filtro de
                                permisos.</small>
                        </div>

                        <div class="g_margin_bottom_20">
                            <label>Permisos Requeridos</label>
                            <div
                                style="max-height: 200px; overflow-y: auto; padding: 10px; border: 1px solid var(--color-neutral-200); border-radius: 8px;">
                                @foreach($allPermissions as $permission)
                                    <label
                                        style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 4px 0;">
                                        <input type="checkbox" wire:model="permisos" value="{{ $permission->name }}">
                                        {{ $permission->name }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="g_margin_bottom_20">
                            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                                <input type="checkbox" wire:model="activo">
                                <strong>¿Ítem Activo?</strong>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="g_boton g_boton_primary" style="width: 100%; margin-top: 20px;">
                        Guardar Ítem <i class="fa-solid fa-floppy-disk"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>