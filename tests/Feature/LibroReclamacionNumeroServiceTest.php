<?php

namespace Tests\Feature;

use App\Services\LibroReclamacion\LibroReclamacionNumeroService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use App\Models\LibroReclamacion\LibroReclamacion;
use Tests\TestCase;

class LibroReclamacionNumeroServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('libro_reclamacions');

        Schema::create('libro_reclamacions', function (Blueprint $table) {
            $table->bigIncrements('ticket');
            $table->foreignId('unidad_negocio_id')->nullable();
            $table->unsignedBigInteger('numero_reclamo')->nullable();
            $table->string('codigo', 20)->nullable();
            $table->string('codigo_ticket', 20)->nullable();
            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->foreignId('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /** @test */
    public function genera_codigo_tck_cuando_no_hay_unidad_de_negocio()
    {
        Config::set('libro_reclamacion_ticket.serie', 'TCK');

        $resultado = app(LibroReclamacionNumeroService::class)->generar(null);

        $this->assertSame('TCK', $resultado['serie']);
        $this->assertSame(1, $resultado['numero_reclamo']);
        $this->assertSame('TCK-000001', $resultado['codigo_ticket']);
    }

    /** @test */
    public function sincroniza_codigo_y_codigo_ticket_en_el_modelo()
    {
        $reclamo = LibroReclamacion::query()->create([
            'codigo_ticket' => 'TCK-000777',
            'numero_reclamo' => 777,
        ]);

        $this->assertSame('TCK-000777', $reclamo->codigo);
        $this->assertSame('TCK-000777', $reclamo->codigo_ticket);
        $this->assertDatabaseHas('libro_reclamacions', [
            'ticket' => $reclamo->ticket,
            'codigo' => 'TCK-000777',
            'codigo_ticket' => 'TCK-000777',
        ]);
    }
}
