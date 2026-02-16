<div class="informacion_contenedor">
    @if (session('success'))
        <div class="g_alerta success g_margin_bottom_10">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="g_alerta error g_margin_bottom_10">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ session('error') }}
        </div>
    @endif

    <div>
        <div class="g_resaltado_indicacion info g_margin_bottom_10">
            <i class="fa-solid fa-bolt"></i>
            <div>
                <strong>¿Por qué solicitar la Letra Digital?</strong>
                <p>Evita trámites presenciales y firma tus letras de forma segura desde la comodidad de tu hogar.</p>
            </div>
        </div>

        <ul class="informacion_beneficios_lista">
            <li class="informacion_beneficio_item">
                <i class="fa-solid fa-check-circle"></i> 100% Digital y Seguro
            </li>
            <li class="informacion_beneficio_item">
                <i class="fa-solid fa-check-circle"></i> Sin costos adicionales
            </li>
            <li class="informacion_beneficio_item">
                <i class="fa-solid fa-check-circle"></i> Firma electrónica con CAVALI
            </li>
        </ul>
    </div>

    <div class="g_resaltado_caja info">
        <span class="g_resaltado_caja_titulo">Detalle de la cuota</span>
        <div class="informacion_resumen_grid">
            <div class="informacion_resumen_item">
                <span class="informacion_resumen_label">Proyecto</span>
                <span class="informacion_resumen_valor">{{ $this->lote['descripcion'] }}</span>
            </div>
            <div class="informacion_resumen_item">
                <span class="informacion_resumen_label">Ubicación</span>
                <span class="informacion_resumen_valor">Mz. {{ $this->lote['id_manzana'] }} - Lt.
                    {{ $this->lote['id_lote'] }}</span>
            </div>
            <div class="informacion_resumen_item">
                <span class="informacion_resumen_label">N° Cuota</span>
                <span class="informacion_resumen_valor">{{ $this->cuota["NroCuota"] }}</span>
            </div>
            <div class="informacion_resumen_item">
                <span class="informacion_resumen_label">Vencimiento</span>
                <span class="informacion_resumen_valor">{{ $this->cuota["FecVencimiento"] }}</span>
            </div>
        </div>
    </div>

    <div class="formulario_botones">
        <button wire:click="guardar" class="g_boton guardar g_boton_largo" wire:loading.attr="disabled"
            wire:target="guardar">
            <span wire:loading.remove wire:target="guardar">
                <i class="fa-solid fa-file-contract"></i> SOLICITAR LETRA DIGITAL
            </span>
            <span wire:loading wire:target="guardar">
                <i class="fa-solid fa-spinner fa-spin"></i> PROCESANDO SOLICITUD...
            </span>
        </button>
    </div>
</div>