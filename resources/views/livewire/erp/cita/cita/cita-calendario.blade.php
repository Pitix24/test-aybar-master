@section('tituloPagina', 'Calendario de Citas')
@section('anchoPantalla', '100%')

<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <div class="cabecera_control">
            <h2 class="g_negrita">Calendario</h2>
            <div class="control_navegacion">
                <button wire:click="navegar(-1)" class="g_boton light"><i class="fa-solid fa-chevron-left"></i></button>
                <div class="control_periodo">
                    @switch($vista)
                        @case('mes') {{ $fechaActual->translatedFormat('F Y') }} @break
                        @case('semana') <span>Semana {{ $fechaActual->weekOfYear }}</span> <small>{{ $fechaActual->year }}</small> @break
                        @case('dia') {{ $fechaActual->translatedFormat('d F Y') }} @break
                        @case('anio') {{ $fechaActual->year }} @break
                    @endswitch
                </div>
                <button wire:click="navegar(1)" class="g_boton light"><i class="fa-solid fa-chevron-right"></i></button>
                
                <button wire:click="irHoy()" class="g_boton {{ $fechaActual->isToday() && $vista == 'dia' ? 'primary' : 'light' }}" style="margin-left: 10px;">
                    Hoy
                </button>
            </div>
        </div>

        <div class="cabecera_titulo_botones">
            <div class="g_grupo_segmentado">
                <button wire:click="cambiarVista('anio')" class="{{ $vista === 'anio' ? 'activo' : '' }}">Año</button>
                <button wire:click="cambiarVista('mes')" class="{{ $vista === 'mes' ? 'activo' : '' }}">Mes</button>
                <button wire:click="cambiarVista('semana')" class="{{ $vista === 'semana' ? 'activo' : '' }}">Semana</button>
                <button wire:click="cambiarVista('dia')" class="{{ $vista === 'dia' ? 'activo' : '' }}">Día</button>
            </div>
        </div>
    </div>

    <div class="g_panel" style="padding: 0; min-height: 70vh;">
        <div class="g_calendario_contenedor">
            @include("livewire.erp.cita.cita.vistas.{$vista}")
        </div>
    </div>
</div>