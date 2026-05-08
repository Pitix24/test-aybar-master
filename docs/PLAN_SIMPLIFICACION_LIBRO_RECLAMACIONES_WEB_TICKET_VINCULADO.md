# Documento Unico de Avance y Evidencia

Proyecto: Simplificacion Libro Reclamaciones (Web -> Ticket -> Libro)
Fecha: 17-04-2026
Estado general: Implementado hasta Fase 5
Owner funcional: Legal / ATC
Owner tecnico: Equipo ERP

## 1. Objetivo del trabajo de hoy

Consolidar la simplificacion del flujo de Libro Reclamaciones para que la creacion se origine en web, exista trazabilidad completa hacia Ticket ATC, y el modulo ERP legal opere sin regresiones en Lista, Ver y Editar.

Flujo objetivo implementado:
Cliente envia formulario web -> Se crea Ticket -> Se crea Libro Reclamacion vinculado por ticket_id -> Legal gestiona con trazabilidad desde ERP.

## 2. Resumen ejecutivo de resultados

1. Se completo el plan tecnico por fases 0, 1, 2, 3, 4 y 5.
2. Se deshabilito la creacion manual ERP (reversible por feature flag).
3. Se implemento contrato tecnico de autocreacion de Ticket desde el formulario web.
4. Se implemento creacion transaccional Ticket + Libro con rollback total.
5. Se implemento trazabilidad visible al Ticket en Lista, Ver y Editar del modulo legal.
6. Se agrego relacion Eloquent segura y eager loading para evitar N+1.
7. Se agrego suite de pruebas de regresion para relacion, visibilidad y acceso.
8. Se resolvio compatibilidad de migraciones para testing en SQLite sin afectar MySQL productivo.

## 3. Avance por fase (estado y evidencia)

## Fase 0 - Congelamiento de creacion ERP

Estado: Implementada

Implementado:
1. Feature flag `libro_reclamacion.crear_erp_habilitado` por defecto deshabilitado.
2. Bloqueo de ruta de crear ERP cuando el flag esta apagado.
3. Ocultamiento del boton Crear en la Lista ERP.
4. Guard adicional en componente Crear para rutas cacheadas/antiguas.

Evidencia documental:
1. docs/COMMIT_FASE_0_LIBRO_RECLAMACIONES.md

## Fase 1 - Contrato de mapeo Web -> Ticket

Estado: Implementada

Implementado:
1. Bloque `ticket_autocreacion` en configuracion del modulo.
2. Defaults de area, tipo, prioridad, canal y created_by para autocreacion.
3. Plantillas para `asunto_inicial` y `descripcion_inicial`.
4. Metodos de resolucion/normalizacion de payload tecnico.

Evidencia documental:
1. docs/COMMIT_FASE_1_LIBRO_RECLAMACIONES.md
2. docs/CAMPO_TECNICOS_AUTOCREACION_TICKET_LIBRO_RECLAMACIONES.md

## Fase 2 - Flujo transaccional Ticket + Libro vinculado

Estado: Implementada

Implementado:
1. Creacion automatica de Ticket dentro de transaccion.
2. Creacion de Libro en la misma transaccion.
3. Enlace por `ticket_id` al Ticket recien creado.
4. Rollback total ante falla de cualquiera de los dos registros.
5. Flag de control operativo para habilitar/deshabilitar autocreacion.
6. Migracion correctiva para entornos historicos sin `ticket_id`.

Evidencia documental:
1. docs/COMMIT_FASE_2_LIBRO_RECLAMACIONES.md

## Fase 3 - Trazabilidad ERP en Lista, Ver y Editar

Estado: Implementada

Implementado:
1. Columna `Ticket ATC` en Lista de Libro Reclamacion.
2. Accion para abrir Ticket vinculado en Lista.
3. Boton `Ver Ticket` en vistas Ver y Editar.
4. Campo informativo `Ticket ATC vinculado` en Ver y Editar.
5. Compatibilidad segura para historicos sin `ticket_id` (`-` / `Sin vincular`).

Evidencia documental:
1. docs/COMMIT_FASE_3_LIBRO_RECLAMACIONES.md

## Fase 4 - Modelo, relacion y consultas

