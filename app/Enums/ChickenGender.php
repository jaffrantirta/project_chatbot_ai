<?php

namespace App\Enums;

enum ChickenGender: string
{
    case Jantan = 'jantan';
    case Betina = 'betina';

    public function label(): string
    {
        return match($this) {
            self::Jantan => 'Jantan',
            self::Betina => 'Betina',
        };
    }
}
