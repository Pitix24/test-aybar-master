<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Comunicación de Ticket</title>
</head>

<body style="margin:0; padding:25px; font-family: Arial, sans-serif; background-color: #f4f7f6;">
    <div
        style="max-width:580px; margin:0 auto; background:#ffffff; border-radius:16px; padding:35px; color:#333333; border: 1px solid #e1e8ed;">
        <div style="text-align:center;">
            <img src="https://aybarcorp.com/public/logo-aybar-corp-verde.png" alt="Aybar Corp"
                style="width:160px; margin-bottom:20px;">
        </div>
        <h2 style="color:#02424e; margin-top:0; font-size:20px; text-align: center;">
            {{ $asunto }}
        </h2>
        <p style="font-size:15px; line-height:1.6; white-space: pre-line; color: #444;">
            {{ $mensaje }}
        </p>
        <div
            style="background-color: #f9f9f9; padding: 20px; border-radius: 8px; margin: 20px 0; border: 1px solid #eee;">
            <h4 style="margin-top: 0; color: #02424e; font-size: 15px;">Referencia del Ticket:</h4>
            <ul style="font-size:14px; line-height:1.6; list-style: none; padding-left: 0; margin-bottom: 0;">
                <li><strong>Ticket Nro:</strong> #{{ $ticket->id }}</li>
                <li><strong>Empresa:</strong> {{ $ticket->unidadNegocio->nombre ?? 'N/A' }}</li>
                <li><strong>Proyecto:</strong> {{ $ticket->proyecto->nombre ?? 'N/A' }}</li>
                <li><strong>Estado actual:</strong> {{ $ticket->estado->nombre ?? 'N/A' }}</li>
            </ul>
        </div>
        <div style="text-align:center; margin:30px 0;">
            <a href="{{ config('app.url') }}" target="_blank"
                style="background-color:#02424e; color:#ffffff !important; padding:12px 24px; text-decoration:none; border-radius:8px; font-size:15px; font-weight:bold; display: inline-block;">
                Ver detalle en el Portal
            </a>
        </div>
        <p
            style="font-size:12px; color:#999; margin-bottom:0; text-align: center; border-top: 1px solid #eeeeee; padding-top: 20px;">
            Este es un correo automático generado por el sistema de atención de Aybar Corp.<br>
            Por favor, no responda directamente a este mensaje.<br><br>
            Saludos,<br>
            <strong>Equipo de Soporte - Aybar Corp</strong>
        </p>
    </div>
</body>

</html>