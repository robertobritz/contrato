<?php

declare(strict_types=1);

namespace App;

enum DocumentType: string
{
    case CPF = 'cpf';
    case CNPJ = 'cnpj';

    public function label(): string
    {
        return match ($this) {
            self::CPF => 'CPF',
            self::CNPJ => 'CNPJ',
        };
    }

    public function mask(): string
    {
        return match ($this) {
            self::CPF => '999.999.999-99',
            self::CNPJ => '99.999.999/9999-99',
        };
    }

    public function maxLength(): int
    {
        return match ($this) {
            self::CPF => 14,
            self::CNPJ => 18,
        };
    }

    public function placeholder(): string
    {
        return match ($this) {
            self::CPF => '000.000.000-00',
            self::CNPJ => '00.000.000/0000-00',
        };
    }
}