Estado: Implementada

Implementado:
1. Relacion Eloquent `ticketRelacionado` en modelo LibroReclamacion.
2. Cast entero de `ticket_id`.
3. Eager loading en Lista, Ver y Editar para reducir N+1.
4. Evitar colision con PK historica `ticket` del modelo.

Evidencia documental:
1. docs/COMMIT_FASE_4_LIBRO_RECLAMACIONES.md

## Fase 5 - Testing y hardening

Estado: Implementada

Implementado:
1. Pruebas de regresion para la relacion `ticketRelacionado`.
2. Pruebas de visibilidad en Lista y navegacion a Ticket.
3. Prueba de boton `Ver Ticket` en vista Ver.
4. Validacion de ausencia de boton Crear sin permiso.
5. Ajuste de migracion legacy para compatibilidad de suite en SQLite.

Resultado validado:
1. Ejecucion: `php artisan test tests/Feature/Livewire/LibroReclamacionFase5Test.php`
2. Estado: 3 pruebas aprobadas, 0 fallas.

Evidencia documental:
1. docs/COMMIT_FASE_5_LIBRO_RECLAMACIONES.md

## 4. Archivos tecnicos clave modificados

## Dominio y logica

1. app/Models/LibroReclamacion/LibroReclamacion.php
2. app/Livewire/Web/LibroReclamacion/LibroReclamacionLivewire.php
3. app/Livewire/Erp/LibroReclamacion/LibroReclamacionLista.php
4. app/Livewire/Erp/LibroReclamacion/LibroReclamacionVer.php
5. app/Livewire/Erp/LibroReclamacion/LibroReclamacionEditar.php
6. config/libro_reclamacion.php

## Vistas

1. resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-lista.blade.php
2. resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-ver.blade.php
3. resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-editar.blade.php
4. resources/views/livewire/erp/atc/ticket/ticket-lista.blade.php

## Migraciones

1. database/migrations/2026_04_17_160100_add_missing_ticket_id_to_libro_reclamacions_table.php
2. database/migrations/2026_02_16_210945_create_prospecto_entrega_fests_table.php

## Pruebas

1. tests/Feature/Livewire/LibroReclamacionFase5Test.php

## 5. Evidencia de calidad y estabilidad

1. No se reportaron errores de sintaxis en los archivos ajustados de fases 3, 4 y 5.
2. Pruebas de Fase 5 ejecutadas en verde (3/3).
3. Compatibilidad mantenida para registros historicos sin `ticket_id`.
4. Produccion MySQL no cambia comportamiento funcional por el ajuste de SQLite en testing.

## 6. Decisiones tecnicas relevantes del dia

1. Se uso nombre de relacion `ticketRelacionado` para evitar conflicto con PK historica `ticket` en LibroReclamacion.
2. Se forzo carga de relacion en consultas de Lista/Ver/Editar para evitar consultas repetidas y fragilidad de vistas.
3. Se aplico condicion por driver en migracion legacy de Entrega Fest para que SQLite no falle por collation MySQL.
4. Se mantuvo enfoque de cambios reversibles por flags/config y sin borrado abrupto de componentes legacy.

## 7. Riesgos residuales y siguiente capa de cobertura

Riesgos residuales:
1. Falta automatizar escenarios completos de rollback forzado por excepciones externas.
2. Falta automatizar pruebas de listener/correos en cadena completa.

Siguiente capa sugerida (opcional):
1. Testear rollback transaccional con fallas inducidas de Ticket.
2. Testear emision de correos del listener de Libro Reclamaciones.
3. Testear contrato de payload de eventos ante nuevos handlers.

## 8. Estado final para cierre

Estado de cierre del dia: Aprobado

1. Fase 0: Implementada
2. Fase 1: Implementada
3. Fase 2: Implementada
4. Fase 3: Implementada
5. Fase 4: Implementada
6. Fase 5: Implementada

Conclusion:
El flujo simplificado Web -> Ticket -> Libro queda operativo, trazable y validado con pruebas base de regresion. La documentacion queda consolidada en este archivo como evidencia unica de avance del 17-04-2026.

## 9. Anexos de trazabilidad (fuente original)

