<?php

namespace App\Livewire\Erp\Soporte;

use App\Models\Area;
use App\Models\Erp\Soporte\EstadoSoporte;
use App\Models\Erp\Soporte\PrioridadSoporte;
use App\Models\Erp\Soporte\Soporte;
use App\Models\Erp\Soporte\SoporteArchivo;
use App\Models\Erp\Soporte\TipoSoporte;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.erp.layout-erp')]
#[Title('Crear Ticket de Soporte')]

class SoporteCrear extends Component
{
    use WithFileUploads;

    public ?int $tipo_soporte_id = null;
    public ?int $prioridad_soporte_id = null;
    public ?int $area_id = null;
    public string $titulo = '';
    public string $descripcion = '';
    public array $archivos = [];

    public function rules(): array
    {
        return [
            'tipo_soporte_id' => 'required|exists:tipo_soportes,id',
            'prioridad_soporte_id' => 'required|exists:prioridad_soportes,id',
            'area_id' => 'nullable|exists:areas,id',
            'titulo' => 'required|string|min:3|max:255',
            'descripcion' => 'required|string|min:10',
            'archivos' => 'nullable|array',
            'archivos.*' => 'file|max:51200|mimes:pdf,docx,xlsx,pptx,jpg,jpeg,png',
        ];
    }

    public function render()
    {
        // Validar permiso de crear soporte
        $this->authorize('create', Soporte::class);

        return view('livewire.erp.soporte.soporte-crear', [
            'tipos' => TipoSoporte::orderBy('nombre')->get(),
            'prioridades' => PrioridadSoporte::orderBy('nombre')->get(),
            'areas' => Area::where('activo', true)->orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    public function guardar(): void
    {
        $this->validate();

        $estadoAbierto = EstadoSoporte::where('nombre', 'ABIERTO')->first();

        DB::transaction(function () use ($estadoAbierto): void {
            $soporte = Soporte::create([
                'tipo_soporte_id' => $this->tipo_soporte_id,
                'prioridad_soporte_id' => $this->prioridad_soporte_id,
                'area_id' => $this->area_id,
                'estado_soporte_id' => $estadoAbierto?->id,
                'titulo' => $this->titulo,
                'descripcion' => $this->descripcion,
                'solicitante_id' => Auth::id(),
            ]);

            foreach ($this->archivos as $archivo) {
                $filename = $archivo->getClientOriginalName();
                $extension = $archivo->getClientOriginalExtension();
                $path = $archivo->store('soportes/' . $soporte->id, 'public');

                SoporteArchivo::create([
                    'archivable_type' => Soporte::class,
                    'archivable_id' => $soporte->id,
                    'user_id' => Auth::id(),
                    'nombre_original' => $filename,
                    'path' => $path,
                    'url' => Storage::url($path),
                    'descripcion' => 'Adjunto inicial',
                    'extension' => $extension,
                    'size' => $archivo->getSize(),
                    'mime_type' => $archivo->getMimeType(),
                ]);
            }
        });

        session()->flash('success', 'Ticket creado correctamente.');
        $this->redirectRoute('erp.soporte.vista.todo', navigate: true);
    }
}
