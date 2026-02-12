<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;

class LoteController extends Controller
{
    public function index()
    {
        return view('modules.cliente.lote');
    }
}
