<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Restablecer contraseña - Aybar Corp</title>
</head>

<body style="margin:0; padding:25px; font-family: Arial, sans-serif;">
    <div style="
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

        <h2 style="color:#02424e; margin-top:0; font-size:24px;">
            ¡Hola {{ $user->name }}!
        </h2>

        <p style="font-size:16px; line-height:1.6; margin:0 0 15px;">
            Recibimos una solicitud para restablecer tu contraseña. Haz clic en el siguiente botón para crear una nueva:
        </p>

        <div style="text-align:center; margin:30px 0;">
            <a href="{{ $url }}" target="_blank" rel="noopener"
                style="background-color:#02424e; color:#ffffff !important; padding:14px 28px; text-decoration:none !important; border-radius:10px; font-size:16px; font-weight:bold; display:inline-block;">
                Restablecer contraseña
            </a>
        </div>

        <p style="font-size:15px; line-height:1.6; margin-bottom:25px;">
            Si no solicitaste este cambio, puedes ignorar este mensaje.
        </p>

        <p style="font-size:15px; color:#666; margin-bottom:0;">
            Saludos,<br>
            <strong>Aybar Corp</strong>
        </p>
    </div>
</body>

</html>