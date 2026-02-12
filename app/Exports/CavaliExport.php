<?php

namespace App\Exports;

use App\Models\EnvioCavali;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CavaliExport implements WithMultipleSheets
{
    public function __construct(
        protected EnvioCavali $envio
    ) {}

    public function sheets(): array
    {
        return [
            new CavaliAceptanteExport($this->envio),
            new CavaliLetrasExport($this->envio),
            new CavaliGiradorExport($this->envio),
        ];
    }
}
