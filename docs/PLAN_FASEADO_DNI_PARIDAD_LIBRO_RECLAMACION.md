# Plan Faseado: DNI Manual + Paridad Crear/Editar/Ver (Libro Reclamacion)

Fecha: 2026-04-13
Estado: Listo para implementacion
Objetivo: permitir guardar DNI digitado manualmente sin depender del boton Buscar, y homologar el flujo de Crear/Editar/Ver de Libro Reclamacion al patron de Ticket.

---

## Resumen Ejecutivo

Problema actual:
- El usuario puede escribir DNI en el campo visible (`dni`), pero si no usa Buscar, en algunos flujos no se persiste en `cliente_documento`.
- Esto rompe el caso de cliente potencial no registrado.

Meta funcional:
- Buscar debe ser opcional (solo autocompletado).
- Si se escribe DNI manual, debe persistir siempre.
- Ver/Editar deben mantener estructura y experiencia equivalente a Ticket, usando campos propios de Libro Reclamacion.

---

## Alcance

Incluye:
- Sincronizacion `dni -> cliente_documento` en Crear y Editar.
- Sincronizacion defensiva antes de guardar/actualizar.
- Paridad visual/flujo entre Crear, Editar y Ver.
- Documentacion de cierre actualizada.

No incluye:
- Rediseño global de estilos ERP.
- Cambios fuera del modulo `Erp/LibroReclamacion`.

---

## FASE 0 - Baseline y Seguridad

Objetivo:
- Congelar estado inicial y reducir riesgo de regresion.

Tareas:
1. Confirmar que la app levanta sin errores y el modulo abre.
2. Verificar en BD que existen estados base (`NUEVO`, etc.).
3. Validar que migraciones pendientes del modulo estan aplicadas.

Validacion:
- `php artisan migrate:status`
- `php artisan db:seed --class=EstadoLibroReclamacionSeeder --force` (si falta estado NUEVO)

Commit sugerido:
- `chore(libro): baseline de entorno para flujo DNI manual`

---

## FASE 1 - Sincronizacion DNI en Crear

Objetivo:
- Garantizar persistencia de DNI manual en alta.

Archivo:
- `app/Livewire/Erp/LibroReclamacion/LibroReclamacionCrear.php`

Tareas:
1. Agregar `updatedDni($value)` para sincronizar:
   - `cliente_documento = trim(dni)`
   - `cliente_tipo_documento = resolverTipoDocumento(...)` cuando aplique
2. Agregar sincronizacion defensiva al inicio de `store()` antes de `create()`.
3. Mantener `buscarCliente()` como flujo opcional de autocompletado.

Validacion manual:
1. Crear caso sin Buscar, solo DNI manual.
2. Guardar y validar en Ver que Documento se muestra.

Commit sugerido:
- `feat(libro-crear): persistencia de DNI manual sin buscarCliente`

---

## FASE 2 - Sincronizacion DNI en Editar

Objetivo:
- Asegurar consistencia de DNI manual en actualizacion.

Archivo:
- `app/Livewire/Erp/LibroReclamacion/LibroReclamacionEditar.php`

Tareas:
1. Agregar `updatedDni($value)` con misma logica de Crear.
2. Agregar sincronizacion defensiva al inicio de `update()`.
3. Mantener Buscar opcional para autocompletar.

Validacion manual:
1. Editar un registro existente.
2. Cambiar DNI sin usar Buscar.
3. Guardar y validar persistencia en Ver.

Commit sugerido:
- `feat(libro-editar): sincroniza DNI manual antes de update`

---

## FASE 3 - Paridad de Estructura en Ver

Objetivo:
- Homologar experiencia de lectura con Ticket.

Archivos:
- `resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-ver.blade.php`
- (si aplica carga de relaciones) `app/Livewire/Erp/LibroReclamacion/LibroReclamacionVer.php`

