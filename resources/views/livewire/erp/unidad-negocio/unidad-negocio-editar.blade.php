@section('tituloPagina', 'Editar Unidad de Negocio')

<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Editar Unidad de Negocio</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.unidad-negocio.vista.todo') }}" class="g_boton g_boton_light">
                Inicio <i class="fa-solid fa-house"></i>
            </a>

            <a href="{{ route('erp.unidad-negocio.vista.crear') }}" class="g_boton g_boton_primary">
                Crear <i class="fa-solid fa-square-plus"></i></a>

            <button type="button" class="g_boton g_boton_danger" onclick="alertaEliminarUnidadNegocio()">
                Eliminar <i class="fa-solid fa-trash-can"></i>
            </button>

            <button type="button" class="g_boton g_boton_dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <form wire:submit="update" class="formulario">
        <div class="g_fila">
            <div class="g_columna_12">
                <div class="g_panel" x-data="{ activeTab: 'general' }">

                    <div class="g_tab_navegacion">
                        <div class="g_tab_botones">
                            <button type="button" @click="activeTab = 'general'"
                                :class="activeTab === 'general' ? 'g_tab_active' : 'g_tab_inactive'"
                                class="g_tab_boton">
                                <i class="fa-solid fa-building"></i> Información General
                            </button>

                            <button type="button" @click="activeTab = 'cavali'"
                                :class="activeTab === 'cavali' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                                <i class="fa-solid fa-user-tie"></i> Representante Legal CAVALI
                            </button>
                        </div>
                    </div>

                    <div x-show="activeTab === 'general'" x-transition class="g_tab_content">
                        <div class="g_fila">
                            <div class="g_columna_8">
                                <div class="g_panel">
                                    <div class="g_fila">
                                        <div class="g_margin_bottom_10 g_columna_6">
                                            <label for="nombre">
                                                Nombre <span class="obligatorio"><i
                                                        class="fa-solid fa-asterisk"></i></span>
                                            </label>
                                            <input type="text" id="nombre" wire:model.blur="nombre"
                                                class="@error('nombre') input-error @enderror" autocomplete="off">
                                            @error('nombre')
                                                <p class="mensaje_error">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="g_margin_bottom_10 g_columna_6">
                                            <label for="razon_social">
                                                Razón social <span class="obligatorio"><i
                                                        class="fa-solid fa-asterisk"></i></span>
                                            </label>
                                            <input type="text" id="razon_social" wire:model.blur="razon_social"
                                                class="@error('razon_social') input-error @enderror" autocomplete="off">
                                            @error('razon_social')
                                                <p class="mensaje_error">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="g_fila">
                                        <div class="g_margin_bottom_10 g_columna_6">
                                            <label for="ruc">
                                                RUC
                                            </label>
                                            <input type="text" id="ruc" wire:model.blur="ruc"
                                                class="@error('ruc') input-error @enderror" autocomplete="off">
                                            @error('ruc')
                                                <p class="mensaje_error">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="g_margin_bottom_10 g_columna_6">
                                            <label for="slin_id">
                                                SLIN ID
                                            </label>
                                            <input type="text" id="slin_id" wire:model.blur="slin_id"
                                                class="@error('slin_id') input-error @enderror" autocomplete="off">
                                            @error('slin_id')
                                                <p class="mensaje_error">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="g_columna_4 g_columna_invertir">
                                <div class="g_panel">
                                    <h4 class="g_panel_titulo">Activo</h4>
                                    <div>
                                        <select id="activo" wire:model.live="activo"
                                            class="@error('activo') input-error @enderror">
                                            <option value="0">DESACTIVADO</option>
                                            <option value="1">ACTIVO</option>
                                        </select>
                                        @error('activo')
                                            <p class="mensaje_error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div x-show="activeTab === 'cavali'" x-transition class="g_tab_content">
                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_6">
                                <label for="cavali_girador_tipo_documento">
                                    Tipo de Documento
                                </label>
                                <select id="cavali_girador_tipo_documento"
                                    wire:model.blur="cavali_girador_tipo_documento"
                                    class="@error('cavali_girador_tipo_documento') input-error @enderror">
                                    <option value="">Seleccione...</option>
                                    <option value="DNI">DNI</option>
                                    <option value="CE">Carnet de Extranjería</option>
                                    <option value="PASAPORTE">Pasaporte</option>
                                    <option value="RUC">RUC</option>
                                </select>
                                @error('cavali_girador_tipo_documento')
                                    <p class="mensaje_error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="g_margin_bottom_10 g_columna_6">
                                <label for="cavali_girador_documento">
                                    Número de Documento
                                </label>
                                <input type="text" id="cavali_girador_documento"
                                    wire:model.blur="cavali_girador_documento"
                                    class="@error('cavali_girador_documento') input-error @enderror" autocomplete="off">
                                @error('cavali_girador_documento')
                                    <p class="mensaje_error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_6">
                                <label for="cavali_girador_nombre">
                                    Nombres
                                </label>
                                <input type="text" id="cavali_girador_nombre" wire:model.blur="cavali_girador_nombre"
                                    class="@error('cavali_girador_nombre') input-error @enderror" autocomplete="off">
                                @error('cavali_girador_nombre')
                                    <p class="mensaje_error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="g_margin_bottom_10 g_columna_6">
                                <label for="cavali_girador_apellido">
                                    Apellidos
                                </label>
                                <input type="text" id="cavali_girador_apellido"
                                    wire:model.blur="cavali_girador_apellido"
                                    class="@error('cavali_girador_apellido') input-error @enderror" autocomplete="off">
                                @error('cavali_girador_apellido')
                                    <p class="mensaje_error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_6">
                                <label for="cavali_girador_email">
                                    Email
                                </label>
                                <input type="email" id="cavali_girador_email" wire:model.blur="cavali_girador_email"
                                    class="@error('cavali_girador_email') input-error @enderror" autocomplete="off">
                                @error('cavali_girador_email')
                                    <p class="mensaje_error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="g_margin_bottom_10 g_columna_6">
                                <label for="cavali_girador_telefono">
                                    Teléfono
                                </label>
                                <input type="text" id="cavali_girador_telefono"
                                    wire:model.blur="cavali_girador_telefono"
                                    class="@error('cavali_girador_telefono') input-error @enderror" autocomplete="off">
                                @error('cavali_girador_telefono')
                                    <p class="mensaje_error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="formulario_botones g_tab_form_buttons">
                        <button type="submit" class="g_boton g_boton_guardar" wire:loading.attr="disabled"
                            wire:target="update">
                            <span wire:loading.remove wire:target="update">
                                <i class="fa-solid fa-save"></i> Actualizar
                            </span>
                            <span wire:loading wire:target="update">
                                <i class="fa-solid fa-spinner fa-spin"></i> Actualizando...
                            </span>
                        </button>

                        <a href="{{ route('erp.unidad-negocio.vista.todo') }}" class="g_boton g_boton_cancelar">
                            <i class="fa-solid fa-times"></i> Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @script
    <script>
        window.alertaEliminarUnidadNegocio = function () {
            Swal.fire({
                title: '¿Quieres eliminar?',
                text: "No podrás recuperarlo.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '¡Sí, eliminar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.eliminarUnidadNegocioOn();
                }
            });
        }
    </script>
    @endscript
</div>