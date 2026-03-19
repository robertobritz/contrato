<?php

declare(strict_types=1);

namespace App\Filament\Resources\ObjetoContratos;

use App\Filament\Resources\ObjetoContratos\Pages\CreateObjetoContrato;
use App\Filament\Resources\ObjetoContratos\Pages\EditObjetoContrato;
use App\Filament\Resources\ObjetoContratos\Pages\ListObjetoContratos;
use App\Filament\Resources\ObjetoContratos\Schemas\ObjetoContratoForm;
use App\Filament\Resources\ObjetoContratos\Tables\ObjetoContratosTable;
use App\Models\ObjetoContrato;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ObjetoContratoResource extends Resource
{
    protected static ?string $model = ObjetoContrato::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $modelLabel = 'Objeto do Contrato';

    protected static ?string $pluralModelLabel = 'Objetos de Contrato';

    public static function form(Schema $schema): Schema
    {
        return ObjetoContratoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ObjetoContratosTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('contratante', fn(Builder $query) => $query->where('user_id', auth()->id()));
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListObjetoContratos::route('/'),
            'create' => CreateObjetoContrato::route('/create'),
            'edit' => EditObjetoContrato::route('/{record}/edit'),
        ];
    }
}
