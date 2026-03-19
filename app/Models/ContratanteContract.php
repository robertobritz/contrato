<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ContratanteContractFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContratanteContract extends Model
{
    /** @use HasFactory<ContratanteContractFactory> */
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_manually_edited' => 'boolean',
            'generated_at' => 'datetime',
        ];
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function contratante(): BelongsTo
    {
        return $this->belongsTo(Contratante::class);
    }
}
