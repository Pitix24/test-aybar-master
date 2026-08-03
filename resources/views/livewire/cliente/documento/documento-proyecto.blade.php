<div>
    <div x-data="{
        showModal: false,
        pdfUrl: '',
        pdfLoading: false,
        pdfError: '',
        tituloDocumento: '',
        soloLectura: false,

        init() {
            window.addEventListener('keydown', (e) => {
                if (this.soloLectura && (e.ctrlKey || e.metaKey) && (e.key === 'p' || e.key === 's' || e.key === 'i' || e.key === 'u')) {
                    e.preventDefault();
                }
            });
        },

        abrirPdf(url, documentoId, titulo, soloLectura) {
            if (!url || url === '#') return;

            if (!soloLectura) {
                // Si no es solo lectura, se abre en una nueva pestaña (comportamiento normal para descargar/ver)
                window.open(url, '_blank');
                $wire.registrarClick(documentoId);
                return;
            }

            // Si es solo lectura, usamos el visor seguro
            this.pdfUrl = url;
            this.tituloDocumento = titulo;
            this.soloLectura = soloLectura;
            this.showModal = true;
            this.renderizarPdf();

            $wire.registrarClick(documentoId);
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
                    <span x-text="tituloDocumento"></span>
                </h3>
                <button x-on:click="showModal = false" class="visor_pdf_close">&times;</button>
            </div>

            {{-- Cuerpo del Visor --}}
            <div class="visor_pdf_body" id="pdf-viewer-wrapper" :oncontextmenu="soloLectura ? 'return false;' : 'return true;'">

                {{-- Loader --}}
                <div class="visor_pdf_alerta" x-show="pdfLoading" style="display: none;">
                    <i class="fa-solid fa-spinner fa-spin" style="color: #3b82f6; font-size: 24px; margin-bottom: 8px;"></i>
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

    {{-- CABECERA --}}
    <div class="documentos_header">
        <div class="documentos_header_info">
            <h2 class="documentos_titulo">
                <i class="fa-solid fa-folder-open"></i>
                Documentos - {{ $proyecto_nombre }}
            </h2>
            <p class="documentos_subtitulo">Archivos y documentos asociados a este proyecto</p>
        </div>
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

    {{-- GRUPOS DE DOCUMENTOS --}}
    @forelse ($this->documentosAgrupados as $tipo)
        <div class="documentos_grupo">
            <h3 class="documentos_grupo_titulo" style="color: {{ $tipo->color ?? '#3b82f6' }}; border-bottom-color: {{ $tipo->color ?? '#3b82f6' }}40;">
                @if($tipo->icono_documentos)
                    <i class="{{ $tipo->icono_documentos }}"></i>
                @endif
                {{ $tipo->nombre }}
            </h3>

            @if($tipo->descripcion)
            <p class="documentos_grupo_desc">{{ $tipo->descripcion }}</p>
            @endif

            <div class="documentos_grid">
                @foreach ($tipo->clienteDocumentos as $doc)
                    <div class="documento_card"
                        @click="abrirPdf('{{ $doc->archivoPdf ? ($doc->solo_lectura ? route('cliente.documentos.stream', $doc->id) : $doc->archivoPdf->url) : '#' }}', {{ $doc->id }}, '{{ addslashes($doc->titulo) }}', {{ $doc->solo_lectura ? 'true' : 'false' }})"
                        title="{{ $doc->titulo }}">

                        <div class="documento_icon" style="color: {{ $tipo->color ?? '#3b82f6' }}">
                            @if($doc->icono)
                                <i class="{{ $doc->icono }}"></i>
                            @else
                                <i class="fa-solid fa-file-pdf"></i>
                            @endif
                        </div>

                        <div class="documento_content">
                            <h4 class="documento_title">{{ $doc->titulo }}</h4>
                            @if ($doc->descripcion)
                                <p class="documento_description">
                                    {{ Str::limit($doc->descripcion, 120) }}
                                </p>
                            @endif
                        </div>

                        <div class="documento_action">
                            @if ($doc->archivoPdf)
                                @if($doc->solo_lectura)
                                <span class="documento_link" style="color: {{ $tipo->color ?? '#3b82f6' }}">
                                    Ver PDF <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                </span>
                                @else
                                <span class="documento_link" style="color: #10b981;">
                                    Descargar PDF <i class="fa-solid fa-download"></i>
                                </span>
                                @endif
                            @else
                                <span class="documento_link documento_link--disabled">
                                    Sin archivo disponible
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="g_alerta info">
            <i class="fa-solid fa-circle-info"></i>
            <p>No hay documentos disponibles para este proyecto en este momento.</p>
        </div>
    @endforelse

    <style>
        /* ─── MODAL VISOR PDF (ESTILOS PROTEGIDOS) ──────────────── */
        .visor_pdf_overlay { position: fixed !important; top: 0 !important; left: 0 !important; width: 100vw !important; height: 100vh !important; background-color: rgba(0, 0, 0, 0.85); backdrop-filter: blur(4px); z-index: 99999 !important; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .visor_pdf_container { background: #ffffff; border-radius: 12px; width: 100%; max-width: 950px; height: 90vh; display: flex; flex-direction: column; box-shadow: 0 10px 30px rgba(0,0,0,0.5); overflow: hidden; }
        .visor_pdf_header { padding: 16px 24px; background: #f8f9fa; border-bottom: 1px solid #e9ecef; display: flex; justify-content: space-between; align-items: center; }
        .visor_pdf_header h3 { margin: 0; font-size: 18px; font-weight: 700; color: #1a1a2e; display: flex; align-items: center; gap: 10px; }
        .visor_pdf_header h3 i { color: #3b82f6; }
        .visor_pdf_close { background: none; border: none; font-size: 28px; cursor: pointer; color: #9e9e9e; transition: color 0.2s; line-height: 1; }
        .visor_pdf_close:hover { color: #dc3545; }
        .visor_pdf_body { flex: 1; overflow-y: auto; background: #e0e0e0; padding: 24px; position: relative; }
        .visor_pdf_alerta { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 24px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: center; color: #616161; font-weight: 500; width: 80%; max-width: 400px; }
        .visor_pdf_error { color: #dc3545; border-left: 4px solid #dc3545; }
        .visor_pdf_footer { padding: 12px 24px; background: #f8f9fa; border-top: 1px solid #e9ecef; text-align: center; font-size: 12px; color: #6c757d; user-select: none; }
        #pdf-render-container canvas { margin: 0 auto 16px auto; display: block; box-shadow: 0 4px 12px rgba(0,0,0,0.2); border-radius: 4px; max-width: 100%; height: auto !important; user-select: none; -webkit-user-select: none; pointer-events: none; }
        @media print { body { display: none !important; } .visor_pdf_overlay { display: none !important; } }

        /* ─── ESTILOS DE DOCUMENTOS ────────────────────── */
        .documentos_header { margin-bottom: 30px; }
        .documentos_titulo { font-size: 22px; font-weight: 700; color: #1a1a2e; margin: 0 0 4px 0; display: flex; align-items: center; gap: 10px; }
        .documentos_titulo i { color: #3b82f6; }
        .documentos_subtitulo { font-size: 14px; color: #6b7280; margin: 0; }

        .documentos_grupo { margin-bottom: 40px; background: #ffffff; border-radius: 12px; border: 1px solid #f3f4f6; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
        .documentos_grupo_titulo { font-size: 18px; font-weight: 700; margin: 0 0 10px 0; display: flex; align-items: center; gap: 8px; padding-bottom: 12px; border-bottom: 2px solid; }
        .documentos_grupo_desc { font-size: 14px; color: #6b7280; margin: 0 0 20px 0; }

        .documentos_grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }

        .documento_card { background: white; border: 1.5px solid #e5e7eb; border-radius: 12px; padding: 20px; cursor: pointer; transition: all 0.25s ease; display: flex; flex-direction: column; gap: 14px; position: relative; overflow: hidden; }
        .documento_card:hover { border-color: #cbd5e1; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); transform: translateY(-3px); }
        .documento_icon { font-size: 32px; line-height: 1; margin-bottom: 5px; }
        .documento_content { flex: 1; display: flex; flex-direction: column; gap: 8px; }
        .documento_title { font-size: 15px; font-weight: 700; color: #1f2937; margin: 0; line-height: 1.4; }
        .documento_description { font-size: 13px; color: #6b7280; margin: 0; line-height: 1.5; }
        .documento_action { padding-top: 14px; border-top: 1px solid #f3f4f6; }
        .documento_link { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; transition: gap 0.2s ease; }
        .documento_card:hover .documento_link { gap: 10px; }
        .documento_link--disabled { color: #9ca3af; cursor: default; }

        @media (max-width: 768px) { .documentos_grid { grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 14px; } .documentos_grupo { padding: 16px; } }
        @media (max-width: 480px) { .documentos_grid { grid-template-columns: 1fr; } }
    </style>
</div>

@assets
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
@endassets
