<?php

namespace App\Support;

class EntregaFestCelular
{
    public static function peru(?string $celular): ?string
    {
        $celular = preg_replace('/\D+/', '', (string) $celular);

        if ($celular === '') {
            return null;
        }

        if (strlen($celular) === 9) {
            return '51' . $celular;
        }

        if (strlen($celular) === 11 && str_starts_with($celular, '51')) {
            return $celular;
        }

        return $celular;
    }
}
