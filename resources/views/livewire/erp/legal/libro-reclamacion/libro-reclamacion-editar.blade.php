<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="buscarCliente,agregarLote,quitarLote,update,eliminarLibroTicketOn"
        message="Guardando cambios..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Editar Ticket Reclamacion #{{ $ticket_model->ticket }}</h2>

        <div class="cabecera_titulo_botones">
            @can('libro-reclamacion.ver')
            <a href="{{ route('erp.libro-reclamacion.vista.ver', $ticket_model->ticket) }}" class="g_boton warning">
                Ver <i class="fa-solid fa-eye"></i>
            </a>
            @endcan

            @can('libro-reclamacion.lista')
            <a href="{{ route('erp.libro-reclamacion.vista.todo') }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>
            @endcan

            @can('ticket.ver')
            @if ($ticket_model->ticketRelacionado)
            <a href="{{ route('erp.ticket.vista.ver', $ticket_model->ticketRelacionado->id) }}" class="g_boton warning">
                Ver Ticket <i class="fa-solid fa-ticket"></i>
            </a>
            @endif
            @endcan
        </div>
    </div>

    <form wire:submit.prevent="update" class="formulario g_panel">
        @include('livewire.erp.legal.libro-reclamacion.libro-reclamacion-form', ['submitAction' => 'update'])

        <div class="formulario_botones">
            @can('libro-reclamacion.editar')
            <button type="button" class="g_boton guardar" wire:loading.attr="disabled"
                onclick="document.querySelector('.libro-reclamacion-form button[wire\\:click]').click()">
                <span wire:loading.remove wire:target="update"><i class="fa-solid fa-floppy-disk"></i> Guardar
                    cambios</span>
                <span wire:loading wire:target="update"><i class="fa-solid fa-spinner fa-spin"></i> Guardando...</span>
            </button>
            @endcan

            @can('libro-reclamacion.eliminar')
            <button type="button" class="g_boton danger" onclick="alertaEliminarLibroTicket()">
                Eliminar <i class="fa-solid fa-trash"></i>
            </button>
            @endcan

            <button type="button" class="g_boton cancelar" onclick="history.back()">
                <i class="fa-solid fa-times"></i> Cancelar
            </button>
        </div>
    </form>

    <div class="g_columna_6 g_margin_bottom_10">
        <label>Dirección</label>
        <input type="text" wire:model.blur="cliente_direccion"
            class="@error('cliente_direccion') input-error @enderror">
        @error('cliente_direccion') <p class="mensaje_error">{{ $message }}</p> @enderror
    </div>
</div>

<div class="g_fila">
    <div class="g_columna_6 g_margin_bottom_10">
        <label>Tipo de documento</label>
        <input type="text" value="{{ $cliente_tipo_documento ?: 'N/D' }}" disabled>
    </div>

    <div class="g_columna_6 g_margin_bottom_10">
        <label>Documento del cliente</label>
        <input type="text" value="{{ $cliente_documento ?: 'N/D' }}" disabled>
    </div>
</div>

<!-- Indicador de menor de edad -->
<div class="g_margin_top_15 g_margin_bottom_10">
    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px;">
        <input type="checkbox" wire:model.live="es_cliente_menor">
        <strong class="g_negrita">¿Es el reclamante menor de edad?</strong>
    </label>
</div>

<!-- Bloque condicional: Datos del representante legal -->
@if ($es_cliente_menor)
<div class="g_margin_top_10 g_alerta warning">
    <i class="fa-solid fa-alert"></i>
    <strong>Representante Legal Requerido (Cliente Menor de Edad)</strong>
</div>

<div class="g_fila g_margin_top_10">
    <div class="g_columna_6 g_margin_bottom_10">
        <label>Nombre del representante legal <span class="obligatorio"><i
                    class="fa-solid fa-asterisk"></i></span></label>
        <input type="text" wire:model.blur="representante_legal_nombre"
            class="@error('representante_legal_nombre') input-error @enderror">
        @error('representante_legal_nombre') <p class="mensaje_error">{{ $message }}</p> @enderror
    </div>

    <div class="g_columna_6 g_margin_bottom_10">
        <label>Apellido paterno del representante legal <span class="obligatorio"><i
                    class="fa-solid fa-asterisk"></i></span></label>
        <input type="text" wire:model.blur="representante_legal_apellido_paterno"
            class="@error('representante_legal_apellido_paterno') input-error @enderror">
        @error('representante_legal_apellido_paterno') <p class="mensaje_error">{{ $message }}</p> @enderror
    </div>

    <div class="g_columna_6 g_margin_bottom_10">
        <label>Apellido materno del representante legal <span class="obligatorio"><i
                    class="fa-solid fa-asterisk"></i></span></label>
        <input type="text" wire:model.blur="representante_legal_apellido_materno"
            class="@error('representante_legal_apellido_materno') input-error @enderror">
        @error('representante_legal_apellido_materno') <p class="mensaje_error">{{ $message }}</p> @enderror
    </div>
</div>
@endif
</div>

