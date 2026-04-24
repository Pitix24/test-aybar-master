<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;

class AvanceProyectoController extends Controller
{
    public function index()
    {
        return view('modules.cliente.avance-proyecto');
    }
}
