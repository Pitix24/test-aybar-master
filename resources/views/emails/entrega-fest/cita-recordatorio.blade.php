<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>✅ Cita de Firma Confirmada - {{ $evento->nombre }}</title>
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
                                alt="Aybar Corp" style="width: 100px; margin-bottom: 25px; opacity: 0.8;">
                            <br>
                            <img src="https://aybarcorp.com/public/assets/entregafest/logo-entrega-fest-blanco.png"
                                alt="Entrega Fest" style="width: 260px; filter: invert(1) brightness(0.2);">
                        </td>
                    </tr>

                    <!-- CUERPO -->
                    <tr>
                        <td style="padding: 0 50px 40px; text-align: center;">
                            <h2 style="margin: 0 0 10px 0; color: #004d55; font-size: 26px; font-weight: 700;">
                                ¡Cita Confirmada! ✅
                            </h2>
                            <p style="margin: 0 0 30px 0; color: #555555; font-size: 17px;">
                                Hola <strong>{{ $prospecto->nombres }}</strong>, tu cita ha sido agendada con éxito:
                            </p>

                            <!-- FECHA DESTACADA -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="0"
                                style="background-color: #f8f9fa; border: 2px solid #004d55; border-radius: 25px; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 25px;">
                                        <span
                                            style="display: block; font-size: 12px; color: #004d55; text-transform: uppercase; font-weight: 700; margin-bottom: 10px; letter-spacing: 1px;">📅
                                            Tu fecha de cita</span>
                                        <span
                                            style="display: block; font-size: 20px; color: #333333; font-weight: 700;">
                                            {{ ucfirst($fechaFormateada) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>

                            <!-- TARJETA DE INFO DARK -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="0"
                                style="background-color: #004d55; border-radius: 25px; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 30px; text-align: left;">
                                        <h3
                                            style="margin: 0 0 15px 0; color: #ffffff; font-size: 16px; font-weight: 700;">
                                            Datos del registro:</h3>
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td width="35%" style="padding-bottom: 10px;">
                                                    <span
                                                        style="font-size: 12px; color: rgba(255,255,255,0.6); text-transform: uppercase;">Proyecto:</span>
                                                </td>
                                                <td style="padding-bottom: 10px;">
                                                    <span
                                                        style="font-size: 14px; color: #ffffff; font-weight: 700;">{{ $prospecto->proyecto?->nombre ?? 'N/A' }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-bottom: 10px;">
                                                    <span
                                                        style="font-size: 12px; color: rgba(255,255,255,0.6); text-transform: uppercase;">Terreno
                                                        / Mz:</span>
                                                </td>
                                                <td style="padding-bottom: 10px;">
                                                    <span
                                                        style="font-size: 14px; color: #ffffff; font-weight: 700;">{{ $prospecto->lote }}
                                                        {{ $prospecto->manzana }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <span
                                                        style="font-size: 12px; color: rgba(255,255,255,0.6); text-transform: uppercase;">DNI:</span>
                                                </td>
                                                <td>
                                                    <span
                                                        style="font-size: 14px; color: #ffffff; font-weight: 700;">{{ $prospecto->dni }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- RECORDATORIO -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="0"
                                style="background-color: #fff1f2; border-radius: 20px; text-align: left;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0; font-size: 14px; color: #d32f2f; line-height: 1.6;">
                                            <strong>⚠️ Importante:</strong><br>
                                            • Presentarse puntualmente el día de la cita.<br>
                                            • Llevar tu <strong>DNI original</strong> físico.
                                        </p>
                                    </td>
                                </tr>
                            </table>
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