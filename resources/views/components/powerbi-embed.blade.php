@props([
    'configured' => false,
    'embedToken' => '',
    'embedUrl' => '',
    'reportId' => '',
    'pageName' => null,
    'titulo' => 'Reporte Power BI',
    'reporteKey' => '',
    'rutaClasica' => '#',
])

{{-- ═══════════ HEADER ═══════════ --}}
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
    <div style="display: flex; align-items: center; gap: 0.75rem;">
        <h2 style="margin: 0; font-size: 1.25rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; color: var(--color-texto-primario, #1e293b);">
            <i class="fa-solid fa-chart-column" style="color: #7C3AED;"></i>
            {{ $titulo }}
        </h2>
        <span style="background: linear-gradient(135deg, #7C3AED, #4F46E5); color: white; 
                      padding: 0.2rem 0.6rem; border-radius: 6px; font-size: 0.7rem; 
                      font-weight: 600; letter-spacing: 0.03em;">
            POWER BI
        </span>
    </div>
    <a href="{{ $rutaClasica }}"
       style="background: var(--color-fondo-hover, #f1f5f9); color: var(--color-texto-secundario, #64748b);
              text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem;
              padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.8rem; font-weight: 500;
              border: 1px solid var(--color-borde, #e2e8f0); transition: all 0.2s ease;"
       onmouseover="this.style.background='var(--color-fondo-hover-dark, #e2e8f0)'"
       onmouseout="this.style.background='var(--color-fondo-hover, #f1f5f9)'">
        <i class="fa-solid fa-chart-pie" style="font-size: 0.75rem;"></i>
        Ver reporte clásico
    </a>
</div>

@if($configured && $embedToken)
    {{-- ═══════════ CONTENEDOR DEL REPORTE ═══════════ --}}
    <div class="g_panel" 
         style="padding: 0; overflow: hidden; border-radius: 12px; position: relative;
                height: calc(100vh - 180px); min-height: 500px;"
         id="pbi-wrapper-{{ $reporteKey }}">

        {{-- Skeleton Loader --}}
        <div id="pbi-loader-{{ $reporteKey }}"
             style="position: absolute; inset: 0; z-index: 10;
                    background: var(--color-fondo, #ffffff);
                    display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 1rem;">
            <div style="display: flex; flex-direction: column; align-items: center; gap: 1.5rem;">
                {{-- Animated icon --}}
                <div style="width: 64px; height: 64px; border-radius: 16px; 
                            background: linear-gradient(135deg, #7C3AED20, #4F46E520);
                            display: flex; align-items: center; justify-content: center;
                            animation: pbi-pulse 2s ease-in-out infinite;">
                    <i class="fa-solid fa-chart-column" style="font-size: 1.5rem; color: #7C3AED;"></i>
                </div>
                <div style="text-align: center;">
                    <p style="margin: 0; font-weight: 600; font-size: 1rem; color: var(--color-texto, #1e293b);">
                        Cargando reporte interactivo
                    </p>
                    <p style="margin: 0.25rem 0 0; font-size: 0.8rem; color: var(--color-texto-secundario, #94a3b8);">
                        Conectando con Power BI...
                    </p>
                </div>
                {{-- Skeleton bars --}}
                <div style="display: flex; gap: 0.5rem; align-items: end; height: 60px;">
                    <div style="width: 16px; background: #e2e8f0; border-radius: 4px; animation: pbi-bar 1.5s ease-in-out infinite; height: 30px;"></div>
                    <div style="width: 16px; background: #e2e8f0; border-radius: 4px; animation: pbi-bar 1.5s ease-in-out 0.2s infinite; height: 50px;"></div>
                    <div style="width: 16px; background: #e2e8f0; border-radius: 4px; animation: pbi-bar 1.5s ease-in-out 0.4s infinite; height: 35px;"></div>
                    <div style="width: 16px; background: #e2e8f0; border-radius: 4px; animation: pbi-bar 1.5s ease-in-out 0.6s infinite; height: 55px;"></div>
                    <div style="width: 16px; background: #e2e8f0; border-radius: 4px; animation: pbi-bar 1.5s ease-in-out 0.8s infinite; height: 25px;"></div>
                    <div style="width: 16px; background: #e2e8f0; border-radius: 4px; animation: pbi-bar 1.5s ease-in-out 1.0s infinite; height: 45px;"></div>
                </div>
            </div>
        </div>

        {{-- Contenedor donde Power BI renderiza el reporte --}}
        <div id="pbi-container-{{ $reporteKey }}"
             style="width: 100%; height: 100%;"></div>
    </div>

    {{-- ═══════════ JAVASCRIPT ═══════════ --}}
    <script>
        (function() {
            const reporteKey   = @json($reporteKey);
            const embedToken   = @json($embedToken);
            const embedUrl     = @json($embedUrl);
            const reportId     = @json($reportId);
            const pageName     = @json($pageName);
            const containerId  = 'pbi-container-' + reporteKey;
            const loaderId     = 'pbi-loader-' + reporteKey;
            const tokenRefreshUrl = @json(route('erp.powerbi.token', ['reportKey' => $reporteKey]));

            // Tiempo de refresh: 55 minutos (tokens duran ~60 min)
            const TOKEN_REFRESH_MS = 55 * 60 * 1000;

            function initReport() {
                const container = document.getElementById(containerId);
                const loader    = document.getElementById(loaderId);
                if (!container || typeof powerbi === 'undefined') {
                    console.error('Power BI client no disponible');
                    return;
                }

                const models = window['powerbi-client'].models;

                const config = {
                    type: 'report',
                    tokenType: models.TokenType.Embed,
                    accessToken: embedToken,
                    embedUrl: embedUrl,
                    id: reportId,
                    permissions: models.Permissions.Read,
                    settings: {
                        panes: {
                            filters: { expanded: false, visible: true },
                            pageNavigation: { visible: true }
                        },
                        background: models.BackgroundType.Transparent,
                        navContentPaneEnabled: true,
                    }
                };

                // Si hay pageName, abrir directamente esa página
                if (pageName) {
                    config.pageName = pageName;
                }

                const report = powerbi.embed(container, config);

                // Ocultar loader cuando el reporte esté listo
                report.on('loaded', function() {
                    if (loader) {
                        loader.style.opacity = '0';
                        loader.style.transition = 'opacity 0.4s ease-out';
                        setTimeout(() => { loader.style.display = 'none'; }, 400);
                    }
                });

                report.on('error', function(event) {
                    console.error('Power BI Error:', event.detail);
                    if (loader) {
                        loader.querySelector('p').textContent = 'Error al cargar el reporte';
                        loader.querySelector('p:last-child').textContent = event.detail?.message || 'Intenta recargar la página';
                    }
                });

                // Auto-refresh del token cada 55 minutos
                setInterval(async () => {
                    try {
                        const response = await fetch(tokenRefreshUrl, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                            credentials: 'same-origin'
                        });

                        if (!response.ok) throw new Error('Token refresh failed');

                        const data = await response.json();
                        if (data.success && data.embedToken) {
                            await report.setAccessToken(data.embedToken);
                            console.log('[Power BI] Token renovado exitosamente');
                        }
                    } catch (err) {
                        console.warn('[Power BI] Error renovando token:', err);
                    }
                }, TOKEN_REFRESH_MS);
            }

            // Iniciar cuando el DOM esté listo
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => setTimeout(initReport, 300));
            } else {
                setTimeout(initReport, 300);
            }
        })();
    </script>

    <style>
        @keyframes pbi-pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.7; }
        }
        @keyframes pbi-bar {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 1; }
        }
    </style>

