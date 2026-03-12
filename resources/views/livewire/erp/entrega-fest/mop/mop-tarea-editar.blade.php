<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="update" message="Actualizando..." />
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            Editar Tarea MOP
            <span>{{ $evento->nombre }}</span>
        </h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.mop.tareas', $evento->id) }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>

            <a href="{{ route('erp.entrega-fest.vista.staff', $evento->id) }}" class="g_boton info">
                <i class="fa-solid fa-grip"></i> Panel de Staff
            </a>

            <button type="button" class="g_boton danger"
                onclick="Livewire.dispatch('alertaConfirmar', { event: 'eliminarTareaOn', titulo: 'Eliminar Tarea', texto: 'Esta accion no se puede deshacer.' })">
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
                <h4 class="g_panel_titulo"><i class="fa-solid fa-pencil"></i> Tarea #{{ $tarea->id }}</h4>

                <div class="g_fila">
                    <div class="g_columna_6 g_margin_bottom_10">
                        <label>Responsable <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                        <select wire:model="user_id" class="@error('user_id') select-error @enderror">
                            <option value="">Seleccione...</option>
                            @foreach($usuarios as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                        @error('user_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                    <div class="g_columna_3 g_margin_bottom_10">
                        <label>Fase</label>
                        <select wire:model="fase">
                            <option value="ANTES">Antes del Evento</option>
                            <option value="DURANTE">Durante el Evento</option>
                            <option value="CIERRE">Cierre</option>
                        </select>
                    </div>
                </div>

                <div class="g_margin_bottom_10">
                    <label>Titulo <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                    <input type="text" wire:model="titulo" class="@error('titulo') input-error @enderror">
                    @error('titulo') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_margin_bottom_10">
                    <label>Instruccion <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                    <textarea wire:model="instruccion" rows="4"
                        class="@error('instruccion') input-error @enderror"></textarea>
                    @error('instruccion') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>


                <div class="formulario_botones">
                    <button type="submit" class="g_boton guardar" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="update"><i class="fa-solid fa-save"></i>
                            Actualizar</span>
                        <span wire:loading wire:target="update"><i class="fa-solid fa-spinner fa-spin"></i>
                            Guardando...</span>
                    </button>
                    <a href="{{ route('erp.entrega-fest.mop.todo', $evento->id) }}" class="g_boton cancelar"><i
                            class="fa-solid fa-times"></i> Cancelar</a>
                </div>
            </form>
        </div>

        <div class="g_columna_4">
            <div class="g_panel">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-circle-info"></i> Resumen</h4>
                <p class="g_inferior g_mayuscula" style="margin:0 0 4px 0; font-size:10px;">Estado</p>
                <span class="g_badge {{ $tarea->esta_completado ? 'success' : 'light' }} g_mayuscula">
                    {{ $tarea->esta_completado ? 'Completada' : 'Pendiente' }}
                </span>
                @if($tarea->esta_completado)
                    <div style="margin-top:12px; border-top:1px solid var(--borde-card-color, #e5e7eb); padding-top:12px;">
                        <p class="g_inferior g_mayuscula" style="margin:0 0 4px 0; font-size:10px;">Completada el</p>
                        <p class="g_inferior" style="margin:0;">
                            {{ $tarea->completado_at ? $tarea->completado_at->format('d/m/Y H:i') : 'N/A' }}
                        </p>
                        
                        @if($tarea->getFirstMediaUrl('evidencias'))
                            <p class="g_inferior g_mayuscula" style="margin:8px 0 4px 0; font-size:10px;">Evidencia</p>
                            <a href="{{ $tarea->getFirstMediaUrl('evidencias') }}" target="_blank">
                                <img src="{{ $tarea->getFirstMediaUrl('evidencias') }}" 
                                    style="width:100%; height:auto; border-radius:8px; border:1px solid #ddd;">
                            </a>
                        @endif
                    </div>
                @endif
                <div style="margin-top:12px; border-top:1px solid var(--borde-card-color, #e5e7eb); padding-top:12px;">
                    <p class="g_inferior g_mayuscula" style="margin:0 0 4px 0; font-size:10px;">Creada</p>
                    <p class="g_inferior" style="margin:0;">{{ $tarea->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>