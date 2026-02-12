<div>
    @if (session('success'))
        <div class="g_alerta_succes">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="g_alerta_error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ session('error') }}
        </div>
    @endif
    <div class="g_panel_parrafo">
        <p>{{ $this->lote['razon_social'] }}/{{ $this->lote['descripcion'] }}</p>
        <p>Mz. {{ $this->lote['id_manzana'] }}, Lt. {{ $this->lote['id_lote'] }}</p>
        <p>N° Cuota. {{ $this->cuota["NroCuota"] }}</p>
        <p>Fech. Venc. {{ $this->cuota["FecVencimiento"] }}</p>
    </div>

    <div class="formulario_botones">
        <button wire:click="guardar" class="g_boton_personalizado verde">
            Solicita tu letra
        </button>
    </div>
</div>