<div class="ef_page">
    @vite('resources/css/erp/entregafest/invitacion.css')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap');
    </style>

    <div class="ef_container">
        @if (!$enviado)
            <div class="ef_header">
                <img src="https://aybarcorp.com/public/assets/entregafest/logo-entrega-fest-blanco.png" alt="Entrega Fest"
                    class="ef_logo_main">
                <p class="ef_header_text">Confirmación de Interés en Participar</p>
                <div class="ef_badge_type">Titular del lote</div>
            </div>

            <div class="ef_body">
                <div class="text-center mb-6">
                    <h2 class="text-3xl font-bold text-[#004d55] mb-2" style="font-family: 'Outfit', sans-serif;">¡Hola, {{ $prospecto->nombres }}!</h2>
                    <p class="text-gray-600" style="font-size: 1.1rem;">
                        Queremos confirmar si estás interesado(a) en asistir a nuestro gran evento de entrega.
                    </p>
                </div>

                <div class="ef_info_card">
                    <div class="ef_info_group">
                        <label>Nombre Completo</label>
                        <span>{{ $prospecto->nombres }}</span>
                    </div>
                    <div class="ef_info_group">
                        <label>DNI / Documento</label>
                        <span>{{ $prospecto->dni }}</span>
                    </div>
                    <div class="ef_info_group">
                        <label>Proyecto</label>
                        <span>{{ $prospecto->proyecto?->nombre ?? 'N/A' }}</span>
                    </div>
                    <div class="ef_info_group">
                        <label>Terreno / MZ</label>
                        <span>
                            {{ $prospecto->lote ?? '—' }}
                            - {{ $prospecto->manzana ?? '—' }}
                        </span>
                    </div>
                </div>

                <div class="mt-8">
                    <p class="ef_question" style="text-align: center; margin-bottom: 25px;">¿Confirmas tu interés en participar del {{ $evento->nombre }}?</p>

                    <div class="ef_btn_group" style="display: grid; grid-template-columns: 1fr; gap: 15px; padding: 0 10px;">
                        <button wire:click="guardarInteres('si')" 
                                class="ef_btn_choice active_si" 
                                style="width: 100%; padding: 20px; text-transform: uppercase; font-weight: 800; border: none; cursor: pointer; border-radius: 15px;">
                            SÍ, estoy interesado(a)
                        </button>
                        <button wire:click="guardarInteres('no')" 
                                class="ef_btn_choice" 
                                style="width: 100%; padding: 20px; text-transform: uppercase; font-weight: 800; border: none; cursor: pointer; background: #e5ddd0; color: #8e8271; border-radius: 15px;">
                            NO podré participar
                        </button>
                    </div>
                </div>

                <p style="margin-top: 30px; text-align: center; font-size: 0.85rem; color: #777; font-style: italic;">
                    * Al confirmar, te consideraremos para el aforo y logística del evento.
                </p>
            </div>

        @else
            <div class="ef_header">
                <img src="https://aybarcorp.com/public/assets/entregafest/logo-entrega-fest-blanco.png" alt="Entrega Fest"
                    class="ef_logo_main">
                <p class="ef_header_text">¡Registro Recibido! 🎫</p>
            </div>

            <div class="ef_body">
                <div class="ef_success_body" style="text-align: center; padding: 40px 20px;">
                    <div style="font-size: 60px; color: #10b981; margin-bottom: 20px;">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <h2 class="ef_success_title" style="margin-bottom: 15px;">¡Muchas gracias!</h2>
                    <p class="ef_success_text" style="font-size: 1.1rem; line-height: 1.6; color: #4b5563;">
                        {{ $mensaje_exito }}
                    </p>
                    
                    <div style="margin-top: 40px; opacity: 0.5;">
                        <img src="https://aybarcorp.com/public/assets/entregafest/logo-aybar-corp-fondo-blanco.png" 
                             alt="Aybar Corp" style="width: 120px; filter: grayscale(1);">
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>