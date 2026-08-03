<?php

namespace App\Livewire\Cliente\Layout;

use App\Models\Proyecto;
use App\Models\UnidadNegocio;
use App\Services\AybarSlinService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class MenuDocumentosSidebar extends Component
{
    public $proyectos = [];
    public $readyToLoad = false;

    public function loadProyectos(AybarSlinService $slinService)
    {
        $this->readyToLoad = true;

        try {
            $perfil = Auth::user()->perfilCliente;
            if (!$perfil || !$perfil->dni) {
                return;
            }

            // Cachear proyectos del cliente para no hacer llamadas a la API en cada request si no es necesario
            // Sin embargo, por simplicidad, los obtenemos aquí de manera diferida.
            $cliente = $slinService->getCliente($perfil->dni);

            if (empty($cliente)) {
                return;
            }

            $this->proyectos = collect($this->obtenerProyectosDelCliente($slinService, $cliente))
                ->map(function($p) {
                    return [
                        'id' => $p->id,
                        'nombre' => $p->nombre,
                    ];
                })->toArray();

        } catch (\Exception $e) {
            Log::error('Error en MenuDocumentosSidebar: ' . $e->getMessage());
        }
    }

    private function obtenerProyectosDelCliente(AybarSlinService $slinService, array $cliente): array
    {
        try {
            $proyectosEncontrados = [];

            foreach ($cliente['empresas'] ?? [] as $empresa) {
                try {
                    $unidadNegocio = UnidadNegocio::where('slin_id', (string)$empresa['id_empresa'])->first();
                    
                    if (!$unidadNegocio) {
                        continue;
                    }

                    $lotes = $slinService->getLotes(
                        $empresa['codigo'],
                        $empresa['id_empresa']
                    );

                    $slinProyectoIds = [];
                    foreach ($lotes as $lote) {
                        if (isset($lote['id_servicio']) && $lote['id_servicio'] === '02') {
                            if (isset($lote['id_proyecto'])) {
                                $slinProyectoIds[] = (string) $lote['id_proyecto'];
                            }
                        }
                    }

                    if (!empty($slinProyectoIds)) {
                        $slinProyectoIds = array_unique(array_map('strval', $slinProyectoIds));

                        $proyectosDeEmpresa = Proyecto::where('unidad_negocio_id', $unidadNegocio->id)
                            ->whereIn('slin_id', $slinProyectoIds)
                            ->where('activo', true)
                            ->get();

                        foreach($proyectosDeEmpresa as $p) {
                            $proyectosEncontrados[$p->id] = $p;
                        }
                    }

                } catch (\Exception $e) {
                    Log::warning('Error al obtener lotes para empresa ' . $empresa['id_empresa'] . ': ' . $e->getMessage());
                }
            }

            return array_values($proyectosEncontrados);
        } catch (\Exception $e) {
            Log::error('Error en obtenerProyectosDelCliente: ' . $e->getMessage());
            return [];
        }
    }

    public function render()
    {
        return view('livewire.cliente.layout.menu-documentos-sidebar');
    }
}
