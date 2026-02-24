<?php

namespace App\Enums;

enum MedicineType: string
{
    case Antibiotik  = 'antibiotik';
    case Vaksin      = 'vaksin';
    case Vitamin     = 'vitamin';
    case Antiparasit = 'antiparasit';
    case Antifungi   = 'antifungi';
    case Lainnya     = 'lainnya';

    public function label(): string
    {
        return match($this) {
            self::Antibiotik  => 'Antibiotik',
            self::Vaksin      => 'Vaksin',
            self::Vitamin     => 'Vitamin',
            self::Antiparasit => 'Antiparasit',
            self::Antifungi   => 'Antifungi',
            self::Lainnya     => 'Lainnya',
        };
    }
}
