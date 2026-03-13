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
    <div class="g_panel_dashboard_grid">
        @forelse($proveedores as $prov)
            <div class="g_panel g_gap_pagina" style="gap:10px; border-left: 4px solid var(--color-success);">

                {{-- HEADER PROVEEDOR --}}
                <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:8px;">
                    <div style="flex:1;">
                        <span class="g_badge success g_mayuscula"
                            style="font-size:11px; margin-bottom:4px; display:inline-flex;">{{ $prov->servicio_tipo }}</span>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <h4 class="g_panel_titulo" style="margin:4px 0 0 0;">{{ $prov->nombre_comercial }}</h4>
                            @can('entrega-fest.staff')
                                <a href="{{ route('erp.entrega-fest.proveedor.editar', [$evento->id, $prov->id]) }}"
                                    class="g_accion editar" title="Editar Proveedor"
                                    style="display:inline-flex; width:26px; height:26px; font-size:10px;">
                                    <i class="fa-solid fa-pencil"></i>
                                </a>
                            @endcan
                        </div>
                        <p class="g_inferior" style="margin:2px 0 0 0;">
                            <i class="fa-solid fa-user"></i> {{ $prov->contacto_nombre }}
                        </p>
                    </div>
                    <select wire:change="actualizarEstado({{ $prov->id }}, $event.target.value)"
                        class="g_boton light g_mayuscula" style="font-size:11px; font-weight:700; cursor:pointer;">
                        <option value="CONFIRMADO" {{ $prov->estado === 'CONFIRMADO' ? 'selected' : '' }}>Confirmado</option>
                        <option value="EN_SITIO" {{ $prov->estado === 'EN_SITIO' ? 'selected' : '' }}>En Sitio</option>
                        <option value="COMPLETADO" {{ $prov->estado === 'COMPLETADO' ? 'selected' : '' }}>Completado</option>
                    </select>
                </div>

                {{-- HORARIOS --}}
                <div class="g_fila" style="margin:0;">
                    <div class="g_columna_3">
                        <p class="g_inferior g_mayuscula" style="margin:0; font-size:10px;">Llegada</p>
                        <p class="g_negrita" style="margin:0;">{{ $prov->h_llegada ?: '--:--' }}</p>
                    </div>
                    <div class="g_columna_3">
                        <p class="g_inferior g_mayuscula" style="margin:0; font-size:10px;">Montaje</p>
                        <p class="g_negrita" style="margin:0; color:var(--color-vivo);">{{ $prov->h_montaje ?: '--:--' }}
                        </p>
                    </div>
                    <div class="g_columna_3">
                        <p class="g_inferior g_mayuscula" style="margin:0; font-size:10px;">Show / Uso</p>
                        <p class="g_negrita" style="margin:0;">{{ $prov->h_show ?: '--:--' }}</p>
                    </div>
                    <div class="g_columna_3">
                        <p class="g_inferior g_mayuscula" style="margin:0; font-size:10px;">Desmontaje</p>
                        <p class="g_negrita" style="margin:0;">{{ $prov->h_desmontaje ?: '--:--' }}</p>
                    </div>
                </div>

                {{-- REQUERIMIENTOS --}}
                @if($prov->requerimientos->count() > 0)
                    <div style="border-top: 1px solid var(--borde-card-color, #e5e7eb); padding-top:10px;">
                        <p class="g_inferior g_mayuscula" style="margin:0 0 6px 0; font-size:10px;">Requerimientos Técnicos</p>
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:6px;">
                            @foreach($prov->requerimientos as $req)
                                <div style="display:flex; align-items:flex-start; gap:8px; margin-bottom:8px;">
                                    <div wire:click="toggleRequerimiento({{ $req->id }})"
                                        style="display:flex; align-items:center; gap:8px; cursor:pointer;"
                                        title="Clic para cambiar estado">
                                        <i class="fa-solid {{ $req->esta_cubierto ? 'fa-circle-check' : 'fa-circle' }}"
                                            style="color: {{ $req->esta_cubierto ? 'var(--color-success)' : 'var(--color-claro-texto)' }}; font-size:13px;"></i>
                                    </div>

                                    <div style="flex:1;">
                                        <span class="g_inferior {{ $req->esta_cubierto ? 'g_tachado' : '' }}"
                                            style="margin:0; display:block;">{{ $req->requerimiento }}</span>

                                        @if($req->esta_cubierto)
                                            <div style="display:flex; align-items:center; gap:10px; margin-top:4px;">
                                                @if($req->media->count() > 0)
                                                    <a href="{{ $req->getFirstMediaUrl('evidencias') }}" target="_blank" class="g_foto_preview">
                                                        <img src="{{ $req->getFirstMediaUrl('evidencias') }}" 
                                                            style="width:35px; height:35px; object-fit:cover; border-radius:6px; border:1px solid #ddd;">
                                                    </a>
                                                @endif
                                                <div style="display:flex; flex-direction:column;">
                                                    <span class="g_inferior" style="font-size:9px; opacity:0.8; line-height:1;">
                                                        <i class="fa-solid fa-user" style="font-size:8px;"></i> {{ $req->user->name ?? 'N/A' }}
                                                    </span>
                                                    @if($req->completado_at)
                                                        <span class="g_inferior" style="font-size:9px; opacity:0.6; line-height:1; margin-top:2px;">
                                                            {{ $req->completado_at->format('H:i') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    @if(!$req->esta_cubierto)
                                        <label for="foto-req-{{ $req->id }}" style="cursor:pointer; margin:0;" title="Subir Evidencia">
                                            <i class="fa-solid fa-camera" style="color:var(--color-info); opacity:0.6; font-size:14px;"></i>
                                            <input type="file" id="foto-req-{{ $req->id }}" wire:model="evidencias.{{ $req->id }}" accept="image/*" style="display:none;">
                                        </label>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- LLAMAR --}}
                @if($prov->contacto_telefono)
                    <a href="tel:{{ $prov->contacto_telefono }}" class="g_boton info g_boton_largo"
                        style="border-radius:6px; font-size:13px;">
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