<div x-show="activeTab === 'asunto'" x-transition class="g_tab_content">
    <div class="g_margin_bottom_10">
        <label>Asunto <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
        <textarea wire:model.blur="asunto" rows="5" class="@error('asunto') input-error @enderror"></textarea>
        @error('asunto') <p class="mensaje_error">{{ $message }}</p> @enderror
    </div>

    @if ($informaciones->isNotEmpty())
    <div class="g_margin_bottom_10">
        <h4 class="g_panel_titulo"><i class="fa-solid fa-layer-group"></i> Lotes disponibles</h4>

        <div class="g_fila">
            <div class="g_columna_8 g_margin_bottom_10">
                <select wire:model.live="lote_id">
                    <option value="">Seleccionar lote</option>
                    @foreach ($informaciones as $lote)
                    <option value="{{ $lote->id }}">
                        {{ $lote->razon_social }} - {{ $lote->proyecto }} - {{ $lote->numero_lote }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="g_columna_4 g_margin_bottom_10">
                <button type="button" wire:click="agregarLote" class="g_boton guardar" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="agregarLote"><i class="fa-solid fa-plus"></i>
                        Agregar</span>
                    <span wire:loading wire:target="agregarLote">Agregando...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    @if (! empty($lotes_agregados))
    <div class="g_margin_bottom_10">
        <h4 class="g_panel_titulo"><i class="fa-solid fa-layer-group"></i> Lotes seleccionados</h4>

        <div class="g_contenedor_tabla">
            <table class="g_tabla">
                <thead>
                    <tr>
                        <th>Razón Social</th>
                        <th>Proyecto</th>
                        <th>Mz./Lt.</th>
                        <th>Estado</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lotes_agregados as $lote)
                    <tr wire:key="lote-{{ $lote['id'] }}">
                        <td>{{ $lote['razon_social'] ?? 'N/D' }}</td>
                        <td>{{ $lote['proyecto'] ?? 'N/D' }}</td>
                        <td>{{ $lote['numero_lote'] ?? 'N/D' }}</td>
                        <td>{{ $lote['estado_lote'] ?? 'N/D' }}</td>
                        <td class="g_celda_acciones g_celda_centro">
                            <button type="button" wire:click="quitarLote('{{ $lote['id'] }}')" class="g_boton danger"
                                title="Quitar">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<div x-show="activeTab === 'nota'" x-transition class="g_tab_content">
    <div class="g_margin_bottom_10">
        <label>Observaciones internas</label>
        <textarea wire:model.blur="observaciones_internas" rows="5"
            class="@error('observaciones_internas') input-error @enderror"></textarea>
        @error('observaciones_internas') <p class="mensaje_error">{{ $message }}</p> @enderror
    </div>
</div>

<div x-show="activeTab === 'auditoria'" x-transition class="g_tab_content">
    <div class="g_fila">
        <div class="g_columna_4 g_margin_bottom_10">
            <label>Creado</label>
            <input type="text" value="{{ optional($ticket_model->created_at)->format('d/m/Y H:i') ?: 'N/D' }}" disabled>
        </div>

        <div class="g_columna_4 g_margin_bottom_10">
            <label>Actualizado</label>
            <input type="text" value="{{ optional($ticket_model->updated_at)->format('d/m/Y H:i') ?: 'N/D' }}" disabled>
        </div>

        <div class="g_columna_4 g_margin_bottom_10">
            <label>Asignado</label>
            <input type="text" value="{{ optional($ticket_model->assigned_at)->format('d/m/Y H:i') ?: 'N/D' }}"
                disabled>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_4 g_margin_bottom_10">
            <label>Creado por</label>
            <input type="text" value="{{ $ticket_model->creador?->name ?: 'N/D' }}" disabled>
        </div>

        <div class="g_columna_4 g_margin_bottom_10">
            <label>Actualizado por</label>
            <input type="text" value="{{ $ticket_model->actualizador?->name ?: 'N/D' }}" disabled>
        </div>

        <div class="g_columna_4 g_margin_bottom_10">
            <label>Eliminado por</label>
            <input type="text" value="{{ $ticket_model->eliminador?->name ?: 'N/D' }}" disabled>
        </div>
    </div>
</div>

<div class="formulario_botones">
    @can('libro-reclamacion.editar')
    <button type="submit" class="g_boton guardar" wire:loading.attr="disabled">
        <span wire:loading.remove wire:target="update"><i class="fa-solid fa-floppy-disk"></i> Guardar
            cambios</span>
        <span wire:loading wire:target="update"><i class="fa-solid fa-spinner fa-spin"></i> Guardando...</span>
    </button>
    @endcan

    @can('libro-reclamacion.eliminar')
    <button type="button" class="g_boton danger" onclick="alertaEliminarLibroTicket()">
        Eliminar <i class="fa-solid fa-trash"></i>
    </button>
    @endcan

    <button type="button" class="g_boton cancelar" onclick="history.back()">
        <i class="fa-solid fa-times"></i> Cancelar
    </button>
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