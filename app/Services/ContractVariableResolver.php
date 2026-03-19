<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Contratado;
use App\Models\Contratante;
use App\Models\ObjetoContrato;

class ContractVariableResolver
{
    public function resolve(string $body, Contratante $contratante, ?Contratado $contratado = null, ?ObjetoContrato $objetoContrato = null): string
    {
        $map = $this->sortedMap($contratante, $contratado, $objetoContrato);

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
    public function unresolvedVariables(string $body, Contratante $contratante, ?Contratado $contratado = null, ?ObjetoContrato $objetoContrato = null): array
    {
        $map = $this->sortedMap($contratante, $contratado, $objetoContrato);
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
    private function sortedMap(Contratante $contratante, ?Contratado $contratado = null, ?ObjetoContrato $objetoContrato = null): array
    {
        $map = $contratante->variableMap();

        if ($contratado !== null) {
            $map = array_merge($map, $contratado->variableMap());
        }

        if ($objetoContrato !== null) {
            $map = array_merge($map, $objetoContrato->variableMap());
        }

        uksort($map, fn(string $a, string $b): int => strlen($b) <=> strlen($a));

        return $map;
    }
}
