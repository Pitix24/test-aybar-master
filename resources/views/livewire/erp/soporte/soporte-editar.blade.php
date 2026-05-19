<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="guardar,asignarGestor,marcarResuelto,cerrar"
        message="Guardando cambios..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Editar Ticket de Soporte #{{ $soporte->codigo }}</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.soporte.vista.todo') }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>

            @can('soporte.vista-ver')
            <a href="{{ route('erp.soporte.vista.ver', $soporte) }}" class="g_boton warning">
                Ver Ticket<i class="fa-solid fa-eye"></i>
            </a>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    <form wire:submit="guardar" class="formulario g_panel">
        <div class="g_fila">
            <div class="g_margin_bottom_10 g_columna_3">
                <label>Tipo <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                <select wire:model.live="tipo_soporte_id" class="@error('tipo_soporte_id') input-error @enderror">
                    <option value="">Seleccione...</option>
                    @foreach ($tipos as $t)
                    <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                    @endforeach
                </select>
                @error('tipo_soporte_id') <p class="mensaje_error">{{ $message }}</p> @enderror
            </div>

            <div class="g_margin_bottom_10 g_columna_3">
                <label>Prioridad <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                <select wire:model.live="prioridad_soporte_id"
                    class="@error('prioridad_soporte_id') input-error @enderror">
                    <option value="">Seleccione...</option>
                    @foreach ($prioridades as $p)
                    <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                    @endforeach
                </select>
                @error('prioridad_soporte_id') <p class="mensaje_error">{{ $message }}</p> @enderror
            </div>

            <div class="g_margin_bottom_10 g_columna_3">
                <label>Estado <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                <select wire:model.live="estado_soporte_id" class="@error('estado_soporte_id') input-error @enderror">
                    <option value="">Seleccione...</option>
                    @foreach ($estados as $e)
                    <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                    @endforeach
                </select>
                @error('estado_soporte_id') <p class="mensaje_error">{{ $message }}</p> @enderror
            </div>

            <div class="g_margin_bottom_10 g_columna_3">
                <label>Área</label>
                <select wire:model.live="area_id" class="@error('area_id') input-error @enderror">
                    <option value="">Sin área asignada</option>
                    @foreach ($areas as $a)
                    <option value="{{ $a->id }}">{{ $a->nombre }}</option>
                    @endforeach
                </select>
                @error('area_id') <p class="mensaje_error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="g_margin_bottom_10">
            <label>Título <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
            <input type="text" wire:model.blur="titulo" class="@error('titulo') input-error @enderror">
            @error('titulo') <p class="mensaje_error">{{ $message }}</p> @enderror
        </div>

        <div class="g_margin_bottom_10">
            <label>Descripción <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
            <textarea wire:model.blur="descripcion" rows="6"
                class="@error('descripcion') input-error @enderror"></textarea>
            @error('descripcion') <p class="mensaje_error">{{ $message }}</p> @enderror
        </div>

        <div class="g_margin_bottom_10">
            <h4 class="g_panel_titulo"><i class="fa-solid fa-note-sticky"></i> Notas / Observaciones</h4>
            <textarea wire:model.blur="observaciones" rows="5" class="@error('observaciones') input-error @enderror"
                placeholder="Agregar notas de seguimiento visibles para gestores creadores"></textarea>
            @error('observaciones') <p class="mensaje_error">{{ $message }}</p> @enderror
        </div>

        <div class="g_margin_bottom_10">
            <h4 class="g_panel_titulo"><i class="fa-solid fa-circle-info"></i> Información del Ticket</h4>

            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Código</label>
                    <input class="input-disabled" type="text" value="{{ $soporte->codigo }}" disabled>
                </div>

                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Solicitante</label>
                    <select wire:model.live="solicitante_id" class="@error('solicitante_id') input-error @enderror">
                        <option value="">Sin solicitante</option>
                        @foreach ($solicitantes as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                    @error('solicitante_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Gestor</label>
                    <select wire:model.live="gestor_id" class="@error('gestor_id') input-error @enderror">
                        <option value="">Sin asignar</option>
                        @foreach ($gestores as $g)
                        <option value="{{ $g->id }}">{{ $g->name }}</option>
                        @endforeach
                    </select>
                    @error('gestor_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Creado</label>
                    <input class="input-disabled" type="text"
                        value="{{ $soporte->created_at?->format('d/m/Y H:i') ?? '—' }}"
                        class="@error('created_at') input-error @enderror" disabled>
                    @error('created_at') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Asignado</label>
                    <input class="input-disabled" type="text"
                        value="{{ $soporte->assigned_at?->format('d/m/Y H:i') ?? 'Pendiente' }}"
                        class="@error('assigned_at') input-error @enderror" disabled>
                    @error('assigned_at') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
                <div class="g_margin_bottom_10 g_columna_4">
                    <label>Resuelto</label>
                    <input class="input-disabled" type="text"
                        value="{{ $soporte->resuelto_at?->format('d/m/Y H:i') ?? 'Pendiente' }}"
                        class="@error('resuelto_at') input-error @enderror" disabled>
                    @error('resuelto_at') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="formulario_botones">
            <button type="submit" class="g_boton guardar" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="guardar">
                    <i class="fa-solid fa-save"></i> Guardar
                </span>
                <span wire:loading wire:target="guardar">
                    <i class="fa-solid fa-spinner fa-spin"></i> Guardando...
                </span>
            </button>

            @if ($soporte->estadoSoporte?->nombre === 'ABIERTO')
            <button type="button" wire:click="asignarGestor" class="g_boton success" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="asignarGestor">
                    <i class="fa-solid fa-user-check"></i> Asignarse
                </span>
                <span wire:loading wire:target="asignarGestor">
                    <i class="fa-solid fa-spinner fa-spin"></i>
                </span>
            </button>
            @endif

            @if ($soporte->estadoSoporte?->nombre === 'EN_PROGRESO' && $soporte->gestor_id === Auth::id())
            <button type="button" wire:click="marcarResuelto" class="g_boton warning" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="marcarResuelto">
                    <i class="fa-solid fa-check"></i> Marcar Resuelto
                </span>
                <span wire:loading wire:target="marcarResuelto">
                    <i class="fa-solid fa-spinner fa-spin"></i>
                </span>
            </button>
            @endif

            @if ($soporte->estadoSoporte?->nombre === 'EN_PROGRESO' && $soporte->gestor_id === Auth::id())
            <button type="button" wire:click="marcarNoProcede" class="g_boton danger" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="marcarNoProcede">
                    <i class="fa-solid fa-check"></i> Marcar No Procedente
                </span>
                <span wire:loading wire:target="marcarNoProcede">
                    <i class="fa-solid fa-spinner fa-spin"></i>
                </span>
            </button>
            @endif

            <button type="button" class="g_boton cancelar" onclick="history.back()">
                <i class="fa-solid fa-times"></i> Cancelar
            </button>
        </div>
    </form>

    <!-- Componente de Archivos -->
    <livewire:erp.soporte.soporte-archivo :soporte="$soporte" />
</div>
