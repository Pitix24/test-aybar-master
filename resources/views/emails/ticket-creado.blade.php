<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Nuevo Ticket - Aybar Corp</title>
</head>

<body style="margin:0; padding:25px; font-family: Arial, sans-serif;">

    <div
        style="
    max-width:580px;
    margin:0 auto;
    background:#ffffff;
    border-radius:16px;
    padding:35px;
    color:#333333;
">

        <div style="text-align:center;">
            <img src="https://aybarcorp.com/public/logo-aybar-corp-verde.png" alt="Aybar Corp"
                style="width:160px; margin-bottom:20px;">
        </div>

        <h2 style="color:#02424e; margin-top:0;">
            Nuevo ticket creado
        </h2>

        <p style="font-size:16px; line-height:1.6;">
            Se ha creado un nuevo ticket con la siguiente información:
        </p>

        <ul style="font-size:15px; line-height:1.6;">
            <li><strong>ID:</strong> #{{ $ticket->id }}</li>
            <li><strong>Área:</strong> {{ $ticket->area->nombre }}</li>
            <li><strong>Gestor:</strong> {{ $ticket->gestor->name }}</li>
            <li><strong>Cliente:</strong> {{ $ticket->nombres }}</li>
            <li><strong>DNI:</strong> {{ $ticket->dni }}</li>
            <li><strong>Asunto:</strong> {{ $ticket->asunto_inicial }}</li>
            <li><strong>Descripción:</strong> {{ $ticket->descripcion_inicial }}</li>
        </ul>

        <div style="text-align:center; margin:30px 0;">
            <a href="{{ $url }}" target="_blank"
                style="
                background-color:#02424e;
                color:#ffffff !important;
                padding:14px 28px;
                text-decoration:none;
                border-radius:10px;
                font-size:16px;
                font-weight:bold;
            ">
                Ver ticket
            </a>
        </div>
       
        <!-- FOOTER -->
        <p style="font-size:15px; color:#666; margin-bottom:0;">
            Saludos,<br>
            <strong>Aybar Corp</strong>
        </p>
    </div>

</body>

</html>
