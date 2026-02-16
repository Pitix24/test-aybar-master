<div class="g_gap_pagina">
    <x-loading-overlay wire:loading message="Procesando registro..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Registrar Nuevo Prospecto</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.prospecto-entrega-fest.vista.todo') }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>
        </div>
    </div>

    <form wire:submit.prevent="store">
        <div class="g_panel">
            <div class="g_fila">
                <div class="g_margin_bottom_15 g_columna_6">
                    <label>Evento / Entrega Fest <span class="obligatorio">*</span></label>
                    <select wire:model="entrega_fest_id" class="@error('entrega_fest_id') select-error @enderror">
                        <option value="">Seleccione el evento...</option>
                        @foreach ($eventos as $e)
                            <option value="{{ $e->id }}">{{ $e->nombre }} ({{ $e->codigo }})</option>
                        @endforeach
                    </select>
                    @error('entrega_fest_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_margin_bottom_15 g_columna_6">
                    <label>DNI / Documento <span class="obligatorio">*</span></label>
                    <input type="text" wire:model="dni" placeholder="Ej: 71234567"
                        class="@error('dni') input-error @enderror">
                    @error('dni') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_15 g_columna_6">
                    <label>Nombres <span class="obligatorio">*</span></label>
                    <input type="text" wire:model="nombre" placeholder="Ej: Juan Pedro"
                        class="@error('nombre') input-error @enderror">
                    @error('nombre') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
                <div class="g_margin_bottom_15 g_columna_6">
                    <label>Apellidos <span class="obligatorio">*</span></label>
                    <input type="text" wire:model="apellidos" placeholder="Ej: Pérez García"
                        class="@error('apellidos') input-error @enderror">
                    @error('apellidos') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_15 g_columna_4">
                    <label>Estado Inicial</label>
                    <select wire:model="estado">
                        <option value="pendiente">Pendiente</option>
                        <option value="observado">Observado</option>
                        <option value="aprobado">Aprobado</option>
                        <option value="rechazado">Rechazado</option>
                    </select>
                </div>
                <div class="g_margin_bottom_15 g_columna_8">
                    <label>Observaciones</label>
                    <input type="text" wire:model="observacion"
                        placeholder="Opcional: motivo de estado, nota interna...">
                </div>
            </div>

            <div class="formulario_botones">
                <button type="submit" class="g_boton guardar">
                    Registrar Prospecto <i class="fa-solid fa-user-plus"></i>
                </button>
            </div>
        </div>
    </form>
</div>