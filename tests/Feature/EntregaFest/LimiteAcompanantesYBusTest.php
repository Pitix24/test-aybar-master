<?php

use App\Events\EntregaFest\EntregaFestAsistenciaConfirmacion;
use App\Livewire\Web\EntregaFest\AsistenciaInvitacionCopropietario;
use App\Livewire\Web\EntregaFest\AsistenciaInvitacionPropietario;
use App\Models\AcompananteEntregaFest;
use App\Models\CopropietarioEntregaFest;
use App\Models\EntregaFest;
use App\Models\InvitadoEntregaFest;
use App\Models\ProspectoEntregaFest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;

uses(RefreshDatabase::class);

/**
 * `limite_acompanantes` y `limite_asientos_bus`: el primero reemplaza el tope
 * de 2 acompañantes que antes estaba fijo en el código; el segundo es un tope
 * aparte sobre cuántos de los confirmados pueden ir en la modalidad bus, donde
 * 0 significa "sin restricción, para todos" (el campo nunca es null: 0 es su
 * valor explícito por defecto).
 *
 * Este repo (aybar) NO tiene reserva parcial de aforo: no existe
 * `$cupoAcompanantes`, `lugaresParaConfirmar` ni `ReservaDeAforo`. La cantidad
 * de acompañantes que el invitado declara se otorga siempre tal cual en
 * `cantidad_acompanantes_permitidos` (ver AsistenciaInvitacionPropietario::save()).
 * Tampoco existe `token`: la invitación pública se identifica por el id
 * numérico del prospecto/copropietario (`propietarioId` / `copropietarioId`
 * en el mount de cada componente), así que aquí se usa el id directamente.
 *
 * No se reutilizan helpers de otros archivos de test: Pest no comparte
 * funciones globales entre archivos, así que todo lo de abajo es local a
 * este archivo (verificado con Grep que ningún otro test declara estos
 * nombres).
 */
beforeEach(function () {
    // La confirmación despacha el aviso a n8n/WhatsApp, que en pruebas no tiene webhook.
    Event::fake([EntregaFestAsistenciaConfirmacion::class]);
});

/** DNIs correlativos: con valores al azar, dos filas podían chocar. */
function dniLimite(): string
{
    static $siguiente = 30000000;

    return (string) $siguiente++;
}

/**
 * `limite_invitados` se deja alto por defecto para que el aforo GENERAL
 * (RedirigeSiAforoLleno) no interfiera con las pruebas de acompañantes/bus.
 */
function eventoConLimites(array $overrides = []): EntregaFest
{
    return EntregaFest::factory()->create(array_merge([
        'limite_invitados' => 100,
        'limite_acompanantes' => 2,
        'limite_asientos_bus' => 0,
    ], $overrides));
}

/**
 * Lote correlativo: la tabla tiene una clave única sobre
 * (entrega_fest_id, proyecto_id, lote, manzana) y el factory a veces sortea
 * lote/manzana en null, lo que puede colisionar entre filas del mismo evento.
 */
function loteLimite(): array
{
    static $siguiente = 0;
    $siguiente++;

    return ['lote' => 'LB'.str_pad((string) $siguiente, 4, '0', STR_PAD_LEFT), 'manzana' => 'M1'];
}

function prospectoLimiteSinResponder(EntregaFest $evento): ProspectoEntregaFest
{
    return ProspectoEntregaFest::factory()->create(loteLimite() + [
        'entrega_fest_id' => $evento->id,
        'activo' => true,
        'invitacion_confirmada' => null,
    ]);
}

function copropietarioLimiteSinResponder(EntregaFest $evento): CopropietarioEntregaFest
{
    $prospecto = ProspectoEntregaFest::factory()->create(loteLimite() + [
        'entrega_fest_id' => $evento->id,
        'activo' => true,
    ]);

    return CopropietarioEntregaFest::create([
        'prospecto_entrega_fest_id' => $prospecto->id,
        'dni' => dniLimite(),
        'nombres' => 'Copropietario Límite',
        'invitacion_confirmada' => null,
    ]);
}

/** Abre el formulario del titular, identificado por su id (no hay token en este repo). */
function formularioLimiteTitular(EntregaFest $evento, ProspectoEntregaFest $prospecto)
{
    return Livewire::test(AsistenciaInvitacionPropietario::class, [
        'slug' => $evento->slug,
        'propietarioId' => $prospecto->id,
    ]);
}

function formularioLimiteCopropietario(EntregaFest $evento, CopropietarioEntregaFest $copropietario)
{
    return Livewire::test(AsistenciaInvitacionCopropietario::class, [
        'slug' => $evento->slug,
        'copropietarioId' => $copropietario->id,
    ]);
}

