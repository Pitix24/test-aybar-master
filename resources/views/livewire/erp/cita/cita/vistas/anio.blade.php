<div class="g_calendario_grid_anio">
    @for ($m = 1; $m <= 12; $m++) 
        @php 
            $fechaMes = \Carbon\Carbon::create($fechaActual->year, $m, 1);
            $totalMes = collect($eventos)->filter(function ($e) use ($m) {
                return \Carbon\Carbon::parse($e['date'])->month == $m;
            })->count();
        @endphp

        <div class="g_calendario_bloque_anio">
            <div class="bloque_cabecera">
                <span class="bloque_mes">{{ $fechaMes->translatedFormat('F') }}</span>
                <button class="g_boton g_boton_soft_primary g_boton_xs" wire:click="irAlMes({{ $m }})">
                    Ver mes
                </button>
            </div>

            <div class="bloque_contenido">
                @if($totalMes > 0)
                    <div class="g_badge success">
                        <i class="fa-solid fa-calendar-check"></i> {{ $totalMes }} {{ $totalMes == 1 ? 'cita' : 'citas' }}
                    </div>
                @else
                    <span class="g_text_muted" style="font-size: 0.8rem;">Sin citas</span>
                @endif
            </div>
        </div>
    @endfor
</div>
