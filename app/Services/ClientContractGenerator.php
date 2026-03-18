<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Client;
use App\Models\ClientContract;
use App\Models\Contract;

class ClientContractGenerator
{
    public function __construct(private ContractVariableResolver $resolver) {}

    public function generate(Contract $contract, Client $client): ClientContract
    {
        $existing = ClientContract::query()
            ->where('contract_id', $contract->id)
            ->where('client_id', $client->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        $resolvedBody = $this->resolver->resolve($contract->body, $client);

        return ClientContract::query()->create([
            'contract_id' => $contract->id,
            'client_id' => $client->id,
            'body' => $resolvedBody,
            'is_manually_edited' => false,
            'generated_at' => now(),
        ]);
    }

    public function regenerate(ClientContract $clientContract): ClientContract
    {
        $contract = $clientContract->contract;
        $client = $clientContract->client;

        $resolvedBody = $this->resolver->resolve($contract->body, $client);

        $clientContract->update([
            'body' => $resolvedBody,
            'is_manually_edited' => false,
            'generated_at' => now(),
        ]);

        return $clientContract->refresh();
    }
}
