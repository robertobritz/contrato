<?php

declare(strict_types=1);

namespace App;

enum ContractType: string
{
    case Locacao = 'locacao';
    case CompraVenda = 'compra_venda';
    case PrestacaoServico = 'prestacao_servico';
    case TermoAditivo = 'termo_aditivo';

    public function label(): string
    {
        return match ($this) {
            self::Locacao => 'Locação',
            self::CompraVenda => 'Compra e Venda',
            self::PrestacaoServico => 'Prestação de Serviço',
            self::TermoAditivo => 'Termo Aditivo',
        };
    }
}
 