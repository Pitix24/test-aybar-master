<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;

class TutorialController extends Controller
{
    public function index()
    {
        return view('modules.cliente.tutorial');
    }
}
