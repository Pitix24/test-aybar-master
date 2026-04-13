<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="buscarCliente,agregarLote,quitarLote,store" message="Guardando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Crear Ticket Libro Reclamacion</h2>

        <div class="cabecera_titulo_botones">
            @can('ticket-libro-reclamacion.lista')
                <a href="{{ route('erp.libro-reclamacion.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i>
                </a>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    <form wire:submit.prevent="store" class="formulario g_panel" x-data="{ activeTab: 'general' }">
        <div class="g_tab_navegacion">
            <div class="g_tab_botones">
                <button type="button" @click="activeTab = 'general'"
                    :class="activeTab === 'general' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                    <i class="fa-solid fa-file-invoice"></i> Información General
                </button>

                <button type="button" @click="activeTab = 'cliente'"
                    :class="activeTab === 'cliente' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                    <i class="fa-solid fa-user"></i> Cliente
                </button>
            </div>
        </div>

        <div x-show="activeTab === 'general'" x-transition class="g_tab_content">
            <div class="g_fila">
                <div class="g_columna_4 g_margin_bottom_10">
                    <label>Unidad de negocio <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                    <select wire:model.live="unidad_negocio_id" class="@error('unidad_negocio_id') input-error @enderror">
                        <option value="">Seleccione...</option>
                        @foreach ($unidades as $unidad)
                            <option value="{{ $unidad->id }}">{{ $unidad->nombre }}</option>
                        @endforeach
                    </select>
                    @error('unidad_negocio_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_columna_4 g_margin_bottom_10">
                    <label>Proyecto <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                    <select wire:model.live="proyecto_id" class="@error('proyecto_id') input-error @enderror">
                        <option value="">Seleccione...</option>
                        @foreach ($proyectos as $proyecto)
                            <option value="{{ $proyecto->id }}">{{ $proyecto->nombre }}</option>
                        @endforeach
                    </select>
                    @error('proyecto_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_columna_4 g_margin_bottom_10">
                    <label>Gestor</label>
                    <select wire:model.live="gestor_id" class="@error('gestor_id') input-error @enderror">
                        <option value="">Sin asignar</option>
                        @foreach ($gestores as $gestor)
                            <option value="{{ $gestor->id }}">{{ $gestor->name }}</option>
                        @endforeach
                    </select>
                    @error('gestor_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="g_fila">
                <div class="g_columna_4 g_margin_bottom_10">
                    <label>Estado Legal</label>
                    <select wire:model.live="estado_legal" class="@error('estado_legal') input-error @enderror">
                        <option value="NUEVO">Nuevo</option>
                        <option value="EN_GESTION">En gestion</option>
                        <option value="OBSERVADO">Observado</option>
                        <option value="RESUELTO">Resuelto</option>
                        <option value="NO_PROCEDE">No procede</option>
                        <option value="CERRADO">Cerrado</option>
                    </select>
                    @error('estado_legal') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_columna_4 g_margin_bottom_10">
                    <label>Asignado desde</label>
                    <input type="text" value="{{ $gestor_id ? 'Gestor definido' : 'Sin asignar' }}" disabled>
                </div>

                <div class="g_columna_4 g_margin_bottom_10">
                    <label>Estado interno</label>
                    <input type="text" value="Nuevo" disabled>
                </div>
            </div>

            <div class="g_margin_bottom_10">
                <label>Observaciones internas</label>
                <textarea wire:model.blur="observaciones_internas" rows="5" class="@error('observaciones_internas') input-error @enderror"></textarea>
                @error('observaciones_internas') <p class="mensaje_error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div x-show="activeTab === 'cliente'" x-transition class="g_tab_content">
            @if (session('info'))
                <div class="g_alerta info">
                    <i class="fa-solid fa-circle-info"></i>
                    {{ session('info') }}
                </div>
            @endif

            @if (session('error'))
                <div class="g_alerta danger">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="g_alerta success">
                    <i class="fa-solid fa-circle-check"></i>
                    {{ session('success') }}
                </div>
            @endif

            <div class="g_fila">
                <div class="g_columna_8 g_margin_bottom_10">
                    <label>DNI / CE / RUC <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                    <input type="text" wire:model.live="dni" x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '')"
                        class="@error('dni') input-error @enderror">
                    @error('dni') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_columna_4 g_margin_bottom_10">
                    <label>&nbsp;</label>
                    <button type="button" wire:click="buscarCliente" class="g_boton guardar" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="buscarCliente"><i class="fa-solid fa-search"></i> Buscar</span>
                        <span wire:loading wire:target="buscarCliente">Buscando...</span>
                    </button>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_columna_6 g_margin_bottom_10">
                    <label>Nombre del cliente <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                    <input type="text" wire:model.blur="cliente_nombre" class="@error('cliente_nombre') input-error @enderror">
                    @error('cliente_nombre') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_columna_6 g_margin_bottom_10">
                    <label>Email</label>
                    <input type="email" wire:model.blur="cliente_email" class="@error('cliente_email') input-error @enderror">
                    @error('cliente_email') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="g_fila">
                <div class="g_columna_6 g_margin_bottom_10">
                    <label>Celular</label>
                    <input type="text" wire:model.blur="cliente_celular" class="@error('cliente_celular') input-error @enderror">
                    @error('cliente_celular') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_columna_6 g_margin_bottom_10">
                    <label>Dirección</label>
                    <input type="text" wire:model.blur="cliente_direccion" class="@error('cliente_direccion') input-error @enderror">
                    @error('cliente_direccion') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="g_margin_bottom_10">
                <label>Asunto <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                <textarea wire:model.blur="asunto" rows="4" class="@error('asunto') input-error @enderror"></textarea>
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
                                <span wire:loading.remove wire:target="agregarLote"><i class="fa-solid fa-plus"></i> Agregar</span>
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
                                            <button type="button" wire:click="quitarLote('{{ $lote['id'] }}')" class="g_boton danger" title="Quitar">
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

        <div class="formulario_botones">
            @can('ticket-libro-reclamacion.crear')
                <button type="submit" class="g_boton guardar" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="store"><i class="fa-solid fa-save"></i> Crear</span>
                    <span wire:loading wire:target="store"><i class="fa-solid fa-spinner fa-spin"></i> Creando...</span>
                </button>
            @endcan

            <button type="button" class="g_boton cancelar" onclick="history.back()">
                <i class="fa-solid fa-times"></i> Cancelar
            </button>
        </div>
    </form>
</div>