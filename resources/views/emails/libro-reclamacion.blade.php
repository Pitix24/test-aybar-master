<h2>Nuevo reclamo recibido</h2>

@php
	$tipoDocumento = trim((string) ($formulario->tipo_documento ?? ''));
	$tipoDocumento = $tipoDocumento === '' ? 'N/D' : ucwords(strtolower(str_replace('_', ' ', $tipoDocumento)));
@endphp

<p><strong>Nombre:</strong> {{ $formulario->nombre }}</p>
<p><strong>Documento:</strong> {{ $tipoDocumento }} - {{ $formulario->numero_documento }}</p>
<p><strong>Detalle:</strong></p>
<p>{{ $formulario->detalle }}</p>