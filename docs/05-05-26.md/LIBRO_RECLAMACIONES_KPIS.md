# KPIs - Libro de Reclamaciones

Fecha: 2026-05-05
Autor: Equipo Aybar (documentación generada por asistente)

## Objetivo

Agregar un panel de KPIs en la vista lista de `Libro de Reclamaciones` que muestre, en tiempo real y reaccionando a filtros, las siguientes métricas:

- Reclamos Totales
- Reclamos Resueltos
- Reclamos Pendientes
- Promedio de Reclamos por día

La fuente de verdad para el estado (resuelto / pendiente) será el `Ticket` asociado (`ticket.estado_ticket_id`).

## Alcance inicial

Implementación mínima viable (Opción A): añadir las métricas dentro del componente Livewire de lista:

- `app/Livewire/Erp/LibroReclamacion/LibroReclamacionLista.php`
- `resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-lista.blade.php`

A futuro: extraer a `LibroReclamacionDashboard` o a un Service `LibroReclamacionStatsService`.

## Requisitos funcionales

1. Las métricas deben recalcularse cuando cambien filtros relevantes: `proyecto_id`, `estado_ticket`, `gestor`, rango de fechas y búsqueda.
2. Las métricas deben ser rápidas: usar `clone $baseQuery` y `whereHas('ticketRelacionado', fn($q) => ...)` para filtrar por estado de ticket.
3. `Promedio por día` = total / max(1, dias) donde `dias` = diferencia en días entre `MIN(created_at)` (del conjunto filtrado) y hoy.
4. Evitar división por cero; si `dias === 0` usar 1.

## Diseño propuesto (código ejemplo)

### Livewire: método `cargarStats()` (ejemplo)

```php
public function cargarStats()
{
    $baseQuery = LibroReclamacion::query()
        ->when($this->proyecto_id, fn($q) => $q->where('proyecto_id', $this->proyecto_id))
        ->when($this->gestor_id, fn($q) => $q->where('gestor_id', $this->gestor_id))
        ->when($this->fecha_desde, fn($q) => $q->whereDate('created_at', '>=', $this->fecha_desde))
        ->when($this->fecha_hasta, fn($q) => $q->whereDate('created_at', '<=', $this->fecha_hasta));

    $this->stats['total'] = (clone $baseQuery)->count();

    $this->stats['resueltos'] = (clone $baseQuery)
        ->whereHas('ticketRelacionado', fn($q) => $q->where('estado_ticket_id', EstadoTicket::id('CERRADO')))
        ->count();

    $pendientesEstados = [
        EstadoTicket::id('EN_GESTION'),
        EstadoTicket::id('EN_ESPERA_CLIENTE'),
        EstadoTicket::id('DERIVADO'),
    ];

    $this->stats['pendientes'] = (clone $baseQuery)
        ->whereHas('ticketRelacionado', fn($q) => $q->whereIn('estado_ticket_id', $pendientesEstados))
        ->count();

    // Promedio por día
    $minFecha = (clone $baseQuery)->min('created_at');
    $dias = 0;
    if ($minFecha) {
        $dias = now()->startOfDay()->diffInDays(
            Carbon\Carbon::parse($minFecha)->startOfDay()
        );
    }
    $dias = max(1, $dias);
    $this->stats['promedio_por_dia'] = round($this->stats['total'] / $dias, 2);
}
```

### Recalculo reactivo

En `updated($property)` del componente lista, detectar cambios en propiedades de filtro y ejecutar:

```php
$this->resetPage();
$this->cargarStats();
```

### Partial Blade (ejemplo de estructura)

Crear partial `resources/views/livewire/erp/libro-reclamacion/partials/kpis.blade.php` con grid de 4 tarjetas reutilizando estilos de `EntregaFest`.

Cada tarjeta recibe un título, el valor y (opcional) un ícono y color.

## Tests sugeridos

- `tests/Unit/LibroReclamacionStatsTest.php`
    - Caso sin reclamos -> todos 0 y promedio 0
    - Caso con reclamos en distintos estados -> validar `total`, `resueltos`, `pendientes` y `promedio_por_dia` (usar `Carbon::setTestNow` y factories)

- `tests/Feature/LibroReclamacionKpiViewTest.php`
    - Renderizar la vista de lista (Livewire test) y comprobar que las métricas aparecen y reaccionan al cambiar filtros.

Comandos para ejecutar tests:

```bash
php artisan test --filter=LibroReclamacionStatsTest
php artisan test --filter=LibroReclamacionKpiViewTest
```

## Consideraciones de performance

- Los conteos usan `COUNT(*)` y `whereHas` — sobre grandes volúmenes, evaluar índices en `libro_reclamacions.created_at` y `tickets.estado_ticket_id`.
- Si la vista tuviera problemas, considerar precomputar stats en cache con TTL corto o usar un servicio que calcule en background.

## UX opcional

- Hacer tarjetas "clickables" para aplicar filtros (ej. click en "Resueltos" aplica filtro `estado_ticket = CERRADO`).
- Añadir tendencia (delta) comparando con periodo anterior.

## Pasos siguientes inmediatos

1. Implementar `cargarStats()` y exponer `$stats` en `LibroReclamacionLista`.
2. Crear partial blade y renderizar las tarjetas en la vista lista.
3. Añadir tests mínimos y ejecutar.

---

Archivo creado automáticamente por el asistente. Recomendación: abrir un PR pequeño para revisión de UI/UX y validar con QA en staging.
