<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClientContracts;

use App\Filament\Resources\ClientContracts\Pages\EditClientContract;
use App\Filament\Resources\ClientContracts\Pages\ListClientContracts;
use App\Filament\Resources\ClientContracts\Schemas\ClientContractForm;
use App\Filament\Resources\ClientContracts\Tables\ClientContractsTable;
use App\Models\ClientContract;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClientContractResource extends Resource
{
    protected static ?string $model = ClientContract::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentDuplicate;

    protected static ?string $modelLabel = 'Contrato do Cliente';

    protected static ?string $pluralModelLabel = 'Contratos dos Clientes';

    public static function form(Schema $schema): Schema
    {
        return ClientContractForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientContractsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('contract', fn (Builder $query) => $query->where('user_id', auth()->id()));
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClientContracts::route('/'),
            'edit' => EditClientContract::route('/{record}/edit'),
        ];
    }
}
