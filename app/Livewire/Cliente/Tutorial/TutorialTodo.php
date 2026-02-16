<?php

namespace App\Livewire\Cliente\Tutorial;

use Livewire\Component;

use App\Models\Tutorial;

class TutorialTodo extends Component
{
    public function registrarClick($id)
    {
        $tutorial = Tutorial::find($id);
        if ($tutorial) {
            $tutorial->increment('clicks');
        }
    }

    public function render()
    {
        $tutoriales = Tutorial::where('activo', true)
            ->with('miniatura')
            ->orderBy('orden')
            ->get();

        return view('livewire.cliente.tutorial.tutorial-todo', compact('tutoriales'));
    }
}
