<div class="g_gap_pagina">
    <x-loading-overlay wire:loading message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Asistencia: <span style="color: var(--color-primary);">{{ $evento->nombre }}</span></h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.vista.prospectos', $evento->id) }}" class="g_boton success">
                Prospectos <i class="fa-solid fa-users-viewfinder"></i>
            </a>

            <a href="{{ route('erp.entrega-fest.vista.invitados', $evento->id) }}" class="g_boton cancelar">
                Invitados <i class="fa-solid fa-users"></i></a>

            <a href="{{ route('erp.entrega-fest.vista.todo') }}" class="g_boton dark">
                <i class="fa-solid fa-list"></i> Lista de Eventos
            </a>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_4">
            <div class="g_panel">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-qrcode"></i> Escanear Código</h4>
                <div class="formulario">
                    <div class="g_margin_bottom_10">
                        <label>Código QR / Manual</label>
                        <input type="text" 
                            wire:model.live="codigo_qr" 
                            id="scanner_input"
                            placeholder="Escanee o escriba el código..."
                            class="g_negrita"
                            style="font-size: 1.2rem; text-align: center; border: 2px solid var(--color-primary);"
                            autofocus>
                    </div>

                    @if ($mensaje)
                        @php
                            $icon = match($mensajeTipo) {
                                'success' => 'fa-circle-check',
                                'warning' => 'fa-triangle-exclamation',
                                'error' => 'fa-circle-xmark',
                                default => 'fa-info-circle'
                            };
                            $colorAlert = match($mensajeTipo) {
                                'success' => '#d4edda',
                                'warning' => '#fff3cd',
                                'error' => '#f8d7da',
                                default => '#e2e3e5'
                            };
                            $borderColor = match($mensajeTipo) {
                                'success' => '#c3e6cb',
                                'warning' => '#ffeeba',
                                'error' => '#f5c6cb',
                                default => '#d6d8db'
                            };
                            $txtColor = match($mensajeTipo) {
                                'success' => '#155724',
                                'warning' => '#856404',
                                'error' => '#721c24',
                                default => '#383d41'
                            };
                        @endphp
                        <div style="margin-top: 15px; padding: 15px; border-radius: 12px; background-color: {{ $colorAlert }}; border: 1px solid {{ $borderColor }}; color: {{ $txtColor }}; display: flex; align-items: center; gap: 10px;">
                            <i class="fa-solid {{ $icon }} fa-lg"></i>
                            <div class="g_negrita">{{ $mensaje }}</div>
                        </div>
                    @endif

                    <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 10px; font-size: 0.9rem; color: #666;">
                        <p style="margin: 0;"><i class="fa-solid fa-circle-info"></i> El cursor debe estar en el campo de texto para que el lector QR funcione automáticamente.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="g_columna_8">
            <div class="g_panel">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-clock-rotate-left"></i> Registros Recientes</h4>

                <div class="g_tabla_cabecera">
                    <div class="g_tabla_cabecera_botones">
                        <button wire:click="resetFiltros" class="g_boton danger" title="Limpiar">
                            <i class="fa-solid fa-rotate-left"></i>
                        </button>
                    </div>

                    <div class="g_tabla_cabecera_filtro formulario" style="flex: 1;">
                        <input type="text" wire:model.live.debounce.400ms="buscar"
                                placeholder="Buscar en registrados (Nombre, DNI o Código)...">
                    </div>
                    
                    <div class="g_tabla_cabecera_filtro formulario">
                        <select wire:model.live="perPage">
                            <option value="20">20</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                </div>

                <div class="g_contenedor_tabla">
                    <table class="g_tabla">
                        <thead>
                            <tr>
                                <th>Cód.</th>
                                <th>Invitado / DNI</th>
                                <th>Proyecto</th>
                                <th class="g_celda_centro">Fecha / Hora</th>
                                <th>Responsable</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($items as $a)
                                <tr wire:key="asistencia-{{ $a->id }}">
                                    <td class="g_negrita" style="color: var(--color-primary);">{{ $a->invitado->codigo_invitado }}</td>
                                    <td>
                                        <div style="margin-bottom:3px;">
                                            @if ($a->invitado->prospecto_entrega_fest_id)
                                                <span class="g_badge success" style="font-size:0.7rem;">TITULAR</span>
                                            @else
                                                <span class="g_badge info" style="font-size:0.7rem;">COPROP.</span>
                                            @endif
                                        </div>
                                        <div class="g_negrita">{{ $a->invitado->nombre_completo ?? 'N/A' }}</div>
                                        <div style="font-size: 0.8rem; color: #666;">
                                            DNI: {{ $a->invitado->prospecto?->dni
                                                ?? $a->invitado->copropietario?->dni
                                                ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.85rem;">
                                            {{ $a->invitado->prospecto?->proyecto?->nombre
                                                ?? $a->invitado->copropietario?->prospecto?->proyecto?->nombre
                                                ?? 'N/A' }}
                                        </div>
                                        <div style="font-size: 0.75rem; color: #777;">
                                            Mz: {{ $a->invitado->manzana ?? '—' }}
                                            / Lt: {{ $a->invitado->lote ?? '—' }}
                                        </div>
                                    </td>
                                    <td class="g_celda_centro">
                                        <div class="g_badge light" style="font-size: 0.75rem;">{{ $a->created_at->format('d/m/Y') }}</div>
                                        <div class="g_negrita" style="margin-top: 4px;">{{ $a->created_at->format('H:i:s') }}</div>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.85rem;">{{ $a->user->name ?? 'Sistema' }}</div>
                                        <div style="font-size: 0.75rem; color: #999;">Método: {{ strtoupper($a->metodo) }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="g_celda_centro" style="padding: 40px; color: #999;">
                                        No hay registros de ingreso aún.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($items->hasPages())
                    <div class="g_paginacion">
                        {{ $items->links('vendor.pagination.default-livewire') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('checkinProcesado', () => {
            // Optional: play a sound or focus input
        });
    });
</script>