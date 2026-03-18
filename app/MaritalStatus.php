<?php

declare(strict_types=1);

namespace App;

enum MaritalStatus: string
{
    case Solteiro = 'solteiro';
    case Casado = 'casado';
    case Divorciado = 'divorciado';
    case Viuvo = 'viuvo';
    case SeparadoJudicialmente = 'separado_judicialmente';
    case UniaoEstavel = 'uniao_estavel';

    public function label(): string
    {
        return match ($this) {
            self::Solteiro => 'Solteiro(a)',
            self::Casado => 'Casado(a)',
            self::Divorciado => 'Divorciado(a)',
            self::Viuvo => 'Viúvo(a)',
            self::SeparadoJudicialmente => 'Separado(a) Judicialmente',
            self::UniaoEstavel => 'União Estável',
        };
    }
}
