<?php

declare(strict_types=1);

namespace App\Filament\Resources\Contracts\Pages;

use App\ContractSourceType;
use App\Filament\Resources\Contracts\ContractResource;
use Filament\Resources\Pages\CreateRecord;

class CreateContract extends CreateRecord
{
    protected static string $resource = ContractResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        if (($data['source_type'] ?? ContractSourceType::Manual->value) === ContractSourceType::Upload->value) {
            $data['source_type'] = ContractSourceType::Upload->value;
        } else {
            $data['source_type'] = ContractSourceType::Manual->value;
            $data['original_file_path'] = null;
        }

        return $data;
    }
}
