<div class="g_gap_pagina">
    <div style="display: flex; justify-content: flex-end; margin-bottom: 0.5rem;">
        <a href="{{ route('erp.reporte.vista.solicitud-evidencia-pago-powerbi') }}" wire:navigate
           style="background: linear-gradient(135deg, #F2C811, #E3BA0B); 
                  color: #000; text-decoration: none; 
                  display: inline-flex; align-items: center; gap: 0.5rem; 
                  padding: 0.5rem 1rem; border-radius: 6px; 
                  font-size: 0.85rem; font-weight: 600;
                  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                  border: 1px solid #D4AF00;">
            <i class="fa-solid fa-chart-column"></i> Ver en Power BI
        </a>
    </div>
    <!-- Fila 1: KPIs -->
    <div class="g_fila">
        <div class="g_panel_dashboard_grid"
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; width: 100%;">
            <div class="g_panel" title="Total solicitudes creadas">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Total Solicitudes</h2>
                        <p class="g_negrita">{{ number_format($totalSolicitudes) }}</p>
                    </div>
                    <i class="fa-solid fa-file-invoice-dollar" style="color: var(--color-primario);"></i>
                </div>
            </div>

            <div class="g_panel" title="Archivos de evidencia subidos por clientes">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Archivos Evidencia</h2>
                        <p class="g_negrita">{{ number_format($totalArchivosEvidencia) }}</p>
                    </div>
                    <i class="fa-solid fa-file-pdf" style="color: #EF4444;"></i>
                </div>
            </div>

            <div class="g_panel" title="Evidencias con fecha de años anteriores">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Evi. Antiguas</h2>
                        <p class="g_negrita" style="color: #EF4444;">{{ number_format($evidenciasAntiguasCount) }}</p>
                    </div>
                    <i class="fa-solid fa-triangle-exclamation" style="color: #F59E0B;"></i>
                </div>
                <div style="margin-top: 0.5rem; font-size: 0.75rem; color: #64748b;">
                    Detectadas por OCR
                </div>
            </div>

            <div class="g_panel" title="Tasa de cumplimiento operativa">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Cumplimiento</h2>
                        <p class="g_negrita">{{ $tasaCumplimiento }}%</p>
                    </div>
                    <i class="fa-solid fa-gauge-high" style="color: #8B5CF6;"></i>
                </div>
            </div>

            <div class="g_panel">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Emails Enviados</h2>
                        <p class="g_negrita">{{ number_format($totalEmailsEnviados) }}</p>
                    </div>
                    <i class="fa-solid fa-envelope-open-text" style="color: #06B6D4;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 2: Tendencias Temporales -->
    <div class="g_fila">
        <div class="g_columna_8">
            <div class="g_panel">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h2 style="font-size: 1.1rem; margin: 0;"><i class="fa-solid fa-chart-line"></i> Comparativa
                        Solicitudes vs Subidas</h2>
                    <input type="month" wire:model.live="mesSeleccionado" class="g_input"
                        style="padding: 0.2rem 0.5rem; width: 140px; font-size: 0.85rem;">
                </div>
                <div style="height: 280px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartComparativo"></canvas>
                </div>
            </div>
        </div>
        <div class="g_columna_4">
            <div class="g_panel">
                <h2 style="margin-bottom: 1.5rem; font-size: 1.1rem; color: #EF4444;"><i
                        class="fa-solid fa-history"></i> Antigüedad de Documentos</h2>
                <div style="height: 280px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartAntiguedad"></canvas>
                </div>
                <div
                    style="margin-top: 1rem; border-top: 1px solid #f1f5f9; padding-top: 0.5rem; font-size: 0.8rem; color: #64748b; text-align: center;">
                    Año impreso en el voucher vs Año actual
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 3: Estados y Bancos -->
    <div class="g_fila">
        <div class="g_columna_4">
            <div class="g_panel">
                <h2 style="margin-bottom: 1.5rem; font-size: 1.1rem;"><i class="fa-solid fa-filter"></i> Estado de
                    Archivos</h2>
                <div style="height: 220px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartEvidenciasEstado"></canvas>
                </div>
            </div>
        </div>
        <div class="g_columna_4">
            <div class="g_panel">
                <h2 style="margin-bottom: 1.5rem; font-size: 1.1rem;"><i class="fa-solid fa-building-columns"></i>
                    Distribución Bancaria</h2>
                <div style="height: 220px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartBancos"></canvas>
                </div>
            </div>
        </div>
        <div class="g_columna_4">
            <div class="g_panel">
                <h2 style="margin-bottom: 1.5rem; font-size: 1.1rem;"><i class="fa-solid fa-clock"></i> Horas de Mayor
                    Carga</h2>
                <div style="height: 220px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartHoras"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 4: Gestores -->
    <div class="g_fila">
        <div class="g_columna_6">
            <div class="g_panel">
                <h2 style="margin-bottom: 1.5rem; font-size: 1.1rem;"><i class="fa-solid fa-award"></i> Rendimiento
                    Gestor (Validadas)</h2>
                <div style="height: 250px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartTopGestores"></canvas>
                </div>
            </div>
        </div>
        <div class="g_columna_6">
            <div class="g_panel">
                <h2 style="margin-bottom: 1.5rem; font-size: 1.1rem;"><i class="fa-solid fa-comments"></i> Actividad de
                    Comunicación</h2>
                <div style="height: 250px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartEmails"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 5: Tabla Principal -->
    <div class="g_fila">
        <div class="g_columna_12">
            <div class="g_panel">
                <h2 style="margin-bottom: 1rem; font-size: 1.1rem;"><i class="fa-solid fa-list-ul"></i> Últimas 5
                    Solicitudes de Pago</h2>
                <div style="overflow-x: auto;">
                    <table class="g_tabla">
                        <thead>
                            <tr>
                                <th>Lote / Cuota</th>
                                <th>Proyecto</th>
                                <th>Razón Social</th>
                                <th>Gestor</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ultimasSolicitudes as $sol)
                                <tr>
                                    <td>
                                        <div style="font-weight: 600;">{{ $sol->lote_completo ?: 'N/A' }}</div>
                                        <div style="font-size: 0.75rem; color: #64748b;">{{ $sol->codigo_cuota }}</div>
                                    </td>
                                    <td>{{ $sol->proyecto?->nombre ?: $sol->nombre_proyecto }}</td>
                                    <td>{{ $sol->unidadNegocio?->nombre ?: $sol->razon_social }}</td>
                                    <td>{{ explode(' ', $sol->gestor?->name ?: 'Sin Asignar')[0] }}</td>
                                    <td>
                                        <span class="g_badge"
                                            style="background: {{ $sol->esta_aprobada ? '#dcfce7' : '#fee2e2' }}; color: {{ $sol->esta_aprobada ? '#166534' : '#991b1b' }};">
                                            {{ $sol->estado?->nombre ?: 'N/A' }}
                                        </span>
                                    </td>
                                    <td>{{ $sol->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_6">
            <div class="g_panel">
                <h2 style="margin-bottom: 1rem; font-size: 1.1rem;"><i class="fa-solid fa-paperclip"></i> Últimas 5
                    Evidencias</h2>
                <div style="overflow-x: auto;">
                    <table class="g_tabla">
                        <thead>
                            <tr>
                                <th>Banco</th>
                                <th>Fecha Voucher</th>
                                <th>Monto</th>
                                <th>Antiguo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ultimasEvidencias as $evid)
                                <tr>
                                    <td>{{ $evid->banco ?: 'Desconocido' }}</td>
                                    <td>{{ $evid->fecha ? \Carbon\Carbon::parse($evid->fecha)->format('d/m/Y') : 'N/A' }}
                                    </td>
                                    <td style="font-weight: 600;">S/ {{ number_format($evid->monto, 2) }}</td>
                                    <td>
                                        @php
                                            $fechaVoucher = $evid->fecha ? \Carbon\Carbon::parse($evid->fecha) : null;
                                        @endphp
                                        @if($fechaVoucher && $fechaVoucher->year < date('Y'))
                                            <i class="fa-solid fa-warning" style="color: #EF4444;"
                                                title="Documento de años anteriores"></i>
                                        @else
                                            <i class="fa-solid fa-check-circle" style="color: #10B981;"></i>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="g_columna_6">
            <div class="g_panel">
                <h2 style="margin-bottom: 1rem; font-size: 1.1rem;"><i class="fa-solid fa-envelope"></i> Últimos 5
                    Correos</h2>
                <div style="overflow-x: auto;">
                    <table class="g_tabla">
                        <thead>
                            <tr>
                                <th>Gestor</th>
                                <th>Asunto</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ultimosEmails as $email)
                                <tr>
                                    <td>{{ explode(' ', $email->emisor?->name ?: 'Sistema')[0] }}</td>
                                    <td
                                        style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $email->asunto }}
                                    </td>
                                    <td>{{ $email->enviado_at->diffForHumans() }}</td>
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
    let chartComparativo = null;
    let chartAntiguedad = null;
    let chartEmails = null;
    let chartHoras = null;

    document.addEventListener('livewire:init', () => {
        const coloresArr = ['#4F46E5', '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#06B6D4', '#6366F1'];
        Chart.defaults.font.family = "'Instrument Sans', sans-serif";
        Chart.defaults.color = '#64748b';

        const initChart = (id, config) => {
            const ctx = document.getElementById(id);
            if (ctx) {
                try {
                    const chart = new Chart(ctx, config);
                    if (id === 'chartComparativo') chartComparativo = chart;
                    else if (id === 'chartAntiguedad') chartAntiguedad = chart;
                    else if (id === 'chartEmails') chartEmails = chart;
                    else if (id === 'chartHoras') chartHoras = chart;
                    return chart;
                } catch (e) { console.error('Error init chart ' + id, e); }
            }
        };

        Livewire.on('actualizarGraficosDinamicos', (payload) => {
            const items = payload[0];
            if (chartComparativo && items.tendencia && items.subidas) {
                chartComparativo.data.labels = items.tendencia.labels;
                chartComparativo.data.datasets[0].data = items.tendencia.data;
                chartComparativo.data.datasets[1].data = items.subidas.data;
                chartComparativo.update();
            }
            if (chartEmails && items.emails) {
                chartEmails.data.labels = items.emails.labels;
                chartEmails.data.datasets[0].data = items.emails.data;
                chartEmails.update();
            }
            if (chartHoras && items.horarios) {
                chartHoras.data.labels = items.horarios.labels;
                chartHoras.data.datasets[0].data = items.horarios.data;
                chartHoras.update();
            }
        });

        setTimeout(() => {
            // 1. Chart Comparativo Lineas
            initChart('chartComparativo', {
                type: 'line',
                data: {
                    labels: @json($solicitudesPorDiaMesActual['labels']),
                    datasets: [
                        { label: 'Solicitudes', data: @json($solicitudesPorDiaMesActual['data']), borderColor: '#4F46E5', backgroundColor: 'rgba(79, 70, 229, 0.05)', fill: true, tension: 0.4 },
                        { label: 'Subidas Evidencia', data: @json($evidenciasSubidasPorDia['data']), borderColor: '#EF4444', backgroundColor: 'rgba(239, 68, 68, 0.05)', fill: true, tension: 0.4 }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'top', labels: { boxWidth: 10, usePointStyle: true } } },
                    scales: { y: { beginAtZero: true }, x: { grid: { display: false } } }
                }
            });

            // 2. Chart Antigüedad Documentos (PIE)
            initChart('chartAntiguedad', {
                type: 'pie',
                data: {
                    labels: @json($antiguedadEvidencias['labels']),
                    datasets: [{ data: @json($antiguedadEvidencias['data']), backgroundColor: coloresArr }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: 10 } } } }
                }
            });

            // 3. Estado Archivos (BAR)
            initChart('chartEvidenciasEstado', {
                type: 'bar',
                data: {
                    labels: @json($evidenciasPorEstado['labels']),
                    datasets: [{ data: @json($evidenciasPorEstado['data']), backgroundColor: '#3B82F6', borderRadius: 4 }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true }, x: { grid: { display: false } } }
                }
            });

            // 4. Bancos (DOUGHNUT)
            initChart('chartBancos', {
                type: 'doughnut',
                data: {
                    labels: @json($distribucionBancos['labels']),
                    datasets: [{ data: @json($distribucionBancos['data']), backgroundColor: coloresArr, borderWidth: 0 }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: 10 } } } },
                    cutout: '65%'
                }
            });

            // 5. Horas (BAR)
            initChart('chartHoras', {
                type: 'bar',
                data: {
                    labels: @json($clientesPorHora['labels']),
                    datasets: [{ label: 'Carga', data: @json($clientesPorHora['data']), backgroundColor: '#10B981', borderRadius: 3 }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true }, x: { ticks: { font: { size: 9 } } } }
                }
            });

            // 6. Gestor (BAR HORIZ)
            initChart('chartTopGestores', {
                type: 'bar',
                data: {
                    labels: @json($topGestores['labels']),
                    datasets: [{ data: @json($topGestores['data']), backgroundColor: coloresArr, borderRadius: 5 }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } }
                }
            });

            // 7. Emails (BAR HORIZ)
            initChart('chartEmails', {
                type: 'bar',
                data: {
                    labels: @json($emailsPorGestor['labels']),
                    datasets: [{ data: @json($emailsPorGestor['data']), backgroundColor: '#06B6D4', borderRadius: 5 }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } }
                }
            });

        }, 500);
    });
</script>