1. docs/COMMIT_FASE_0_LIBRO_RECLAMACIONES.md
2. docs/COMMIT_FASE_1_LIBRO_RECLAMACIONES.md
3. docs/COMMIT_FASE_2_LIBRO_RECLAMACIONES.md
4. docs/COMMIT_FASE_3_LIBRO_RECLAMACIONES.md
5. docs/COMMIT_FASE_4_LIBRO_RECLAMACIONES.md
6. docs/COMMIT_FASE_5_LIBRO_RECLAMACIONES.md
7. docs/CAMPO_TECNICOS_AUTOCREACION_TICKET_LIBRO_RECLAMACIONES.md

## 10. Matriz de campos de libro_reclamacions (decision de saneamiento)

Objetivo de esta matriz:
1. Identificar campos canonicos del flujo actual.
2. Diferenciar campos de compatibilidad historica.
3. Marcar candidatos de deprecacion sin romper produccion.

Recomendacion ejecutiva:
1. No botar la tabla ni rehacer desde cero en esta etapa.
2. Aplicar saneamiento progresivo por fases (write-new/read-old, luego retiro controlado).

### 10.1 Campos canonicos (mantener)

1. Identidad y trazabilidad:
	- ticket (PK historica)
	- ticket_id (vinculo a tickets.id)
	- codigo
	- codigo_ticket
2. Operacion legal:
	- estado_libro_reclamaciones_id
	- clasificacion
	- tipo_pedido
	- gestor_id
	- assigned_at
	- observaciones_internas
3. Contexto de negocio:
	- unidad_negocio_id
	- proyecto_id
	- asunto
	- lotes
4. Cliente canonico ERP:
	- cliente_id
	- cliente_tipo_documento
	- cliente_documento
	- cliente_nombre
	- cliente_email
	- cliente_celular
	- cliente_direccion
5. Auditoria:
	- created_by
	- updated_by
	- deleted_by
	- created_at
	- updated_at
	- deleted_at

### 10.2 Campos de compatibilidad historica (mantener temporalmente)

Estos campos siguen en uso por flujo web/correos/fallbacks de vista:
1. nombre
2. apellido_paterno
3. apellido_materno
4. domicilio
5. telefono
6. email
7. tipo_documento
8. numero_documento
9. detalle
10. pedido
11. manzana
12. lote
13. tipo_bien_contratado
14. monto_reclamado
15. descripcion
16. conformidad
17. observaciones
18. estado
19. serie
20. numero_reclamo
21. nota_fuente_titulo
22. nota_fuente_fecha

### 10.3 Candidatos de deprecacion prioritaria (sin uso funcional actual)

Observacion: En el barrido de logica/vistas/tests del modulo, estos campos no muestran uso funcional directo actual.

1. fecha_respuesta
2. archivo_1
3. archivo_2
4. archivo_3
5. archivo_4
6. leido

Politica sugerida:
1. Marcar como deprecated en documentacion interna.
2. Bloquear nuevas escrituras desde app.
3. Monitorear 1 ciclo release.
4. Eliminar por migracion recien cuando no haya lecturas externas/reportes.

### 10.4 Plan recomendado de saneamiento (sin rehacer tabla)

Fase A - Contrato canonico:
1. Confirmar que ERP y web escriban prioritariamente en cliente_* y ticket_id.
2. Mantener legacy solo como respaldo de lectura.

Fase B - Dual read controlado:
1. Leer primero canonico.
2. Fallback a legacy donde aun aplique.
3. Registrar metricas de fallback para saber cuando ya no se usa.

Fase C - Limpieza segura:
1. Eliminar primero campos sin uso (archivos, fecha_respuesta, leido).
2. Mantener historicos sensibles hasta validar reportes y exportaciones.
3. Reindexar y ajustar consultas finales.

### 10.5 Conclusiones de arquitectura

1. El tamano de la tabla se explica por convivencia de modelo historico + modelo simplificado actual.
2. El modulo ya opera estable con el esquema actual y no exige reset estructural.
3. Rehacer tabla completa hoy aumenta riesgo (migracion de datos, vistas, mails, reportes y referencias cruzadas).
4. La estrategia de menor riesgo y menor costo es deprecacion progresiva con migraciones pequenas y verificables.
