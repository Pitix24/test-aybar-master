<div class="g_gap_pagina">
    <x-loading-overlay wire:loading message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Asistencia: <span style="color: var(--color-primary);">{{ $evento->nombre }}</span></h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.vista.todo') }}" class="g_boton dark">
                <i class="fa-solid fa-list"></i> Lista de Eventos
            </a>
        </div>
    </div>

    @include('livewire.erp.entrega-fest.entrega-fest.entrega-fest-navegacion')

    <div class="g_fila">
        <div class="g_columna_4">
            <div class="g_panel">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-qrcode"></i> Escanear Código</h4>
                <div class="formulario">
                    <div class="g_margin_bottom_10">
                        <label>Código QR / Manual</label>
                        <input type="text" wire:model.live="codigo_qr" placeholder="Escanee o escriba el código..."
                            autofocus>
                    </div>

                    @if ($mensaje)
                        <div class="alert alert-{{ $mensajeTipo }}"
                            style="margin-top: 10px; padding: 10px; border-radius: 4px; border: 1px solid;">
                            {{ $mensaje }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="g_columna_8">
            <div class="g_panel">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-clock-rotate-left"></i> Registros Recientes</h4>

                <div class="formulario g_margin_bottom_15">
                    <div class="g_fila">
                        <div class="g_columna_8">
                            <input type="text" wire:model.live.debounce.400ms="buscar"
                                placeholder="Buscar por nombre, dni o código...">
                        </div>
                        <div class="g_columna_4">
                            <select wire:model.live="perPage">
                                <option value="20">20 registros</option>
                                <option value="50">50 registros</option>
                                <option value="100">100 registros</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="g_contenedor_tabla">
                    <table class="g_tabla">
                        <thead>
                            <tr>
                                <th>Cód. Inv</th>
                                <th>Prospecto / DNI</th>
                                <th>Fecha / Hora</th>
                                <th>Registrado por</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $a)
                                <tr wire:key="asistencia-{{ $a->id }}">
                                    <td class="g_negrita">{{ $a->invitado->codigo_invitado }}</td>
                                    <td>
                                        <div class="g_negrita">{{ $a->invitado->prospecto->nombre_completo ?? 'N/A' }}</div>
                                        <div style="font-size: 0.8rem; color: #666;">DNI:
                                            {{ $a->invitado->prospecto->dni ?? 'N/A' }}</div>
                                    </td>
                                    <td>
                                        <span class="g_badge light">{{ $a->fecha_checkin->format('d/m/Y') }}</span>
                                        <span class="g_negrita">{{ $a->fecha_checkin->format('H:i') }}</span>
                                    </td>
                                    <td>{{ $a->user->name ?? 'Sistema' }}</td>
                                </tr>
                            @endforeach
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