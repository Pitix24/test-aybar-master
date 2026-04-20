# Fase 2 - Escritura Canonica (Libro Reclamaciones)

Fecha: 2026-04-20
Estado: Cerrada
Objetivo de fase: detener nuevas escrituras legacy de identidad/contacto y persistir en `cliente_*`.

## 1) Implementacion realizada
Se actualizo el flujo Web de registro para que la creacion de `libro_reclamacions` escriba campos canonicos de cliente en lugar de columnas legacy.

Archivo modificado:
- `app/Livewire/Web/LibroReclamacion/LibroReclamacionLivewire.php`

Cambios clave:
- En `LibroReclamacion::create([...])` se reemplazo escritura legacy por escritura canonica:
  - `cliente_tipo_documento`
  - `cliente_documento`
  - `cliente_nombre`
  - `cliente_email`
  - `cliente_celular`
  - `cliente_direccion`
- Se agrego metodo auxiliar `resolverNombreClienteCanonico()` para consolidar nombre completo desde nombres/apellidos del formulario.
- Se mantiene compatibilidad con columnas legacy no nulas a traves del `booted()->creating(...)` del modelo `LibroReclamacion`.

## 2) Verificacion ejecutada
Pruebas focalizadas ejecutadas:
- `tests/Feature/Livewire/LibroReclamacionFase5Test.php`

Resultado:
- 5 pruebas aprobadas
- 0 fallas

## 3) Estado del alcance de Fase 2
Completado:
- Escritura canonica en flujo Web de alta.
- ERP crear/editar ya trabajaba en canonico para identidad/contacto.
- Barrida final en modulo Libro sin hallazgos de escrituras primarias legacy en create/update activos.

Observacion de compatibilidad:
- El modelo `LibroReclamacion` mantiene fallback en `booted()->creating(...)` para columnas legacy no nulas. Este comportamiento es esperado en Fase 2 y se retirara en Fase 4.

## 4) Criterio de cierre de Fase 2
La fase se considera cerrada cuando:
1. Todas las altas/ediciones activas escriben identidad/contacto solo en `cliente_*`.
2. No hay paths de codigo de aplicacion que escriban manualmente `nombre`, `apellido_*`, `tipo_documento`, `numero_documento`, `telefono`, `email`, `domicilio` como fuente primaria.
3. Se mantiene operatividad funcional sin regresiones en Web/ERP.

## 5) Proximo paso
Iniciar Fase 3: migracion/backfill de datos historicos con metrica de actualizados, conflictos y pendientes.
