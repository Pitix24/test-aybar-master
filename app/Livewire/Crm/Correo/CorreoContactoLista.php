<?php

namespace App\Livewire\Crm\Correo;

use App\Models\CorreoContacto;
use App\Models\CorreoLista;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.erp.layout-erp')]
#[Title('Gestión de Contactos - Email Marketing')]
class CorreoContactoLista extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $lista_id = '';
    public $archivo_excel;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = CorreoContacto::with('listas')
            ->where(function ($q) {
                $q->where('nombres', 'like', '%' . $this->search . '%')
                    ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });

        if ($this->lista_id) {
            $query->whereHas('listas', function ($q) {
                $q->where('correo_listas.id', $this->lista_id);
            });
        }

        return view('livewire.crm.correo.correo-contacto-lista', [
            'contactos' => $query->latest()->paginate(15),
            'listas' => CorreoLista::all()
        ]);
    }
}
