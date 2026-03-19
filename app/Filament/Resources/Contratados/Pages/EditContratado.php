<?php

declare(strict_types=1);

namespace App\Filament\Resources\Contratados\Pages;

use App\Filament\Resources\Contratados\ContratadoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditContratado extends EditRecord
{
    protected static string $resource = ContratadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
