<?php

declare(strict_types=1);

namespace App\Filament\Resources\Contratados\Pages;

use App\Filament\Resources\Contratados\ContratadoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContratados extends ListRecords
{
    protected static string $resource = ContratadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
