@php
$inicioMes = $fechaActual->copy()->startOfMonth();
$primerDiaSemana = $inicioMes->dayOfWeekIso; // 1 (Lun) a 7 (Dom)
$diasEnMes = $inicioMes->daysInMonth;
@endphp

<div class="g_calendario_grid_mes">
    <!-- Cabecera de días -->
    @foreach(['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'] as $diaLabel)
        <div class="g_calendario_cabecera_semana">{{ $diaLabel }}</div>
    @endforeach

    <!-- Espacios vacíos antes del inicio del mes -->
    @for ($i = 1; $i < $primerDiaSemana; $i++) 
        <div class="g_calendario_celda_mes vacio"></div>
    @endfor

    <!-- Días del mes -->
    @for ($dia = 1; $dia <= $diasEnMes; $dia++) 
        @php 
            $fechaObj = $fechaActual->copy()->day($dia);
            $fechaStr = $fechaObj->toDateString();
            $items = collect($eventos)->where('date', $fechaStr);
        @endphp

        <div class="g_calendario_celda_mes {{ $fechaObj->isToday() ? 'hoy' : '' }}" wire:click="irAlDiaDeMes({{ $dia }})">
            <div class="celda_cabecera">
                <span class="dia_numero">{{ $dia }}</span>
            </div>

            <div class="celda_eventos">
                @foreach ($items as $ev)
                    <div class="g_calendario_evento_pildora" style="border-left: 3px solid {{ $ev['color'] }}" title="{{ $ev['title'] }}">
                        <span class="hora">{{ $ev['time'] }}</span>
                        <span class="titulo">{{ $ev['title'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endfor
</div>
