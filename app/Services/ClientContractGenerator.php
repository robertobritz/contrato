<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Contract;
use App\Models\Contratado;
use App\Models\Contratante;
use App\Models\ContratanteContract;
use App\Models\ObjetoContrato;

class ClientContractGenerator
{
    public function __construct(private ContractVariableResolver $resolver) {}

    public function generate(Contract $contract, Contratante $contratante, ?Contratado $contratado = null, ?ObjetoContrato $objetoContrato = null): ContratanteContract
    {
        $existing = ContratanteContract::query()
            ->where('contract_id', $contract->id)
            ->where('contratante_id', $contratante->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        $resolvedBody = $this->resolver->resolve($contract->body, $contratante, $contratado, $objetoContrato);

        return ContratanteContract::query()->create([
            'contract_id' => $contract->id,
            'contratante_id' => $contratante->id,
            'contratado_id' => $contratado?->id,
            'objeto_contrato_id' => $objetoContrato?->id,
            'body' => $resolvedBody,
            'is_manually_edited' => false,
            'generated_at' => now(),
        ]);
    }

    public function regenerate(ContratanteContract $contratanteContract): ContratanteContract
    {
        $contract = $contratanteContract->contract;
        $contratante = $contratanteContract->contratante;
        $contratado = $contratanteContract->contratado;
        $objetoContrato = $contratanteContract->objetoContrato;

        $resolvedBody = $this->resolver->resolve($contract->body, $contratante, $contratado, $objetoContrato);

        $contratanteContract->update([
            'body' => $resolvedBody,
            'is_manually_edited' => false,
            'generated_at' => now(),
        ]);

        return $contratanteContract->refresh();
    }
}
