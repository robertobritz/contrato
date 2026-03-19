<?php

declare(strict_types=1);

namespace App\Models;

use App\MaritalStatus;
use Database\Factories\ContratanteFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contratante extends Model
{
    /** @use HasFactory<ContratanteFactory> */
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

    public function contratanteContracts(): HasMany
    {
        return $this->hasMany(ContratanteContract::class);
    }

    /**
     * Returns all available variable keys mapped to their human-readable labels.
     *
     * @return array<string, string>
     */
    public static function availableVariableLabels(): array
    {
        return [
            '$contratante.nome' => 'Nome completo',
            '$contratante.email' => 'E-mail',
            '$contratante.telefone' => 'Telefone',
            '$contratante.cpf' => 'CPF',
            '$contratante.rg' => 'RG',
            '$contratante.nascimento' => 'Data de nascimento',
            '$contratante.nacionalidade' => 'Nacionalidade',
            '$contratante.estado_civil' => 'Estado civil',
            '$contratante.profissao' => 'Profissão',
            '$contratante.endereco' => 'Endereço (logradouro)',
            '$contratante.endereco_numero' => 'Número do endereço',
            '$contratante.endereco_complemento' => 'Complemento',
            '$contratante.bairro' => 'Bairro',
            '$contratante.cidade' => 'Cidade',
            '$contratante.estado' => 'Estado (UF)',
            '$contratante.cep' => 'CEP',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function variableMap(): array
    {
        return [
            '$contratante.nome' => $this->name ?? '',
            '$contratante.email' => $this->email ?? '',
            '$contratante.telefone' => $this->phone ?? '',
            '$contratante.cpf' => $this->cpf ?? '',
            '$contratante.rg' => $this->rg ?? '',
            '$contratante.nascimento' => $this->birth_date?->format('d/m/Y') ?? '',
            '$contratante.nacionalidade' => $this->nationality ?? '',
            '$contratante.estado_civil' => $this->marital_status?->label() ?? '',
            '$contratante.profissao' => $this->profession ?? '',
            '$contratante.endereco' => $this->address ?? '',
            '$contratante.endereco_numero' => $this->address_number ?? '',
            '$contratante.endereco_complemento' => $this->address_complement ?? '',
            '$contratante.bairro' => $this->neighborhood ?? '',
            '$contratante.cidade' => $this->city ?? '',
            '$contratante.estado' => $this->state ?? '',
            '$contratante.cep' => $this->zip_code ?? '',
        ];
    }
}
