<?php

namespace App\Exports\Letra;

use App\Models\EnvioCavali;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromCollection;
class CavaliExport implements WithMultipleSheets
{
    public function __construct(
        protected EnvioCavali $envio
    ) {
    }

    public function sheets(): array
    {
        return [
            new CavaliAceptanteExport($this->envio),
            new CavaliLetrasExport($this->envio),
            new CavaliGiradorExport($this->envio),
        ];
    }
}
