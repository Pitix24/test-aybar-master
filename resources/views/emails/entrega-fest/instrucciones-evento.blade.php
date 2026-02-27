<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Instrucciones del Evento - {{ $evento->nombre }}</title>
</head>

<body style="margin:0; padding:25px; font-family: Arial, sans-serif; background-color: #f4f7f9;">
    <div
        style="max-width:600px; margin:0 auto; background:#ffffff; border-radius:16px; overflow:hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.08);">

        {{-- Header --}}
        <div
            style="background: linear-gradient(135deg, #1a237e 0%, #0d47a1 100%); padding: 30px; text-align: center; color: white;">
            <img src="https://aybarcorp.com/public/logo-aybar-corp-verde.png" alt="Aybar Corp"
                style="width:160px; margin-bottom:15px;">
            <h1 style="margin:0; font-size:22px; font-weight:700;">{{ $evento->nombre }}</h1>
            <p style="margin:8px 0 0; opacity:0.9; font-size:15px;">📋 Instrucciones para el Evento</p>
        </div>

        {{-- Imagen principal --}}
        <div style="text-align:center; padding: 0;">
            <img src="{{ $imagenUrl }}" alt="Instrucciones del Evento"
                style="width:100%; max-width:600px; display:block; margin:0 auto;">
        </div>

        {{-- Cuerpo --}}
        <div style="padding: 30px;">
            <h2 style="color:#1a237e; margin-top:0; font-size:20px;">
                ¡Hola, {{ $nombrePersona }}! 👋
            </h2>

            <p style="font-size:15px; line-height:1.7; color:#555;">
                Queremos compartirte las instrucciones y detalles importantes para el evento
                <strong style="color:#1a237e;">{{ $evento->nombre }}</strong>.
                Por favor, léelas con atención para que todo salga perfecto el día del evento.
            </p>

            <div style="background:#e8eaf6; border-radius:12px; padding:20px; margin:20px 0;">
                <p style="margin:0 0 8px; font-weight:bold; color:#1a237e; font-size:15px;">
                    ⚠️ Recuerda:
                </p>
                <ul style="margin:0; padding-left:20px; color:#444; font-size:14px; line-height:2;">
                    <li>Llegar <strong>puntualmente</strong> a la hora indicada.</li>
                    <li>Presentar tu <strong>código de invitado / QR</strong> al ingresar.</li>
                    <li>Llevar tu <strong>DNI original</strong>.</li>
                    <li>Cualquier consulta, comunícate con nosotros antes del evento.</li>
                </ul>
            </div>

            <p style="font-size:14px; color:#888; line-height:1.6; text-align:center; margin-top:25px;">
                Si tienes alguna duda, no dudes en contactarnos.<br>
                ¡Te esperamos!
            </p>
        </div>

        {{-- Footer --}}
        <div
            style="background:#f8f9fa; padding:20px; text-align:center; font-size:12px; color:#999; border-top:1px solid #eee;">
            &copy; {{ date('Y') }} Aybar Corp. Todos los derechos reservados.<br>
            Este es un correo automático, por favor no lo respondas.
        </div>
    </div>
</body>

</html>