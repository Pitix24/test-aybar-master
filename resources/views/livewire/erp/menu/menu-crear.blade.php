<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="{{ isset($editando) ? 'update' : 'store' }}" message="Guardando..." />

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

    <form wire:submit="{{ isset($editando) ? 'update' : 'store' }}">
        <div class="g_fila">
            <!-- COLUMNA IZQUIERDA: GENERAL -->
            <div class="g_columna_8">
                <div class="g_panel">
                    <h3 class="g_margin_bottom_20">Información General</h3>

                    <div class="formulario">
                        <div class="g_fila">
                            <div class="g_columna_6 g_margin_bottom_20">
                                <label>Nombre del Ítem <span class="text-danger">*</span></label>
                                <input type="text" wire:model.blur="nombre" placeholder="Ej: Usuarios"
                                    class="@error('nombre') input-error @enderror">
                                @error('nombre') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>

                            <div class="g_columna_6 g_margin_bottom_20">
                                <label>Ítem Padre</label>
                                <select wire:model.live="parent_id" class="@error('parent_id') input-error @enderror">
                                    <option value="">-- Sin Padre (Nivel 1) --</option>
                                    @foreach($menusPadre as $m)
                                        <option value="{{ $m->id }}">
                                            {{ str_repeat('— ', $m->nivel - 1) }}{{ $m->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('parent_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>

                            <div class="g_columna_4 g_margin_bottom_20">
                                <label>Icono (FA Class)</label>
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <input type="text" wire:model.live="icono" placeholder="fa-solid fa-star"
                                        class="@error('icono') input-error @enderror">
                                    <div class="g_panel" style="padding: 10px; margin:0;">
                                        <i class="{{ $icono ?: 'fa-solid fa-question' }} fa-lg"></i>
                                    </div>
                                </div>
                                @error('icono') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>

                            <div class="g_columna_4 g_margin_bottom_20">
                                <label>Orden</label>
                                <input type="number" wire:model.blur="orden"
                                    class="@error('orden') input-error @enderror">
                                @error('orden') <p class="mensaje_error">{{ $message }}</p> @enderror
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
                                <input type="text" wire:model.blur="ruta" placeholder="erp.admin.vista.todo"
                                    class="@error('ruta') input-error @enderror">
                                <small style="color: var(--color-neutral-400);">Usa '#' si es un ítem agrupador.</small>
                                @error('ruta') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>

                            <div class="g_columna_6 g_margin_bottom_20">
                                <label>URL Manual</label>
                                <input type="text" wire:model.blur="url" placeholder="/erp/admin"
                                    class="@error('url') input-error @enderror">
                                <small style="color: var(--color-neutral-400);">Opcional, se usa para marcar el ítem
                                    como activo.</small>
                                @error('url') <p class="mensaje_error">{{ $message }}</p> @enderror
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
                            <label>Permiso Requerido (Slug)</label>
                            <input type="text" wire:model.blur="permiso" placeholder="Ej: area-vista-todo"
                                class="@error('permiso') input-error @enderror">
                            <small style="color: var(--color-neutral-400);">Si se deja vacío, el ítem será público para
                                cualquier usuario autenticado.</small>
                            @error('permiso') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        <div class="g_margin_bottom_20">
                            <label>Estado</label>
                            <div class="g_switch-wrapper">
                                <label class="g_switch">
                                    <input type="checkbox" wire:model.live="activo">
                                    <span class="g_switch-slider"></span>
                                </label>
                                <span class="g_switch-label">
                                    {{ $activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                            @error('activo') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <button type="submit" class="g_boton g_boton_primary" style="width: 100%; margin-top: 20px;"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            {{ isset($editando) ? 'Actualizar Ítem' : 'Guardar Ítem' }} <i
                                class="fa-solid fa-floppy-disk"></i>
                        </span>
                        <span wire:loading>
                            Procesando... <i class="fa-solid fa-spinner fa-spin"></i>
                        </span>
                    </button>
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