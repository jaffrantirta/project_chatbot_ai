<?php

namespace App\Enums;

enum HealthStatus: string
{
    case Sehat            = 'sehat';
    case Sakit            = 'sakit';
    case DalamPengobatan  = 'dalam_pengobatan';
    case Sembuh           = 'sembuh';
    case Mati             = 'mati';

    public function label(): string
    {
        return match($this) {
            self::Sehat           => 'Sehat',
            self::Sakit           => 'Sakit',
            self::DalamPengobatan => 'Dalam Pengobatan',
            self::Sembuh          => 'Sembuh',
            self::Mati            => 'Mati',
        };
    }
}
