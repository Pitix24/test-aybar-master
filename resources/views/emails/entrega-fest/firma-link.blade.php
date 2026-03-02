<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Agenda tu Cita de Firma - {{ $evento->nombre }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap');
    </style>
</head>

<body style="margin:0; padding:0; font-family: 'Outfit', Helvetica, Arial, sans-serif; background-color: #f8f9fa;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0"
        style="background-color: #f8f9fa; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="100%" border="0" cellspacing="0" cellpadding="0"
                    style="max-width: 600px; background-color: #ffffff; border-radius: 40px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    <!-- CABECERA CON LOGOS -->
                    <tr>
                        <td align="center" style="padding: 40px 30px 20px;">
                            <img src="https://aybarcorp.com/public/assets/entregafest/logo-aybar-corp-fondo-blanco.png"
                                alt="Aybar Corp" style="width: 120px; margin-bottom: 25px; opacity: 0.8;">
                            <br>
                            <img src="https://aybarcorp.com/public/assets/entregafest/logo-entrega-fest-blanco.png"
                                alt="Entrega Fest" style="width: 280px; filter: invert(1) brightness(0.2);">
                        </td>
                    </tr>

                    <!-- CUERPO -->
                    <tr>
                        <td style="padding: 0 50px 40px; text-align: center;">
                            <h2 style="margin: 0 0 10px 0; color: #004d55; font-size: 28px; font-weight: 700;">
                                ¡Hola, {{ $prospecto->nombres }}!
                            </h2>
                            <p style="margin: 0 0 30px 0; color: #555555; font-size: 18px; font-weight: 600;">
                                Tu contrato definitivo ya está listo ✅
                            </p>

                            <!-- TARJETA DE INFO DARK -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="0"
                                style="background-color: #004d55; border-radius: 25px; margin-bottom: 30px; box-shadow: 0 10px 20px rgba(0,77,85,0.2);">
                                <tr>
                                    <td style="padding: 30px; text-align: left;">
                                        <h3
                                            style="margin: 0 0 15px 0; color: #ffffff; font-size: 18px; font-weight: 700;">
                                            Datos de tu terreno:</h3>
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td width="35%" style="padding-bottom: 10px;">
                                                    <span
                                                        style="font-size: 13px; color: rgba(255,255,255,0.7); text-transform: uppercase;">Proyecto:</span>
                                                </td>
                                                <td style="padding-bottom: 10px;">
                                                    <span
                                                        style="font-size: 15px; color: #ffffff; font-weight: 700;">{{ $prospecto->proyecto?->nombre ?? 'N/A' }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <span
                                                        style="font-size: 13px; color: rgba(255,255,255,0.7); text-transform: uppercase;">Terreno
                                                        / Mz:</span>
                                                </td>
                                                <td>
                                                    <span
                                                        style="font-size: 15px; color: #ffffff; font-weight: 700;">{{ $prospecto->lote }}
                                                        {{ $prospecto->manzana }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 30px 0; color: #444444; font-size: 16px; line-height: 1.6;">
                                Por favor, selecciona la fecha y hora de tu preferencia para la firma de tu contrato.
                            </p>

                            <!-- BOTÓN AMBAR -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $link }}" target="_blank"
                                            style="background: linear-gradient(135deg, #f8cc00 0%, #ff7e33 100%); color: #ffffff !important; padding: 20px 45px; text-decoration: none; border-radius: 25px; font-size: 20px; font-weight: 700; display: inline-block; box-shadow: 0 8px 15px rgba(255,126,51,0.3);">
                                            📅 Agendar mi Cita
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 40px 0 0 0; color: #999999; font-size: 13px; line-height: 1.5;">
                                Si el botón no funciona, puedes copiar y pegar este enlace en tu navegador:<br>
                                <a href="{{ $link }}" style="color: #004d55; word-break: break-all;">{{ $link }}</a>
                            </p>
                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td align="center"
                            style="padding: 30px; border-top: 1px solid #eeeeee; background-color: #fcfcfc;">
                            <p style="margin: 0; color: #888888; font-size: 13px;">
                                Saludos cordiales,<br>
                                <strong style="color: #666666;">Equipo Aybar Corp</strong>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>