<div class="g_gap_pagina">
    <x-loading-overlay wire:loading message="Procesando registro..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Añadir Prospecto: <span style="color: var(--color-primary);">{{ $evento->nombre }}</span></h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.vista.prospectos', $evento->id) }}" class="g_boton dark">
                <i class="fa-solid fa-arrow-left"></i> Cancelar y Volver
            </a>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_8">
            <form wire:submit.prevent="store" class="g_panel formulario">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-user-tag"></i> Datos del Prospecto</h4>

                <div class="g_fila">
                    <div class="g_margin_bottom_10 g_columna_6">
                        <label>Proyecto <span class="obligatorio">*</span></label>
                        <select wire:model="proyecto_id" class="@error('proyecto_id') select-error @enderror">
                            <option value="">Seleccione el proyecto...</option>
                            @foreach ($proyectos as $p)
                                <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                            @endforeach
                        </select>
                        @error('proyecto_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>

                    <div class="g_margin_bottom_10 g_columna_6">
                        <label>DNI / Documento <span class="obligatorio">*</span></label>
                        <input type="text" wire:model="dni" class="@error('dni') input-error @enderror">
                        @error('dni') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="g_fila">
                    <div class="g_margin_bottom_10 g_columna_6">
                        <label>Nombres Completos <span class="obligatorio">*</span></label>
                        <input type="text" wire:model="nombres" class="@error('nombres') input-error @enderror">
                        @error('nombres') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>

                    <div class="g_margin_bottom_10 g_columna_6">
                        <label>Correo Electrónico <span class="obligatorio">*</span></label>
                        <input type="email" wire:model="email" class="@error('email') input-error @enderror">
                        @error('email') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="g_fila">
                    <div class="g_margin_bottom_10 g_columna_6">
                        <label>Celular <span class="obligatorio">*</span></label>
                        <input type="text" wire:model="celular" class="@error('celular') input-error @enderror">
                        @error('celular') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>

                    <div class="g_margin_bottom_10 g_columna_6">
                        <label>Estado Prospecto <span class="obligatorio">*</span></label>
                        <select wire:model="estado">
                            <option value="pendiente">Pendiente</option>
                            <option value="observado">Observado</option>
                            <option value="aprobado">Aprobado</option>
                            <option value="rechazado">Rechazado</option>
                        </select>
                    </div>
                </div>

                <div class="g_fila">
                    <div class="g_margin_bottom_10 g_columna_6">
                        <label>Manzana</label>
                        <input type="text" wire:model="manzana">
                    </div>

                    <div class="g_margin_bottom_10 g_columna_6">
                        <label>Lote</label>
                        <input type="text" wire:model="lote">
                    </div>
                </div>

                <div class="g_fila">
                    <div class="g_margin_bottom_10 g_columna_12">
                        <label>Observaciones</label>
                        <textarea wire:model="observacion" rows="5"></textarea>
                    </div>
                </div>

                <div class="formulario_botones">
                    <button type="submit" class="g_boton guardar" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="store">
                            <i class="fa-solid fa-save"></i> Crear
                        </span>
                        <span wire:loading wire:target="store">
                            <i class="fa-solid fa-spinner fa-spin"></i> Creando...
                        </span>
                    </button>

                    <button type="button" class="g_boton cancelar" onclick="history.back()">
                        <i class="fa-solid fa-times"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>