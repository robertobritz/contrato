<?php

declare(strict_types=1);

namespace App\Filament\Resources\Contratantes\Pages;

use App\Filament\Resources\Contratantes\ContratanteResource;
use Filament\Resources\Pages\CreateRecord;

class CreateContratante extends CreateRecord
{
    protected static string $resource = ContratanteResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}
