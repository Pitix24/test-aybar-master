<h2>Hemos recibido su reclamo</h2>

@php
	$nombreCompleto = trim(($reclamo->nombre ?? '') . ' ' . ($reclamo->apellido_paterno ?? '') . ' ' . ($reclamo->apellido_materno ?? ''));
	$formatearValor = static function (?string $valor): string {
		$texto = trim((string) $valor);

		if ($texto === '') {
			return 'N/D';
		}

		return ucwords(strtolower(str_replace('_', ' ', $texto)));
	};
@endphp

<p>Estimado(a) {{ $nombreCompleto !== '' ? $nombreCompleto : 'cliente' }},</p>

<p>Su registro fue creado correctamente.</p>

<p><strong>Ticket:</strong> {{ $reclamo->codigo_ticket }}</p>
<p><strong>Fecha:</strong> {{ optional($reclamo->created_at)->format('d/m/Y H:i') }}</p>
<p><strong>Tipo:</strong> {{ $formatearValor($reclamo->tipo_pedido) }}</p>

<p>Conserve este correo para seguimiento.</p>