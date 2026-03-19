<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContratanteContracts\Pages;

use App\Filament\Resources\ContratanteContracts\ContratanteContractResource;
use App\Models\Contract;
use App\Models\Contratado;
use App\Models\Contratante;
use App\Models\ObjetoContrato;
use App\Services\ClientContractGenerator;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateContratanteContract extends CreateRecord
{
    protected static string $resource = ContratanteContractResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $contract = Contract::findOrFail($data['contract_id']);
        $contratante = Contratante::findOrFail($data['contratante_id']);
        $contratado = isset($data['contratado_id']) ? Contratado::find($data['contratado_id']) : null;
        $objetoContrato = isset($data['objeto_contrato_id']) ? ObjetoContrato::find($data['objeto_contrato_id']) : null;

        $contratanteContract = app(ClientContractGenerator::class)->generate($contract, $contratante, $contratado, $objetoContrato);

        if ($contratanteContract->wasRecentlyCreated === false) {
            Notification::make()
                ->warning()
                ->title('Combinação já existente')
                ->body('Este contratante já possui um contrato gerado para o contrato base selecionado.')
                ->send();
        }

        return $contratanteContract->refresh();
    }
}
