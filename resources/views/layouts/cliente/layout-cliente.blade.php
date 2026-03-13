@extends('layouts.web.layout-web')

@section('contenido')
    <div class="layout_cliente">
        @if (session()->has('impersonator_id'))
            <div
                style="background: #1e293b; color: white; padding: 10px; text-align: center; font-size: 0.9rem; border-bottom: 2px solid #ef4444;">
                Usted está visualizando el portal como <strong>{{ auth()->user()->name }}</strong>.
                <a href="{{ route('impersonate.leave') }}"
                    style="color: #ef4444; margin-left: 10px; text-decoration: underline; font-weight: bold;">
                    Regresar al Administrador
                </a>
            </div>
        @endif
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