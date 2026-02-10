<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="derivar" message="Procesando derivación..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Derivar Ticket</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.ticket.vista.editar', $ticket->id) }}" class="g_boton g_boton_dark">
                <i class="fa-solid fa-arrow-left"></i> Regresar al ticket
            </a>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_8">
            <div class="g_panel" x-data="{ activeTab: 'informacion' }">
                <div class="g_tab_navegacion">
                    <div class="g_tab_botones">
                        <button type="button" @click="activeTab = 'informacion'"
                            :class="activeTab === 'informacion' ? 'g_tab_active' : 'g_tab_inactive'"
                            class="g_tab_boton">
                            <i class="fa-solid fa-share-from-square"></i> Información
                        </button>

                        <button type="button" @click="activeTab = 'derivaciones'"
                            :class="activeTab === 'derivaciones' ? 'g_tab_active' : 'g_tab_inactive'"
                            class="g_tab_boton">
                            <i class="fa-solid fa-route"></i> Derivaciones
                        </button>
                    </div>
                </div>

                <div x-show="activeTab === 'informacion'" x-transition class="g_tab_content formulario">
                    <form wire:submit="derivar">
                        <div class="g_fila">
                            <div class="g_columna_6 g_margin_bottom_10">
                                <label>Área inicial</label>
                                <input type="text" disabled value="{{ $ticket->area->nombre ?? 'Sin asignar' }}">
                            </div>

                            <div class="g_columna_6 g_margin_bottom_10">
                                <label>Gestor inicial</label>
                                <input type="text" disabled value="{{ $ticket->gestor->name ?? 'Sin asignar' }}">
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_columna_6 g_margin_bottom_10">
                                <label for="a_area_id">Área destino <span class="obligatorio"><i
                                            class="fa-solid fa-asterisk"></i></span></label>
                                <select id="a_area_id" wire:model.live="a_area_id" required>
                                    <option value="" selected disabled>Seleccionar área destino</option>
                                    @foreach ($areas as $area)
                                        <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('a_area_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>

                            <div class="g_columna_6 g_margin_bottom_10">
                                <label for="gestor_id">Gestor destino<span class="obligatorio"><i
                                            class="fa-solid fa-asterisk"></i></span></label>
                                <select id="gestor_id" wire:model.live="gestor_id">
                                    <option value="" selected disabled>Sin asignar</option>
                                    @foreach ($gestores as $usuario)
                                        <option value="{{ $usuario->id }}">
                                            {{ $usuario->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('gestor_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_columna_12">
                                <label for="motivo">Motivo<span class="obligatorio"><i
                                            class="fa-solid fa-asterisk"></i></span></label>
                                <textarea id="motivo" wire:model.live="motivo" rows="4"></textarea>
                                @error('motivo') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="formulario_botones">
                            <button type="submit" class="g_boton g_boton_guardar">
                                <i class="fa-solid fa-route"></i> Ejecutar Derivación
                            </button>
                        </div>
                    </form>
                </div>

                <div x-show="activeTab === 'derivaciones'" x-transition class="g_tab_content">
                    <div class="g_contenedor_tabla">
                        <table class="g_tabla">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>De Áre → A Área</th>
                                    <th>Participantes</th>
                                    <th>Motivo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($derivados as $der)
                                    <tr wire:key="der-list-{{ $der->id }}">
                                        <td>
                                            <div class="g_negrita">{{ $der->created_at->format('d/m/Y') }}</div>
                                            <small>{{ $der->created_at->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            <div style="font-size: 0.8rem; color: #64748b;">
                                                {{ $der->deArea->nombre ?? 'N/A' }}
                                            </div>
                                            <i class="fa-solid fa-arrow-right"
                                                style="padding: 0 5px; font-size: 0.7rem;"></i>
                                            <span class="g_badge g_badge_primary">{{ $der->aArea->nombre ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <div style="font-size: 0.75rem;"><strong>Deriva:</strong>
                                                {{ $der->usuarioDeriva->name ?? 'N/A' }}</div>
                                            <div style="font-size: 0.75rem;"><strong>Recibe:</strong>
                                                {{ $der->usuarioRecibe->name ?? 'N/A' }}</div>
                                        </td>
                                        <td style="font-size: 0.85rem;">{{ $der->motivo }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="g_celda_vacia">No hay registros de derivación.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="g_columna_4 g_gap_pagina">
            <div class="g_panel">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-circle-info"></i> Información del Ticket</h4>

                <div class="formulario">
                    <div class="g_margin_bottom_10">
                        <label>Estado Actual</label>
                        <div><span class="g_badge g_badge_light">{{ $ticket->estado->nombre ?? 'N/A' }}</span></div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Empresa</label>
                        <input type="text" disabled value="{{ $ticket->unidadNegocio->nombre ?? 'N/A' }}">
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Proyecto</label>
                        <input type="text" disabled value="{{ $ticket->proyecto->nombre ?? 'N/A' }}">
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Gestor Asignado</label>
                        <input type="text" disabled value="{{ $ticket->gestor->name ?? 'N/A' }}">
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Asunto</label>
                        <textarea disabled rows="2">{{ $ticket->asunto_inicial }}</textarea>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Descripción</label>
                        <textarea disabled rows="4">{{ $ticket->descripcion_inicial }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>