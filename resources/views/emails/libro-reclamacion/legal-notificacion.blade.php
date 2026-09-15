<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Libro de Reclamaciones</title>
</head>
<body style="margin:0;padding:0;background-color:#f2f5f9;font-family:Verdana,Arial,sans-serif;color:#1e293b;">
@include('emails.libro-reclamacion._hoja-reclamacion', ['reclamo' => $reclamo, 'mostrarTicketInterno' => true])
</body>
</html>
