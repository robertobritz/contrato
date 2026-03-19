<?php

declare(strict_types=1);

namespace App\Filament\Resources\Contratantes\Pages;

use App\Filament\Resources\Contratantes\ContratanteResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditContratante extends EditRecord
{
    protected static string $resource = ContratanteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
