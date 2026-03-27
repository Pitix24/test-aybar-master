<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Tu Pase de Entrada - {{ $evento->nombre }}</title>
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
                    <!-- HEADER GRADIENTE -->
                    <tr>
                        <td align="center"
                            style="background: linear-gradient(135deg, #f8cc00 0%, #ff7e33 100%); padding: 50px 30px 40px;">
                            <img src="https://aybarcorp.com/public/assets/entregafest/logo-entrega-fest-blanco.png"
                                alt="Entrega Fest"
                                style="width: 260px; max-width: 90%; height: auto; display: block; margin-bottom: 20px;">
                            <h1
                                style="margin: 0; font-size: 22px; color: #004d55; text-transform: uppercase; letter-spacing: 1px; font-weight: 700;">
                                Tu Pase de Entrada 🎫
                            </h1>
                        </td>
                    </tr>

                    <!-- CUERPO -->
                    <tr>
                        <td style="padding: 40px; text-align: center;">
                            <h2 style="margin: 0 0 10px 0; color: #333333; font-size: 24px; font-weight: 700;">
                                ¡Hola, {{ $nombrePersona }}!
                            </h2>
                            <p style="margin: 0 0 30px 0; color: #555555; font-size: 16px; line-height: 1.6;">
                                Tu asistencia al evento <strong>{{ $evento->nombre }}</strong> ha sido confirmada. Aquí
                                tienes tu pase oficial de ingreso:
                            </p>

                            <!-- TICKET BOX -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="0"
                                style="background-color: #ffffff; border: 2px dashed #004d55; border-radius: 25px; margin-bottom: 35px; padding: 30px;">
                                <tr>
                                    <td align="center">
                                        <p
                                            style="margin: 0 0 20px 0; font-size: 11px; text-transform: uppercase; font-weight: 700; color: #004d55; letter-spacing: 1px;">
                                            Presenta este código al ingresar</p>

                                        <div
                                            style="background: #ffffff; padding: 15px; border-radius: 20px; display: inline-block; border: 1px solid #eeeeee; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ $invitado->codigo_invitado }}"
                                                alt="QR Access" style="width: 200px; height: 200px; display: block;">
                                        </div>

                                        <p
                                            style="margin: 20px 0 0 0; font-size: 26px; font-weight: 800; color: #004d55; letter-spacing: 5px;">
                                            {{ $invitado->codigo_invitado }}</p>
                                    </td>
                                </tr>
                            </table>

                            <!-- DETALLES -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="0"
                                style="background-color: #f8f9fa; border-radius: 20px; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 20px 25px;">
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="padding-bottom: 15px; border-bottom: 1px solid #eeeeee;">
                                                    <span
                                                        style="font-size: 12px; color: #888888; text-transform: uppercase;">Proyecto</span>
                                                    <span
                                                        style="float: right; font-weight: 700; color: #333333;">{{ $proyecto }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 15px 0; border-bottom: 1px solid #eeeeee;">
                                                    <span
                                                        style="font-size: 12px; color: #888888; text-transform: uppercase;">Lote
                                                        / Manzana</span>
                                                    <span
                                                        style="float: right; font-weight: 700; color: #333333;">{{ $lote }}
                                                        / {{ $manzana }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 15px 0; border-bottom: 1px solid #eeeeee;">
                                                    <span
                                                        style="font-size: 12px; color: #888888; text-transform: uppercase;">Acompañantes</span>
                                                    <span
                                                        style="float: right; font-weight: 700; color: #333333;">{{ $invitado->cantidad_acompanantes_permitidos }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-top: 15px;">
                                                    <span
                                                        style="font-size: 12px; color: #888888; text-transform: uppercase;">Transporte</span>
                                                    <span
                                                        style="float: right; font-weight: 700; color: #333333;">{{ $invitado->transporte === 'bus' ? 'Bus Aybar' : 'Propio' }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p
                                style="margin: 0; color: #d32f2f; font-size: 14px; font-weight: 700; background: #fff1f2; padding: 12px; border-radius: 10px; display: inline-block;">
                                ⚠️ No olvides llevar tu DNI físico el día del evento.
                            </p>
                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td align="center"
                            style="background-color: #f1f3f5; padding: 30px; border-top: 1px solid #eeeeee;">
                            <img src="https://aybarcorp.com/public/assets/entregafest/logo-aybar-corp-fondo-blanco.png"
                                alt="Aybar Corp"
                                style="width: 100px; margin-bottom: 10px; filter: grayscale(1); opacity: 0.5;">
                            <p style="margin: 0; color: #aaaaaa; font-size: 12px;">
                                &copy; {{ date('Y') }} Aybar Corp. Todos los derechos reservados.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>