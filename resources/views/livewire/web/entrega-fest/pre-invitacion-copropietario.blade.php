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
            </div>

            <div class="ef_body">
                <div style="text-align: center; margin-bottom: 25px;">
                    <h2 style="margin: 0 0 10px 0; color: #004d55; font-size: 24px; font-weight: 700; text-align: center;">
                        ¡Hola, {{ $copropietario->nombres }}!
                    </h2>
                    <p style="font-size: 1.1rem; color: #555555; line-height: 1.6; text-align: center;">
                        Por favor, complete este formulario solo si desea participar en el evento.
                    </p>
                </div>

                <div class="ef_info_card">
                    <div class="ef_info_group">
                        <label>Proyecto</label>
                        <span>{{ $copropietario->prospecto?->proyecto?->nombre ?? 'N/A' }}</span>
                    </div>
                    <div class="ef_info_group">
                        <label>DNI/RUC/CE</label>
                        <span>{{ $copropietario->dni }}</span>
                    </div>
                    <div class="ef_info_group">
                        <label>Lote/MZ</label>
                        <span>
                            {{ $copropietario->prospecto?->lote ?? '—' }}
                            - {{ $copropietario->prospecto?->manzana ?? '—' }}
                        </span>
                    </div>
                </div>

                <div class="mt-8">
                    <div class="ef_btn_group"
                        style="display: grid; grid-template-columns: 1fr; gap: 15px; padding: 0 10px;">
                        <button wire:click="guardarInteres('si')" class="ef_btn_choice active_si"
                            style="width: 100%; padding: 20px; text-transform: uppercase; font-weight: 800; border: none; cursor: pointer; border-radius: 15px;">
                            Sí, estoy interesado
                        </button>
                        <button wire:click="guardarInteres('no')" class="ef_btn_choice"
                            style="width: 100%; padding: 20px; text-transform: uppercase; font-weight: 800; border: none; cursor: pointer; background: #e5ddd0; color: #8e8271; border-radius: 15px;">
                            No estoy interesado
                        </button>
                    </div>
                </div>

                <p style="margin-top: 30px; text-align: center; font-size: 0.85rem; color: #777; font-style: italic;">
                    *Este correo no es una confirmación de asistencia.
                </p>
            </div>

        @else
            <div class="ef_header">
                <img src="https://aybarcorp.com/public/assets/entregafest/logo-entrega-fest-blanco.png" alt="Entrega Fest"
                    class="ef_logo_main">
                <p class="ef_header_text">¡Registro Recibido!</p>
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
                </div>
            </div>
        @endif
    </div>
</div>