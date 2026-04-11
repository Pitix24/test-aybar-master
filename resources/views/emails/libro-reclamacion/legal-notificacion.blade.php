@php
	$proyecto = $reclamo->proyecto;
	$unidad = optional($proyecto)->unidadNegocio;

	$clienteNombre = trim(
		($reclamo->nombre ?? '') . ' ' . ($reclamo->apellido_paterno ?? '') . ' ' . ($reclamo->apellido_materno ?? '')
	);

	$tipoPedidoMap = [
		'RECLAMO' => 'Reclamo',
		'QUEJA' => 'Queja',
		'NO_DEFINIDO' => 'No definido',
	];

	$tipoBienMap = [
		'PRODUCTO' => 'Producto',
		'SERVICIO' => 'Servicio',
		'NO_DEFINIDO' => 'No definido',
	];

	$tipoDocumentoMap = [
		'DNI' => 'DNI',
		'RUC' => 'RUC',
		'CE' => 'Carné de Extranjería',
		'NO_DEFINIDO' => 'No definido',
	];

	$tipoPedido = strtoupper((string) $reclamo->tipo_pedido);
	$tipoBien = strtoupper((string) $reclamo->tipo_bien_contratado);
	$tipoDocumento = strtoupper((string) $reclamo->tipo_documento);

	$formatear = static function (string $valor): string {
		$texto = trim($valor);

		if ($texto === '') {
			return 'N/D';
		}

		return ucwords(strtolower(str_replace('_', ' ', $texto)));
	};

	$marca = static fn (bool $activo): string => $activo ? '[X]' : '[ ]';
@endphp

