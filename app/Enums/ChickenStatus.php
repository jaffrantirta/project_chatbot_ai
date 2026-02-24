<?php

namespace App\Enums;

enum ChickenStatus: string
{
    case Sehat   = 'sehat';
    case Sakit   = 'sakit';
    case Mati    = 'mati';
    case Terjual = 'terjual';

    public function label(): string
    {
        return match($this) {
            self::Sehat   => 'Sehat',
            self::Sakit   => 'Sakit',
            self::Mati    => 'Mati',
            self::Terjual => 'Terjual',
        };
    }
}
