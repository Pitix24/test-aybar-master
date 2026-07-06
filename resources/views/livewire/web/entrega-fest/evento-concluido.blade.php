<div class="ef_page">
    @vite('resources/css/erp/entregafest/invitacion.css')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap');
    </style>

    <div class="ef_container">
        {{-- ============== HEADER ============== --}}
        <div class="ef_header">
            <img src="https://aybarcorp.com/public/assets/entregafest/logo-entrega-fest-blanco.png"
                 alt="Entrega Fest"
                 class="ef_logo_main">
            <div class="ef_badge_type">Edición Finalizada</div>
        </div>

        {{-- ============== BODY ============== --}}
        <div class="ef_body">
            <div class="ef_success_body" style="text-align: center; padding: 30px 15px;">

                {{-- Ícono principal --}}
                <div style="font-size: 70px; color: #004d55; margin-bottom: 20px; line-height: 1;">
                    <i class="fa-solid fa-circle-check"></i>
                </div>

                {{-- Título --}}
                <h2 class="ef_success_title" style="margin-bottom: 15px;">
                    ¡Este evento ha concluido!
                </h2>

                {{-- Mensaje principal --}}
                <p class="ef_success_text"
                   style="font-size: 1.1rem; line-height: 1.6; color: #4b5563; margin-bottom: 25px;">
                    El evento
                    <strong style="color: #004d55;">"{{ $nombreEvento }}"</strong>
                    ha culminado con éxito. Agradecemos profundamente tu interés en formar parte de
                    esta experiencia.
                </p>

                {{-- Tarjeta de información del evento --}}
                <div class="ef_info_card" style="text-align: left; margin-bottom: 25px;">
                    <div class="ef_info_group">
                        <label>Evento</label>
                        <span>{{ $nombreEvento }}</span>
                    </div>

                    @if($fechaEvento)
                        <div class="ef_info_group">
                            <label>Fecha de Realización</label>
                            <span style="text-transform: capitalize;">{{ $fechaEvento }}</span>
                        </div>
                    @endif

                    <div class="ef_info_group">
                        <label>Estado</label>
                        <span style="color: #10b981; font-weight: 700;">
                            <i class="fa-solid fa-flag-checkered"></i> Concluido
                        </span>
                    </div>
                </div>

                {{-- Mensaje de invitación a futuras ediciones --}}
                <div style="margin-top: 25px; padding: 20px; background: #f0fdfa;
                            border: 1px dashed #99f6e4; border-radius: 15px;
                            font-size: 0.95rem; color: #004d55; line-height: 1.6;">
                    <div style="font-size: 32px; margin-bottom: 10px;">
                        <i class="fa-regular fa-calendar-days"></i>
                    </div>
                    <p style="margin: 0; font-weight: 600;">
                        Pronto tendremos nuevas oportunidades
                        para encontrarnos y compartir momentos especiales contigo.
                    </p>
                    <p style="margin: 8px 0 0 0; font-size: 0.85rem; color: #555;">
                        ¡Te invitamos a mantenerte conectado con las últimas novedades, proyectos y
                        noticias de Aybar Corp a través de nuestro canal oficial de WhatsApp!
                        Únete en el siguiente enlace y mantente siempre informado.
                    </p>
                    <a href="https://whatsapp.com/channel/0029Vb8BOQD8qIzvaZQ3sY1S" target="_blank"
                       style="display: inline-block; margin-top: 15px; padding: 12px 20px;
                              background: #25d366; color: white; text-decoration: none;
                              border-radius: 8px; font-weight: 600;">
                        <i class="fa-brands fa-whatsapp"></i> Unirme al WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
