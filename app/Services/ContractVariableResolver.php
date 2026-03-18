<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Client;

class ContractVariableResolver
{
    public function resolve(string $body, Client $client): string
    {
        $map = $this->sortedMap($client);

        foreach ($map as $variable => $value) {
            if ($value !== '') {
                $body = str_replace($variable, $value, $body);
            }
        }

        return $body;
    }

    /**
     * @return array<int, string>
     */
    public function unresolvedVariables(string $body, Client $client): array
    {
        $map = $this->sortedMap($client);
        $unresolved = [];

        foreach ($map as $variable => $value) {
            if ($value === '' && str_contains($body, $variable)) {
                $unresolved[] = $variable;
            }
        }

        return $unresolved;
    }

    /**
     * @return array<string, string>
     */
    private function sortedMap(Client $client): array
    {
        $map = $client->variableMap();
        uksort($map, fn (string $a, string $b): int => strlen($b) <=> strlen($a));

        return $map;
    }
}
