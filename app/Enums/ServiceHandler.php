<?php

namespace App\Enums;

enum ServiceHandler: string
{
    case Generic = 'generic';
    case Dtsen = 'dtsen';
    case Pbi = 'pbi';

    public function label(): string
    {
        return match ($this) {
            self::Generic => 'Layanan Umum',
            self::Dtsen => 'SK DTSEN',
            self::Pbi => 'Reaktivasi KIS/PBI-JK',
        };
    }
}
