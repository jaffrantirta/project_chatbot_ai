<?php

namespace App\Enums;

enum DocumentType: string
{
    case Pdf    = 'pdf';
    case Manual = 'manual';
    case Jurnal = 'jurnal';
    case Web    = 'web';

    public function label(): string
    {
        return match($this) {
            self::Pdf    => 'PDF',
            self::Manual => 'Manual',
            self::Jurnal => 'Jurnal',
            self::Web    => 'Web',
        };
    }
}
