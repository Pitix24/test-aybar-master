<div class="g_gap_pagina">

    {{-- CABECERA --}}
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            Logística de Proveedores
            <span>{{ $evento->nombre }}</span>
        </h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.vista.staff', $evento->id) }}" class="g_boton info">
                <i class="fa-solid fa-grip"></i> Panel de Staff
            </a>

            <a href="{{ route('erp.entrega-fest.proveedor.crear', $evento->id) }}" class="g_boton primary">
                Crear <i class="fa-solid fa-square-plus"></i>
            </a>

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    {{-- CARDS DE PROVEEDORES --}}
    <style>
        .g_grid_proveedores {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            width: 100%;
        }

        @media (max-width: 1600px) { .g_grid_proveedores { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 1200px) { .g_grid_proveedores { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 768px) { .g_grid_proveedores { grid-template-columns: 1fr; } }
        
        .card_proveedor {
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }
        .card_proveedor:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }
    </style>

    <div class="g_grid_proveedores">
        @forelse($proveedores as $prov)
            @php
                $borderColor = match($prov->estado) {
                    'CONFIRMADO' => 'var(--color-info)',
                    'EN_SITIO' => 'var(--color-warning)',
                    'COMPLETADO' => 'var(--color-success)',
                    default => 'var(--color-light-border)'
                };
                $badgeClass = match($prov->estado) {
                    'CONFIRMADO' => 'info',
                    'EN_SITIO' => 'warning',
                    'COMPLETADO' => 'success',
                    default => 'light'
                };
            @endphp
            <div class="g_panel g_gap_pagina card_proveedor" style="gap:15px; border-left: 5px solid {{ $borderColor }};">

                <div>
                    {{-- HEADER PROVEEDOR --}}
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom: 12px;">
                        <div style="flex:1;">
                            <span class="g_badge {{ $badgeClass }} g_mayuscula"
                                style="font-size:10px; margin-bottom:6px; display:inline-flex;">{{ $prov->servicio_tipo }}</span>
                            <div style="display:flex; align-items:center; gap:8px;">
                                <h4 class="g_panel_titulo" style="margin:0; font-size: 1.1rem;">{{ $prov->nombre_comercial }}</h4>
                                @can('entrega-fest.staff')
                                    <a href="{{ route('erp.entrega-fest.proveedor.editar', [$evento->id, $prov->id]) }}"
                                        class="g_accion editar" title="Editar Proveedor"
                                        style="display:inline-flex; width:24px; height:24px; font-size:10px;">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                @endcan
                            </div>
                            <p class="g_inferior" style="margin:4px 0 0 0; opacity: 0.8;">
                                <i class="fa-solid fa-user"></i> {{ $prov->contacto_nombre }}
                            </p>
                        </div>
                    </div>

                    <div class="g_margin_bottom_15">
                        <select wire:change="actualizarEstado({{ $prov->id }}, $event.target.value)"
                            class="g_boton light g_mayuscula g_boton_largo" style="font-size:11px; font-weight:700; cursor:pointer; justify-content: space-between;">
                            <option value="CONFIRMADO" {{ $prov->estado === 'CONFIRMADO' ? 'selected' : '' }}>🟢 Confirmado</option>
                            <option value="EN_SITIO" {{ $prov->estado === 'EN_SITIO' ? 'selected' : '' }}>🟡 En Sitio</option>
                            <option value="COMPLETADO" {{ $prov->estado === 'COMPLETADO' ? 'selected' : '' }}>🔵 Completado</option>
                        </select>
                    </div>

                    {{-- HORARIOS --}}
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; background: rgba(0,0,0,0.02); padding: 10px; border-radius: 8px; margin-bottom: 15px;">
                        <div>
                            <p class="g_inferior g_mayuscula" style="margin:0; font-size:9px; opacity: 0.6;">Llegada</p>
                            <p class="g_negrita" style="margin:0; font-size: 13px;">{{ $prov->h_llegada ? \Carbon\Carbon::parse($prov->h_llegada)->format('H:i') : '--:--' }}</p>
                        </div>
                        <div>
                            <p class="g_inferior g_mayuscula" style="margin:0; font-size:9px; opacity: 0.6;">Montaje</p>
                            <p class="g_negrita" style="margin:0; font-size: 13px; color:var(--color-vivo);">{{ $prov->h_montaje ? \Carbon\Carbon::parse($prov->h_montaje)->format('H:i') : '--:--' }}</p>
                        </div>
                        <div>
                            <p class="g_inferior g_mayuscula" style="margin:0; font-size:9px; opacity: 0.6;">Show / Uso</p>
                            <p class="g_negrita" style="margin:0; font-size: 13px;">{{ $prov->h_show ? \Carbon\Carbon::parse($prov->h_show)->format('H:i') : '--:--' }}</p>
                        </div>
                        <div>
                            <p class="g_inferior g_mayuscula" style="margin:0; font-size:9px; opacity: 0.6;">Desmontaje</p>
                            <p class="g_negrita" style="margin:0; font-size: 13px;">{{ $prov->h_desmontaje ? \Carbon\Carbon::parse($prov->h_desmontaje)->format('H:i') : '--:--' }}</p>
                        </div>
                    </div>

                    {{-- REQUERIMIENTOS --}}
                    @if($prov->requerimientos->count() > 0)
                        <div style="border-top: 1px solid var(--color-light-border); padding-top:12px; margin-bottom: 15px;">
                            <p class="g_negrita g_mayuscula" style="margin:0 0 10px 0; font-size:10px; color: var(--color-claro-texto);">Requerimientos Técnicos</p>
                            <div class="g_gap_pagina" style="gap:8px;">
                                @foreach($prov->requerimientos as $req)
                                    <div style="display:flex; align-items:flex-start; gap:10px; background: white; padding: 6px 10px; border-radius: 6px; border: 1px solid var(--color-light-border);">
                                        <div wire:click="toggleRequerimiento({{ $req->id }})"
                                            style="display:flex; align-items:center; cursor:pointer;"
                                            title="Clic para cambiar estado">
                                            <i class="fa-solid {{ $req->esta_cubierto ? 'fa-circle-check' : 'fa-circle' }}"
                                                style="color: {{ $req->esta_cubierto ? 'var(--color-success)' : '#cbd5e1' }}; font-size:16px;"></i>
                                        </div>

                                        <div style="flex:1;">
                                            <span class="g_inferior {{ $req->esta_cubierto ? 'g_tachado' : '' }}"
                                                style="margin:0; font-size: 12px; font-weight: 500;">{{ $req->requerimiento }}</span>

                                            @if($req->esta_cubierto)
                                                <div style="display:flex; align-items:center; gap:8px; margin-top:6px;">
                                                    @if($req->media->count() > 0)
                                                        <a href="{{ $req->getFirstMediaUrl('evidencias') }}" target="_blank" class="g_foto_preview">
                                                            <img src="{{ $req->getFirstMediaUrl('evidencias') }}" 
                                                                style="width:30px; height:30px; object-fit:cover; border-radius:4px;">
                                                        </a>
                                                    @endif
                                                    <div style="display:flex; flex-direction:column;">
                                                        <span class="g_inferior" style="font-size:9px; opacity:0.7;">
                                                            {{ $req->user->name ?? 'Admin' }}
                                                        </span>
                                                        @if($req->completado_at)
                                                            <span class="g_inferior" style="font-size:8px; opacity:0.5;">
                                                                {{ $req->completado_at->format('H:i') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        @if(!$req->esta_cubierto)
                                            <label for="foto-req-{{ $req->id }}" style="cursor:pointer; margin:0;" title="Subir Evidencia">
                                                <i class="fa-solid fa-camera" style="color:var(--color-info); opacity:0.5; font-size:14px;"></i>
                                                <input type="file" id="foto-req-{{ $req->id }}" wire:model="evidencias.{{ $req->id }}" accept="image/*" style="display:none;">
                                            </label>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- LLAMAR --}}
                @if($prov->contacto_telefono)
                    <a href="tel:{{ $prov->contacto_telefono }}" class="g_boton info g_boton_largo"
                        style="border-radius:8px; font-size:13px; justify-content: center; height: 40px;">
                        <i class="fa-solid fa-phone"></i> Llamar: {{ $prov->contacto_telefono }}
                    </a>
                @endif
            </div>
        @empty
            <div class="g_alerta info" style="grid-column: 1/-1;">
                <i class="fa-solid fa-circle-info"></i>
                No hay proveedores registrados para este evento.
            </div>
        @endforelse
    </div>

</div>