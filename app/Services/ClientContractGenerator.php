<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Contract;
use App\Models\Contratante;
use App\Models\ContratanteContract;

class ClientContractGenerator
{
    public function __construct(private ContractVariableResolver $resolver) {}

    public function generate(Contract $contract, Contratante $contratante): ContratanteContract
    {
        $existing = ContratanteContract::query()
            ->where('contract_id', $contract->id)
            ->where('contratante_id', $contratante->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        $resolvedBody = $this->resolver->resolve($contract->body, $contratante);

        return ContratanteContract::query()->create([
            'contract_id' => $contract->id,
            'contratante_id' => $contratante->id,
            'body' => $resolvedBody,
            'is_manually_edited' => false,
            'generated_at' => now(),
        ]);
    }

    public function regenerate(ContratanteContract $contratanteContract): ContratanteContract
    {
        $contract = $contratanteContract->contract;
        $contratante = $contratanteContract->contratante;

        $resolvedBody = $this->resolver->resolve($contract->body, $contratante);

        $contratanteContract->update([
            'body' => $resolvedBody,
            'is_manually_edited' => false,
            'generated_at' => now(),
        ]);

        return $contratanteContract->refresh();
    }
}
