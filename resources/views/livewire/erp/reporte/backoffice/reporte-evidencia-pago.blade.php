<div class="g_gap_pagina">
    <!-- Fila 1: KPIs Técnicos -->
    <div class="g_fila">
        <div class="g_panel_dashboard_grid"
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; width: 100%;">
            <div class="g_panel" title="Total de archivos subidos al sistema">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Total Archivos</h2>
                        <p class="g_negrita">{{ number_format($totalArchivos) }}</p>
                    </div>
                    <i class="fa-solid fa-file-invoice" style="color: var(--color-primario);"></i>
                </div>
            </div>

            <div class="g_panel" title="Archivos procesados automáticamente vía SLIN">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Cierres SLIN</h2>
                        <p class="g_negrita">{{ number_format($cierreSlin) }}</p>
                    </div>
                    <i class="fa-solid fa-robot" style="color: #10B981;"></i>
                </div>
            </div>

            <div class="g_panel" title="Archivos cerrados manualmente por un gestor">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Cierres Manuales</h2>
                        <p class="g_negrita">{{ number_format($cierreManual) }}</p>
                    </div>
                    <i class="fa-solid fa-hand-holding-dollar" style="color: #F59E0B;"></i>
                </div>
            </div>

            <div class="g_panel" title="Suma total de montos detectados en vouchers">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Monto Total</h2>
                        <p class="g_negrita">S/ {{ number_format($montoTotalProcesado, 2) }}</p>
                    </div>
                    <i class="fa-solid fa-coins" style="color: #8B5CF6;"></i>
                </div>
            </div>

            <div class="g_panel" title="Grado de automatización (SLIN vs Manual)">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Tasa Auto.</h2>
                        <p class="g_negrita">{{ $tasaAutomatizacion }}%</p>
                    </div>
                    <i class="fa-solid fa-microchip" style="color: #06B6D4;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 2: Análisis de Cierre y Montos -->
    <div class="g_fila">
        <div class="g_columna_8">
            <div class="g_panel" style="height: 100%;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h2 style="font-size: 1.1rem; margin: 0;"><i class="fa-solid fa-chart-line"></i> Evolución de
                        Recaudación (Día/Mes)</h2>
                    <input type="month" wire:model.live="mesSeleccionado" class="g_input"
                        style="padding: 0.2rem 0.5rem; width: 150px; font-size: 0.85rem;">
                </div>
                <div style="height: 300px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartMontos"></canvas>
                </div>
            </div>
        </div>
        <div class="g_columna_4">
            <div class="g_panel" style="height: 100%;">
                <h2 style="margin-bottom: 1.5rem; font-size: 1.1rem;"><i class="fa-solid fa-gears"></i> Método de Cierre
                    (Mes)</h2>
                <div style="height: 300px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartMetodo"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 3: Distribución Técnica -->
    <div class="g_fila">
        <div class="g_columna_4">
            <div class="g_panel">
                <h2 style="margin-bottom: 1.5rem; font-size: 1rem;"><i class="fa-solid fa-building-columns"></i>
                    Distribución por Banco</h2>
                <div style="height: 250px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartBancos"></canvas>
                </div>
            </div>
        </div>
        <div class="g_columna_4">
            <div class="g_panel">
                <h2 style="margin-bottom: 1.5rem; font-size: 1rem;"><i class="fa-solid fa-file-code"></i> Tipos de
                    Archivo</h2>
                <div style="height: 250px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartExtension"></canvas>
                </div>
            </div>
        </div>
        <div class="g_columna_4">
            <div class="g_panel">
                <h2 style="margin-bottom: 1.5rem; font-size: 1rem;"><i class="fa-solid fa-filter"></i> Estado de
                    Validación</h2>
                <div style="height: 250px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartEstado"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 4: Detalle Técnico Reciente -->
    <div class="g_fila">
        <div class="g_columna_12">
            <div class="g_panel">
                <h2 style="margin-bottom: 1rem; font-size: 1.1rem;"><i class="fa-solid fa-receipt"></i> Últimas 8
                    Evidencias Técnicas Procesadas</h2>
                <div style="overflow-x: auto;">
                    <table class="g_tabla">
                        <thead>
                            <tr>
                                <th>Origen / Solicitud</th>
                                <th>Referencia / Banco</th>
                                <th>Monto Detectado</th>
                                <th>Tipo Proceso</th>
                                <th>Estado</th>
                                <th>Fecha Procesado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ultimasEvidencias as $evid)
                                <tr>
                                    <td>
                                        <div style="font-weight: 600;">EVID-{{ $evid->id }}</div>
                                        <div style="font-size: 0.75rem; color: #64748b;">SOL:
                                            #{{ $evid->solicitud_evidencia_pago_id }}</div>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.85rem;">{{ $evid->banco ?: 'No detectado' }}</div>
                                        <div style="font-size: 0.7rem; color: #64748b;">Op:
                                            {{ $evid->numero_operacion ?: 'N/A' }}</div>
                                    </td>
                                    <td style="font-weight: 600; color: var(--color-primario);">S/
                                        {{ number_format($evid->monto, 2) }}</td>
                                    <td>
                                        @if($evid->slin_respuesta)
                                            <span style="color: #10B981; font-size: 0.8rem; font-weight: 600;">
                                                <i class="fa-solid fa-robot"></i> Automático (SLIN)
                                            </span>
                                        @else
                                            <span style="color: #F59E0B; font-size: 0.8rem; font-weight: 600;">
                                                <i class="fa-solid fa-user"></i> Manual
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="g_badge"
                                            style="background: {{ str_contains(strtolower($evid->estado?->nombre), 'aprobado') ? '#dcfce7' : '#fee2e2' }}; color: {{ str_contains(strtolower($evid->estado?->nombre), 'aprobado') ? '#166534' : '#991b1b' }};">
                                            {{ $evid->estado?->nombre ?: 'Pendiente' }}
                                        </span>
                                    </td>
                                    <td>{{ $evid->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let chartMontos = null;
    let chartMetodo = null;

    document.addEventListener('livewire:init', () => {
        const coloresArr = ['#10B981', '#F59E0B', '#3B82F6', '#EF4444', '#8B5CF6', '#06B6D4', '#4F46E5', '#6366F1'];
        Chart.defaults.font.family = "'Instrument Sans', sans-serif";
        Chart.defaults.color = '#64748b';

        const initChart = (id, config) => {
            const ctx = document.getElementById(id);
            if (ctx) {
                try {
                    const chart = new Chart(ctx, config);
                    if (id === 'chartMontos') chartMontos = chart;
                    else if (id === 'chartMetodo') chartMetodo = chart;
                    return chart;
                } catch (e) { console.error('Error init chart ' + id, e); }
            }
        };

        Livewire.on('actualizarGraficosDinamicos', (payload) => {
            const items = payload[0];
            if (chartMontos && items.montos) {
                chartMontos.data.labels = items.montos.labels;
                chartMontos.data.datasets[0].data = items.montos.data;
                chartMontos.update();
            }
            if (chartMetodo && items.metodos) {
                chartMetodo.data.labels = items.metodos.labels;
                chartMetodo.data.datasets[0].data = items.metodos.data;
                chartMetodo.update();
            }
        });

        setTimeout(() => {
            // 1. Evolución de Montos (Día/Mes)
            initChart('chartMontos', {
                type: 'line',
                data: {
                    labels: @json($evolucionMontos['labels']),
                    datasets: [{
                        label: 'Monto Recaudado',
                        data: @json($evolucionMontos['data']),
                        borderColor: '#8B5CF6',
                        backgroundColor: 'rgba(139, 92, 246, 0.05)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { callback: (value) => 'S/ ' + value.toLocaleString() }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });

            // 2. Método de Cierre (DOUGHNUT)
            initChart('chartMetodo', {
                type: 'doughnut',
                data: {
                    labels: @json($metodoCierre['labels']),
                    datasets: [{
                        data: @json($metodoCierre['data']),
                        backgroundColor: ['#10B981', '#F59E0B'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true } }
                    },
                    cutout: '70%'
                }
            });

            // 3. Por Banco (BAR HORIZ)
            initChart('chartBancos', {
                type: 'bar',
                data: {
                    labels: @json($distribucionBancos['labels']),
                    datasets: [{
                        data: @json($distribucionBancos['data']),
                        backgroundColor: '#3B82F6',
                        borderRadius: 5
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } }
                }
            });

            // 4. Por Extensión (PIE)
            initChart('chartExtension', {
                type: 'pie',
                data: {
                    labels: @json($porExtension['labels']),
                    datasets: [{
                        data: @json($porExtension['data']),
                        backgroundColor: coloresArr,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: 9 } } }
                    }
                }
            });

            // 5. Por Estado (BAR)
            initChart('chartEstado', {
                type: 'bar',
                data: {
                    labels: @json($porEstado['labels']),
                    datasets: [{
                        data: @json($porEstado['data']),
                        backgroundColor: '#F59E0B',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true }, x: { grid: { display: false } } }
                }
            });

        }, 500);
    });
</script>