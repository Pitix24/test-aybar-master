<div class="g_gap_pagina">
    <x-loading-overlay wire:loading message="Procesando..." />

    {{-- Librería para escaneo de QR con cámara --}}
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Asistencia: <span style="color: var(--color-primary);">{{ $evento->nombre }}</span></h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.vista.panel', $evento->id) }}" class="g_boton info">
                <i class="fa-solid fa-grip"></i> Panel de Gestión
            </a>

            <a href="{{ route('erp.entrega-fest.invitado.todo', $evento->id) }}" class="g_boton success">
                Invitados <i class="fa-solid fa-user-group"></i>
            </a>

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_4">
            <div class="g_panel">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-qrcode"></i> Escanear Código</h4>
                <div class="formulario">

                    <div id="qr-reader-container" style="display: none; margin-bottom: 20px;">
                        <div id="reader"
                            style="width: 100%; border-radius: 10px; overflow: hidden; border: 1px solid #ccc;"></div>
                        <button type="button" class="g_boton danger small w-full g_margin_top_10" id="btn-stop-scan">
                            <i class="fa-solid fa-stop"></i> Detener Cámara
                        </button>
                    </div>

                    <button type="button" class="g_boton warning w-full g_margin_bottom_20" id="btn-start-scan">
                        <i class="fa-solid fa-camera"></i> Usar Cámara / Celular
                    </button>

                    <div class="g_margin_bottom_10">
                        <label>Código QR / DNI</label>
                        <input type="text" wire:model.live="codigo_qr" id="scanner_input"
                            placeholder="Escanee o escriba el código o DNI..." class="g_negrita"
                            style="font-size: 1.2rem; text-align: center; border: 2px solid var(--color-primary);"
                            autofocus>
                    </div>

                    @if ($mensaje)
                        @php
                            $icon = match ($mensajeTipo) {
                                'success' => 'fa-circle-check',
                                'warning' => 'fa-triangle-exclamation',
                                'error' => 'fa-circle-xmark',
                                default => 'fa-info-circle'
                            };
                            $colorAlert = match ($mensajeTipo) {
                                'success' => '#d4edda',
                                'warning' => '#fff3cd',
                                'error' => '#f8d7da',
                                default => '#e2e3e5'
                            };
                            $borderColor = match ($mensajeTipo) {
                                'success' => '#c3e6cb',
                                'warning' => '#ffeeba',
                                'error' => '#f5c6cb',
                                default => '#d6d8db'
                            };
                            $txtColor = match ($mensajeTipo) {
                                'success' => '#155724',
                                'warning' => '#856404',
                                'error' => '#721c24',
                                default => '#383d41'
                            };
                        @endphp
                        <div id="alerta-asistencia"
                            style="margin-top: 15px; padding: 15px; border-radius: 12px; background-color: {{ $colorAlert }}; border: 1px solid {{ $borderColor }}; color: {{ $txtColor }}; display: flex; align-items: center; gap: 10px;">
                            <i class="fa-solid {{ $icon }} fa-lg"></i>
                            <div class="g_negrita">{{ $mensaje }}</div>
                        </div>
                    @endif

                    <div
                        style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 10px; font-size: 0.9rem; color: #666;">
                        <p style="margin: 0;"><i class="fa-solid fa-circle-info"></i> El cursor debe estar en el campo
                            de texto para que el lector QR físico funcione automáticamente.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="g_columna_8">
            <div class="g_panel">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-clock-rotate-left"></i> Registros Recientes</h4>

                <div class="g_tabla_cabecera">
                    <div class="g_tabla_cabecera_botones">
                        <button wire:click="exportExcelTodo" class="g_boton excel" wire:loading.attr="disabled"
                            wire:target="exportExcelTodo">
                            <span wire:loading.remove wire:target="exportExcelTodo">Excel Todo <i
                                    class="fa-solid fa-file-export"></i></span>
                            <span wire:loading wire:target="exportExcelTodo">Generando... <i
                                    class="fa-solid fa-spinner fa-spin"></i></span>
                        </button>

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
                                <th>Segunda Asistencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($items as $a)
                                                    <tr wire:key="asistencia-{{ $a->id }}">
                                                        <td class="g_negrita" style="color: var(--color-primary);">
                                                            {{ $a->invitado->codigo_invitado }}
                                                        </td>
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
                                                            @if($a->invitado->acompanantes->count() > 0)
                                                                <div style="margin-top: 6px; padding-top: 6px; border-top: 1px dashed #ddd;">
                                                                    <div
                                                                        style="font-size: 0.75rem; color: #666; font-weight: bold; margin-bottom: 2px;">
                                                                        <i class="fa-solid fa-users" style="color:var(--color-primary)"></i>
                                                                        Acompañantes:
                                                                    </div>
                                                                    @foreach($a->invitado->acompanantes as $ac)
                                                                        <div style="font-size: 0.75rem; color: #555;">
                                                                            &bull; {{ Str::limit($ac->nombres, 20) }}
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif
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
                                                            <div class="g_badge light" style="font-size: 0.75rem;">
                                                                {{ $a->created_at->format('d/m/Y') }}
                                                            </div>
                                                            <div class="g_negrita" style="margin-top: 4px;">
                                                                {{ $a->created_at->format('H:i:s') }}
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div style="font-size: 0.85rem;">{{ $a->user->name ?? 'Sistema' }}</div>
                                                            <div style="font-size: 0.75rem; color: #999;">Método: {{ strtoupper($a->metodo) }}
                                                            </div>
                                                        </td>
                                                        <td class="g_celda_centro">
                                                            <button wire:click="toggleSegundaAsistencia({{ $a->id }})"
                                                                class="g_boton {{ $a->segunda_asistencia ? 'success' : 'danger' }}">
                                                                {{ $a->segunda_asistencia ? 'SI' : 'NO' }}
                                                            </button>
                                                        </td>
                                                    </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="g_celda_centro" style="padding: 40px; color: #999;">
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
        let html5QrCode = null;
        const scanBtn = document.getElementById('btn-start-scan');
        const stopBtn = document.getElementById('btn-stop-scan');
        const readerContainer = document.getElementById('qr-reader-container');
        const scannerInput = document.getElementById('scanner_input');

        const onScanSuccess = (decodedText, decodedResult) => {
            // Ponemos el valor en el input de Livewire
            @this.set('codigo_qr', decodedText);

            // Vibración suave si el móvil lo permite
            if (navigator.vibrate) {
                navigator.vibrate(100);
            }

            // Opcional: Detener el escáner tras el éxito para ahorrar batería o dejarlo activo
            // stopScanner(); 
        };

        const startScanner = async () => {
            scanBtn.style.display = 'none';
            readerContainer.style.display = 'block';

            html5QrCode = new Html5Qrcode("reader");
            const config = { fps: 10, qrbox: { width: 250, height: 250 } };

            try {
                // Preferimos la cámara trasera ('environment')
                await html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess);
            } catch (err) {
                console.error("Error al iniciar el escáner: ", err);
                alert("No se pudo acceder a la cámara. Verifique los permisos.");
                stopScanner();
            }
        };

        const stopScanner = () => {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    html5QrCode.clear();
                    readerContainer.style.display = 'none';
                    scanBtn.style.display = 'block';
                }).catch(err => {
                    console.error("Error al detener: ", err);
                    readerContainer.style.display = 'none';
                    scanBtn.style.display = 'block';
                });
            }
        };

        scanBtn.addEventListener('click', startScanner);
        stopBtn.addEventListener('click', stopScanner);

        Livewire.on('checkinProcesado', () => {
            // El input se limpia solo en el componente, pero aseguramos foco si no está en modo cámara
            if (readerContainer.style.display === 'none') {
                scannerInput.focus();
            }

            // Auto-cerrar el mensaje después de 5 segundos para limpiar la vista
            setTimeout(() => {
                const alerta = document.getElementById('alerta-asistencia');
                if (alerta) alerta.style.opacity = '0.5';
            }, 5000);
        });
    });
</script>