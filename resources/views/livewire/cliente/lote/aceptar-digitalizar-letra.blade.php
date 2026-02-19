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

    <div class="g_resaltado_caja info g_margin_bottom_10">
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

    @if (Auth::user()->rol === 'admin')
        <div class="g_panel g_margin_bottom_10">
            <div class="g_panel_titulo">Información del Cliente (Modo Admin)</div>

            <div class="formulario">

                <p class="g_resaltado_indicacion info">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>
                        Como administrador, puede <strong>completar o corregir los datos</strong> de contacto del cliente
                        antes de enviar la solicitud.
                    </span>
                </p>

                <div class="g_fila g_margin_bottom_10">
                    <div class="g_columna_12">
                        <label for="nombres">Nombres completos</label>
                        <input type="text" id="nombres" wire:model="nombres"
                            class="g_input @error('nombres') input-error @enderror">
                        @error('nombres')
                            <p class="mensaje_error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="g_fila g_margin_bottom_10">
                    <div class="g_columna_6">
                        <label for="dni">DNI/RUC/CE</label>
                        <input type="text" id="dni" wire:model="dni" class="g_input @error('dni') input-error @enderror">
                        @error('dni')
                            <p class="mensaje_error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="g_columna_6">
                        <label for="celular">Celular</label>
                        <input type="text" id="celular" wire:model="celular"
                            class="g_input @error('celular') input-error @enderror">
                    </div>
                </div>

                <div class="g_fila g_margin_bottom_10">
                    <div class="g_columna_12">
                        <label for="email">Email</label>
                        <input type="email" id="email" wire:model="email"
                            class="g_input @error('email') input-error @enderror">
                    </div>
                </div>

                <div class="g_fila g_margin_bottom_10">
                    <div class="g_columna_12">
                        <label for="direccion">Dirección</label>
                        <input type="text" id="direccion" wire:model="direccion"
                            class="g_input @error('direccion') input-error @enderror">
                    </div>
                </div>
            </div>
        </div>
    @endif

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