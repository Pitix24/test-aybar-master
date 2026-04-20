<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Web\LibroReclamacion\LibroReclamacionLivewire;
use App\Livewire\Erp\LibroReclamacion\LibroReclamacionLista;
use App\Livewire\Erp\LibroReclamacion\LibroReclamacionVer;
use App\Models\Area;
use App\Models\Canal;
use App\Models\EstadoTicket;
use App\Models\LibroReclamacion\LibroReclamacion;
use App\Models\PrioridadTicket;
use App\Models\Proyecto;
use App\Models\SubTipoSolicitud;
use App\Models\Ticket;
use App\Models\TipoSolicitud;
use App\Models\UnidadNegocio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class LibroReclamacionFase5Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Livewire::withoutLazyLoading();
    }

    #[Test]
    public function la_relacion_ticket_vinculado_se_carga_correctamente(): void
    {
        [$ticket, $libro] = $this->crearLibroVinculado();

        $reclamacion = LibroReclamacion::with('ticketRelacionado')->findOrFail($libro->ticket);

        $this->assertTrue($reclamacion->relationLoaded('ticketRelacionado'));
        $this->assertTrue($reclamacion->ticketRelacionado->is($ticket));
    }

    #[Test]
    public function la_lista_muestra_el_ticket_vinculado_y_oculta_crear_sin_permiso(): void
    {
        config(['libro_reclamacion.crear_erp_habilitado' => true]);

        [$ticket, $libro] = $this->crearLibroVinculado();
        $usuario = $this->crearUsuarioConPermisos([
            'ticket-libro-reclamacion.lista',
            'ticket-libro-reclamacion.ver',
            'ticket.ver',
        ]);

        $this->actingAs($usuario);

        Livewire::test(LibroReclamacionLista::class)
            ->assertSee('Ticket')
            ->assertSeeHtml(route('erp.ticket.vista.ver', $ticket->id))
            ->assertDontSee('Crear');
    }


    #[Test]
    public function ver_y_editar_muestran_acceso_al_ticket_vinculado(): void
    {
        [$ticket, $libro] = $this->crearLibroVinculado();
        $usuario = $this->crearUsuarioConPermisos([
            'ticket-libro-reclamacion.ver',
            'ticket-libro-reclamacion.editar',
            'ticket-libro-reclamacion.lista',
            'ticket.ver',
        ]);

        $this->actingAs($usuario);

        $verComponent = app(LibroReclamacionVer::class);
        $verComponent->mount($libro->ticket);
        $verHtml = view('livewire.erp.libro-reclamacion.libro-reclamacion-ver', [
            'ticket' => $verComponent->ticket,
        ])->render();

        $this->assertStringContainsString(route('erp.ticket.vista.ver', $ticket->id), $verHtml);
        $this->assertStringContainsString('Ver Ticket', $verHtml);
    }

    #[Test]
    public function web_sin_datos_suficientes_guarda_no_procede_sin_ticket(): void
    {
        Mail::fake();

        UnidadNegocio::factory()->create(['activo' => true]);

        Livewire::test(LibroReclamacionLivewire::class)
            ->call('enviar');

        $libro = LibroReclamacion::query()->latest('ticket')->first();

        $this->assertNotNull($libro);
        $this->assertSame('NO_PROCEDE', $libro->clasificacion);
        $this->assertNull($libro->ticket_id);
        $this->assertDatabaseCount('tickets', 0);
    }

    #[Test]
    public function web_con_datos_de_seguimiento_crea_ticket_y_queda_pendiente_verificacion(): void
    {
        Mail::fake();

        $unidadNegocio = UnidadNegocio::factory()->create(['activo' => true]);
        $this->prepararCatalogosAutocreacion();

        config([
            'libro_reclamacion.unidad_default_id' => $unidadNegocio->id,
        ]);

        Livewire::test(LibroReclamacionLivewire::class)
            ->set('nombre', 'Carlos')
            ->set('apellido_paterno', 'Torres')
            ->set('email', 'carlos.torres@example.com')
            ->set('tipo_pedido', 'reclamo')
            ->call('enviar');

        $libro = LibroReclamacion::query()->latest('ticket')->first();

        $this->assertNotNull($libro);
        $this->assertSame('PENDIENTE_REVISION', $libro->clasificacion);
        $this->assertNotNull($libro->ticket_id);
        $this->assertDatabaseCount('tickets', 1);

        $ticket = Ticket::query()->find($libro->ticket_id);
        $this->assertNotNull($ticket);
        $this->assertSame($unidadNegocio->id, $ticket->unidad_negocio_id);
    }

    protected function crearLibroVinculado(): array
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $unidadNegocio = UnidadNegocio::factory()->create(['activo' => true]);
        $proyecto = Proyecto::factory()->create([
            'unidad_negocio_id' => $unidadNegocio->id,
            'activo' => true,
        ]);

        $area = Area::forceCreate([
            'nombre' => 'Legal Fase 5',
            'email_buzon' => 'legal@example.com',
            'color' => '#111111',
            'icono' => 'fa-briefcase',
            'activo' => true,
        ]);

        $tipoSolicitud = TipoSolicitud::forceCreate([
            'nombre' => 'LIBRO DE RECLAMACIONES',
            'tiempo_solucion' => 48,
            'activo' => true,
        ]);

        $subTipoSolicitud = SubTipoSolicitud::forceCreate([
            'tipo_solicitud_id' => $tipoSolicitud->id,
            'nombre' => 'RECLAMO',
            'tiempo_solucion' => 48,
            'activo' => true,
        ]);

        $canal = Canal::forceCreate([
            'nombre' => 'Libro Reclamación Fase 5',
            'activo' => true,
        ]);

        $estadoTicket = \App\Models\EstadoTicket::forceCreate([
            'nombre' => 'NUEVO FASE 5',
            'color' => '#000000',
            'icono' => 'fa-circle',
            'activo' => true,
        ]);

        $prioridadTicket = PrioridadTicket::forceCreate([
            'nombre' => 'ALTA FASE 5',
            'tiempo_permitido' => 48,
            'color' => '#ff0000',
            'icono' => 'fa-triangle-exclamation',
            'activo' => true,
        ]);

        $ticket = Ticket::forceCreate([
            'unidad_negocio_id' => $unidadNegocio->id,
            'proyecto_id' => $proyecto->id,
            'area_id' => $area->id,
            'tipo_solicitud_id' => $tipoSolicitud->id,
            'sub_tipo_solicitud_id' => $subTipoSolicitud->id,
            'canal_id' => $canal->id,
            'estado_ticket_id' => $estadoTicket->id,
            'prioridad_ticket_id' => $prioridadTicket->id,
            'asunto_inicial' => 'RECLAMO - 75540928',
            'descripcion_inicial' => 'Caso de prueba Fase 5',
            'dni' => '75540928',
            'nombres' => 'Matias Lazaro Pomasoncco',
            'email' => 'mglp2404@example.com',
            'celular' => '937184206',
            'direccion' => 'Jr. 10 de diciembre 245',
            'origen' => 'FORMULARIO_WEB_LIBRO_RECLAMACION',
        ]);

        $libro = LibroReclamacion::forceCreate([
            'unidad_negocio_id' => $unidadNegocio->id,
            'proyecto_id' => $proyecto->id,
            'ticket_id' => $ticket->id,
            'manzana' => 'L',
            'lote' => '11',
            'serie' => 'TCK',
            'numero_reclamo' => 1,
            'codigo_ticket' => 'AYB-000001',
            'codigo' => 'AYB-000002',
            'nombre' => 'Matias',
            'apellido_paterno' => 'Lazaro',
            'apellido_materno' => 'Pomasoncco',
            'domicilio' => 'Jr. 10 de diciembre 245',
            'telefono' => '937184206',
            'email' => 'mglp2404@example.com',
            'tipo_documento' => 'DNI',
            'numero_documento' => '75540928',
            'tipo_bien_contratado' => 'PRODUCTO',
            'monto_reclamado' => 2500,
            'descripcion' => 'Producto en prueba',
            'tipo_pedido' => 'RECLAMO',
            'detalle' => 'Detalle de prueba',
            'pedido' => 'Pedido de prueba',
            'conformidad' => true,
            'clasificacion' => 'PENDIENTE_REVISION',
            'cliente_documento' => '75540928',
            'cliente_nombre' => 'Matias Lazaro Pomasoncco',
            'cliente_email' => 'mglp2404@example.com',
            'cliente_celular' => '937184206',
            'cliente_direccion' => 'Jr. 10 de diciembre 245',
            'asunto' => 'RECLAMO - 75540928',
            'lotes' => [],
            'nota_fuente_titulo' => 'Formulario web',
            'observaciones_internas' => null,
        ]);

        return [$ticket, $libro];
    }

    protected function crearUsuarioConPermisos(array $permisos): User
    {
        $usuario = User::factory()->create([
            'activo' => true,
            'rol' => 'admin',
        ]);

        foreach ($permisos as $permiso) {
            Permission::findOrCreate($permiso, 'web');
        }

        $usuario->givePermissionTo($permisos);

        return $usuario;
    }

    protected function prepararCatalogosAutocreacion(): void
    {
        $area = Area::forceCreate([
            'nombre' => 'Legal Web Test',
            'email_buzon' => 'legal-web@example.com',
            'color' => '#123456',
            'icono' => 'fa-briefcase',
            'activo' => true,
        ]);

        $tipoSolicitud = TipoSolicitud::forceCreate([
            'nombre' => 'LIBRO DE RECLAMACIONES',
            'tiempo_solucion' => 48,
            'activo' => true,
        ]);

        SubTipoSolicitud::forceCreate([
            'tipo_solicitud_id' => $tipoSolicitud->id,
            'nombre' => 'RECLAMO',
            'tiempo_solucion' => 48,
            'activo' => true,
        ]);

        $canal = Canal::forceCreate([
            'nombre' => 'FORMULARIO WEB',
            'activo' => true,
        ]);

        $estadoTicket = EstadoTicket::forceCreate([
            'nombre' => EstadoTicket::NUEVO,
            'color' => '#00aa00',
            'icono' => 'fa-circle',
            'activo' => true,
        ]);

        $prioridadTicket = PrioridadTicket::forceCreate([
            'nombre' => 'ALTA WEB',
            'tiempo_permitido' => 48,
            'color' => '#ff0000',
            'icono' => 'fa-triangle-exclamation',
            'activo' => true,
        ]);

        config([
            'libro_reclamacion.ticket_autocreacion.habilitado' => true,
            'libro_reclamacion.ticket_autocreacion.area_legal_id' => $area->id,
            'libro_reclamacion.ticket_autocreacion.canal_id' => $canal->id,
            'libro_reclamacion.ticket_autocreacion.tipo_solicitud_id' => $tipoSolicitud->id,
            'libro_reclamacion.ticket_autocreacion.prioridad_ticket_id' => $prioridadTicket->id,
        ]);
    }
}
