<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Erp\GrupoProyecto\GrupoProyectoCrear;
use App\Models\GrupoProyecto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GrupoProyectoCrearTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function puede_renderizar_el_componente_de_creacion()
    {
        Livewire::test(GrupoProyectoCrear::class)
            ->assertStatus(200);
    }

    /** @test */
    public function puede_crear_un_grupo_proyecto()
    {
        Livewire::test(GrupoProyectoCrear::class)
            ->set('nombre', 'Proyecto Alpha')
            ->set('activo', true)
            ->call('store')
            ->assertDispatched('alertaLivewire');

        $this->assertDatabaseHas('grupo_proyectos', [
            'nombre' => 'Proyecto Alpha',
            'activo' => 1,
            'slug' => 'proyecto-alpha',
        ]);
    }

    /** @test */
    public function valida_campos_requeridos()
    {
        Livewire::test(GrupoProyectoCrear::class)
            ->set('nombre', '')
            ->call('store')
            ->assertHasErrors(['nombre' => 'required']);
    }

    /** @test */
    public function valida_que_el_nombre_sea_unico()
    {
        GrupoProyecto::factory()->create(['nombre' => 'Proyecto Beta']);

        Livewire::test(GrupoProyectoCrear::class)
            ->set('nombre', 'Proyecto Beta')
            ->call('store')
            ->assertHasErrors(['nombre' => 'unique']);
    }
}
