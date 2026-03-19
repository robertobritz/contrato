<?php

declare(strict_types=1);

namespace App\Models;

use App\ContractSourceType;
use App\ContractType;
use Database\Factories\ContractFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    /** @use HasFactory<ContractFactory> */
    use HasFactory, HasUuids;

    protected $guarded = [];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'source_type' => ContractSourceType::class,
            'contract_type' => ContractType::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contratanteContracts(): HasMany
    {
        return $this->hasMany(ContratanteContract::class);
    }
}
