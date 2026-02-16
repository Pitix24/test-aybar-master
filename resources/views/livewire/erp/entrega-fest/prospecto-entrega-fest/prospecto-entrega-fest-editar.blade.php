<div class="g_gap_pagina">
    <x-loading-overlay wire:loading message="Guardando evaluación..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Evaluar Prospecto: {{ $nombre }} {{ $apellidos }}</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.prospecto-entrega-fest.vista.todo') }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>
        </div>
    </div>

    <form wire:submit.prevent="update">
        <div class="g_panel">
            <div class="g_fila">
                <div class="g_margin_bottom_15 g_columna_6">
                    <label>Evento / Entrega Fest <span class="obligatorio">*</span></label>
                    <select wire:model="entrega_fest_id" class="@error('entrega_fest_id') select-error @enderror">
                        @foreach ($eventos as $e)
                            <option value="{{ $e->id }}">{{ $e->nombre }} ({{ $e->codigo }})</option>
                        @endforeach
                    </select>
                    @error('entrega_fest_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_margin_bottom_15 g_columna_6">
                    <label>DNI / Documento <span class="obligatorio">*</span></label>
                    <input type="text" wire:model="dni" class="@error('dni') input-error @enderror">
                    @error('dni') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_15 g_columna_6">
                    <label>Nombres <span class="obligatorio">*</span></label>
                    <input type="text" wire:model="nombre" class="@error('nombre') input-error @enderror">
                    @error('nombre') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
                <div class="g_margin_bottom_15 g_columna_6">
                    <label>Apellidos <span class="obligatorio">*</span></label>
                    <input type="text" wire:model="apellidos" class="@error('apellidos') input-error @enderror">
                    @error('apellidos') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_15 g_columna_4">
                    <label>Estado de Evaluación</label>
                    <select wire:model="estado" style="font-weight: bold; color: var(--color-primary);">
                        <option value="pendiente">Pendiente</option>
                        <option value="observado">Observado</option>
                        <option value="aprobado">Aprobado</option>
                        <option value="rechazado">Rechazado</option>
                    </select>
                </div>
                <div class="g_margin_bottom_15 g_columna_8">
                    <label>Observaciones / Motivo</label>
                    <textarea wire:model="observacion" rows="3"
                        placeholder="Explique el motivo del estado si es observado o rechazado..."></textarea>
                </div>
            </div>

            <div class="formulario_botones">
                <button type="submit" class="g_boton guardar">
                    Actualizar Evaluación <i class="fa-solid fa-floppy-disk"></i>
                </button>
            </div>
        </div>
    </form>
</div>