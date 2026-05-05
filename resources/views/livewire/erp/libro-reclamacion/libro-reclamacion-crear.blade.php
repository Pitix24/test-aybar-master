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

    <div class="g_fila">
        <div class="g_columna_8">
            <form wire:submit.prevent="store" class="formulario g_panel">
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
                            <label>Unidad de negocio <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <select wire:model.live="unidad_negocio_id"
                                class="@error('unidad_negocio_id') input-error @enderror">
                                <option value="">Seleccione...</option>
                                @foreach ($unidades as $unidad)
                                <option value="{{ $unidad->id }}">{{ $unidad->nombre }}</option>
                                @endforeach
                            </select>
                            @error('unidad_negocio_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        <div class="g_columna_4 g_margin_bottom_10">
                            <label>Código de ticket</label>
                            <input type="text" value="{{ $codigo ?: 'NUL' }}" disabled>
                        </div>

                        <div class="g_columna_4 g_margin_bottom_10">
                            <label>Proyecto <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <select wire:model.live="proyecto_id" class="@error('proyecto_id') input-error @enderror">
                                <option value="">Seleccione...</option>
                                @foreach ($proyectos as $proyecto)
                                <option value="{{ $proyecto->id }}">{{ $proyecto->nombre }}</option>
                                @endforeach
                            </select>
                            @error('proyecto_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        <div class="g_columna_4 g_margin_bottom_10">
                            <label>Gestor (Area Legal)</label>
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
                            <label>Subtipo <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                            <select wire:model.live="tipo_pedido" class="@error('tipo_pedido') input-error @enderror">
                                <option value="">Seleccione...</option>
                                <option value="RECLAMO">RECLAMO</option>
                                <option value="QUEJA">QUEJA</option>
                            </select>
                            @error('tipo_pedido') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        <div class="g_columna_4 g_margin_bottom_10">
                            <label>Asignado desde</label>
                            <input type="text" value="{{ $gestor_id ? 'Gestor definido' : 'Sin asignar' }}" disabled>
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Asunto <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                        <textarea wire:model.blur="asunto" rows="4"
                            class="@error('asunto') input-error @enderror"></textarea>
                        @error('asunto') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Observaciones internas</label>
                        <textarea wire:model.blur="observaciones_internas" rows="5"
                            class="@error('observaciones_internas') input-error @enderror"></textarea>
                        @error('observaciones_internas') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>

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
                                            <button type="button" wire:click="quitarLote('{{ $lote['id'] }}')"
                                                class="g_boton danger" title="Quitar">
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

                <div x-show="activeTab === 'cliente'" x-transition class="g_tab_content">
                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Nombre del cliente <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <input type="text" wire:model.blur="cliente_nombre"
                                class="@error('cliente_nombre') input-error @enderror">
                            @error('cliente_nombre') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>DNI / CE / RUC (opcional)</label>
                            <input type="text" wire:model.live="dni"
                                x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '')"
                                class="@error('dni') input-error @enderror">
                            @error('dni') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Email</label>
                            <input type="email" wire:model.blur="cliente_email"
                                class="@error('cliente_email') input-error @enderror">
                            @error('cliente_email') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Celular</label>
                            <input type="text" wire:model.blur="cliente_celular"
                                class="@error('cliente_celular') input-error @enderror">
                            @error('cliente_celular') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Dirección</label>
                        <input type="text" wire:model.blur="cliente_direccion"
                            class="@error('cliente_direccion') input-error @enderror">
                        @error('cliente_direccion') <p class="mensaje_error">{{ $message }}</p> @enderror
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
                            @error('representante_legal_apellido_paterno') <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Apellido materno del representante legal <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <input type="text" wire:model.blur="representante_legal_apellido_materno"
                                class="@error('representante_legal_apellido_materno') input-error @enderror">
                            @error('representante_legal_apellido_materno') <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    @endif
                </div>

                @include('livewire.erp.libro-reclamacion.libro-reclamacion-form')
            </form>
        </div>

        <div class="g_columna_4 formulario">
            <div class="g_panel">
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

                <h4 class="g_panel_titulo">Buscar cliente</h4>

                <div class="g_margin_bottom_10">
                    <label>DNI/CE/RUC</label>
                    <input type="text" wire:model.live="dni" x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '')"
                        class="@error('dni') input-error @enderror">
                    @error('dni') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="formulario_botones g_margin_bottom_10">
                    <button type="button" wire:click="buscarCliente" class="g_boton guardar"
                        wire:loading.attr="disabled" wire:target="buscarCliente">
                        <span wire:loading.remove wire:target="buscarCliente"><i class="fa-solid fa-search"></i>
                            Buscar</span>
                        <span wire:loading wire:target="buscarCliente">Buscando...</span>
                    </button>
                </div>

                @if ($informaciones->isNotEmpty())
                <h4 class="g_panel_titulo"><i class="fa-solid fa-layer-group"></i> Lotes disponibles</h4>

                <div class="g_margin_bottom_10">
                    <select wire:model.live="lote_id">
                        <option value="">Seleccionar lote</option>
                        @foreach ($informaciones as $lote)
                        <option value="{{ $lote->id }}">
                            {{ $lote->razon_social }} - {{ $lote->proyecto }} - {{ $lote->numero_lote }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="formulario_botones">
                    <button type="button" wire:click="agregarLote" class="g_boton guardar" wire:loading.attr="disabled"
                        wire:target="agregarLote">
                        <span wire:loading.remove wire:target="agregarLote"><i class="fa-solid fa-plus"></i>
                            Agregar</span>
                        <span wire:loading wire:target="agregarLote">Agregando...</span>
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>