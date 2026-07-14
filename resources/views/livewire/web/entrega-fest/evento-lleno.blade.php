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
            <div class="ef_badge_type" style="background-color: #004d55; color: white;">Aforo Completo</div>
        </div>

        {{-- ============== BODY ============== --}}
        <div class="ef_body">
            <div class="ef_success_body" style="text-align: center; padding: 30px 15px;">

                {{-- Ícono principal --}}
                <div style="font-size: 70px; color: #f59e0b; margin-bottom: 20px; line-height: 1;">
                    <i class="fa-solid fa-users-slash"></i>
                </div>

                {{-- Título --}}
                <h2 class="ef_success_title" style="margin-bottom: 15px;">
                    ¡Aforo Máximo Alcanzado!
                </h2>

                {{-- Mensaje principal --}}

                <p class="ef_success_text"
                   style="font-size: 1.1rem; line-height: 1.6; color: #4b5563; margin-bottom: 25px;">
                    Lamentablemente, el evento
                    <strong style="color: #004d55;">"{{ $nombreEvento }}"</strong>
                    ha alcanzado el aforo máximo permitido.
                </p>
                <p class="ef_success_text"
                   style="font-size: 1.1rem; line-height: 1.6; color: #4b5563; margin-bottom: 25px;">
                    ¡No te preocupes! La asistencia al evento no es obligatoria.
                    Si ya cancelaste tus pagos, podrás solicitar la documentación correspondiente comunicándote con
                    nuestro Call Center al
                    <strong style="color: #004d55;">(01) 904-9838</strong> .
                </p>

                {{-- Mensaje de invitación a WhatsApp --}}
                <div style="margin-top: 25px; padding: 20px; background: #f0fdfa;
                            border: 1px dashed #99f6e4; border-radius: 15px;
                            font-size: 0.95rem; color: #004d55; line-height: 1.6;">
                    <div style="font-size: 32px; margin-bottom: 10px;">
                        <i class="fa-solid fa-mobile-screen-button"></i>
                    </div>
                    <p style="margin: 0; font-weight: 600;">
                        ¡No te pierdas nuestros próximos eventos!
                    </p>
                    <p style="margin: 8px 0 0 0; font-size: 0.85rem; color: #555;">
                        Te invitamos a mantenerte conectado con las últimas novedades, proyectos y
                        noticias de Aybar Corp a través de nuestro canal oficial de WhatsApp.
                        Únete y sé el primero en enterarte de futuras invitaciones.
                    </p>
                    <a href="https://whatsapp.com/channel/0029Vb8BOQD8qIzvaZQ3sY1S" target="_blank"
                       style="display: inline-block; margin-top: 15px; padding: 12px 20px;
                              background: #25d366; color: white; text-decoration: none;
                              border-radius: 8px; font-weight: 600; box-shadow: 0 4px 6px rgba(37, 211, 102, 0.3);">
                        <i class="fa-brands fa-whatsapp text-lg"></i> Unirme al Canal de WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
