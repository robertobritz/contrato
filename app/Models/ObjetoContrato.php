<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObjetoContrato extends Model
{
    /** @use HasFactory<ObjetoContratoFactory> */
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'quantidade' => 'decimal:2',
            'valor' => 'decimal:2',
        ];
    }

    public function contratante(): BelongsTo
    {
        return $this->belongsTo(Contratante::class);
    }

    public function contratado(): BelongsTo
    {
        return $this->belongsTo(Contratado::class);
    }

    /**
     * Returns all available variable keys mapped to their human-readable labels.
     *
     * @return array<string, string>
     */
    public static function availableVariableLabels(): array
    {
        return [
            '$objeto.tipo' => 'Tipo (serviço ou produto)',
            '$objeto.descricao' => 'Descrição',
            '$objeto.quantidade' => 'Quantidade',
            '$objeto.unidade' => 'Unidade de medida',
            '$objeto.valor' => 'Valor unitário',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function variableMap(): array
    {
        return [
            '$objeto.tipo' => $this->tipo ?? '',
            '$objeto.descricao' => $this->descricao ?? '',
            '$objeto.quantidade' => (string) ($this->quantidade ?? ''),
            '$objeto.unidade' => $this->unidade ?? '',
            '$objeto.valor' => (string) ($this->valor ?? ''),
        ];
    }
}
