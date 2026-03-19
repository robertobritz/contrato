<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Contratante;

class ContractVariableResolver
{
    public function resolve(string $body, Contratante $contratante): string
    {
        $map = $this->sortedMap($contratante);

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
    public function unresolvedVariables(string $body, Contratante $contratante): array
    {
        $map = $this->sortedMap($contratante);
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
    private function sortedMap(Contratante $contratante): array
    {
        $map = $contratante->variableMap();
        uksort($map, fn(string $a, string $b): int => strlen($b) <=> strlen($a));

        return $map;
    }
}
