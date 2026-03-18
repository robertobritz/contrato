<?php

declare(strict_types=1);

namespace App\Models;

use App\MaritalStatus;
use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    /** @use HasFactory<ClientFactory> */
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'marital_status' => MaritalStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function clientContracts(): HasMany
    {
        return $this->hasMany(ClientContract::class);
    }

    /**
     * @return array<string, string>
     */
    public function variableMap(): array
    {
        return [
            '$cliente.nome' => $this->name ?? '',
            '$cliente.email' => $this->email ?? '',
            '$cliente.telefone' => $this->phone ?? '',
            '$cliente.cpf' => $this->cpf ?? '',
            '$cliente.rg' => $this->rg ?? '',
            '$cliente.nascimento' => $this->birth_date?->format('d/m/Y') ?? '',
            '$cliente.nacionalidade' => $this->nationality ?? '',
            '$cliente.estado_civil' => $this->marital_status?->label() ?? '',
            '$cliente.profissao' => $this->profession ?? '',
            '$cliente.endereco' => $this->address ?? '',
            '$cliente.endereco_numero' => $this->address_number ?? '',
            '$cliente.endereco_complemento' => $this->address_complement ?? '',
            '$cliente.bairro' => $this->neighborhood ?? '',
            '$cliente.cidade' => $this->city ?? '',
            '$cliente.estado' => $this->state ?? '',
            '$cliente.cep' => $this->zip_code ?? '',
        ];
    }
}
