<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;

class ReglamentoController extends Controller
{
    public function index()
    {
        return view('modules.cliente.reglamento');
    }
}
