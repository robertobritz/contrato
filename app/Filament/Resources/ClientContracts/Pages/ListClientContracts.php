<?php

namespace App\Filament\Resources\ClientContracts\Pages;

use App\Filament\Resources\ClientContracts\ClientContractResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClientContracts extends ListRecords
{
    protected static string $resource = ClientContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
