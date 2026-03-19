<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContratanteContracts\Pages;

use App\Filament\Resources\ContratanteContracts\ContratanteContractResource;
use App\Models\Contract;
use App\Models\Contratante;
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

        $contratanteContract = app(ClientContractGenerator::class)->generate($contract, $contratante);

        if ($contratanteContract->wasRecentlyCreated === false) {
            Notification::make()
                ->warning()
                ->title('Combinação já existente')
                ->body('Este contratante já possui um contrato gerado para o contrato base selecionado.')
                ->send();
        }

        $updateData = [
            'contratado_id' => $data['contratado_id'] ?? null,
            'objeto_contrato_id' => $data['objeto_contrato_id'] ?? null,
        ];

        $contratanteContract->update($updateData);

        return $contratanteContract->refresh();
    }
}
