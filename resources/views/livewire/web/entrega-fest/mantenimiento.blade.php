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
            <div class="ef_badge_type" style="background-color: #C9832A; color: white;">Mantenimiento Temporal</div>
        </div>

        {{-- ============== BODY ============== --}}
        <div class="ef_body">
            <div class="ef_success_body" style="text-align: center; padding: 30px 15px;">

                {{-- Ícono principal --}}
                <div style="font-size: 70px; color: #C9832A; margin-bottom: 20px; line-height: 1;">
                    <i class="fa-solid fa-screwdriver-wrench"></i>
                </div>

                {{-- Título --}}
                <h2 class="ef_success_title" style="margin-bottom: 15px;">
                    Estimado cliente
                </h2>

                {{-- Mensaje principal --}}
                <p class="ef_success_text"
                   style="font-size: 1.1rem; line-height: 1.6; color: #4b5563; margin-bottom: 25px;">
                    Nos encontramos realizando mejoras en el sistema, por lo que el módulo de invitaciones
                    para el Entregafest estará habilitado en breve. Te pedimos confirmar tu asistencia
                    nuevamente en <strong style="color: #004d55;">30 minutos</strong> mediante el mismo enlace.
                </p>

                <div style="margin-top: 25px; padding: 20px; background: #f0fdfa;
                            border: 1px dashed #99f6e4; border-radius: 15px;
                            font-size: 0.95rem; color: #004d55; line-height: 1.6;">
                    <div style="font-size: 32px; margin-bottom: 10px;">
                        <i class="fa-regular fa-clock"></i>
                    </div>
                    <p style="margin: 0; font-weight: 600;">
                        Disculpa las molestias
                    </p>
                    <p style="margin: 8px 0 0 0; font-size: 0.85rem; color: #555;">
                        Tu invitación sigue vigente. Si el problema persiste luego de varios minutos,
                        comunícate con nuestro Call Center al
                        <strong>(01) 904-9838</strong>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
