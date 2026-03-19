<?php

declare(strict_types=1);

namespace App\Filament\Resources\ObjetoContratos\Pages;

use App\Filament\Resources\ObjetoContratos\ObjetoContratoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditObjetoContrato extends EditRecord
{
    protected static string $resource = ObjetoContratoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
