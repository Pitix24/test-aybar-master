@section('tituloPagina', 'Calendario de Citas')

<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <div>
            <h2>Calendario de Citas</h2>
            <p style="margin: 0; color: #64748b; text-transform: capitalize;">
                {{ $fechaActual->translatedFormat('F Y') }}
            </p>
        </div>
        <div class="cabecera_titulo_botones">
            <div class="g_grupo_botones">
                <button wire:click="navegar(-1)" class="g_boton g_boton_light"><i
                        class="fa-solid fa-chevron-left"></i></button>
                <button wire:click="irHoy()" class="g_boton g_boton_light">Hoy</button>
                <button wire:click="navegar(1)" class="g_boton g_boton_light"><i
                        class="fa-solid fa-chevron-right"></i></button>
            </div>

            <div class="g_grupo_botones" style="margin-left: 15px;">
                <button wire:click="cambiarVista('mes')"
                    class="g_boton {{ $vista == 'mes' ? 'g_boton_primary' : 'g_boton_light' }}">Mes</button>
                <button wire:click="cambiarVista('semana')"
                    class="g_boton {{ $vista == 'semana' ? 'g_boton_primary' : 'g_boton_light' }}">Semana</button>
                <button wire:click="cambiarVista('dia')"
                    class="g_boton {{ $vista == 'dia' ? 'g_boton_primary' : 'g_boton_light' }}">Día</button>
            </div>

            <a href="{{ route('erp.cita.vista.todo') }}" class="g_boton g_boton_dark" style="margin-left: 15px;">
                Citas <i class="fa-solid fa-list"></i></a>
        </div>
    </div>

    @if($vista == 'mes')
        <div class="g_panel" style="padding: 10px;">
            <div class="g_calendario_grid">
                <!-- Cabecera de días -->
                @php $diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo']; @endphp
                @foreach($diasSemana as $dia)
                    <div class="g_calendario_cabecera_dia">{{ $dia }}</div>
                @endforeach

                <!-- Celdas del mes -->
                @php
                    $startOfCalendar = $fechaActual->copy()->startOfMonth()->startOfWeek(Carbon\Carbon::MONDAY);
                    $endOfCalendar = $fechaActual->copy()->endOfMonth()->endOfWeek(Carbon\Carbon::SUNDAY);
                    $currentDay = $startOfCalendar->copy();
                    $today = Carbon\Carbon::today();
                @endphp

                @while($currentDay <= $endOfCalendar)
                    <div
                        class="g_calendario_celda {{ $currentDay->month != $fechaActual->month ? 'fuera_mes' : '' }} {{ $currentDay->isToday() ? 'hoy' : '' }}">
                        <div class="dia_numero">{{ $currentDay->day }}</div>

                        <div class="eventos_contenedor">
                            @foreach(collect($eventos)->where('date', $currentDay->toDateString()) as $evento)
                                <a href="{{ route('erp.cita.vista.editar', $evento['id']) }}" class="evento_item"
                                    style="border-left: 3px solid {{ $evento['color'] }}">
                                    <span class="hora">{{ $evento['time'] }}</span>
                                    <span class="titulo">{{ $evento['title'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    @php $currentDay->addDay(); @endphp
                @endwhile
            </div>
        </div>
    @else
        <div class="g_panel">
            <div class="g_vacio">
                <p>La vista {{ $vista }} se implementará próximamente.</p>
                <i class="fa-solid fa-person-digging"></i>
            </div>
        </div>
    @endif

    <style>
        .g_calendario_grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background: #e2e8f0;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
        }

        .g_calendario_cabecera_dia {
            background: #f8fafc;
            padding: 12px;
            text-align: center;
            font-weight: 700;
            color: #64748b;
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        .g_calendario_celda {
            background: white;
            min-height: 120px;
            padding: 8px;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .g_calendario_celda.fuera_mes {
            background: #f1f5f9;
        }

        .g_calendario_celda.hoy {
            background: #fffbeb;
        }

        .g_calendario_celda.hoy .dia_numero {
            background: var(--primary);
            color: white;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .dia_numero {
            font-weight: 700;
            color: #475569;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .eventos_contenedor {
            display: flex;
            flex-direction: column;
            gap: 2px;
            flex: 1;
            overflow-y: auto;
        }

        .evento_item {
            background: #f8fafc;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.7rem;
            text-decoration: none;
            color: #1e293b;
            display: flex;
            flex-direction: column;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            transition: transform 0.1s;
        }

        .evento_item:hover {
            transform: scale(1.02);
            background: white;
            z-index: 10;
        }

        .evento_item .hora {
            font-weight: 800;
            color: #64748b;
        }

        .evento_item .titulo {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .g_grupo_botones {
            display: inline-flex;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
        }

        .g_grupo_botones .g_boton {
            border: none;
            border-radius: 0;
            border-right: 1px solid #e2e8f0;
        }

        .g_grupo_botones .g_boton:last-child {
            border-right: none;
        }
    </style>
</div>