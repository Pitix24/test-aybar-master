@php
    $fecha = $fechaActual->toDateString();
    $items = collect($eventos)->where('date', $fecha);

    // Horario de 6:00am a 10:00pm
    $horas = [];
    for ($h = 6; $h <= 22; $h++) {
        $horas[] = sprintf('%02d:00', $h);
    }
@endphp

<div class="g_calendario_view_dia">
    <div class="g_calendario_timeline">
        @foreach ($horas as $hora)
            @php
                $eventosHora = $items->filter(
                    fn($ev) =>
                    substr($ev['time'], 0, 2) === substr($hora, 0, 2)
                );
            @endphp

            <div class="g_calendario_timeline_fila">
                <div class="timeline_hora">
                    <span>{{ $hora }}</span>
                </div>

                <div class="timeline_contenido">
                    @foreach ($eventosHora as $ev)
                        <div class="g_calendario_evento_detalle" style="border-left: 5px solid {{ $ev['color'] }}">
                            <div class="evento_info">
                                <span class="evento_tiempo">
                                    <i class="fa-regular fa-clock"></i> {{ $ev['time'] }} — {{ $ev['end_time'] }}
                                </span>
                                <h3 class="evento_titulo">{{ $ev['title'] }}</h3>
                                <div class="evento_metadata">
                                    <span><i class="fa-solid fa-user"></i> {{ $ev['cliente'] }}</span>
                                    <span><i class="fa-solid fa-location-dot"></i> {{ $ev['sede'] ?? '—' }}</span>
                                    <span><i class="fa-solid fa-building-user"></i> {{ $ev['area'] }}</span>
                                </div>
                            </div>

                            <div class="evento_acciones">
                                <a href="{{ route('erp.cita.vista.editar', $ev['id']) }}"
                                    class="g_boton g_boton_soft_primary g_boton_sm">
                                    <i class="fa-solid fa-eye"></i> Atender
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>