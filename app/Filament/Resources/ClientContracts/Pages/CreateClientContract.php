<?php

namespace App\Filament\Resources\ClientContracts\Pages;

use App\Filament\Resources\ClientContracts\ClientContractResource;
use Filament\Resources\Pages\CreateRecord;

class CreateClientContract extends CreateRecord
{
    protected static string $resource = ClientContractResource::class;
}
