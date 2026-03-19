<?php

declare(strict_types=1);

namespace App\Filament\Resources\Contratados\Pages;

use App\Filament\Resources\Contratados\ContratadoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateContratado extends CreateRecord
{
    protected static string $resource = ContratadoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}
