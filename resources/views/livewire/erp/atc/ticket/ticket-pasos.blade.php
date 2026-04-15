<div>
    <x-loading-overlay wire:loading wire:target="togglePaso" message="Actualizando estado..." />

    <div class="g_panel">
        <h4 class="g_panel_titulo">
            <i class="fa-solid fa-list-check"></i> Flujo de Proceso
            @if($ticket->tipoSolicitud)
                <span class="g_badge info">{{ $ticket->tipoSolicitud->nombre }}</span>
            @endif
        </h4>

        @if($pasos->isEmpty())
            <div class="g_vacio">
                <p>No hay un flujo configurado para este tipo de solicitud.</p>
                @can('tipo-solicitud.vista-flujo')
                    <a href="{{ route('erp.tipo-solicitud.vista.flujo', $ticket->tipo_solicitud_id) }}" class="g_boton light">
                        Configurar Flujo <i class="fa-solid fa-gears"></i>
                    </a>
                @endcan
            </div>
        @else
            <div class="g_contenedor_pasos">
                <div class="lista_pasos_flujo">
                    @foreach($pasos as $paso)
                        <div class="paso_flujo_item {{ $paso->completado ? 'paso_completado' : '' }}" wire:key="ticket-paso-{{ $paso->id }}">
                            <div class="paso_flujo_check">
                                <input type="checkbox" id="check-paso-{{ $paso->id }}" 
                                    wire:click="togglePaso({{ $paso->id }})"
                                    {{ $paso->completado ? 'checked' : '' }}>
                            </div>
                            <label for="check-paso-{{ $paso->id }}" class="paso_flujo_info">
                                <div class="paso_flujo_superior">
                                    <span class="paso_flujo_nombre">{{ $paso->flujoPaso->nombre_paso }}</span>
                                    @if($paso->completado)
                                        <span class="g_badge success tiny">
                                            <i class="fa-solid fa-check"></i> Completado
                                        </span>
                                    @endif
                                </div>
                                <div class="paso_flujo_inferior">
                                    @if($paso->flujoPaso->descripcion)
                                        <p class="paso_flujo_desc">{{ $paso->flujoPaso->descripcion }}</p>
                                    @endif
                                    @if($paso->completado)
                                        <span class="paso_flujo_meta">
                                            <i class="fa-solid fa-user"></i> {{ $paso->user->name ?? 'Sistema' }} 
                                            | <i class="fa-solid fa-calendar"></i> {{ $paso->fecha_completado->format('d/m/Y H:i') }}
                                        </span>
                                    @endif
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <style>
        .lista_pasos_flujo {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 10px;
        }
        .paso_flujo_item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            padding: 15px;
            border-radius: 8px;
            background: #fdfdfd;
            border: 1px solid #eee;
            transition: all 0.3s ease;
        }
        .paso_flujo_item:hover {
            border-color: #ddd;
            background: #fafafa;
        }
        .paso_flujo_item.paso_completado {
            background-color: #f0fff4;
            border-color: #c6f6d5;
        }
        .paso_flujo_check input {
            width: 20px;
            height: 20px;
            cursor: pointer;
            margin-top: 2px;
        }
        .paso_flujo_info {
            flex: 1;
            cursor: pointer;
        }
        .paso_flujo_superior {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 5px;
        }
        .paso_flujo_nombre {
            font-weight: 600;
            font-size: 1rem;
            color: #2d3748;
        }
        .paso_flujo_desc {
            font-size: 0.85rem;
            color: #718096;
            margin-bottom: 5px;
        }
        .paso_flujo_meta {
            font-size: 0.75rem;
            color: #a0aec0;
            display: block;
        }
        .tiny {
            font-size: 0.65rem;
            padding: 2px 6px;
        }
        .paso_completado .paso_flujo_nombre {
            text-decoration: line-through;
            color: #38a169;
        }
    </style>
</div>
