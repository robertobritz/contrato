<?php

declare(strict_types=1);

namespace App;

enum ContractSourceType: string
{
    case Upload = 'upload';
    case Manual = 'manual';

    public function label(): string
    {
        return match ($this) {
            self::Upload => 'Fazer upload de arquivo Word',
            self::Manual => 'Escrever manualmente',
        };
    }
}