Tareas:
1. Revisar bloques visuales para mantener patron:
   - Informacion general
   - Cliente
   - Asunto / Lotes
   - Nota fuente
   - Observaciones internas
   - Auditoria
2. Confirmar uso de campos propios de Libro Reclamacion.
3. Mantener rutas y acciones actuales (Lista, Editar).

Validacion manual:
1. Abrir Ver de un registro con DNI manual.
2. Confirmar que Documento, estado y auditoria se ven coherentes.

Commit sugerido:
- `refactor(libro-ver): paridad de estructura con ticket`

---

## FASE 4 - Paridad fina en Crear/Editar (UX)

Objetivo:
- Uniformar comportamiento y mensajes para uso diario.

Archivos:
- `resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-crear.blade.php`
- `resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-editar.blade.php`
- `app/Livewire/Erp/LibroReclamacion/LibroReclamacionCrear.php`
- `app/Livewire/Erp/LibroReclamacion/LibroReclamacionEditar.php`

Tareas:
1. Confirmar texto de campo: `DNI / CE / RUC (opcional)`.
2. Alinear mensajes de validacion (mostrar primer error claro).
3. Confirmar que `cliente_documento` no sea obligatorio para potencial cliente.

Validacion manual:
1. Crear con DNI vacio + nombre/asunto validos.
2. Crear con DNI manual sin Buscar.
3. Editar con/sin DNI y verificar clasificacion esperada.

Commit sugerido:
- `refactor(libro-ux): paridad de validaciones y mensajes en crear/editar`

---

## FASE 5 - QA Tecnico + Documentacion de Cierre

Objetivo:
- Cerrar implementacion con evidencia y checklist.

Archivos:
- `docs/CONSOLIDACION_TICKET_LIBRO_RECLAMACIONS.md`
- (opcional) evidencia de pruebas interna

Tareas:
1. Ejecutar chequeo tecnico:
   - `get_errors()` en archivos tocados
   - flujo Crear/Editar/Ver manual
2. Actualizar doc de cierre con regla final:
   - Buscar es opcional
   - DNI manual persiste
   - Paridad de flujo aplicada
3. Registrar pendientes no bloqueantes (si aparecen).

Commit sugerido:
- `docs(libro): cierre funcional de DNI manual y paridad de flujo`

---

## Checklist de Aceptacion Final

- [ ] Se puede crear registro sin Buscar y con DNI manual persistido.
- [ ] Se puede editar DNI sin Buscar y se guarda correctamente.
- [ ] Ver muestra Documento correcto (no N/D cuando existe dato).
- [ ] Buscar sigue funcionando como autocompletado opcional.
- [ ] Crear/Editar/Ver mantienen estructura equivalente a Ticket.
- [ ] No hay errores de sintaxis o runtime en archivos modificados.

---

## Plan de Guardados (Control de Commits)

Orden recomendado:
1. FASE 0
2. FASE 1
3. FASE 2
4. FASE 3
5. FASE 4
6. FASE 5

Regla practica:
- 1 fase = 1 commit.
- No mezclar cambios de UX con logica de persistencia en el mismo commit.
- Cerrar cada fase solo con validacion minima ejecutada.

---

## Riesgos y Mitigacion

Riesgo 1:
- DNI manual se borra por eventos Livewire.
Mitigacion:
- Sync reactivo + sync defensivo antes de guardar.

Riesgo 2:
- Regresion en flujo Buscar.
Mitigacion:
- Mantener `buscarCliente()` intacto y validar ambos caminos.

Riesgo 3:
- Inconsistencia visual entre Ver y Ticket.
Mitigacion:
- Checklist de bloques visuales y prueba manual comparativa.

---

## Resultado Esperado

Al finalizar:
- El equipo puede registrar reclamos de clientes potenciales sin friccion.
- El campo DNI ya no depende del boton Buscar para persistirse.
- El modulo Libro Reclamacion queda consistente, simple y predecible para operacion diaria.
