<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Pre-invitación - {{ $evento->nombre }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap');
    </style>
</head>

<body style="margin:0; padding:0; font-family: 'Outfit', Helvetica, Arial, sans-serif; background-color: #f8f9fa;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0"
        style="background-color: #f8f9fa; padding: 20px 10px;">
        <tr>
            <td align="center">
                <table width="100%" border="0" cellspacing="0" cellpadding="0"
                    style="max-width: 600px; background-color: #ffffff; border-radius: 40px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    @php
                        $imagenHeader = $plantilla?->getFirstMediaUrl('imagen') ?: $evento->getFirstMediaUrl('imagen_invitacion');
                    @endphp

                    <!-- HEADER DINÁMICO (Se muestra solo si existe imagen) -->
                    @if($imagenHeader)
                        <tr>
                            <td align="center" style="padding: 0;">
                                <img src="{{ $imagenHeader }}" alt="{{ $evento->nombre }}"
                                    style="width: 100%; max-width: 600px; height: auto; display: block;">
                            </td>
                        </tr>
                    @endif

                    <!-- CUERPO -->
                    <tr>
                        <td style="padding: 25px 20px;">
                            <h2
                                style="margin: 0 0 10px 0; color: #004d55; font-size: 24px; font-weight: 700; text-align: center;">
                                ¡Hola, {{ $copropietario->nombres }}!
                            </h2>

                            <p
                                style="margin: 0 0 30px 0; color: #555555; font-size: 15px; line-height: 1.6; text-align: center;">
                                {{ $plantilla->subtitulo ?? 'Te invitamos a completar este formulario si deseas participar en el evento.' }}
                            </p>

                            <!-- TARJETA DE INFO DARK -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="0"
                                style="background-color: #004d55; border-radius: 25px; margin-bottom: 35px; box-shadow: 0 8px 20px rgba(0,77,85,0.2);">
                                <tr>
                                    <td style="padding: 25px 20px;">
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td width="55%" style="padding-bottom: 20px;">
                                                    <span
                                                        style="display: block; font-size: 11px; color: rgba(255,255,255,0.6); text-transform: uppercase; font-weight: 700; margin-bottom: 4px; letter-spacing: 0.5px;">PROYECTO</span>
                                                    <span
                                                        style="display: block; font-size: 16px; color: #ffffff; font-weight: 700;">{{ $copropietario->prospecto->proyecto->nombre ?? 'N/A' }}</span>
                                                </td>
                                                <td width="45%" style="padding-bottom: 20px;">
                                                    <span
                                                        style="display: block; font-size: 11px; color: rgba(255,255,255,0.6); text-transform: uppercase; font-weight: 700; margin-bottom: 4px; letter-spacing: 0.5px;">DNI/RUC/CE</span>
                                                    <span
                                                        style="display: block; font-size: 16px; color: #ffffff; font-weight: 700;">{{ $copropietario->dni }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <span
                                                        style="display: block; font-size: 11px; color: rgba(255,255,255,0.6); text-transform: uppercase; font-weight: 700; margin-bottom: 4px; letter-spacing: 0.5px;">LOTE/MZ</span>
                                                    <span
                                                        style="display: block; font-size: 16px; color: #ffffff; font-weight: 700;">{{ $copropietario->prospecto->lote }}
                                                        {{ $copropietario->prospecto->manzana }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p
                                style="margin: 0 0 30px 0; font-size: 12px; color: #777777; font-style: italic; text-align: left;">
                                *Este correo no es una confirmación de asistencia.
                            </p>

                            <!-- BOTÓN DE ACCIÓN -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="10">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $link }}" target="_blank"
                                            style="background-color: #e68a00; color: #ffffff; padding: 18px 35px; text-decoration: none; border-radius: 15px; font-size: 18px; font-weight: 700; display: inline-block; text-shadow: 0 1px 2px rgba(0,0,0,0.1); box-shadow: 0 4px 15px rgba(230,138,0,0.3);">
                                            Confirmar interés
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p
                                style="margin: 40px 0 0 0; color: #999999; font-size: 12px; text-align: center; line-height: 1.4;">
                                Si el botón no funciona, puedes copiar y pegar este enlace en tu navegador:<br>
                                <a href="{{ $link }}" style="color: #ff7e33; text-decoration: none;">{{ $link }}</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>