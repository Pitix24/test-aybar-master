<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="store" message="Guardando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Crear Ticket Libro Reclamacion</h2>

        <div class="cabecera_titulo_botones">
            @can('ticket-libro-reclamacion.lista')
                <a href="{{ route('erp.libro-reclamacion.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i>
                </a>
            @endcan
        </div>
    </div>

    <form wire:submit.prevent="store" class="g_fila">
        <div class="g_columna_8 formulario">
            <div class="g_panel g_gap_pagina">
                <div class="g_fila">
                    <div class="g_columna_4">
                        <label>Codigo</label>
                        <input type="text" wire:model="codigo" class="@error('codigo') input-error @enderror">
                        @error('codigo') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                    <div class="g_columna_4">
                        <label>Ticket origen (Libro)</label>
                        <input type="number" wire:model="libro_reclamacion_ticket"
                            class="@error('libro_reclamacion_ticket') input-error @enderror">
                        @error('libro_reclamacion_ticket') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                    <div class="g_columna_4">
                        <label>Clasificacion</label>
                        <select wire:model="clasificacion" class="@error('clasificacion') input-error @enderror">
                            <option value="PROCEDE">Procede</option>
                            <option value="NO_PROCEDE">No procede</option>
                            <option value="PENDIENTE_REVISION">Pendiente revision</option>
                        </select>
                        @error('clasificacion') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="g_fila">
                    <div class="g_columna_4">
                        <label>Unidad de negocio</label>
                        <select wire:model="unidad_negocio_id" class="@error('unidad_negocio_id') input-error @enderror">
                            <option value="">Seleccione</option>
                            @foreach ($unidades as $unidad)
                                <option value="{{ $unidad->id }}">{{ $unidad->nombre }}</option>
                            @endforeach
                        </select>
                        @error('unidad_negocio_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                    <div class="g_columna_4">
                        <label>Proyecto</label>
                        <select wire:model="proyecto_id" class="@error('proyecto_id') input-error @enderror">
                            <option value="">Seleccione</option>
                            @foreach ($proyectos as $proyecto)
                                <option value="{{ $proyecto->id }}">{{ $proyecto->nombre }}</option>
                            @endforeach
                        </select>
                        @error('proyecto_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                    <div class="g_columna_4">
                        <label>Cliente</label>
                        <select wire:model="cliente_id" class="@error('cliente_id') input-error @enderror">
                            <option value="">Seleccione</option>
                            @foreach ($usuarios as $usuario)
                                <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                            @endforeach
                        </select>
                        @error('cliente_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="g_fila">
                    <div class="g_columna_4">
                        <label>Gestor</label>
                        <select wire:model="gestor_id" class="@error('gestor_id') input-error @enderror">
                            <option value="">Sin asignar</option>
                            @foreach ($usuarios as $usuario)
                                <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                            @endforeach
                        </select>
                        @error('gestor_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                    <div class="g_columna_4">
                        <label>Estado legal</label>
                        <select wire:model="estado_legal" class="@error('estado_legal') input-error @enderror">
                            <option value="NUEVO">Nuevo</option>
                            <option value="EN_GESTION">En gestion</option>
                            <option value="OBSERVADO">Observado</option>
                            <option value="RESUELTO">Resuelto</option>
                            <option value="NO_PROCEDE">No procede</option>
                            <option value="CERRADO">Cerrado</option>
                        </select>
                        @error('estado_legal') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label>Nota fuente</label>
                    <textarea wire:model="nota_fuente" rows="5" class="@error('nota_fuente') input-error @enderror"></textarea>
                    @error('nota_fuente') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label>Observaciones internas</label>
                    <textarea wire:model="observaciones_internas" rows="4"
                        class="@error('observaciones_internas') input-error @enderror"></textarea>
                    @error('observaciones_internas') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="formulario_botones">
                    <button type="submit" class="g_boton guardar" wire:loading.attr="disabled">
                        Guardar <i class="fa-solid fa-floppy-disk"></i>
                    </button>
                    <button type="button" class="g_boton light" onclick="history.back()">
                        Regresar <i class="fa-solid fa-arrow-left"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
