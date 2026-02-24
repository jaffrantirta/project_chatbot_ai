<?php

namespace App\Enums;

enum ChatSessionStatus: string
{
    case Active = 'active';
    case Closed = 'closed';

    public function label(): string
    {
        return match($this) {
            self::Active => 'Aktif',
            self::Closed => 'Ditutup',
        };
    }
}
