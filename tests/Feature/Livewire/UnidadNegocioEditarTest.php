<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Erp\UnidadNegocio\UnidadNegocioEditar;
use App\Models\UnidadNegocio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UnidadNegocioEditarTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function puede_renderizar_el_componente_de_edicion()
    {
        $unidad = UnidadNegocio::factory()->create();

        Livewire::test(UnidadNegocioEditar::class, ['id' => $unidad->id])
            ->assertStatus(200)
            ->assertSet('nombre', $unidad->nombre);
    }

    /** @test */
    public function puede_actualizar_una_unidad_de_negocio()
    {
        $unidad = UnidadNegocio::factory()->create([
            'nombre' => 'Nombre Original',
            'razon_social' => 'Razon Original',
        ]);

        Livewire::test(UnidadNegocioEditar::class, ['id' => $unidad->id])
            ->set('nombre', 'Nombre Editado')
            ->set('razon_social', 'Razon Editada')
            ->call('update')
            ->assertDispatched('alertaLivewire');

        $this->assertDatabaseHas('unidad_negocios', [
            'id' => $unidad->id,
            'nombre' => 'Nombre Editado',
            'razon_social' => 'Razon Editada',
        ]);
    }

    /** @test */
    public function valida_campos_al_actualizar()
    {
        $unidad = UnidadNegocio::factory()->create();

        Livewire::test(UnidadNegocioEditar::class, ['id' => $unidad->id])
            ->set('nombre', '') // Requerido
            ->call('update')
            ->assertHasErrors(['nombre' => 'required']);
    }

    /** @test */
    public function valida_duplicidad_excepto_si_mismo()
    {
        $unidad1 = UnidadNegocio::factory()->create(['nombre' => 'Unidad 1', 'ruc' => '11111111111']);
        $unidad2 = UnidadNegocio::factory()->create(['nombre' => 'Unidad 2', 'ruc' => '22222222222']);

        // Intentar actualizar unidad1 con el nombre de unidad2
        Livewire::test(UnidadNegocioEditar::class, ['id' => $unidad1->id])
            ->set('nombre', 'Unidad 2')
            ->call('update')
            ->assertHasErrors(['nombre' => 'unique']);

        // Intentar actualizar unidad1 con su mismo nombre (debe pasar)
        Livewire::test(UnidadNegocioEditar::class, ['id' => $unidad1->id])
            ->set('nombre', 'Unidad 1')
            ->call('update')
            ->assertHasNoErrors(['nombre']);
    }

    /** @test */
    public function puede_eliminar_una_unidad_de_negocio()
    {
        $unidad = UnidadNegocio::factory()->create();

        Livewire::test(UnidadNegocioEditar::class, ['id' => $unidad->id])
            ->call('eliminarUnidadNegocioOn');

        // Verificar Soft Deletes
        $this->assertSoftDeleted('unidad_negocios', [
            'id' => $unidad->id,
        ]);
    }
}
