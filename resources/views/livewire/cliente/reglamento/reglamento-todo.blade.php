<div>
    <div x-data="{
        showModal: false,
        pdfUrl: '',
        pdfLoading: false,
        pdfError: '',
        tituloReglamento: '',

        init() {
            window.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && (e.key === 'p' || e.key === 's' || e.key === 'i' || e.key === 'u')) {
                    e.preventDefault();
                }
            });
        },

        abrirPdf(url, reglamentoId, titulo) {
            if (!url || url === '#') return;
            this.pdfUrl = url;
            this.tituloReglamento = titulo;
            this.showModal = true;
            this.renderizarPdf();

            $wire.registrarClick(reglamentoId);
        },

        renderizarPdf() {
            this.pdfLoading = true;
            this.pdfError = '';
            const container = document.getElementById('pdf-render-container');
            if (container) container.innerHTML = '';

            try {
                if (typeof pdfjsLib === 'undefined') {
                    throw new Error('La librería PDF.js no está disponible. Recarga la página.');
                }

                pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';

                pdfjsLib.getDocument(this.pdfUrl).promise.then(pdf => {
                    for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                        pdf.getPage(pageNum).then(page => {
                            const scale = 1.3;
                            const viewport = page.getViewport({ scale: scale });

                            const canvas = document.createElement('canvas');
                            canvas.className = 'shadow-md mx-auto mb-4 border border-gray-200 select-none';
                            const context = canvas.getContext('2d');
                            canvas.height = viewport.height;
                            canvas.width = viewport.width;

                            container.appendChild(canvas);

                            const renderContext = {
                                canvasContext: context,
                                viewport: viewport
                            };
                            page.render(renderContext);
                        });
                    }
                    this.pdfLoading = false;
                }).catch(err => {
                    console.error('Error cargando PDF:', err);
                    this.pdfError = 'Error al leer el archivo. Es posible que la ruta sea inválida o esté protegida.';
                    this.pdfLoading = false;
                });

            } catch (error) {
                console.error('Error JS:', error);
                this.pdfError = error.message;
                this.pdfLoading = false;
            }
        }
    }" x-cloak class="w-full h-full">


    {{-- VISTA MODAL SEGURA CON CSS PERSONALIZADO --}}
    <div class="visor_pdf_overlay" x-show="showModal" style="display: none;">
        <div class="visor_pdf_container">
            
            {{-- Cabecera del Visor --}}
            <div class="visor_pdf_header">
                <h3>
                    <i class="fa-solid fa-file-pdf"></i>
                    <span x-text="tituloReglamento"></span>
                </h3>
                <button x-on:click="showModal = false" class="visor_pdf_close">&times;</button>
            </div>

            {{-- Cuerpo del Visor --}}
            <div class="visor_pdf_body" id="pdf-viewer-wrapper" oncontextmenu="return false;">
                
                {{-- Loader --}}
                <div class="visor_pdf_alerta" x-show="pdfLoading" style="display: none;">
                    <i class="fa-solid fa-spinner fa-spin" style="color: #ffb001; font-size: 24px; margin-bottom: 8px;"></i>
                    <p>Preparando vista segura del documento...</p>
                </div>

                {{-- Error --}}
                <div class="visor_pdf_alerta visor_pdf_error" x-show="pdfError" style="display: none;">
                    <i class="fa-solid fa-triangle-exclamation" style="font-size: 24px; margin-bottom: 8px;"></i>
                    <p x-text="pdfError"></p>
                </div>

                {{-- Contenedor de Canvas --}}
                <div id="pdf-render-container" wire:ignore></div>
            </div>

            {{-- Pie del Visor --}}
            <div class="visor_pdf_footer">
                <i class="fa-solid fa-shield-halved"></i> Modo de visualización protegida. Descargas e impresiones deshabilitadas.
            </div>
        </div>
    </div>

    {{-- CABECERA CON FILTRO --}}
    <div class="reglamentos_header">
        <div class="reglamentos_header_info">
            <h2 class="reglamentos_titulo">
                <i class="fa-solid fa-scale-balanced"></i>
                Reglamentos
            </h2>
            <p class="reglamentos_subtitulo">Documentos reglamentarios asignados a tus proyectos</p>
        </div>

        @if (count($proyectos) > 1)
        <div class="reglamentos_filtro">
            <label for="filtro_proyecto" class="filtro_label">
                <i class="fa-solid fa-filter"></i> Filtrar por proyecto
            </label>
            <select id="filtro_proyecto"
                    wire:model.live="proyectoFiltro"
                    class="filtro_select">
                <option value="">Todos los proyectos</option>
                @foreach ($proyectos as $id => $nombre)
                    <option value="{{ $id }}">{{ $nombre }}</option>
                @endforeach
            </select>
        </div>
        @endif
    </div>

    {{-- ALERTAS DE SESIÓN --}}
    @if (session()->has('info'))
        <div class="g_alerta info">
            <i class="fa-solid fa-circle-info"></i>
            <p>{{ session('info') }}</p>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="g_alerta danger">
            <i class="fa-solid fa-circle-exclamation"></i>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- LOADER LIVEWIRE --}}
    <div wire:loading.block class="reglamentos_loading">
        <i class="fa-solid fa-spinner fa-spin"></i> Cargando reglamentos...
    </div>

    {{-- GRID DE REGLAMENTOS --}}
    <div class="reglamentos_grid" wire:loading.class="reglamentos_grid--loading">
        @forelse ($reglamentos as $reglamento)
            <div class="reglamento_card"
                @click="abrirPdf('{{ $reglamento->archivoPdf ? route('cliente.reglamento.stream', $reglamento->id) : '#' }}', {{ $reglamento->id }}, '{{ addslashes($reglamento->titulo) }}')"
                title="{{ $reglamento->titulo }}">

                <div class="reglamento_icon">
                    <i class="fa-solid fa-file-pdf"></i>
                </div>

                <div class="reglamento_content">
                    <h3 class="reglamento_title">{{ $reglamento->titulo }}</h3>
                    <span class="reglamento_proyecto_badge">
                        <i class="fa-solid fa-building"></i>
                        {{ $reglamento->proyecto->nombre ?? 'Proyecto' }}
                    </span>
                    @if ($reglamento->descripcion)
                        <p class="reglamento_description">
                            {{ Str::limit($reglamento->descripcion, 120) }}
                        </p>
                    @endif
                </div>

                <div class="reglamento_action">
                    @if ($reglamento->archivoPdf)
                        <span class="reglamento_link">
                            Ver PDF <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </span>
                    @else
                        <span class="reglamento_link reglamento_link--disabled">
                            Sin archivo disponible
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="g_alerta info reglamentos_empty">
                <i class="fa-solid fa-circle-info"></i>
                <p>No hay reglamentos disponibles
                    @if ($proyectoFiltro)
                        para el proyecto seleccionado.
                    @else
                        para tus proyectos en este momento.
                    @endif
                </p>
            </div>
        @endforelse
    </div>

    @if ($reglamentos->count() > 0)
        <p class="reglamentos_contador">
            {{ $reglamentos->count() }} {{ $reglamentos->count() === 1 ? 'reglamento' : 'reglamentos' }} encontrado{{ $reglamentos->count() === 1 ? '' : 's' }}
        </p>
    @endif

    <style>
        /* ─── MODAL VISOR PDF (ESTILOS PROTEGIDOS) ──────────────── */
        .visor_pdf_overlay {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            background-color: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(4px);
            z-index: 99999 !important; /* Forzamos que quede sobre todo */
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .visor_pdf_container {
            background: #ffffff;
            border-radius: 12px;
            width: 100%;
            max-width: 950px;
            height: 90vh; /* Altura fija para el modal */
            display: flex;
            flex-direction: column;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            overflow: hidden;
        }

        .visor_pdf_header {
            padding: 16px 24px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .visor_pdf_header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #1a1a2e;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .visor_pdf_header h3 i {
            color: #ffb001;
        }

        .visor_pdf_close {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #9e9e9e;
            transition: color 0.2s;
            line-height: 1;
        }

        .visor_pdf_close:hover {
            color: #dc3545;
        }

        .visor_pdf_body {
            flex: 1;
            overflow-y: auto;
            background: #e0e0e0;
            padding: 24px;
            position: relative;
        }

        .visor_pdf_alerta {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 24px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
            color: #616161;
            font-weight: 500;
            width: 80%;
            max-width: 400px;
        }

        .visor_pdf_error {
            color: #dc3545;
            border-left: 4px solid #dc3545;
        }

        .visor_pdf_footer {
            padding: 12px 24px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            user-select: none;
        }

        #pdf-render-container canvas {
            margin: 0 auto 16px auto;
            display: block;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            border-radius: 4px;
            max-width: 100%;
            height: auto !important; /* Para que sea responsivo */
            user-select: none;
            -webkit-user-select: none;
            pointer-events: none;
        }

        @media print {
            body { display: none !important; }
            .visor_pdf_overlay { display: none !important; }
        }

        /* ─── ESTILOS ORIGINALES DE TU VISTA ────────────────────── */
        .reglamentos_header { display: flex; align-items: flex-start; justify-content: space-between; gap: 20px; flex-wrap: wrap; margin-bottom: 24px; }
        .reglamentos_titulo { font-size: 22px; font-weight: 700; color: #1a1a2e; margin: 0 0 4px 0; display: flex; align-items: center; gap: 10px; }
        .reglamentos_titulo i { color: #ffb001; }
        .reglamentos_subtitulo { font-size: 13px; color: #9e9e9e; margin: 0; }
        .reglamentos_filtro { display: flex; flex-direction: column; gap: 6px; min-width: 240px; }
        .filtro_label { font-size: 12px; font-weight: 600; color: #616161; text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 6px; }
        .filtro_select { width: 100%; padding: 9px 14px; border: 1.5px solid #e0e0e0; border-radius: 8px; font-size: 14px; color: #212121; background: white; cursor: pointer; transition: border-color 0.2s ease, box-shadow 0.2s ease; appearance: auto; }
        .filtro_select:focus { outline: none; border-color: #ffb001; box-shadow: 0 0 0 3px rgba(255, 176, 1, 0.12); }
        .reglamentos_loading { text-align: center; padding: 40px; color: #9e9e9e; font-size: 15px; }
        .reglamentos_grid--loading { opacity: 0.5; pointer-events: none; }
        .reglamentos_grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; transition: opacity 0.2s ease; }
        .reglamentos_empty { grid-column: 1 / -1; }
        .reglamento_card { background: white; border: 1.5px solid #e8e8e8; border-radius: 12px; padding: 22px; cursor: pointer; transition: all 0.25s ease; display: flex; flex-direction: column; gap: 14px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04); position: relative; overflow: hidden; }
        .reglamento_card::before { content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: #e8e8e8; transition: background 0.25s ease; }
        .reglamento_card:hover { border-color: #ffb001; box-shadow: 0 6px 20px rgba(255, 176, 1, 0.12); transform: translateY(-3px); }
        .reglamento_card:hover::before { background: #ffb001; }
        .reglamento_icon { font-size: 40px; color: #ffb001; line-height: 1; }
        .reglamento_content { flex: 1; display: flex; flex-direction: column; gap: 8px; }
        .reglamento_title { font-size: 16px; font-weight: 700; color: #212121; margin: 0; line-height: 1.4; }
        .reglamento_proyecto_badge { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 600; color: #163f49; background: #dceef1; padding: 3px 10px; border-radius: 20px; width: fit-content; }
        .reglamento_description { font-size: 13px; color: #9e9e9e; margin: 0; line-height: 1.5; }
        .reglamento_action { padding-top: 14px; border-top: 1px solid #f5f5f5; }
        .reglamento_link { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; color: #ffb001; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; transition: gap 0.2s ease; }
        .reglamento_card:hover .reglamento_link { gap: 10px; }
        .reglamento_link--disabled { color: #bdbdbd; cursor: default; }
        .reglamentos_contador { margin-top: 16px; font-size: 12px; color: #bdbdbd; text-align: right; }
        @media (max-width: 768px) { .reglamentos_header { flex-direction: column; } .reglamentos_filtro { width: 100%; min-width: unset; } .reglamentos_grid { grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 14px; } }
        @media (max-width: 480px) { .reglamentos_grid { grid-template-columns: 1fr; } }
    </style>
</div>

@assets
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
@endassets
