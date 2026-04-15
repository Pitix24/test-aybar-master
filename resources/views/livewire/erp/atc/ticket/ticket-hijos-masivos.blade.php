<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="agregarALista, crearHijosMasivos" message="Procesando..." />

    <div class="g_fila">
        {{-- Formulario de creación rápida --}}
        <div class="g_columna_12">
            <div class="g_panel">
                <div class="g_tab_navegacion">
                    <div class="g_tab_botones">
                        <button type="button" class="g_tab_boton g_tab_active">
                            <i class="fa-solid fa-file-invoice"></i> Información General
                        </button>
                    </div>
                </div>

                <div class="g_tab_content">
                    <div class="formulario">
                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Unidad de Negocio</label>
                                <input type="text" disabled value="{{ $parentTicket->unidadNegocio->nombre }}">
                            </div>
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Proyecto</label>
                                <input type="text" disabled value="{{ $parentTicket->proyecto->nombre }}">
                            </div>
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Área Origen*</label>
                                <select wire:model.live="area_id" class="@error('area_id') input-error @enderror">
                                    <option value="">Seleccione...</option>
                                    @foreach($areas as $area)
                                        <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('area_id') <span class="g_error">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Tipo Solicitud*</label>
                                <select wire:model.live="tipo_solicitud_id"
                                    class="@error('tipo_solicitud_id') input-error @enderror">
                                    <option value="">Seleccione...</option>
                                    @foreach($tiposSolicitud as $ts)
                                        <option value="{{ $ts->id }}">{{ $ts->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('tipo_solicitud_id') <span class="g_error">{{ $message }}</span> @enderror
                            </div>
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Subtipo</label>
                                <select wire:model.live="sub_tipo_solicitud_id">
                                    <option value="">Seleccione...</option>
                                    @foreach($subTiposSolicitud as $sts)
                                        <option value="{{ $sts->id }}">{{ $sts->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Canal*</label>
                                <select wire:model="canal_id" class="@error('canal_id') input-error @enderror">
                                    <option value="">Seleccione...</option>
                                    @foreach($canales as $canal)
                                        <option value="{{ $canal->id }}">{{ $canal->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('canal_id') <span class="g_error">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Prioridad*</label>
                                <select wire:model="prioridad_ticket_id"
                                    class="@error('prioridad_ticket_id') input-error @enderror">
                                    <option value="">Seleccionar...</option>
                                    @foreach($prioridades as $pr)
                                        <option value="{{ $pr->id }}">{{ $pr->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('prioridad_ticket_id') <span class="g_error">{{ $message }}</span> @enderror
                            </div>
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Gestor Origen*</label>
                                <select wire:model="gestor_id" class="@error('gestor_id') input-error @enderror">
                                    <option value="">Seleccione...</option>
                                    @foreach($gestoresDisponibles as $gestor)
                                        <option value="{{ $gestor->id }}">{{ $gestor->name }}</option>
                                    @endforeach
                                </select>
                                @error('gestor_id') <span class="g_error">{{ $message }}</span> @enderror
                            </div>
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Estado</label>
                                <input type="text" disabled value="Nuevo">
                            </div>
                        </div>

                        <div class="g_margin_bottom_10">
                            <label>Asunto*</label>
                            <input type="text" wire:model="asunto" class="@error('asunto') input-error @enderror"
                                placeholder="Asunto del ticket hijo">
                            @error('asunto') <span class="g_error">{{ $message }}</span> @enderror
                        </div>

                        <div class="g_margin_bottom_10">
                            <label>Descripción detallada*</label>
                            <textarea wire:model="descripcion" rows="3"
                                class="@error('descripcion') input-error @enderror"
                                placeholder="Detalles de este requerimiento específico"></textarea>
                            @error('descripcion') <span class="g_error">{{ $message }}</span> @enderror
                        </div>

                        <div class="g_fila g_margin_top_10">
                            <div class="g_columna_12" style="display: flex; gap: 10px;">
                                <button type="button" wire:click="agregarALista" class="g_boton primary">
                                    Agregar a la lista <i class="fa-solid fa-plus"></i>
                                </button>
                                <button type="button" wire:click="$dispatch('cerrarModalHijosMasivos')"
                                    class="g_boton light">
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="g_fila">
        {{-- Lista de espera --}}
        @if(!empty($hijosParaCrear))
            <div class="g_columna_12 g_margin_top_20">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Tickets por crear ({{ count($hijosParaCrear) }})</h4>
                    <div class="g_contenedor_tabla">
                        <table class="g_tabla">
                            <thead>
                                <tr>
                                    <th>Asunto</th>
                                    <th>Área Origen</th>
                                    <th>Gestor Origen</th>
                                    <th>Tipo / Subtipo</th>
                                    <th>Área Destino</th>
                                    <th>Gestor Destino</th>
                                    <th class="g_celda_centro">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($hijosParaCrear as $index => $h)
                                    <tr wire:key="hijo-prev-{{ $index }}">
                                        <td class="g_negrita">{{ $h['asunto'] }}</td>
                                        <td>
                                            <span class="g_badge light">{{ $h['area_origen_nombre'] }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted small">{{ $h['gestor_origen_nombre'] }}</span>
                                        </td>
                                        <td>
                                            <span class="g_badge info tiny">{{ $h['tipo_solicitud_nombre'] }}</span>
                                        </td>
                                        <td>
                                            <select wire:model.live="hijosParaCrear.{{ $index }}.area_id" class="small_select">
                                                <option value="">Seleccione...</option>
                                                @foreach($areas as $area)
                                                    <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select wire:model="hijosParaCrear.{{ $index }}.gestor_id" class="small_select">
                                                <option value="">Seleccione...</option>
                                                @foreach($this->getGestoresPorArea($h['area_id'], $h['tipo_solicitud_id']) as $gestor)
                                                    <option value="{{ $gestor->id }}">{{ $gestor->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="g_celda_centro">
                                            <button wire:click="quitarDeLista({{ $index }})" class="g_boton danger">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="g_margin_top_20" style="display: flex; justify-content: flex-end;">
                        <button type="button" wire:click="crearHijosMasivos" class="g_boton success big">
                            CREAR HIJOS MASIVOS Y DERIVAR <i class="fa-solid fa-check-double"></i>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>