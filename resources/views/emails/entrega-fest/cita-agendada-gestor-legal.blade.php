@php
    use Carbon\Carbon;

    $prospecto = $prospecto;
    $evento    = $prospecto->entregaFest;

    // Detección de reubicación
    $fueReubicado    = !empty($prospecto->reubicado_manzana) || !empty($prospecto->reubicado_lote);
    $manzana         = $fueReubicado ? $prospecto->reubicado_manzana : $prospecto->manzana;
    $lote            = $fueReubicado ? $prospecto->reubicado_lote    : $prospecto->lote;
    $proyectoActivo  = $fueReubicado && $prospecto->reubicadoProyecto
                          ? $prospecto->reubicadoProyecto
                          : $prospecto->proyecto;
    $sede            = optional($proyectoActivo?->unidadNegocio);

    // Gestor (puede no existir)
    $gestor   = $prospecto->gestorLegal;
    $saludo   = $gestor ? "Estimado(a) {$gestor->name}" : 'Estimado equipo Legal';

    // Fecha amigable
    $fechaFirma = $prospecto->fecha_firma
        ? Carbon::parse($prospecto->fecha_firma)->locale('es')->translatedFormat('l, d \d\e F \d\e Y \a \l\a\s H:i')
        : 'Sin fecha';

    // URL al expediente del prospecto en el ERP (Opción B)
    $urlExpediente = url("/erp/entrega-fest/prospecto/editar/{$prospecto->entrega_fest_id}/{$prospecto->id}");
