@php
$inicio = $fechaActual->copy()->startOfWeek(Carbon\Carbon::MONDAY);
@endphp

<div class="g_calendario_grid_semana">
    @for ($i = 0; $i < 7; $i++) 
        @php 
            $day = $inicio->copy()->addDays($i);
            $fecha = $day->toDateString();
            $items = collect($eventos)->where('date', $fecha);
        @endphp

        <div class="g_calendario_columna_semana {{ $day->isToday() ? 'hoy' : '' }}" wire:click="irAlDiaDeSemana('{{ $fecha }}')">
            <div class="columna_cabecera">
                <span class="dia_nombre">{{ $day->translatedFormat('D') }}</span>
                <span class="dia_numero">{{ $day->day }}</span>
            </div>

            <div class="columna_contenido">
                @foreach ($items as $ev)
                    <div class="g_calendario_evento_card" style="border-left: 4px solid {{ $ev['color'] }}">
                        <div class="card_hora">{{ $ev['time'] }} - {{ $ev['end_time'] }}</div>
                        <div class="card_titulo">{{ $ev['title'] }}</div>
                        <div class="card_meta">{{ $ev['sede'] ?? 'Sin sede' }}</div>
                    </div>
                @endforeach

                @if($items->isEmpty())
                    <div class="g_text_muted g_text_center" style="margin-top: 20px; font-size: 0.8rem; opacity: 0.5;">
                        <i class="fa-solid fa-calendar-day"></i>
                    </div>
                @endif
            </div>
        </div>
    @endfor
</div>
