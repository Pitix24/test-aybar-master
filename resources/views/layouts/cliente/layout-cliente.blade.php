@extends('layouts.web.layout-web')

@section('contenido')
    <div class="layout_cliente">
        <div class="g_centrar_pagina">
            @if (session('bienvenida_cliente'))
                <x-modal maxWidth="600px" :title="session('bienvenida_cliente')">
                    @include('modules.cliente.modal.bienvenida-cliente')

                    <x-slot:pie>
                        <button type="button" @click="open = false" class="g_boton guardar">
                            ¡ENTENDIDO!
                        </button>
                    </x-slot:pie>
                </x-modal>
            @endif
            <div class="grid_layout_cliente">
                <aside class="contenedor_nav_links">
                    <div class="g_pading_pagina">
                        <div class="g_gap_pagina g_margin_top_40 g_margin_bottom_40">
                            @include('layouts.cliente.menu-cliente')
                        </div>
                    </div>
                </aside>

                <div class="contenido_pagina">
                    <div class="g_pading_pagina">
                        <div class="g_gap_pagina g_margin_top_40 g_margin_bottom_40">
                            @yield('contenidoCliente')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection