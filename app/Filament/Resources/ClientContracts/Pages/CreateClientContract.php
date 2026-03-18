<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClientContracts\Pages;

use App\Filament\Resources\ClientContracts\ClientContractResource;
use App\Models\Client;
use App\Models\Contract;
use App\Services\ClientContractGenerator;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateClientContract extends CreateRecord
{
    protected static string $resource = ClientContractResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $contract = Contract::findOrFail($data['contract_id']);
        $client = Client::findOrFail($data['client_id']);

        $clientContract = app(ClientContractGenerator::class)->generate($contract, $client);

        if ($clientContract->wasRecentlyCreated === false) {
            Notification::make()
                ->warning()
                ->title('Combinação já existente')
                ->body('Este cliente já possui um contrato gerado para o contrato base selecionado.')
                ->send();
        }

        return $clientContract;
    }
}
