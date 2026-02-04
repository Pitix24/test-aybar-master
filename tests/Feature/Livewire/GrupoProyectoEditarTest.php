<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Erp\GrupoProyecto\GrupoProyectoEditar;
use App\Models\GrupoProyecto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GrupoProyectoEditarTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function puede_renderizar_el_componente_de_edicion()
    {
        $grupo = GrupoProyecto::factory()->create();

        Livewire::test(GrupoProyectoEditar::class, ['id' => $grupo->id])
            ->assertStatus(200)
            ->assertSet('nombre', $grupo->nombre);
    }

    /** @test */
    public function puede_actualizar_un_grupo_proyecto()
    {
        $grupo = GrupoProyecto::factory()->create([
            'nombre' => 'Nombre Original',
            'activo' => false,
        ]);

        Livewire::test(GrupoProyectoEditar::class, ['id' => $grupo->id])
            ->set('nombre', 'Nombre Editado')
            ->set('activo', true)
            ->call('update')
            ->assertDispatched('alertaLivewire');

        $this->assertDatabaseHas('grupo_proyectos', [
            'id' => $grupo->id,
            'nombre' => 'Nombre Editado',
            'activo' => 1,
        ]);
    }

    /** @test */
    public function valida_campos_al_actualizar()
    {
        $grupo = GrupoProyecto::factory()->create();

        Livewire::test(GrupoProyectoEditar::class, ['id' => $grupo->id])
            ->set('nombre', '')
            ->call('update')
            ->assertHasErrors(['nombre' => 'required']);
    }

    /** @test */
    public function valida_duplicidad_excepto_si_mismo()
    {
        $grupo1 = GrupoProyecto::factory()->create(['nombre' => 'Grupo 1']);
        $grupo2 = GrupoProyecto::factory()->create(['nombre' => 'Grupo 2']);

        // Intentar actualizar grupo1 con el nombre de grupo2
        Livewire::test(GrupoProyectoEditar::class, ['id' => $grupo1->id])
            ->set('nombre', 'Grupo 2')
            ->call('update')
            ->assertHasErrors(['nombre' => 'unique']);

        // Intentar actualizar grupo1 con su mismo nombre (debe pasar)
        Livewire::test(GrupoProyectoEditar::class, ['id' => $grupo1->id])
            ->set('nombre', 'Grupo 1')
            ->call('update')
            ->assertHasNoErrors(['nombre']);
    }

    /** @test */
    public function puede_eliminar_un_grupo_proyecto()
    {
        $grupo = GrupoProyecto::factory()->create();

        Livewire::test(GrupoProyectoEditar::class, ['id' => $grupo->id])
            ->call('eliminarGrupoProyectoOn');

        // Verificar Soft Deletes
        $this->assertSoftDeleted('grupo_proyectos', [
            'id' => $grupo->id,
        ]);
    }
}
