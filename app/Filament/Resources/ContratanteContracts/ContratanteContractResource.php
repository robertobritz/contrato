<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContratanteContracts;

use App\Filament\Resources\ContratanteContracts\Pages\CreateContratanteContract;
use App\Filament\Resources\ContratanteContracts\Pages\EditContratanteContract;
use App\Filament\Resources\ContratanteContracts\Pages\ListContratanteContracts;
use App\Filament\Resources\ContratanteContracts\Schemas\ContratanteContractForm;
use App\Filament\Resources\ContratanteContracts\Tables\ContratanteContractsTable;
use App\Models\ContratanteContract;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ContratanteContractResource extends Resource
{
    protected static ?string $model = ContratanteContract::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentDuplicate;

    protected static ?string $modelLabel = 'Contrato do Contratante';

    protected static ?string $pluralModelLabel = 'Contratos dos Contratantes';

    public static function form(Schema $schema): Schema
    {
        return ContratanteContractForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContratanteContractsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('contract', fn(Builder $query) => $query->where('user_id', auth()->id()));
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContratanteContracts::route('/'),
            'create' => CreateContratanteContract::route('/create'),
            'edit' => EditContratanteContract::route('/{record}/edit'),
        ];
    }
}
