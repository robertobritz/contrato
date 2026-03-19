<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ObjetoContratoFactory;
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
}
