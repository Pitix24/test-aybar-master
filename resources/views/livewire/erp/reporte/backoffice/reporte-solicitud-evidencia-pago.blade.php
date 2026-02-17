<div class="g_gap_pagina">

    <div class="g_fila">
        <div class="g_panel_4_grid">

            <div class="g_panel">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Total Evidencias recibidas</h2>
                        <p class="g_negrita">{{ $totalSolicitudes }}</p>
                    </div>
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
            </div>

            <div class="g_panel">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Evidencias validadas</h2>
                        <p class="g_negrita">{{ $solicitudesValidadas['data'][0] }}</p>
                    </div>
                    <i class="fa-solid fa-check-circle"></i>
                </div>
            </div>

            <div class="g_panel">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Evidencias pendientes</h2>
                        <p class="g_negrita">{{ $solicitudesValidadas['data'][1] }}</p>
                    </div>
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
            </div>

            <div class="g_panel">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Evidencias no asignadas</h2>
                        <p class="g_negrita">{{ $solicitudesSinAsignar }}</p>
                    </div>
                    <i class="fa-solid fa-user-tie"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_12">
            <div class="g_panel">
                <h2>Evidencia de pago recibidas(por Fecha)</h2>
                <canvas id="chartPorFecha" height="150"></canvas>
            </div>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_12">
            <div class="g_panel">
                <h2>Evidencia de pago por Razón Social</h2>
                <canvas id="chartPorUnidad" height="150"></canvas>
            </div>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_12">
            <div class="g_panel">
                <h2>Evidencia de pago por Proyecto</h2>
                <canvas id="chartPorProyecto" height="150"></canvas>
            </div>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_12">
            <div class="g_panel">
                <h2>Top Gestores - Evidencia de pago Validadas</h2>
                <canvas id="chartTopGestores" height="150"></canvas>
            </div>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_3">
            <div class="g_panel">
                <h2>Evidencia de pago por Estado</h2>
                <canvas id="chartPorEstado" height="150"></canvas>
            </div>
        </div>

        <div class="g_columna_3">
            <div class="g_panel">
                <h2>Evidencia de pago Asignadas vs Sin Asignar</h2>
                <canvas id="chartAsignacion" height="150"></canvas>
            </div>
        </div>

        <div class="g_columna_3">
            <div class="g_panel">
                <h2>Evidencia de pago Validadas vs Pendientes</h2>
                <canvas id="chartValidadas" height="150"></canvas>
            </div>
        </div>

        <div class="g_columna_3">
            <div class="g_panel">
                <h2>Evidencia de pago por Cantidad de Evidencias</h2>
                <canvas id="chartEvidencias" height="150"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:init', () => {

        const colores = ['#4F46E5', '#3B82F6', '#10B981', '#F59E0B', '#EF4444'];

        new Chart(document.getElementById('chartPorEstado'), {
            type: 'pie',
            data: {
                labels: @json($solicitudesPorEstado['labels']),
                datasets: [{
                    data: @json($solicitudesPorEstado['data']),
                    backgroundColor: colores,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        new Chart(document.getElementById('chartPorUnidad'), {
            type: 'bar',
            data: {
                labels: @json($solicitudesPorUnidad['labels']),
                datasets: [{
                    label: 'Solicitudes',
                    data: @json($solicitudesPorUnidad['data']),
                    backgroundColor: colores
                }]
            },
            options: {
                responsive: true
            }
        });

        new Chart(document.getElementById('chartPorProyecto'), {
            type: 'bar',
            data: {
                labels: @json($solicitudesPorProyecto['labels']),
                datasets: [{
                    label: 'Solicitudes',
                    data: @json($solicitudesPorProyecto['data']),
                    backgroundColor: colores
                }]
            },
            options: {
                responsive: true
            }
        });

        new Chart(document.getElementById('chartPorFecha'), {
            type: 'line',
            data: {
                labels: @json($solicitudesPorFecha['labels']),
                datasets: [{
                    label: 'Solicitudes',
                    data: @json($solicitudesPorFecha['data']),
                    borderColor: colores,
                    backgroundColor: colores,
                    fill: false,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        new Chart(document.getElementById('chartTopGestores'), {
            type: 'bar',
            data: {
                labels: @json($topGestores['labels']),
                datasets: [{
                    label: 'Solicitudes validadas',
                    data: @json($topGestores['data']),
                    backgroundColor: colores,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        new Chart(document.getElementById('chartAsignacion'), {
            type: 'pie',
            data: {
                labels: ['Asignadas', 'Sin asignar'],
                datasets: [{
                    data: [{{ $solicitudesAsignadas }}, {{ $solicitudesSinAsignar }}],
                    backgroundColor: colores,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        new Chart(document.getElementById('chartValidadas'), {
            type: 'pie',
            data: {
                labels: @json($solicitudesValidadas['labels']),
                datasets: [{
                    data: @json($solicitudesValidadas['data']),
                    backgroundColor: colores
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        new Chart(document.getElementById('chartEvidencias'), {
            type: 'pie',
            data: {
                labels: @json($solicitudesPorCantidadEvidencias['labels']),
                datasets: [{
                    data: @json($solicitudesPorCantidadEvidencias['data']),
                    backgroundColor: colores
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

    });
</script>