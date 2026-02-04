<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Erp\UnidadNegocio\UnidadNegocioCrear;
use App\Models\UnidadNegocio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Livewire\Livewire;
use Tests\TestCase;

class UnidadNegocioCrearTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function puede_renderizar_el_componente()
    {
        Livewire::test(UnidadNegocioCrear::class)
            ->assertStatus(200);
    }

    /** @test */
    public function puede_crear_una_unidad_de_negocio_exitosamente()
    {
        Livewire::test(UnidadNegocioCrear::class)
            ->set('nombre', 'Unidad Test')
            ->set('razon_social', 'Razón Social Test S.A.C.')
            ->call('store')
            ->assertDispatched('alertaLivewire');

        // Verificar que se creó en la base de datos
        $this->assertDatabaseHas('unidad_negocios', [
            'nombre' => 'Unidad Test',
            'razon_social' => 'Razón Social Test S.A.C.',
        ]);
    }

    /** @test */
    public function puede_crear_unidad_con_todos_los_campos_completos()
    {
        Livewire::test(UnidadNegocioCrear::class)
            ->set('nombre', 'Unidad Completa')
            ->set('razon_social', 'Razón Social Completa S.A.C.')
            ->set('ruc', '20123456789')
            ->set('slin_id', 'SLIN123')
            ->set('cavali_girador_tipo_documento', 'DNI')
            ->set('cavali_girador_documento', '12345678')
            ->set('cavali_girador_nombre', 'Juan')
            ->set('cavali_girador_apellido', 'Pérez')
            ->set('cavali_girador_email', 'juan@example.com')
            ->set('cavali_girador_telefono', '987654321')
            ->call('store')
            ->assertDispatched('alertaLivewire');

        // Verificar todos los campos en la base de datos
        $this->assertDatabaseHas('unidad_negocios', [
            'nombre' => 'Unidad Completa',
            'razon_social' => 'Razón Social Completa S.A.C.',
            'ruc' => '20123456789',
            'slin_id' => 'SLIN123',
            'cavali_girador_tipo_documento' => 'DNI',
            'cavali_girador_documento' => '12345678',
            'cavali_girador_nombre' => 'Juan',
            'cavali_girador_apellido' => 'Pérez',
            'cavali_girador_email' => 'juan@example.com',
            'cavali_girador_telefono' => '987654321',
        ]);
    }

    /** @test */
    public function puede_crear_unidad_solo_con_campos_requeridos()
    {
        Livewire::test(UnidadNegocioCrear::class)
            ->set('nombre', 'Unidad Mínima')
            ->set('razon_social', 'Razón Social Mínima')
            ->call('store')
            ->assertDispatched('alertaLivewire');

        $this->assertDatabaseHas('unidad_negocios', [
            'nombre' => 'Unidad Mínima',
            'razon_social' => 'Razón Social Mínima',
            'ruc' => null,
            'slin_id' => null,
        ]);
    }

    /** @test */
    public function valida_que_el_nombre_sea_requerido()
    {
        Livewire::test(UnidadNegocioCrear::class)
            ->set('nombre', '')
            ->set('razon_social', 'Razón Social Test')
            ->call('store')
            ->assertHasErrors(['nombre' => 'required'])
            ->assertDispatched('alertaLivewire');

        $this->assertDatabaseCount('unidad_negocios', 0);
    }

    /** @test */
    public function valida_que_la_razon_social_sea_requerida()
    {
        Livewire::test(UnidadNegocioCrear::class)
            ->set('nombre', 'Unidad Test')
            ->set('razon_social', '')
            ->call('store')
            ->assertHasErrors(['razon_social' => 'required'])
            ->assertDispatched('alertaLivewire');

        $this->assertDatabaseCount('unidad_negocios', 0);
    }

    /** @test */
    public function valida_que_el_nombre_sea_unico()
    {
        // Crear una unidad de negocio existente
        UnidadNegocio::create([
            'nombre' => 'Unidad Existente',
            'razon_social' => 'Razón Social Existente',
        ]);

        Livewire::test(UnidadNegocioCrear::class)
            ->set('nombre', 'Unidad Existente')
            ->set('razon_social', 'Nueva Razón Social')
            ->call('store')
            ->assertHasErrors(['nombre' => 'unique'])
            ->assertDispatched('alertaLivewire');

        $this->assertDatabaseCount('unidad_negocios', 1);
    }

    /** @test */
    public function valida_que_ruc_sea_unico()
    {
        UnidadNegocio::create([
            'nombre' => 'Unidad 1',
            'razon_social' => 'Razón Social 1',
            'ruc' => '20123456789',
        ]);

        Livewire::test(UnidadNegocioCrear::class)
            ->set('nombre', 'Unidad 2')
            ->set('razon_social', 'Razón Social 2')
            ->set('ruc', '20123456789')
            ->call('store')
            ->assertHasErrors(['ruc' => 'unique']);

        $this->assertDatabaseCount('unidad_negocios', 1);
    }

    /** @test */
    public function valida_que_ruc_tenga_11_caracteres()
    {
        Livewire::test(UnidadNegocioCrear::class)
            ->set('nombre', 'Unidad Test')
            ->set('razon_social', 'Razón Social Test')
            ->set('ruc', '123456') // Solo 6 caracteres
            ->call('store')
            ->assertHasErrors(['ruc' => 'size']);
    }

    /** @test */
    public function valida_que_slin_id_sea_unico()
    {
        UnidadNegocio::create([
            'nombre' => 'Unidad 1',
            'razon_social' => 'Razón Social 1',
            'slin_id' => 'SLIN123',
        ]);

        Livewire::test(UnidadNegocioCrear::class)
            ->set('nombre', 'Unidad 2')
            ->set('razon_social', 'Razón Social 2')
            ->set('slin_id', 'SLIN123')
            ->call('store')
            ->assertHasErrors(['slin_id' => 'unique']);

        $this->assertDatabaseCount('unidad_negocios', 1);
    }

    /** @test */
    public function valida_formato_de_email_cavali()
    {
        Livewire::test(UnidadNegocioCrear::class)
            ->set('nombre', 'Unidad Test')
            ->set('razon_social', 'Razón Social Test')
            ->set('cavali_girador_email', 'email-invalido')
            ->call('store')
            ->assertHasErrors(['cavali_girador_email' => 'email']);
    }

    /** @test */
    public function valida_en_tiempo_real_con_updated()
    {
        Livewire::test(UnidadNegocioCrear::class)
            ->set('nombre', '')
            ->assertHasErrors(['nombre' => 'required']);
    }

    /** @test */
    public function valida_longitud_maxima_de_campos()
    {
        $nombreLargo = str_repeat('a', 256);

        Livewire::test(UnidadNegocioCrear::class)
            ->set('nombre', $nombreLargo)
            ->set('razon_social', 'Razón Social Test')
            ->call('store')
            ->assertHasErrors(['nombre' => 'max']);
    }
}
