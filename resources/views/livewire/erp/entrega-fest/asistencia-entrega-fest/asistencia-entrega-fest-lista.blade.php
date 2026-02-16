<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina" style="background: var(--color-primary); color: white;">
        <h2 style="color: white;"><i class="fa-solid fa-qrcode"></i> CONTROL DE ACCESO - ENTREGA FEST</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.invitado-entrega-fest.vista.todo') }}" class="g_boton light">
                Ver Invitados <i class="fa-solid fa-users"></i>
            </a>
        </div>
    </div>

    <div class="g_fila">
        <!-- Panel de Escaneo -->
        <div class="g_columna_5">
            <div class="g_panel" style="text-align: center; padding: 40px 20px;">
                <h3 class="g_margin_bottom_20">Escaneo de Invitación</h3>

                <div class="g_margin_bottom_20">
                    <label>Seleccionar Evento Activo</label>
                    <select wire:model.live="entrega_fest_id" style="font-size: 1.1rem; padding: 12px;">
                        <option value="">-- Seleccione Evento --</option>
                        @foreach ($eventos as $e)
                            <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_20">
                    <input type="text" wire:model.live="codigo_qr" id="qrInput"
                        placeholder="Escanea el código QR aquí..."
                        style="height: 60px; font-size: 1.5rem; text-align: center; border: 2px solid var(--color-primary);"
                        autocomplete="off" autofocus>
                    <p class="g_texto_secundario g_margin_top_10">El sistema procesará automáticamente al detectar el
                        código.</p>
                </div>

                @if ($mensaje)
                    <div class="g_alerta {{ $mensajeTipo }}" style="padding: 20px; font-size: 1.1rem; border-radius: 12px;">
                        <i class="fa-solid {{ $mensajeTipo === 'success' ? 'fa-circle-check' : ($mensajeTipo === 'warning' ? 'fa-triangle-exclamation' : 'fa-circle-xmark') }}"
                            style="font-size: 2rem; margin-bottom: 10px;"></i>
                        <p>{{ $mensaje }}</p>
                    </div>
                @endif
            </div>

            <div class="g_panel g_margin_top_20" style="background: #fafafa;">
                <h4><i class="fa-solid fa-keyboard"></i> Instrucciones</h4>
                <ul style="text-align: left; padding-left: 20px; color: #666; font-size: 0.9rem;">
                    <li>Asegúrese de que el cursor esté siempre en el campo de texto.</li>
                    <li>Use un lector de códigos de barras/QR configurado como teclado.</li>
                    <li>Si el QR está dañado, busque al invitado en la lista manual.</li>
                </ul>
            </div>
        </div>

        <!-- Panel de Recientes -->
        <div class="g_columna_7">
            <div class="g_panel">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-clock-rotate-left"></i> Ingresos Recientes</h4>

                <div class="g_contenedor_tabla">
                    <table class="g_tabla">
                        <thead>
                            <tr>
                                <th>Hora</th>
                                <th>Invitado / DNI</th>
                                <th>Método</th>
                                <th class="g_celda_centro">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($asistenciasRecientes as $a)
                                <tr wire:key="asistencia-{{ $a->id }}">
                                    <td class="g_negrita" style="color: var(--color-primary);">
                                        {{ $a->fecha_checkin->format('H:i:s') }}</td>
                                    <td>
                                        <div class="g_negrita">{{ $a->invitado->prospecto->nombre_completo }}</div>
                                        <div class="g_texto_pequeno">DNI: {{ $a->invitado->prospecto->dni }}</div>
                                    </td>
                                    <td>
                                        <span class="g_badge dark">{{ strtoupper($a->metodo) }}</span>
                                    </td>
                                    <td class="g_celda_centro">
                                        <button class="g_accion light" title="Ver Detalles del Invitado">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">
                                        <div class="g_vacio_pequeno">No hay ingresos registrados aún.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="g_margin_top_10">
                    {{ $asistenciasRecientes->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            const qrInput = document.getElementById('qrInput');

            // Mantener el foco en el input para escaneo continuo
            document.addEventListener('click', () => {
                qrInput.focus();
            });

            @this.on('checkinProcesado', () => {
                setTimeout(() => {
                    qrInput.focus();
                }, 100);
            });
        });
    </script>
</div>