@else
    {{-- ═══════════ ESTADO NO CONFIGURADO ═══════════ --}}
    <div class="g_panel" 
         style="display: flex; flex-direction: column; align-items: center; justify-content: center;
                height: calc(100vh - 180px); min-height: 400px; text-align: center;">
        
        <div style="width: 80px; height: 80px; border-radius: 20px; 
                    background: linear-gradient(135deg, #F3E8FF, #EDE9FE);
                    display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
            <i class="fa-solid fa-plug-circle-xmark" style="font-size: 2rem; color: #7C3AED;"></i>
        </div>

        <h3 style="margin: 0 0 0.5rem; font-size: 1.25rem; font-weight: 600; 
                   color: var(--color-texto, #1e293b);">
            Power BI no configurado
        </h3>

        <p style="margin: 0 0 1.5rem; font-size: 0.9rem; color: var(--color-texto-secundario, #64748b);
                  max-width: 450px; line-height: 1.6;">
            {{ $configured === false && isset($message) ? $message : 'Este reporte requiere configurar las credenciales de Azure y el Report ID en el archivo .env del servidor.' }}
        </p>

        <div style="background: var(--color-fondo-hover, #f8fafc); border-radius: 10px; padding: 1.25rem 1.5rem;
                    max-width: 500px; width: 100%; text-align: left; border: 1px solid var(--color-borde, #e2e8f0);">
            <p style="margin: 0 0 0.75rem; font-weight: 600; font-size: 0.85rem; color: var(--color-texto, #1e293b);">
                <i class="fa-solid fa-circle-info" style="color: #3B82F6; margin-right: 0.3rem;"></i>
                Variables necesarias en .env:
            </p>
            <code style="font-size: 0.75rem; color: #7C3AED; line-height: 1.8; display: block;">
                POWERBI_TENANT_ID=...<br>
                POWERBI_CLIENT_ID=...<br>
                POWERBI_CLIENT_SECRET=...<br>
                POWERBI_WORKSPACE_ID=...<br>
                POWERBI_REPORT_{{ strtoupper(str_replace('-', '_', $reporteKey)) }}=...
            </code>
        </div>

        <a href="{{ $rutaClasica }}" 
           style="margin-top: 1.5rem; background: linear-gradient(135deg, #7C3AED, #4F46E5); 
                  color: white; text-decoration: none;
                  display: inline-flex; align-items: center; gap: 0.5rem;
                  padding: 0.6rem 1.2rem; border-radius: 10px; font-size: 0.85rem; font-weight: 500;
                  box-shadow: 0 4px 12px rgba(79,70,229,0.3); transition: all 0.2s ease;"
           onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 16px rgba(79,70,229,0.4)'"
           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(79,70,229,0.3)'">
            <i class="fa-solid fa-chart-pie"></i>
            Ir al reporte clásico
        </a>
    </div>
@endif
