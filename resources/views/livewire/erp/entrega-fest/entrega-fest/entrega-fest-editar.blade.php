<div class="g_gap_pagina">
    <x-loading-overlay wire:loading message="Actualizando evento..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Editar Evento: {{ $nombre }}</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.vista.todo') }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>
            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    <form wire:submit.prevent="update">
        <div class="g_fila">
            <div class="g_columna_8">
                <div class="g_panel" x-data="{ activeTab: 'general' }">
                    <div class="g_tab_navegacion">
                        <div class="g_tab_botones">
                            <button type="button" @click="activeTab = 'general'"
                                :class="activeTab === 'general' ? 'g_tab_active' : 'g_tab_inactive'"
                                class="g_tab_boton">
                                <i class="fa-solid fa-file-lines"></i> Información General
                            </button>
                            <button type="button" @click="activeTab = 'stats'"
                                :class="activeTab === 'stats' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                                <i class="fa-solid fa-chart-pie"></i> Resumen de Datos
                            </button>
                        </div>
                    </div>

                    <div x-show="activeTab === 'general'" x-transition class="g_tab_content">
                        <div class="g_fila">
                            <div class="g_margin_bottom_15 g_columna_8">
                                <label>Nombre del Evento <span class="obligatorio">*</span></label>
                                <input type="text" wire:model="nombre" class="@error('nombre') input-error @enderror">
                                @error('nombre') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>
                            <div class="g_margin_bottom_15 g_columna_4">
                                <label>Código único <span class="obligatorio">*</span></label>
                                <input type="text" wire:model="codigo" class="@error('codigo') input-error @enderror">
                                @error('codigo') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="g_margin_bottom_15">
                            <label>Descripción del Evento</label>
                            <textarea wire:model="descripcion" rows="4"></textarea>
                            @error('descripcion') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_15 g_columna_6">
                                <label>Unidad de Negocio <span class="obligatorio">*</span></label>
                                <select wire:model.live="unidad_negocio_id"
                                    class="@error('unidad_negocio_id') select-error @enderror">
                                    <option value="">Seleccione...</option>
                                    @foreach ($unidades as $u)
                                        <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('unidad_negocio_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>
                            <div class="g_margin_bottom_15 g_columna_6">
                                <label>Proyecto</label>
                                <select wire:model="proyecto_id" {{ !$unidad_negocio_id ? 'disabled' : '' }}>
                                    <option value="">Seleccione...</option>
                                    @foreach ($proyectos as $p)
                                        <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('proyecto_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_15 g_columna_8">
                                <label>Cliente Responsable <span class="obligatorio">*</span></label>
                                <select wire:model="cliente_id" class="@error('cliente_id') select-error @enderror">
                                    <option value="">Seleccione...</option>
                                    @foreach ($clientes as $c)
                                        <option value="{{ $c->id }}">{{ $c->nombre_completo ?? $c->dni }}</option>
                                    @endforeach
                                </select>
                                @error('cliente_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>
                            <div class="g_margin_bottom_15 g_columna_4">
                                <label>Fecha de Entrega <span class="obligatorio">*</span></label>
                                <input type="date" wire:model="fecha_entrega"
                                    class="@error('fecha_entrega') input-error @enderror">
                                @error('fecha_entrega') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div x-show="activeTab === 'stats'" x-transition class="g_tab_content">
                        <div class="g_fila">
                            <div class="g_columna_4">
                                <div class="g_panel"
                                    style="background: #f0f7ff; border-left: 4px solid var(--color-primary);">
                                    <h4 style="margin: 0; font-size: 0.9rem; color: #555;">Prospectos</h4>
                                    <p
                                        style="font-size: 1.8rem; margin: 10px 0; font-weight: bold; color: var(--color-primary);">
                                        {{ $evento->prospectos()->count() }}
                                    </p>
                                </div>
                            </div>
                            <div class="g_columna_4">
                                <div class="g_panel"
                                    style="background: #f0fff4; border-left: 4px solid var(--color-success);">
                                    <h4 style="margin: 0; font-size: 0.9rem; color: #555;">Invitados</h4>
                                    <p
                                        style="font-size: 1.8rem; margin: 10px 0; font-weight: bold; color: var(--color-success);">
                                        {{ $evento->invitados()->count() }}
                                    </p>
                                </div>
                            </div>
                            <div class="g_columna_4">
                                <div class="g_panel"
                                    style="background: #fff9f0; border-left: 4px solid var(--color-warning);">
                                    <h4 style="margin: 0; font-size: 0.9rem; color: #555;">Confirmados</h4>
                                    <p
                                        style="font-size: 1.8rem; margin: 10px 0; font-weight: bold; color: var(--color-warning);">
                                        {{ $evento->invitados()->where('confirmado', true)->count() }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="g_margin_top_20">
                            <p class="g_texto_secundario"><i class="fa-solid fa-info-circle"></i> Estas métricas se
                                actualizan a medida que se gestionan los prospectos e invitados.</p>
                        </div>
                    </div>

                    <div class="formulario_botones">
                        <button type="submit" class="g_boton guardar">
                            Actualizar Evento <i class="fa-solid fa-floppy-disk"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="g_columna_4">
                <div class="g_panel g_margin_bottom_20">
                    <h4 class="g_panel_titulo"><i class="fa-solid fa-sliders"></i> Estado y Configuración</h4>

                    <div class="g_margin_bottom_20">
                        <div class="g_switch_wrapper" style="justify-content: space-between;">
                            <label>Evento activo:</label>
                            <label class="g_switch">
                                <input type="checkbox" wire:model="activo">
                                <span class="g_switch_slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="g_panel">
                    <h4 class="g_panel_titulo"><i class="fa-solid fa-clock"></i> Auditoría</h4>
                    <div style="font-size: 0.85rem; color: #666;">
                        <p class="g_margin_bottom_5"><strong>Registrado por:</strong> {{ $evento->user->name }}</p>
                        <p class="g_margin_bottom_5"><strong>Fecha Registro:</strong>
                            {{ $evento->created_at->format('d/m/Y H:i') }}</p>
                        <p><strong>Última Modif:</strong> {{ $evento->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>