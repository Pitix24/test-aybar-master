<div class="g_gap_pagina">
    <x-loading-overlay wire:loading message="Guardando evento..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Nuevo Evento Entrega Fest</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.vista.todo') }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>
            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    <form wire:submit.prevent="store">
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
                        </div>
                    </div>

                    <div x-show="activeTab === 'general'" x-transition class="g_tab_content">
                        <div class="g_fila">
                            <div class="g_margin_bottom_15 g_columna_8">
                                <label>Nombre del Evento <span class="obligatorio">*</span></label>
                                <input type="text" wire:model="nombre" class="@error('nombre') input-error @enderror"
                                    placeholder="Ej: Entrega Fest Verano 2026">
                                @error('nombre') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>
                            <div class="g_margin_bottom_15 g_columna_4">
                                <label>Código único <span class="obligatorio">*</span></label>
                                <input type="text" wire:model="codigo" class="@error('codigo') input-error @enderror"
                                    placeholder="EF-2026-001">
                                @error('codigo') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="g_margin_bottom_15">
                            <label>Descripción del Evento</label>
                            <textarea wire:model="descripcion" rows="4"
                                placeholder="Detalles sobre el evento..."></textarea>
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

                    <div class="formulario_botones">
                        <button type="submit" class="g_boton guardar">
                            Crear Evento <i class="fa-solid fa-floppy-disk"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="g_columna_4">
                <div class="g_panel">
                    <h4 class="g_panel_titulo"><i class="fa-solid fa-sliders"></i> Estado y Configuración</h4>

                    <div class="g_margin_bottom_20">
                        <div class="g_switch_wrapper" style="justify-content: space-between;">
                            <label>Habilitar evento:</label>
                            <label class="g_switch">
                                <input type="checkbox" wire:model="activo">
                                <span class="g_switch_slider"></span>
                            </label>
                        </div>
                    </div>

                    <div class="g_vacio_pequeno" style="padding: 20px;">
                        <i class="fa-solid fa-circle-info"
                            style="font-size: 1.5rem; color: var(--color-primary); margin-bottom: 10px;"></i>
                        <p style="font-size: 0.85rem; color: #666; line-height: 1.4;">
                            Al crear el evento, podrá empezar a cargar prospectos y convertirlos en invitados oficiales
                            para esta fecha.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>