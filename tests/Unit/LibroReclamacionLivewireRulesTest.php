<?php

namespace Tests\Unit;

use App\Livewire\Web\LibroReclamacion\LibroReclamacionLivewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LibroReclamacionLivewireRulesTest extends TestCase
{
    #[Test]
    public function el_representante_legal_es_opcional_en_el_formulario_web(): void
    {
        $component = new class extends LibroReclamacionLivewire {
            public function exposedRules(): array
            {
                return $this->rules();
            }
        };

        $rules = $component->exposedRules();

        $this->assertSame('nullable|string|max:255', $rules['representante_legal_nombre']);
        $this->assertSame('nullable|string|max:255', $rules['representante_legal_apellido_paterno']);
        $this->assertSame('nullable|string|max:255', $rules['representante_legal_apellido_materno']);
    }
}
