<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="update, eliminarLibroTicketOn" message="Guardando cambios..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Editar Ticket Libro Reclamacion</h2>

        <div class="cabecera_titulo_botones">
            @can('ticket-libro-reclamacion.ver')
                <a href="{{ route('erp.libro-reclamacion.vista.ver', $ticket_model->id) }}" class="g_boton warning">
                    Ver <i class="fa-solid fa-eye"></i>
                </a>
            @endcan

            @can('ticket-libro-reclamacion.lista')
                <a href="{{ route('erp.libro-reclamacion.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i>
                </a>
            @endcan
        </div>
    </div>

    <form wire:submit.prevent="update" class="g_fila">
        <div class="g_columna_8 formulario">
            <div class="g_panel g_gap_pagina">
                <div class="g_fila">
                    <div class="g_columna_4">
                        <label>Codigo (bloqueado)</label>
                        <input type="text" wire:model="codigo" disabled>
                    </div>
                    <div class="g_columna_4">
                        <label>Ticket origen (bloqueado)</label>
                        <input type="text" wire:model="libro_reclamacion_ticket" disabled>
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

                    <div class="g_columna_4">
                        <label>Assigned at</label>
                        <input type="text" value="{{ optional($ticket_model->assigned_at)->format('d/m/Y H:i') ?: 'N/D' }}" disabled>
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
                        Guardar cambios <i class="fa-solid fa-floppy-disk"></i>
                    </button>

                    @can('ticket-libro-reclamacion.eliminar')
                        <button type="button" class="g_boton danger" onclick="alertaEliminarLibroTicket()">
                            Eliminar <i class="fa-solid fa-trash"></i>
                        </button>
                    @endcan
                </div>
            </div>
        </div>
    </form>

    @script
    <script>
        window.alertaEliminarLibroTicket = function () {
            Swal.fire({
                title: '¿Eliminar ticket?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.eliminarLibroTicketOn();
                }
            });
        }
    </script>
    @endscript
</div>