/** Declara `$cantidad` acompañantes válidos sobre un componente ya abierto. */
function llenarAcompanantesLimite($componente, int $cantidad)
{
    $componente->set('cantidad_acompanantes', $cantidad);

    for ($i = 0; $i < $cantidad; $i++) {
        $componente->set("acompanantes.$i.dni", dniLimite())
            ->set("acompanantes.$i.nombres", "Acompañante Límite $i");
    }

    return $componente;
}

/**
 * Ocupa `$personasEnBus` asientos de bus con una confirmación previa ya
 * hecha (titular + acompañantes), directo en BD, sin pasar por el formulario.
 */
function ocuparBus(EntregaFest $evento, int $personasEnBus): void
{
    $prospecto = ProspectoEntregaFest::factory()->create(loteLimite() + [
        'entrega_fest_id' => $evento->id,
        'activo' => true,
    ]);

    $invitado = InvitadoEntregaFest::create([
        'entrega_fest_id' => $evento->id,
        'prospecto_entrega_fest_id' => $prospecto->id,
        'codigo_invitado' => 'BUS-'.uniqid(),
        'cantidad_acompanantes_permitidos' => $personasEnBus - 1,
        'confirmado' => true,
        'transporte' => InvitadoEntregaFest::TRANSPORTE_BUS,
    ]);

    for ($i = 1; $i < $personasEnBus; $i++) {
        AcompananteEntregaFest::create([
            'dni' => dniLimite(),
            'nombres' => "Bus Previo $i",
            'prospecto_entrega_fest_id' => $prospecto->id,
            'invitado_entrega_fest_id' => $invitado->id,
        ]);
    }
}

// ---------------------------------------------------------------
// limite_acompanantes: reemplaza el tope de 2 que antes era fijo
// ---------------------------------------------------------------

it('acepta hasta el límite de acompañantes configurado en el evento, no el default de 2', function () {
    $evento = eventoConLimites(['limite_acompanantes' => 4]);
    $prospecto = prospectoLimiteSinResponder($evento);

    $componente = formularioLimiteTitular($evento, $prospecto)->set('asistira', 'si');
    llenarAcompanantesLimite($componente, 4)->call('save')->assertHasNoErrors();

    expect($prospecto->fresh()->invitado->cantidad_acompanantes_permitidos)->toBe(4)
        ->and($prospecto->fresh()->invitado->acompanantes()->count())->toBe(4);
});

it('rechaza una cantidad de acompañantes mayor al límite configurado del evento', function () {
    $evento = eventoConLimites(['limite_acompanantes' => 4]);
    $prospecto = prospectoLimiteSinResponder($evento);

    $componente = formularioLimiteTitular($evento, $prospecto)->set('asistira', 'si');
    llenarAcompanantesLimite($componente, 5)->call('save')->assertHasErrors(['cantidad_acompanantes']);

    expect(InvitadoEntregaFest::where('prospecto_entrega_fest_id', $prospecto->id)->exists())->toBeFalse();
});

it('con límite de acompañantes en 1, pedir 2 falla la validación de Livewire', function () {
    $evento = eventoConLimites(['limite_acompanantes' => 1]);
    $prospecto = prospectoLimiteSinResponder($evento);

    $componente = formularioLimiteTitular($evento, $prospecto)->set('asistira', 'si');
    llenarAcompanantesLimite($componente, 2)->call('save')->assertHasErrors(['cantidad_acompanantes']);

    expect(InvitadoEntregaFest::where('prospecto_entrega_fest_id', $prospecto->id)->exists())->toBeFalse();
});

// ---------------------------------------------------------------
// limite_asientos_bus: tope aparte y opcional sobre la modalidad bus
// ---------------------------------------------------------------

it('si el bus se llena mientras el formulario del titular seguía abierto, el transporte final queda en propio', function () {
    $evento = eventoConLimites(['limite_asientos_bus' => 2]);
    $prospecto = prospectoLimiteSinResponder($evento);

    // El formulario abre con cupo de bus disponible (todavía nadie ocupó los 2 asientos).
    $componente = formularioLimiteTitular($evento, $prospecto)
        ->set('asistira', 'si')
        ->set('cantidad_acompanantes', 0)
        ->set('transporte', 'bus');

    // Otra confirmación agota el bus antes de que este formulario guarde.
    ocuparBus($evento, personasEnBus: 2);

    $componente->call('save');

    expect($prospecto->fresh()->invitado->transporte)->toBe(InvitadoEntregaFest::TRANSPORTE_PROPIO);
});

it('sin límite de asientos de bus configurado, la modalidad bus no se restringe aunque haya muchas confirmaciones previas', function () {
    $evento = eventoConLimites(['limite_asientos_bus' => 0]);

    ocuparBus($evento, personasEnBus: 5);
    ocuparBus($evento, personasEnBus: 5);

    $prospecto = prospectoLimiteSinResponder($evento);
    formularioLimiteTitular($evento, $prospecto)
        ->set('asistira', 'si')
        ->set('cantidad_acompanantes', 0)
        ->set('transporte', 'bus')
        ->call('save');

    expect($prospecto->fresh()->invitado->transporte)->toBe(InvitadoEntregaFest::TRANSPORTE_BUS);
});

