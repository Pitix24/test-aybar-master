<div class="g_gap_pagina">

    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            <span>{{ $evento->nombre }}</span>
            Nuevo Bloque de Itinerario
        </h2>

        <div class="cabecera_titulo_botones">
            @can('itinerario.lista')
                <a href="{{ route('erp.entrega-fest.itinerario.todo', $evento->id) }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i>
                </a>
            @endcan

            @can('entrega-fest.ver-staff')
                <a href="{{ route('erp.entrega-fest.vista.staff', $evento->id) }}" class="g_boton info">
                    <i class="fa-solid fa-grip"></i> Panel de Staff
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
                <h4 class="g_panel_titulo"><i class="fa-solid fa-clock"></i> Datos del Bloque</h4>

                <div class="g_margin_bottom_10">
                    <label>Titulo del Bloque <span class="obligatorio"><i
                                class="fa-solid fa-asterisk"></i></span></label>
                    <input type="text" wire:model="titulo" class="@error('titulo') input-error @enderror"
                        placeholder="Ej: Bienvenida y registro de invitados">
                    @error('titulo') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_fila">
                    <div class="g_columna_6 g_margin_bottom_10">
                        <label>Hora de Inicio <span class="obligatorio"><i
                                    class="fa-solid fa-asterisk"></i></span></label>
                        <input type="time" wire:model="hora_inicio" class="@error('hora_inicio') input-error @enderror">
                        @error('hora_inicio') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                    <div class="g_columna_6 g_margin_bottom_10">
                        <label>Hora de Fin</label>
                        <input type="time" wire:model="hora_fin">
                    </div>
                </div>

                <div class="g_margin_bottom_10">
                    <label>Ubicación / Zona</label>
                    <input type="text" wire:model="ubicacion" placeholder="Ej: Puerta principal, Escenario A">
                </div>

                <div class="g_margin_bottom_10">
                    <label>Descripcion del Bloque</label>
                    <textarea wire:model="descripcion" rows="3"
                        placeholder="Actividades o notas del bloque..."></textarea>
                </div>

                <div class="g_margin_bottom_10">
                    <label>Orden de Aparicion</label>
                    <input type="number" wire:model="orden" min="0" style="width:120px;">
                    <p class="leyenda">Define la posicion en el itinerario (menor numero = aparece primero).</p>
                </div>

                <div class="formulario_botones">
                    @can('itinerario.crear')
                        <button type="submit" class="g_boton guardar" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="store"><i class="fa-solid fa-save"></i> Crear
                                Bloque</span>
                            <span wire:loading wire:target="store"><i class="fa-solid fa-spinner fa-spin"></i>
                                Creando...</span>
                        </button>
                    @endcan

                    <button type="button" class="g_boton cancelar" onclick="history.back()">
                        <i class="fa-solid fa-times"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>