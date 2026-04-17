# Plan: Simplificacion Libro Reclamaciones (Web -> Ticket -> Libro)

Fecha: 17-04-2026  
Estado: En ejecucion por fases (Fase 0, 1, 2, 3, 4 y 5 implementadas)

## Objetivo

Simplificar el modulo de Libro de Reclamaciones para que su canal de entrada sea solo el formulario web, y convertir el flujo en:

Cliente envia formulario web -> Correo cliente -> Correo legal -> Crear Ticket -> Crear Libro Reclamacion vinculado al Ticket.

El objetivo operativo es que el equipo legal gestione el caso en Tickets, manteniendo Libro Reclamaciones como registro legal y de validacion.

## Alcance y reglas definidas

1. Se deshabilita la creacion de Libro desde ERP por ahora.
2. No se eliminan componentes ni vistas de Crear ERP; solo se ocultan/bloquean accesos.
3. El formulario web sigue siendo el unico punto de creacion.
4. Se utilizara `ticket_id` en `libro_reclamacions` para vincular con `tickets.id`.
5. El Ticket y el Libro son listas distintas, pero vinculadas.
6. Para el Ticket automatico:
   - Area destino: Legal (id 3).
   - Canal: Libro Reclamacion (id 4 en catalogo `canals`).
7. En Libro ERP se mantiene edicion de datos de cliente y datos del caso (decision vigente).

## Avance ejecutado al 17-04-2026

1. Fase 0 completada:
   - Se deshabilito creacion ERP por feature flag.
   - Se condiciono el registro de ruta de crear.
   - Se aplico hardening del menu ERP para no romper cuando la ruta no existe.
2. Fase 1 completada:
   - Se implemento contrato tecnico en `config/libro_reclamacion.php`.
   - Se definieron defaults operativos para area, tipo, subtipo, prioridad y canal.
3. Fase 2 completada:
   - Se implemento creacion transaccional Ticket + Libro vinculado por `ticket_id`.
   - Se mantiene rollback total ante error.
4. Hotfix de esquema aplicado:
   - Se agrego migracion correctiva para crear `ticket_id` en `libro_reclamacions` cuando falta en bases historicas.
5. Hotfix de canal aplicado:
   - Se corrigio default tecnico de canal a `Libro Reclamacion`, resolviendo `canal_id = 4`.
6. Hotfix de visualizacion ERP aplicado:
   - En lista de Tickets se agrego tolerancia para registros historicos con `canal_id` nulo (render seguro con `-`).
7. Fase 3 completada:
   - Se agrego columna `Ticket ATC` y acceso rapido en Lista de Libro.
   - Se agrego boton `Ver Ticket` en Ver y Editar de Libro.
   - Se agrego campo informativo de ticket vinculado en vista general de Ver/Editar.
8. Fase 4 completada:
   - Se agrego relacion Eloquent al Ticket vinculado como `ticketRelacionado`.
   - Se evito colision con la PK historica `ticket` del modelo `libro_reclamacions`.
   - Se ajustaron consultas de Lista, Ver y Editar para cargar la relacion y reducir N+1.
   - Se adiciono cast entero a `ticket_id` para lectura consistente.10. Fase 5 completada:
   - Se agregaron pruebas de regresion para la relacion y la trazabilidad al Ticket vinculado.
   - Se valido la Lista de Libro con el boton de acceso al Ticket.
   - Se valido la vista Ver con el boton `Ver Ticket`.
   - Se ajusto una migracion legacy para que la suite funcione en SQLite de testing.
## Fases y tareas

## Fase 0 - Congelamiento de creacion ERP

Objetivo: impedir nuevas creaciones manuales desde ERP sin borrar codigo existente.

Tareas:
1. Ocultar boton Crear de la lista ERP de Libro.
2. Bloquear ruta de creacion ERP (`/erp/libro-reclamacion/crear`) con estrategia reversible:
   - opcion recomendada: feature flag,
   - y/o revocacion de permiso `ticket-libro-reclamacion.crear`.
3. Mantener Ver/Editar/Lista operativos.
4. Limpiar cache de permisos despues del ajuste.

Criterio de aceptacion:
1. Usuario legal no ve boton Crear.
2. Acceso directo por URL de crear devuelve 403 o redireccion controlada.
3. No hay impacto en lista, ver y editar.

## Fase 1 - Contrato de mapeo Web -> Ticket

Objetivo: cerrar el contrato de datos antes de tocar logica transaccional.

Tareas:
1. Definir mapeo exacto de campos del formulario web hacia Ticket.
2. Definir mapeo de `tipo_pedido` (RECLAMO/QUEJA) a `tipo_solicitud_id` y `sub_tipo_solicitud_id`.
3. Registrar IDs oficiales de catalogos para area, canal, estado inicial y prioridad.
4. Documentar defaults cuando datos del formulario lleguen vacios (formulario permite campos opcionales).

Criterio de aceptacion:
1. Tabla de mapeo aprobada por negocio y tecnica.
2. Sin valores hardcodeados ambiguos pendientes de definicion.

## Fase 2 - Flujo transaccional Ticket + Libro vinculado

Objetivo: implementar la nueva secuencia atomica en el envio web.

Tareas:
1. En el submit web, crear Ticket dentro de transaccion.
2. Crear Libro Reclamacion en la misma transaccion.
3. Guardar `ticket_id` en Libro con el ID del Ticket recien creado.
4. Si falla cualquier paso, rollback total.
5. Confirmar que el evento/correos usan datos persistidos consistentes.

