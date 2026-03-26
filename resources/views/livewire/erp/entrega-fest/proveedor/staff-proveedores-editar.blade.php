<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="update" message="Actualizando..." />
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            Editar Proveedor
            <span>{{ $evento->nombre }}</span>
        </h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.proveedor.todo', $evento->id) }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>

            <a href="{{ route('erp.entrega-fest.vista.staff', $evento->id) }}" class="g_boton info">
                <i class="fa-solid fa-grip"></i> Panel de Staff
            </a>

            <a href="{{ route('erp.entrega-fest.proveedor.crear', $evento->id) }}" class="g_boton primary">
                Crear <i class="fa-solid fa-square-plus"></i>
            </a>

            <button type="button" class="g_boton danger"
                onclick="Livewire.dispatch('alertaConfirmar', { event: 'eliminarProveedorOn', titulo: 'Eliminar Proveedor', texto: 'Esta accion no se puede deshacer.' })">
                Eliminar <i class="fa-solid fa-trash"></i>
            </button>

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_8">
            <form wire:submit.prevent="update" class="formulario g_panel g_gap_pagina">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-truck-fast"></i> Datos del Servicio</h4>

                <div class="g_fila">
                    <div class="g_columna_6 g_margin_bottom_10">
                        <label>Nombre Comercial / Empresa <span class="obligatorio"><i
                                    class="fa-solid fa-asterisk"></i></span></label>
                        <input type="text" wire:model="nombre_comercial"
                            class="@error('nombre_comercial') input-error @enderror">
                        @error('nombre_comercial') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                    <div class="g_columna_6 g_margin_bottom_10">
                        <label>Tipo de Servicio <span class="obligatorio"><i
                                    class="fa-solid fa-asterisk"></i></span></label>
                        <input type="text" wire:model="servicio_tipo"
                            class="@error('servicio_tipo') input-error @enderror">
                        @error('servicio_tipo') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="g_fila">
                    <div class="g_columna_6 g_margin_bottom_10">
                        <label>Nombre del Contacto</label>
                        <input type="text" wire:model="contacto_nombre">
                    </div>
                    <div class="g_columna_6 g_margin_bottom_10">
                        <label>Teléfono del Contacto</label>
                        <input type="text" wire:model="contacto_telefono">
                    </div>
                </div>

                <h4 class="g_panel_titulo"><i class="fa-solid fa-clock"></i> Horarios Operativos</h4>
                <div class="g_fila">
                    <div class="g_columna_3 g_margin_bottom_10">
                        <label>Hora Llegada</label>
                        <input type="time" wire:model="h_llegada">
                    </div>
                    <div class="g_columna_3 g_margin_bottom_10">
                        <label>Hora Montaje</label>
                        <input type="time" wire:model="h_montaje">
                    </div>
                    <div class="g_columna_3 g_margin_bottom_10">
                        <label>Hora del Show</label>
                        <input type="time" wire:model="h_show">
                    </div>
                    <div class="g_columna_3 g_margin_bottom_10">
                        <label>Hora Desmontaje</label>
                        <input type="time" wire:model="h_desmontaje">
                    </div>
                </div>

                <div class="g_margin_bottom_10">
                    <label>Estado Inicial</label>
                    <select wire:model="estado">
                        <option value="CONFIRMADO">Confirmado</option>
                        <option value="EN_SITIO">En Sitio</option>
                        <option value="COMPLETADO">Completado</option>
                    </select>
                </div>

                <div class="formulario_botones">
                    <button type="submit" class="g_boton guardar" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="update"><i class="fa-solid fa-save"></i>
                            Actualizar</span>
                        <span wire:loading wire:target="update"><i class="fa-solid fa-spinner fa-spin"></i>
                            Guardando...</span>
                    </button>
                    <a href="{{ route('erp.entrega-fest.proveedor.todo', $evento->id) }}" class="g_boton cancelar"><i
                            class="fa-solid fa-times"></i> Cancelar</a>
                </div>
            </form>
        </div>

        <div class="g_columna_4 g_gap_pagina">
            <div class="g_panel">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-list-check"></i> Requerimientos Técnicos</h4>
                <p class="g_panel_parrafo">Añade qué necesita el proveedor (puntos de luz, agua, espacio libre, etc.)
                </p>

                <div class="g_gap_pagina" style="gap:10px;">
                    @foreach($requerimientos as $index => $req)
                        <div style="display:flex; gap:8px;">
                            <input type="text" wire:model="requerimientos.{{ $index }}.texto"
                                placeholder="Ej: Punto de luz 220v" style="flex:1;">
                            <button type="button" wire:click="removerRequerimiento({{ $index }})"
                                class="g_boton danger small" style="padding:0 10px;">
                                <i class="fa-solid fa-times"></i>
                            </button>
                        </div>
                    @endforeach
                    <button type="button" wire:click="agregarRequerimiento" class="g_boton info g_boton_largo"
                        style="justify-content:center;">
                        <i class="fa-solid fa-plus"></i> Añadir Requerimiento
                    </button>
                </div>
            </div>

            <div class="g_panel formulario">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-list-check"></i> Observaciones </h4>
                <p class="g_panel_parrafo g_margin_bottom_10">Observaciones adicionales sobre el proveedor.</p>
                <textarea wire:model="observaciones" placeholder="Escribir observaciones..." rows="8"></textarea>
            </div>
        </div>
    </div>
</div>