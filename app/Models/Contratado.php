<?php

declare(strict_types=1);

namespace App\Models;

use App\DocumentType;
use App\MaritalStatus;
use Database\Factories\ContratadoFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contratado extends Model
{
    /** @use HasFactory<ContratadoFactory> */
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'marital_status' => MaritalStatus::class,
            'document_type' => DocumentType::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function objetoContratos(): HasMany
    {
        return $this->hasMany(ObjetoContrato::class);
    }

    /**
     * Returns all available variable keys mapped to their human-readable labels.
     *
     * @return array<string, string>
     */
    public static function availableVariableLabels(): array
    {
        return [
            '$contratado.nome' => 'Nome completo',
            '$contratado.email' => 'E-mail',
            '$contratado.telefone' => 'Telefone',
            '$contratado.cpf' => 'CPF',
            '$contratado.rg' => 'RG',
            '$contratado.nascimento' => 'Data de nascimento',
            '$contratado.nacionalidade' => 'Nacionalidade',
            '$contratado.estado_civil' => 'Estado civil',
            '$contratado.profissao' => 'Profissão',
            '$contratado.endereco' => 'Endereço (logradouro)',
            '$contratado.endereco_numero' => 'Número do endereço',
            '$contratado.endereco_complemento' => 'Complemento',
            '$contratado.bairro' => 'Bairro',
            '$contratado.cidade' => 'Cidade',
            '$contratado.estado' => 'Estado (UF)',
            '$contratado.cep' => 'CEP',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function variableMap(): array
    {
        return [
            '$contratado.nome' => $this->name ?? '',
            '$contratado.email' => $this->email ?? '',
            '$contratado.telefone' => $this->phone ?? '',
            '$contratado.cpf' => $this->cpf ?? '',
            '$contratado.rg' => $this->rg ?? '',
            '$contratado.nascimento' => $this->birth_date?->format('d/m/Y') ?? '',
            '$contratado.nacionalidade' => $this->nationality ?? '',
            '$contratado.estado_civil' => $this->marital_status?->label() ?? '',
            '$contratado.profissao' => $this->profession ?? '',
            '$contratado.endereco' => $this->address ?? '',
            '$contratado.endereco_numero' => $this->address_number ?? '',
            '$contratado.endereco_complemento' => $this->address_complement ?? '',
            '$contratado.bairro' => $this->neighborhood ?? '',
            '$contratado.cidade' => $this->city ?? '',
            '$contratado.estado' => $this->state ?? '',
            '$contratado.cep' => $this->zip_code ?? '',
        ];
    }
}
