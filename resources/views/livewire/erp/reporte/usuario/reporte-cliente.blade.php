<div class="g_gap_pagina">
    <div class="g_fila">
        <div class="g_panel_4_grid">
            <div class="g_panel">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Total Clientes</h2>
                        <p class="g_negrita">{{ $totalClientes }}</p>
                    </div>
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>

            <div class="g_panel">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Aceptaron 2 Políticas</h2>
                        <p class="g_negrita">{{ $clientesPoliticas['data'][1] }}</p>
                    </div>
                    <i class="fa-solid fa-user-check"></i>
                </div>
            </div>

            <div class="g_panel">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Con Dirección</h2>
                        <p class="g_negrita">{{ $clientesConDireccion['data'][0] }}</p>
                    </div>
                    <i class="fa-solid fa-location-dot"></i>
                </div>
            </div>

            <div class="g_panel">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Vericados Email</h2>
                        <p class="g_negrita">{{ $clientesEmailVerificado['data'][0] }}</p>
                    </div>
                    <i class="fa-solid fa-envelope-circle-check"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_12">
            <div class="g_panel">
                <h2>Clientes nuevos por día (mes actual)</h2>
                <canvas id="chartDiaMes" height="150"></canvas>
            </div>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_4">
            <div class="g_panel">
                <h2>Clientes por aceptación de políticas</h2>
                <canvas id="chartPoliticas" height="150"></canvas>
            </div>
        </div>
        <div class="g_columna_4">
            <div class="g_panel">
                <h2>Clientes por verificación de email</h2>
                <canvas id="chartClientesEmail" height="150"></canvas>
            </div>
        </div>
        <div class="g_columna_4">
            <div class="g_panel">
                <h2>Clientes con dirección</h2>
                <canvas id="chartClientesDireccion" height="150"></canvas>
            </div>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_12">
            <div class="g_panel">
                <h2>Clientes Nuevos por Mes</h2>
                <canvas id="chartMeses" height="150"></canvas>
            </div>
        </div>
    </div>

    <div class="g_panel">
        <h2>Perfil Global de Clientes</h2>
        <canvas id="chartRadar" height="250"></canvas>
    </div>
</div>
<script>
    document.addEventListener('livewire:init', () => {
        const colores = ['#4F46E5', '#3B82F6', '#10B981', '#F59E0B', '#EF4444'];
        const bordeRadar = '#4F46E5';

        new Chart(document.getElementById('chartMeses'), {
            type: 'bar',
            data: {
                labels: @json(array_values($clientesPorMes['labels'])),
                datasets: [{
                    label: 'Clientes nuevos',
                    data: @json(array_values($clientesPorMes['data'])),
                    backgroundColor: '#3B82F6'
                }]
            },
            options: {
                responsive: true
            }
        });

        new Chart(document.getElementById('chartDiaMes'), {
            type: 'line',
            data: {
                labels: @json($clientesPorDiaMesActual['labels']),
                datasets: [{
                    label: 'Clientes nuevos',
                    data: @json($clientesPorDiaMesActual['data']),
                    fill: false,
                    borderColor: '#10B981',
                    tension: 0.3,
                    pointBackgroundColor: '#10B981',
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        new Chart(document.getElementById('chartPoliticas'), {
            type: 'pie',
            data: {
                labels: @json($clientesPoliticas['labels']),
                datasets: [{
                    data: @json($clientesPoliticas['data']),
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

        new Chart(document.getElementById('chartClientesEmail'), {
            type: 'pie',
            data: {
                labels: @json($clientesEmailVerificado['labels']),
                datasets: [{
                    data: @json($clientesEmailVerificado['data']),
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

        new Chart(document.getElementById('chartClientesDireccion'), {
            type: 'pie',
            data: {
                labels: @json($clientesConDireccion['labels']),
                datasets: [{
                    data: @json($clientesConDireccion['data']),
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

        new Chart(document.getElementById('chartRadar'), {
            type: 'radar',
            data: {
                labels: @json($clientesRadar['labels']),
                datasets: [{
                    label: 'Perfil de Clientes',
                    data: @json($clientesRadar['data']),
                    backgroundColor: colores,
                    borderColor: bordeRadar,
                    borderWidth: 2,
                    pointBackgroundColor: bordeRadar,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    r: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 } // si quieres números enteros
                    }
                },
                plugins: {
                    legend: { position: 'top' }
                }
            }
        });
    });
</script>