Criterio de aceptacion:
1. Cada nuevo Libro web queda vinculado a un Ticket (`ticket_id` no nulo).
2. No existen registros huerfanos en errores (ni ticket solo, ni libro solo).

## Fase 3 - Ajuste del modulo legal ERP

Objetivo: alinear ERP Libro a una operacion legal simplificada y trazable.

Tareas:
1. Reforzar vista Lista para mostrar vinculacion con Ticket con un botón parecido a Lista Citas.
2. Reforzar vista Ver y Editar para navegar al Ticket vinculado con un Botón 'Ver Ticket'.
3. Mantener/ajustar edicion legal de cliente y caso segun reglas aprobadas.
4. Retirar dependencias de captura manual para creacion.

Criterio de aceptacion:
1. Legal visualiza caso + datos cliente + acceso al Ticket vinculado.
2. No hay rutas de creacion manual visibles/funcionales.

Estado: Implementada.

## Fase 4 - Modelo, relaciones y consultas

Objetivo: dejar el dominio consistente y mantenible.

Tareas:
1. Agregar/validar relacion al Ticket vinculado en modelo `LibroReclamacion`.
2. Ajustar `with()` y consultas de Lista/Ver/Editar para evitar N+1.
3. Revisar fillable/casts usados realmente por flujo simplificado.
4. Preparar limpieza progresiva de campos legacy no usados (sin borrado abrupto).

Criterio de aceptacion:
1. Consultas optimizadas y sin regresiones funcionales.
2. Modelo con relacion clara Libro -> Ticket.

Estado: Implementada.

## Fase 5 - Pruebas y hardening

Objetivo: asegurar estabilidad antes de despliegue.

Tareas:
1. Pruebas E2E del flujo web completo.
2. Pruebas de permisos (bloqueo crear ERP).
3. Pruebas de rollback transaccional ante fallos.
4. Pruebas de correos al cliente y equipo legal.
5. Validacion manual de legal en Lista/Ver/Editar con ticket vinculado.

Criterio de aceptacion:
1. Flujo estable en QA sin huerfanos ni inconsistencias.
2. Permisos y visibilidad coherentes con lo definido.
Estado: Implementada.

## Fase 6 - Testing extensible (opcional)

Objetivo: preparar suite de pruebas para ciclos futuros.

Pendiente de refinamiento:
1. Pruebas de rollback transaccional ante fallos de Ticket.
2. Pruebas de correos emitidos por el listener.
3. Validacion de payload de eventos si se agregan nuevos handlers.

Nota: Esta fase es opcional y depende de politica de testing del equipo. Los casos basicos de regresion ya estan cubiertos en Fase 5.
Estado: Implementada.

## Slicing sugerido para commits (reversible)

1. Commit A - Access control ERP
   - ocultar boton Crear,
   - bloquear ruta/permiso de crear.

2. Commit B - Contrato de mapeo
   - config/constantes de IDs,
   - documentacion de mapeo RECLAMO/QUEJA.

3. Commit C - Core transaccional
   - crear Ticket,
   - crear Libro,
   - vincular `ticket_id`,
   - rollback seguro.

4. Commit D - Ajustes ERP legal
   - lista/ver/editar con trazabilidad a ticket.

5. Commit E - Modelo y consultas
   - relacion `ticket()`,
   - eager loading y ajustes de consulta.

6. Commit F - Testing y cierre documental
   - tests,
   - checklist de despliegue,
   - resumen tecnico final.

## Archivos objetivo (referencia inicial)

1. `routes/erp/libro-reclamacion.php`
2. `resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-lista.blade.php`
3. `database/seeders/RolesYPermisosSeeder.php`
4. `app/Livewire/Web/LibroReclamacion/LibroReclamacionLivewire.php`
5. `app/Models/LibroReclamacion/LibroReclamacion.php`
6. `app/Events/LibroReclamacion/LibroReclamacionRegistrado.php`
7. `app/Listeners/LibroReclamacion/EnviarCorreosLibroReclamacion.php`
8. `resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-ver.blade.php`
9. `app/Livewire/Erp/LibroReclamacion/LibroReclamacionLista.php`
10. `database/migrations/2026_02_16_211526_create_libro_reclamacions_table.php`

## Checklist de inicio de implementacion

1. Confirmar IDs de tipo_solicitud/sub_tipo para RECLAMO y QUEJA.
2. Confirmar ID del canal Formulario Web en catalogo actual. Resultado: confirmado `Libro Reclamacion` id `4`.
3. Definir mecanismo de bloqueo crear ERP (permiso, feature flag, o ambos).
4. Definir si correos deben salir antes o despues de persistir Ticket+Libro (recomendado: despues de persistir).
5. Acordar estrategia de despliegue: gradual con feature flag en produccion.

## Riesgos y mitigacion

1. Riesgo: acceso residual por URL a crear ERP.
   - Mitigacion: bloqueo por middleware/permiso + flag y pruebas de acceso.

2. Riesgo: desalineacion de catalogos Ticket (tipo/canal/area).
   - Mitigacion: Fase 1 obligatoria con IDs cerrados antes de codificar.

3. Riesgo: registros huerfanos en fallos parciales.
   - Mitigacion: transaccion unica + pruebas de rollback.

4. Riesgo: regresion en correos.
   - Mitigacion: pruebas de listener/mail y validacion de payload final.

## Resultado esperado

Al finalizar estas fases, el flujo quedara estandarizado en un solo canal (web), con trazabilidad completa entre Libro y Ticket, y con operacion legal simplificada y mantenible dentro del ERP.