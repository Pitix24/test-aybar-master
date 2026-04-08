<h2>Hemos recibido su reclamo</h2>

<p>Estimado(a) {{ $reclamo->nombre }} {{ $reclamo->apellido_paterno }} {{ $reclamo->apellido_materno }},</p>

<p>Su registro fue creado correctamente.</p>

<p><strong>Ticket:</strong> {{ $reclamo->codigo_ticket }}</p>
<p><strong>Fecha:</strong> {{ optional($reclamo->created_at)->format('d/m/Y H:i') }}</p>
<p><strong>Tipo:</strong> {{ $reclamo->tipo_pedido }}</p>

<p>Conserve este correo para seguimiento.</p>