@extends('layouts.web.layout-web')

@section('contenido')
    <div class="layout_cliente">
        <div class="g_centrar_pagina">
            @if (session('bienvenida_cliente'))
                <x-modal :open="true" max-width="600px">
                    <x-slot:titulo>
                        <h2 class="r_titulo_1 minimo left color_1">{{ session('bienvenida_cliente') }}</h2>
                    </x-slot:titulo>

                    <x-slot:cuerpo>
                        @include('modules.cliente.modal.bienvenida-cliente')
                    </x-slot:cuerpo>
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