<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Libro de Reclamaciones</title>
</head>
<body style="margin:0;padding:0;background-color:#f2f5f9;font-family:Verdana,Arial,sans-serif;color:#1e293b;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f2f5f9;padding:24px 10px;">
	<tr>
		<td align="center">
			<table role="presentation" width="860" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:860px;background:#ffffff;border:1px solid #d9e2ec;border-collapse:collapse;">
				<tr>
					<td style="padding:18px 20px;border-bottom:2px solid #0f3d6e;">
						<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td style="font-size:12px;color:#475569;line-height:1.6;">
									<div><strong>PROVEEDOR:</strong> {{ $unidad->razon_social ?? $unidad->nombre ?? 'N/D' }}</div>
									<div><strong>RUC:</strong> {{ $unidad->ruc ?? 'N/D' }}</div>
									<div><strong>DIRECCION:</strong> {{ $unidad->direccion ?? 'N/D' }}</div>
								</td>
								<td align="right" style="vertical-align:top;">
									<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #0f3d6e;border-collapse:collapse;min-width:260px;">
										<tr>
											<td style="padding:8px 10px;background:#0f3d6e;color:#ffffff;font-size:12px;font-weight:bold;text-align:center;letter-spacing:.3px;">
												LIBRO DE RECLAMACIONES
											</td>
										</tr>
										<tr>
											<td style="padding:10px;background:#ffffff;color:#0f172a;font-size:13px;font-weight:bold;text-align:center;">
												HOJA DE RECLAMACION N&deg; {{ $reclamo->codigo_ticket ?? 'N/D' }}
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr>
					<td style="padding:16px 20px 6px 20px;">
						<div style="font-size:12px;font-weight:bold;color:#0f3d6e;margin-bottom:8px;">1. IDENTIFICACION DEL CONSUMIDOR RECLAMANTE</div>
						<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #cbd5e1;border-collapse:collapse;">
							<tr>
								<td style="width:50%;padding:8px 10px;border-right:1px solid #cbd5e1;border-bottom:1px solid #cbd5e1;font-size:12px;"><strong>Nombre:</strong> {{ $clienteNombre ?: 'N/D' }}</td>
								<td style="width:50%;padding:8px 10px;border-bottom:1px solid #cbd5e1;font-size:12px;"><strong>Telefono:</strong> {{ $reclamo->telefono ?: 'N/D' }}</td>
							</tr>
							<tr>
								<td style="padding:8px 10px;border-right:1px solid #cbd5e1;border-bottom:1px solid #cbd5e1;font-size:12px;"><strong>Tipo y Nro Documento:</strong> {{ $tipoDocumentoMap[$tipoDocumento] ?? $formatear($tipoDocumento) }} {{ $reclamo->numero_documento ?: '' }}</td>
								<td style="padding:8px 10px;border-bottom:1px solid #cbd5e1;font-size:12px;"><strong>Email:</strong> {{ $reclamo->email ?: 'N/D' }}</td>
							</tr>
							<tr>
								<td colspan="2" style="padding:8px 10px;font-size:12px;"><strong>Domicilio:</strong> {{ $reclamo->domicilio ?: 'N/D' }}</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr>
					<td style="padding:8px 20px 6px 20px;">
						<div style="font-size:12px;font-weight:bold;color:#0f3d6e;margin-bottom:8px;">2. IDENTIFICACION DEL BIEN CONTRATADO</div>
						<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #cbd5e1;border-collapse:collapse;">
							<tr>
								<td style="width:50%;padding:8px 10px;border-right:1px solid #cbd5e1;font-size:12px;"><strong>Proyecto:</strong> {{ $proyecto->nombre ?? 'N/D' }}</td>
								<td style="width:50%;padding:8px 10px;font-size:12px;">
									<strong>Tipo de bien:</strong>
									<span style="display:inline-block;margin-left:6px;">{{ $marca($tipoBien === 'PRODUCTO') }} Producto</span>
									<span style="display:inline-block;margin-left:8px;">{{ $marca($tipoBien === 'SERVICIO') }} Servicio</span>
									<span style="display:inline-block;margin-left:8px;">{{ $marca($tipoBien === 'NO_DEFINIDO') }} No definido</span>
								</td>
							</tr>
							<tr>
								<td style="width:50%;padding:8px 10px;border-top:1px solid #cbd5e1;border-right:1px solid #cbd5e1;font-size:12px;"><strong>Manzana:</strong> {{ $reclamo->manzana ?: 'N/D' }}</td>
								<td style="width:50%;padding:8px 10px;border-top:1px solid #cbd5e1;font-size:12px;"><strong>Lote:</strong> {{ $reclamo->lote ?: 'N/D' }}</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr>
					<td style="padding:8px 20px 12px 20px;">
						<div style="font-size:12px;font-weight:bold;color:#0f3d6e;margin-bottom:8px;">3. DETALLE DE LA RECLAMACION Y PEDIDO DEL CONSUMIDOR</div>
						<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #cbd5e1;border-collapse:collapse;">
							<tr>
								<td style="padding:8px 10px;border-bottom:1px solid #cbd5e1;font-size:12px;">
									<strong>Tipo:</strong>
									<span style="display:inline-block;margin-left:6px;">{{ $marca($tipoPedido === 'RECLAMO') }} Reclamo</span>
									<span style="display:inline-block;margin-left:8px;">{{ $marca($tipoPedido === 'QUEJA') }} Queja</span>
									<span style="display:inline-block;margin-left:8px;">{{ $marca($tipoPedido === 'NO_DEFINIDO') }} No definido</span>
								</td>
							</tr>
							<tr>
								<td style="padding:8px 10px;border-bottom:1px solid #cbd5e1;font-size:11px;color:#475569;line-height:1.5;">
									<strong>Reclamo:</strong> Disconformidad relacionada con los productos o servicios.<br>
									<strong>Queja:</strong> Disconformidad no relacionada con productos o servicios; o malestar por la atencion al publico.
								</td>
							</tr>
							<tr>
								<td style="padding:8px 10px;border-bottom:1px solid #cbd5e1;font-size:12px;line-height:1.5;"><strong>Detalle:</strong><br>{{ $reclamo->detalle ?: 'Sin detalle registrado.' }}</td>
							</tr>
							<tr>
								<td style="padding:8px 10px;font-size:12px;line-height:1.5;"><strong>Pedido:</strong><br>{{ $reclamo->pedido ?: 'Sin pedido registrado.' }}</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr>
					<td style="padding:12px 20px;background:#f8fafc;border-top:1px solid #e2e8f0;font-size:11px;color:#64748b;line-height:1.5;">
						El proveedor debera dar respuesta al reclamo o queja en un plazo no mayor de quince (15) dias habiles improrrogables.<br>
						La formulacion del reclamo no impide acudir a otras vias de solucion de controversias ni es requisito previo para interponer una denuncia ante INDECOPI.<br>
						Ticket interno: {{ $reclamo->ticket }}
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</body>
</html>