@endphp

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cita de Contrato Confirmada</title>
</head>
<body style="margin:0;padding:0;background-color:#f2f5f9;font-family:Verdana,Arial,sans-serif;color:#1e293b;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f2f5f9;padding:24px 10px;">
        <tr>
            <td align="center">
                <table role="presentation" width="860" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:860px;background:#ffffff;border:1px solid #d9e2ec;border-collapse:collapse;">

                    {{-- HEADER --}}
                    <tr>
                        <td style="padding:18px 20px;border-bottom:2px solid #8e44ad;background:#ffffff;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="font-size:12px;color:#475569;line-height:1.6;">
                                        <div style="font-size:14px;font-weight:bold;color:#0f172a;">{{ $evento->nombre }}</div>
                                        <div style="margin-top:4px;color:#64748b;">Notificación interna — Área Legal</div>
                                    </td>
                                    <td align="right" style="vertical-align:top;">
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #8e44ad;border-collapse:collapse;min-width:260px;">
                                            <tr>
                                                <td style="padding:8px 10px;background:#8e44ad;color:#ffffff;font-size:12px;font-weight:bold;text-align:center;letter-spacing:.3px;">
                                                    CITA DE CONTRATO CONFIRMADA
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:10px;background:#ffffff;color:#0f172a;font-size:13px;font-weight:bold;text-align:center;">
                                                    Prospecto N° {{ $prospecto->id }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- SALUDO --}}
                    <tr>
                        <td style="padding:18px 20px 6px 20px;font-size:13px;color:#1e293b;line-height:1.6;">
                            <p style="margin:0;">{{ $saludo }},</p>
                            <p style="margin:8px 0 0 0;">
                                Te informamos que el cliente <strong>{{ $prospecto->nombres }}</strong>
                                ha confirmado su cita de firma de contrato. A continuación encontrarás los datos relevantes:
                            </p>
                        </td>
                    </tr>

                    {{-- 1. DATOS DEL CLIENTE --}}
                    <tr>
                        <td style="padding:14px 20px 6px 20px;">
                            <div style="font-size:12px;font-weight:bold;color:#8e44ad;margin-bottom:8px;">1. DATOS DEL CLIENTE</div>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #cbd5e1;border-collapse:collapse;">
                                <tr>
                                    <td style="width:50%;padding:8px 10px;border-right:1px solid #cbd5e1;border-bottom:1px solid #cbd5e1;font-size:12px;">
                                        <strong>Nombre:</strong> {{ $prospecto->nombres }}
                                    </td>
                                    <td style="width:50%;padding:8px 10px;border-bottom:1px solid #cbd5e1;font-size:12px;">
                                        <strong>DNI:</strong> {{ $prospecto->dni }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 10px;border-right:1px solid #cbd5e1;border-bottom:1px solid #cbd5e1;font-size:12px;">
                                        <strong>Email:</strong> {{ $prospecto->email ?: 'N/D' }}
                                    </td>
                                    <td style="padding:8px 10px;border-bottom:1px solid #cbd5e1;font-size:12px;">
                                        <strong>Celular:</strong> {{ $prospecto->celular ?: 'N/D' }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- 2. UBICACIÓN DEL LOTE --}}
                    <tr>
                        <td style="padding:8px 20px 6px 20px;">
                            <div style="font-size:12px;font-weight:bold;color:#8e44ad;margin-bottom:8px;">
                                2. UBICACIÓN DEL LOTE
                                @if($fueReubicado)
                                    <span style="display:inline-block;margin-left:6px;padding:2px 8px;background:#f59e0b;color:#fff;font-size:10px;border-radius:10px;">REUBICADO</span>
                                @endif
                            </div>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #cbd5e1;border-collapse:collapse;">
                                <tr>
                                    <td style="width:33%;padding:8px 10px;border-right:1px solid #cbd5e1;font-size:12px;">
                                        <strong>Proyecto:</strong> {{ $proyectoActivo?->nombre ?? 'N/D' }}
                                    </td>
                                    <td style="width:33%;padding:8px 10px;border-right:1px solid #cbd5e1;font-size:12px;">
                                        <strong>Manzana:</strong> {{ $manzana ?: 'N/D' }}
                                    </td>
                                    <td style="width:34%;padding:8px 10px;font-size:12px;">
                                        <strong>Lote:</strong> {{ $lote ?: 'N/D' }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- 3. CITA DE FIRMA --}}
                    <tr>
                        <td style="padding:8px 20px 6px 20px;">
                            <div style="font-size:12px;font-weight:bold;color:#8e44ad;margin-bottom:8px;">3. CITA DE FIRMA</div>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #cbd5e1;border-collapse:collapse;">
                                <tr>
                                    <td style="padding:10px;background:#f0e6f5;font-size:14px;color:#0f172a;text-align:center;font-weight:bold;border-bottom:1px solid #cbd5e1;">
                                        🗓️ {{ ucfirst($fechaFirma) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 10px;font-size:12px;line-height:1.5;">
                                        <strong>Sede de firma:</strong> {{ $sede->nombre ?? 'N/D' }}<br>
                                        <strong>Dirección:</strong> {{ $sede->direccion ?? 'N/D' }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- 4. BOTÓN VER EXPEDIENTE --}}
                    <tr>
                        <td style="padding:18px 20px;text-align:center;">
                            <a href="{{ $urlExpediente }}"
                            style="display:inline-block;padding:12px 28px;background:#8e44ad;color:#ffffff;text-decoration:none;font-size:13px;font-weight:bold;border-radius:6px;letter-spacing:.3px;">
                                📂 VER EXPEDIENTE EN LA PLATAFORMA
                            </a>
                        </td>
                    </tr>

                    {{-- AVISO IMPORTANTE --}}
                    @if(!$gestor)
                    <tr>
                        <td style="padding:0 20px 12px 20px;">
                            <div style="background:#fef3c7;border:1px dashed #f59e0b;border-radius:6px;padding:10px;color:#92400e;font-size:12px;">
                                ⚠️ <strong>Atención:</strong> Este prospecto aún <strong>no tiene un Gestor Legal asignado</strong>.
                                Por favor coordinen internamente la asignación a la brevedad.
                            </div>
                        </td>
                    </tr>
                    @endif

                    {{-- FOOTER --}}
                    <tr>
                        <td style="padding:12px 20px;background:#f8fafc;border-top:1px solid #e2e8f0;font-size:11px;color:#64748b;line-height:1.5;">
                            Este es un correo automático generado por Plataforma Interna.<br>
                            No responder a este correo, ya que no se encuentra monitoreado.<br>
                            Para cualquier consulta, comunícate con el equipo de soporte interno.<br>
                            <strong>Notificación generada:</strong> {{ now()->format('d/m/Y H:i') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
