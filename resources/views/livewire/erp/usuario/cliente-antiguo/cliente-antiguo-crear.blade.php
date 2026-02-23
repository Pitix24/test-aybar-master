<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Crear Cliente Antiguo (DB2)</h2>

        <div class="cabecera_titulo_botones">
            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>

            <a href="{{ route('erp.cliente-antiguo.vista.todo') }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_8 g_gap_pagina">
            <div class="g_panel formulario">
                <h4 class="g_panel_titulo">🔍 Búsqueda de Referencia</h4>
                <p class="g_margin_bottom_10"><small>Ingrese el DNI/RUC para verificar si ya existen registros en la
                        base
                        de datos antigua.</small></p>

                <div class="g_fila">
                    <div class="g_columna_8">
                        <label for="dni">DNI / CE / RUC <span class="obligatorio"><i
                                    class="fa-solid fa-asterisk"></i></span></label>
                        <input type="text" id="dni" wire:model.live="dni" class="@error('dni') input-error @enderror"
                            placeholder="Ej: 70654321" autocomplete="off">
                        @error('dni')
                            <p class="mensaje_error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="g_columna_4 centrar_iconos">
                        <label>&nbsp;</label>
                        <button type="button" wire:click="buscarCliente" class="g_boton primary g_columna_12"
                            wire:loading.attr="disabled" wire:target="buscarCliente">
                            <span wire:loading.remove wire:target="buscarCliente">
                                <i class="fa-solid fa-magnifying-glass"></i> Buscar
                            </span>
                            <span wire:loading wire:target="buscarCliente">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            @if ($informaciones && $informaciones->isNotEmpty())
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Registros Encontrados (DB Antigua)</h4>

                    <div class="tabla_contenido">
                        <div class="contenedor_tabla">
                            <table class="g_tabla g_tabla_pequena">
                                <thead>
                                    <tr>
                                        <th>Nº</th>
                                        <th>Razón Social</th>
                                        <th>Proyecto</th>
                                        <th>Etapa</th>
                                        <th>N° Lote</th>
                                        <th>Nombre</th>
                                        <th>Código</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($informaciones as $index => $informacion)
                                        <tr>
                                            <td> {{ $index + 1 }} </td>
                                            <td class="g_negrita g_celda_wrap">{{ $informacion->razon_social }}</td>
                                            <td><span class="g_badge g_badge_light">{{ $informacion->proyecto }}</span>
                                            </td>
                                            <td>{{ $informacion->etapa }}</td>
                                            <td class="g_negrita">{{ $informacion->numero_lote }}</td>
                                            <td class="g_celda_wrap">{{ $informacion->nombre }}</td>
                                            <td class="g_negrita">{{ $informacion->codigo_cliente }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="g_columna_4">
            <form wire:submit="store" class="g_panel formulario">
                <h4 class="g_panel_titulo">Nuevos Datos</h4>

                <div class="g_margin_bottom_10">
                    <label for="razon_social">Razón Social <span class="obligatorio"><i
                                class="fa-solid fa-asterisk"></i></span></label>
                    <input type="text" id="razon_social" wire:model="razon_social"
                        class="@error('razon_social') input-error @enderror" placeholder="LOTES DEL PERU S.A.C"
                        autocomplete="off">
                    @error('razon_social')
                        <p class="mensaje_error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="g_fila">
                    <div class="g_columna_6 g_margin_bottom_10">
                        <label for="codigo_proyecto">Cód. Proyecto</label>
                        <input type="text" id="codigo_proyecto" wire:model="codigo_proyecto"
                            class="@error('codigo_proyecto') input-error @enderror" placeholder="Ej: ADP"
                            autocomplete="off">
                        @error('codigo_proyecto')
                            <p class="mensaje_error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="g_columna_6 g_margin_bottom_10">
                        <label for="proyecto">Proyecto <span class="obligatorio"><i
                                    class="fa-solid fa-asterisk"></i></span></label>
                        <input type="text" id="proyecto" wire:model="proyecto"
                            class="@error('proyecto') input-error @enderror" placeholder="CAMTABRIA LAGOONS"
                            autocomplete="off">
                        @error('proyecto')
                            <p class="mensaje_error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="g_fila">
                    <div class="g_columna_4 g_margin_bottom_10">
                        <label for="etapa">Etapa <span class="obligatorio"><i
                                    class="fa-solid fa-asterisk"></i></span></label>
                        <input type="number" id="etapa" wire:model="etapa"
                            class="@error('etapa') input-error @enderror" placeholder="1" autocomplete="off">
                        @error('etapa')
                            <p class="mensaje_error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="g_columna_4 g_margin_bottom_10">
                        <label for="lote">Mza-Lote <span class="obligatorio"><i
                                    class="fa-solid fa-asterisk"></i></span></label>
                        <input type="text" id="lote" wire:model="lote"
                            class="@error('lote') input-error @enderror" placeholder="1" autocomplete="off">
                        @error('lote')
                            <p class="mensaje_error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="g_columna_4 g_margin_bottom_10">
                        <label for="estado_lote">Estado Lote</label>
                        <input type="text" id="estado_lote" wire:model="estado_lote"
                            class="@error('estado_lote') input-error @enderror" placeholder="VENDIDO"
                            autocomplete="off">
                        @error('estado_lote')
                            <p class="mensaje_error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="g_margin_bottom_10">
                    <label for="nombre">Nombre Completo <span class="obligatorio"><i
                                class="fa-solid fa-asterisk"></i></span></label>
                    <input type="text" id="nombre" wire:model="nombre" class="@error('nombre') input-error @enderror"
                        placeholder="YEP TAY FELIX WINGNAM" autocomplete="off">
                    @error('nombre')
                        <p class="mensaje_error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="g_margin_bottom_20">
                    <label for="codigo">Código Cliente <span class="obligatorio"><i
                                class="fa-solid fa-asterisk"></i></span></label>
                    <input type="text" id="codigo" wire:model="codigo" class="@error('codigo') input-error @enderror"
                        placeholder="CTL1H16" autocomplete="off">
                    @error('codigo')
                        <p class="mensaje_error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="formulario_botones">
                    <button type="submit" class="g_boton guardar g_columna_12" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="store">
                            <i class="fa-solid fa-save"></i> Guardar Registro
                        </span>
                        <span wire:loading wire:target="store">
                            <i class="fa-solid fa-spinner fa-spin"></i> Procesando...
                        </span>
                    </button>
                    <button type="button" class="g_boton cancelar g_columna_12" onclick="history.back()">
                        <i class="fa-solid fa-times"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>