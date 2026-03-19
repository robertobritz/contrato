<?php

declare(strict_types=1);

namespace App\Filament\Resources\Contratantes\Pages;

use App\Filament\Resources\Contratantes\ContratanteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContratantes extends ListRecords
{
    protected static string $resource = ContratanteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
