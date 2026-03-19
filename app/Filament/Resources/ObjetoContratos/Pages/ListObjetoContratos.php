<?php

declare(strict_types=1);

namespace App\Filament\Resources\ObjetoContratos\Pages;

use App\Filament\Resources\ObjetoContratos\ObjetoContratoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListObjetoContratos extends ListRecords
{
    protected static string $resource = ObjetoContratoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