it('el copropietario también respeta el límite de asientos de bus', function () {
    $evento = eventoConLimites(['limite_asientos_bus' => 2]);
    $copropietario = copropietarioLimiteSinResponder($evento);

    $componente = formularioLimiteCopropietario($evento, $copropietario)
        ->set('asistira', 'si')
        ->set('cantidad_acompanantes', 0)
        ->set('transporte', 'bus');

    ocuparBus($evento, personasEnBus: 2);

    $componente->call('save');

    expect($copropietario->fresh()->invitado->transporte)->toBe(InvitadoEntregaFest::TRANSPORTE_PROPIO);
});

it('cuando el bus alcanza para el titular pero no para su acompañante declarado, el transporte final también se corrige a propio', function () {
    $evento = eventoConLimites(['limite_asientos_bus' => 1]);
    $prospecto = prospectoLimiteSinResponder($evento);

    $componente = formularioLimiteTitular($evento, $prospecto)->set('asistira', 'si');
    llenarAcompanantesLimite($componente, 1)->set('transporte', 'bus')->call('save');

    expect($prospecto->fresh()->invitado->transporte)->toBe(InvitadoEntregaFest::TRANSPORTE_PROPIO);
});

// ---------------------------------------------------------------
// Concurrencia: save() revalida contra la BD, no confía en lo que
// mount() leyó cuando el visitante abrió el formulario.
// ---------------------------------------------------------------

it('si el aforo general se llena mientras el segundo formulario seguía abierto, ese segundo no entra y no queda a medias', function () {
    $evento = eventoConLimites(['limite_invitados' => 1]);
    $primero = prospectoLimiteSinResponder($evento);
    $segundo = prospectoLimiteSinResponder($evento);

    // Los dos abren el formulario cuando todavía queda 1 lugar libre: el mount()
    // de ninguno los redirige a aforo-lleno.
    $formularioPrimero = formularioLimiteTitular($evento, $primero)->set('asistira', 'si');
    $formularioSegundo = formularioLimiteTitular($evento, $segundo)->set('asistira', 'si');

    // El primero guarda y ocupa el único lugar disponible.
    $formularioPrimero->call('save');

    // El segundo, con el formulario ya abierto desde antes, intenta guardar después.
    $formularioSegundo->call('save');

    expect($primero->fresh()->invitacion_confirmada)->toBeTrue()
        ->and($segundo->fresh()->invitacion_confirmada)->toBeNull()
        ->and(InvitadoEntregaFest::where('prospecto_entrega_fest_id', $segundo->id)->exists())->toBeFalse()
        ->and(InvitadoEntregaFest::where('entrega_fest_id', $evento->id)->where('confirmado', true)->count())->toBe(1);
});

it('el copropietario tampoco entra si el aforo general se llena mientras su formulario seguía abierto', function () {
    $evento = eventoConLimites(['limite_invitados' => 1]);
    $prospecto = prospectoLimiteSinResponder($evento);
    $copropietario = copropietarioLimiteSinResponder($evento);

    $formularioCopropietario = formularioLimiteCopropietario($evento, $copropietario)->set('asistira', 'si');

    // Otra confirmación (el titular) ocupa el único lugar antes de que el
    // copropietario, que ya tenía el formulario abierto, llegue a guardar.
    formularioLimiteTitular($evento, $prospecto)->set('asistira', 'si')->call('save');

    $formularioCopropietario->call('save');

    expect($copropietario->fresh()->invitacion_confirmada)->toBeNull()
        ->and(InvitadoEntregaFest::where('copropietario_entrega_fest_id', $copropietario->id)->exists())->toBeFalse()
        ->and(InvitadoEntregaFest::where('entrega_fest_id', $evento->id)->where('confirmado', true)->count())->toBe(1);
});

it('si el limite de acompanantes baja en la BD mientras el formulario ya estaba abierto, save() valida contra el valor nuevo', function () {
    $evento = eventoConLimites(['limite_acompanantes' => 2]);
    $prospecto = prospectoLimiteSinResponder($evento);

    // El formulario abre cuando el limite todavia era 2, y el visitante llena
    // 2 acompanantes -validos en ese momento segun rules()-.
    $componente = formularioLimiteTitular($evento, $prospecto)->set('asistira', 'si');
    llenarAcompanantesLimite($componente, 2);

    // El admin baja el limite a 1 desde el editor ERP mientras el formulario sigue abierto.
    $evento->update(['limite_acompanantes' => 1]);

    $componente->call('save')->assertHasErrors(['cantidad_acompanantes']);

    expect(InvitadoEntregaFest::where('prospecto_entrega_fest_id', $prospecto->id)->exists())->toBeFalse();
});
